<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
date_default_timezone_set('Asia/Kolkata');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/razorpay-php/Razorpay.php';

use Razorpay\Api\Api;

/* ===========================
   VALIDATE REQUEST
=========================== */
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST)) {
    http_response_code(400);
    exit('Invalid request');
}

/* ===========================
   READ POST DATA
=========================== */
$full_name      = trim($_POST['full_name'] ?? '');
$mobile_number  = trim($_POST['mobile_number'] ?? '');
$street_name    = trim($_POST['street_name'] ?? '');
$landmark       = trim($_POST['landmark'] ?? '');
$city_name      = trim($_POST['city_name'] ?? '');
$taluka         = trim($_POST['taluka'] ?? '');
$district       = trim($_POST['district'] ?? '');
$pincode        = trim($_POST['pincode'] ?? '');
$language       = trim($_POST['language'] ?? ''); // SKU
$quantity       = max(1, (int)($_POST['quantity'] ?? 1));
$paymentMode    = $_POST['paymentMode'] ?? 'prepaid';

/* ===========================
   BASIC VALIDATION
=========================== */
if ($full_name === '' || $mobile_number === '' || $pincode === '' || $language === '') {
    exit('Missing required fields');
}
if (!preg_match('/^\d{10}$/', $mobile_number)) exit('Invalid mobile number');
if (!preg_match('/^\d{6}$/', $pincode)) exit('Invalid pincode');

$paymentMode = in_array($paymentMode, ['prepaid','cod'], true) ? $paymentMode : 'prepaid';

/* ===========================
   FETCH PRODUCT
=========================== */
$stmt = $con->prepare("
    SELECT product_mrp, product_prepaid_price, product_cod_charge, product_cod_amount
    FROM tbl_products
    WHERE product_sku_code = ?
    LIMIT 1
");
$stmt->bind_param("s", $language);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows !== 1) exit('Product not found');
$product = $res->fetch_assoc();

/* ===========================
   AMOUNT CALCULATION
=========================== */
$item_mrp = (float)$product['product_mrp'];

if ($paymentMode === 'prepaid') {
    $pay_now = round($product['product_prepaid_price'] * $quantity, 2);
    $cod_adv = 0.00;
    $cod_rem = 0.00;
    $total   = $pay_now;
} else {
    $cod_adv = round($product['product_cod_charge'] * $quantity, 2);
    $cod_rem = round($product['product_cod_amount'] * $quantity, 2);
    $pay_now = $cod_adv;
    $total   = $cod_adv + $cod_rem;
}

/* ===========================
   CREATE RAZORPAY ORDER
=========================== */
$page_tracking_id = date('ymdHis') . rand(10,99);
$api = new Api($api_key, $api_secret);

try {
    $rpOrder = $api->order->create([
        'receipt'  => $page_tracking_id,
        'amount'   => (int)round($pay_now * 100),
        'currency' => 'INR'
    ]);
} catch (Exception $e) {
    exit('Razorpay Error: '.$e->getMessage());
}

$razorpay_order_id = $rpOrder->id;

/* ===========================
   INSERT ORDER (EASEBUZZ TABLE)
=========================== */
$sql = "
INSERT INTO tbl_orders (
    order_id, fname, mobno, street, landmark, city, taluka, district, pincode,
    product_sku, item_mrp, qty, order_type, mode_of_payment,
    total_amount, cod_advance_amount, cod_remaining_amount,
    order_date, order_time, payment_status,
    pg_txnid, pg_amount, pg_status, pg_payment_source,
    meta, channel_id, created_at, updated_at
) VALUES (
    '$page_tracking_id','$full_name','$mobile_number','$street_name','$landmark',
    '$city_name','$taluka','$district','$pincode',
    '$language','$item_mrp','$quantity','$paymentMode','razorpay',
    '$total','$cod_adv','$cod_rem',
    CURDATE(),CURTIME(),'pending',
    '$razorpay_order_id','$pay_now','created','razorpay',
    NULL,1,NOW(),NOW()
)";
if (!$con->query($sql)) exit('DB Error: '.$con->error);

$callback_url = "https://beperfectgroup.in/order/thank_you.php?order_id=$page_tracking_id";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Secure Payment</title>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<style>
body{
    margin:0;
    font-family:Poppins,Arial,sans-serif;
    background:linear-gradient(135deg,#f0f4f8,#d9e4f5);
}
.card{
    max-width:420px;
    background:#fff;
    margin:10vh auto;
    padding:30px;
    border-radius:14px;
    text-align:center;
    box-shadow:0 15px 35px rgba(0,0,0,.15);
}
.btn{
    padding:14px 30px;
    border:none;
    border-radius:30px;
    background:linear-gradient(135deg,#6a9c78,#4a7659);
    color:#fff;
    font-size:16px;
    cursor:pointer;
}
.note{color:#6c757d;font-size:14px;margin-top:15px;}
</style>
</head>

<body onload="startPayment()">

<div class="card">
    <h2>Complete Your Payment</h2>
    <p>Order ID: <b><?= htmlspecialchars($page_tracking_id) ?></b></p>
    <p>Amount to Pay: <b>₹<?= number_format($pay_now,2) ?></b></p>

    <button class="btn" onclick="startPayment()">Pay Now</button>

    <p class="note">Do not refresh or close this page.</p>
</div>

<script>
function startPayment(){
    var options = {
        key: "<?= htmlspecialchars($api_key) ?>",
        amount: "<?= (int)round($pay_now * 100) ?>",
        currency: "INR",
        name: "Be Perfect Group",
        description: "Order <?= htmlspecialchars($page_tracking_id) ?>",
        order_id: "<?= htmlspecialchars($razorpay_order_id) ?>",
        callback_url: "<?= htmlspecialchars($callback_url) ?>",
        theme: { color:"#4a7659" },
        modal: {
            ondismiss: function(){
                alert("Payment cancelled. You can retry.");
            }
        }
    };
    var rzp = new Razorpay(options);
    rzp.open();
}
</script>

</body>
</html>
