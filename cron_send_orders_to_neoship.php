<?php
/* =========================================================
   STRICT ERROR VISIBILITY
========================================================= */
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);

/* =========================================================
   CONFIG & DB
========================================================= */
include 'config.php'; // must define $con (mysqli)

/* =========================================================
   CREATE LOG TABLE (IF NOT EXISTS)
========================================================= */
mysqli_query($con, "
CREATE TABLE IF NOT EXISTS tbl_order_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(100),
    message TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

/* =========================================================
   HELPER: LOG
========================================================= */
function log_order($con, $order_id, $message) {
    $oid = mysqli_real_escape_string($con, $order_id);
    $msg = mysqli_real_escape_string($con, $message);
    mysqli_query(
        $con,
        "INSERT INTO tbl_order_logs (order_id, message) VALUES ('$oid', '$msg')"
    );
}

/* =========================================================
   FETCH ELIGIBLE ORDERS (DIRECT)
========================================================= */
$sql = "
    SELECT * 
    FROM tbl_orders 
    WHERE payment_status IN ('success', 'paid')
      AND (ship_shipment_id IS NULL OR ship_shipment_id = '')
ORDER BY id DESC
LIMIT 25
";

$res = mysqli_query($con, $sql);
if (!$res) {
    die("❌ DB ERROR: " . mysqli_error($con));
}

if (mysqli_num_rows($res) === 0) {
    echo "<h3>No orders to process</h3>";
    exit;
}

echo "<h2>🚚 Sending Orders to NeoShip</h2><hr>";

/* =========================================================
   PROCESS ORDERS
========================================================= */
while ($order = mysqli_fetch_assoc($res)) {

    try {

        /* =============================================
           FETCH PRODUCT
        ============================================= */
        $stmt = $con->prepare(
            "SELECT * FROM tbl_products WHERE product_sku_code = ? LIMIT 1"
        );
        $stmt->bind_param("s", $order['product_sku']);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        if (!$product) {
            throw new Exception("Product not found for SKU {$order['product_sku']}");
        }

        /* =============================================
           BASIC ORDER VALUES
        ============================================= */
        $qty = max(1, (int)$order['qty']);

        $payment_mode = (strtolower($order['order_type']) === 'cod')
            ? 'COD'
            : 'Pre-paid';

        $cod_amount = ($payment_mode === 'COD')
            ? (float)$order['total_amount']
            : 0;

        $weight_kg = (float)($product['product_weight_kgs'] ?? 0.5);
        $weight_gm = max(100, (int)($qty * $weight_kg * 1000));

        $length = (int)($product['product_length_cm'] ?? 10);
        $width  = (int)($product['product_width_cm'] ?? 10);
        $height = (int)($product['product_height_cm'] ?? 10);

        $address = trim($order['street'] . ' ' . $order['landmark']);
        if (strlen($address) > 200) {
            $address = substr($address, 0, 197) . "...";
        }

        /* =============================================
           NEOSHIP PAYLOAD
        ============================================= */
        $payload = [
            "seller_code"       => "985263",
            "order_id"          => $order['order_id'],
            "name"              => $order['fname'],
            "phone"             => $order['mobno'],
            "address"           => $address,
            "city"              => $order['city'],
            "state"             => "Maharashtra",
            "country"           => "India",
            "delevery_pincode"  => $order['pincode'],
            "payment_mode"      => $payment_mode,
            "cod_amount"        => round($cod_amount, 2),
            "total_amount"      => round((float)$order['total_amount'], 2),
            "weight"            => $weight_gm,
            "shipment_length"   => $length,
            "shipment_width"    => $width,
            "shipment_height"   => $height,
            "shipping_mode"     => "Surface",
            "quantity"          => $qty,
            "products_desc"     => $product['product_name'],
            "address_type"      => "home"
        ];

        /* =============================================
           SEND TO NEOSHIP
        ============================================= */
        $ch = curl_init("https://ship.neotechking.com/api/order_add.php");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => ["Content-Type: application/json"],
            CURLOPT_TIMEOUT        => 30
        ]);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception("cURL Error: " . curl_error($ch));
        }
        curl_close($ch);

        $neo = json_decode(trim($response), true);
        if (
            json_last_error() !== JSON_ERROR_NONE ||
            empty($neo['success']) ||
            $neo['success'] !== true ||
            empty($neo['order_id'])
        ) {
            throw new Exception("NeoShip Failed Response: " . $response);
        }

        $neo_id = $neo['order_id'];

        /* =============================================
           UPDATE ORDER
        ============================================= */
        $upd = "
        UPDATE tbl_orders SET
            ship_shipment_id = '".mysqli_real_escape_string($con, $neo_id)."',
            ship_odr_id      = '".mysqli_real_escape_string($con, $neo_id)."',
            routing_used     = 'neoship',
            updated_at       = NOW()
        WHERE id = {$order['id']}
        ";
        mysqli_query($con, $upd);

        /* =============================================
           LOG SUCCESS
        ============================================= */
        log_order(
            $con,
            $order['order_id'],
            "✅ NeoShip Order Created | Shipment ID: {$neo_id}"
        );

        echo "<div style='color:green;font-weight:bold'>
              ✅ {$order['order_id']} → NeoShip SUCCESS ({$neo_id})
              </div><hr>";

    } catch (Exception $e) {

        log_order(
            $con,
            $order['order_id'],
            "❌ " . $e->getMessage()
        );

        echo "<div style='color:red;font-weight:bold'>
              ❌ {$order['order_id']} FAILED<br>
              {$e->getMessage()}
              </div><hr>";
    }

    @ob_flush();
    @flush();
}

echo "<h3>✅ Processing Completed</h3>";
