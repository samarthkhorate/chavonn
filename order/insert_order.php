<?php
/**
 * lang_order_place.php
 * Backend to insert order data (Prepaid or Partial COD) and start Easebuzz payment.
 * - No prepared statements (regular INSERT with real_escape_string).
 * - COD = partial COD (advance collected now via Easebuzz; remaining at delivery).
 */

date_default_timezone_set('Asia/Kolkata');

// =======================
// Includes / Config
// =======================
require_once __DIR__ . '/../config.php'; // must define $con (mysqli)
require_once __DIR__ . '/../easebuzz-lib/easebuzz_payment_gateway.php'; // Easebuzz PHP SDK

// If your config.php DOES NOT define these, uncomment and fill them here.
// $MERCHANT_KEY = 'YOUR_EASEBUZZ_KEY';
// $SALT         = 'YOUR_EASEBUZZ_SALT';
// $ENV          = 'test'; // or 'prod'
// $payment_url  = 'https://pay.easebuzz.in/pay/';

if (!isset($MERCHANT_KEY, $SALT, $ENV, $payment_url)) {
    http_response_code(500);
    exit('Gateway configuration missing: please define $MERCHANT_KEY, $SALT, $ENV, $payment_url.');
}

// =======================
// Helpers
// =======================
function generateOrderId(): string {
    return date('ymdHis') . str_pad((string)mt_rand(0, 99), 2, '0', STR_PAD_LEFT);
}
function esc_or_null(?string $val): string {
    global $con;
    if ($val === null) return 'NULL';
    $val = trim($val);
    if ($val === '') return 'NULL';
    return "'" . $con->real_escape_string($val) . "'";
}
function must($val, string $name) {
    if (!isset($val) || trim((string)$val) === '') {
        http_response_code(422);
        exit("Error: Missing required field: {$name}");
    }
}
function safe_productinfo(string $raw): string {
    $name = preg_replace('/[^A-Za-z0-9 \-]/', '', $raw);
    $name = preg_replace('/\s+/', ' ', trim($name));
    if ($name === '') $name = 'BOOK';
    return substr($name, 0, 100);
}
function base_url_here(): string {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir    = rtrim(str_replace('\\', '/', dirname($_SERVER['PHP_SELF'] ?? '/')), '/');
    if ($dir === '') $dir = '/';
    return $scheme . '://' . $host . $dir;
}

// =======================
// Read POST
// =======================
$fname       = $_POST['full_name']      ?? '';
$mobno       = $_POST['mobile_number']  ?? '';
$street      = $_POST['street_name']    ?? '';
$landmark    = $_POST['landmark']       ?? '';
$city        = $_POST['city_name']      ?? '';
$taluka      = $_POST['taluka']         ?? '';
$district    = $_POST['district']       ?? '';
$pincode     = $_POST['pincode']        ?? '';
$product_sku = $_POST['language']       ?? ''; // sku
$qty         = isset($_POST['quantity']) && (int)$_POST['quantity'] > 0 ? (int)$_POST['quantity'] : 1;
$order_type  = $_POST['paymentMode']    ?? 'prepaid'; // 'prepaid' | 'cod'

// =======================
// Validate basics
// =======================
must($fname, 'full_name');
must($mobno, 'mobile_number');
must($pincode, 'pincode');
must($product_sku, 'product_sku');

if (!preg_match('/^\d{10}$/', $mobno)) {
    http_response_code(422);
    exit('Error: Invalid mobile number (10 digits).');
}
if (!preg_match('/^\d{6}$/', $pincode)) {
    http_response_code(422);
    exit('Error: Invalid pincode (6 digits).');
}
$order_type = in_array($order_type, ['prepaid','cod'], true) ? $order_type : 'prepaid';

// =======================
// Fetch product
// =======================
$sku_esc = $con->real_escape_string($product_sku);
$product_sql = "
    SELECT product_name, product_mrp, product_prepaid_price, product_cod_charge, product_cod_amount
    FROM tbl_products
    WHERE product_sku_code = '{$sku_esc}'
    LIMIT 1
";
$product_res = $con->query($product_sql);
if (!$product_res || $product_res->num_rows === 0) {
    http_response_code(404);
    exit('Error: Product not found for the given SKU.');
}
$product = $product_res->fetch_assoc();

// =======================
// Compute amounts (FIXED)
// =======================
// Raw numbers
$product_name      = (string)$product['product_name'];
$item_mrp          = (float)$product['product_mrp'];
$prepaid_price_raw = (float)$product['product_prepaid_price'];
$cod_charge_raw    = (float)$product['product_cod_charge']; // advance per unit
$cod_amount_raw    = (float)$product['product_cod_amount']; // remaining per unit

// Sensible fallbacks
$unit_prepaid  = ($prepaid_price_raw > 0) ? $prepaid_price_raw : ($item_mrp > 0 ? $item_mrp : 0.00);
$unit_cod_adv  = ($cod_charge_raw    > 0) ? $cod_charge_raw    : 0.00;
$unit_cod_rem  = ($cod_amount_raw    > 0) ? $cod_amount_raw    : max(0.00, $unit_prepaid - $unit_cod_adv);

// Multiply by quantity
$prepaid_total = round($unit_prepaid * $qty, 2);
$cod_adv_total = round($unit_cod_adv * $qty, 2);  // to be paid now
$cod_rem_total = round($unit_cod_rem * $qty, 2);  // to be paid on delivery

if ($order_type === 'prepaid') {
    // Charge now and store the total as full price
    $pay_now              = $prepaid_total;
    $cod_advance_amount   = 0.00;
    $cod_remaining_amount = 0.00;
    $order_total_amount   = $prepaid_total; // <— prepaid total
} else {
    // Partial COD: pay advance now, rest on delivery
    $pay_now              = $cod_adv_total;          // Easebuzz charge NOW
    $cod_advance_amount   = $cod_adv_total;
    $cod_remaining_amount = $cod_rem_total;
    // IMPORTANT: store TOTAL (advance + remaining) in tbl_orders.total_amount
    $order_total_amount   = round($cod_adv_total + $cod_rem_total, 2); // <— FIXED
}

// =======================
// Prepare DB fields
// =======================
$order_id       = generateOrderId();
$order_date     = date('Y-m-d');
$order_time     = date('H:i:s');
$payment_status = 'pending';
$mode_of_payment= 'easebuzz';
$channel_id     = 1;

// =======================
// INSERT
// =======================
$insert_sql = "
INSERT INTO tbl_orders (
    order_id, fname, mobno, street, landmark, city, taluka, district,
    pincode, product_sku, item_mrp, qty, order_type, mode_of_payment, total_amount,
    cod_advance_amount, cod_remaining_amount,
    order_date, order_time, payment_status, created_at, updated_at, channel_id
) VALUES (
    '{$order_id}',
    " . esc_or_null($fname) . ",
    " . esc_or_null($mobno) . ",
    " . esc_or_null($street) . ",
    " . esc_or_null($landmark) . ",
    " . esc_or_null($city) . ",
    " . esc_or_null($taluka) . ",
    " . esc_or_null($district) . ",
    " . esc_or_null($pincode) . ",
    " . esc_or_null($product_sku) . ",
    " . number_format((float)$item_mrp, 2, '.', '') . ",
    " . (int)$qty . ",
    " . esc_or_null($order_type) . ",
    " . esc_or_null($mode_of_payment) . ",
    " . number_format((float)$order_total_amount, 2, '.', '') . ",  -- <— store TOTAL
    " . number_format((float)$cod_advance_amount, 2, '.', '') . ",
    " . number_format((float)$cod_remaining_amount, 2, '.', '') . ",
    " . esc_or_null($order_date) . ",
    " . esc_or_null($order_time) . ",
    " . esc_or_null($payment_status) . ",
    NOW(), NOW(), " . (int)$channel_id . "
)";
$ins_ok = $con->query($insert_sql);
if (!$ins_ok) {
    http_response_code(500);
    exit('Database Error: ' . $con->error);
}

// =======================
// Build Easebuzz payload
// =======================
$eb = new Easebuzz($MERCHANT_KEY, $SALT, $ENV);

$productinfo = safe_productinfo($product_name);

$base        = base_url_here();
$success_url = $base . "/thankyou.php?order_id=" . rawurlencode($order_id);
$failure_url = $base . "/thankyou.php?order_id=" . rawurlencode($order_id);

// amount to charge now (prepaid = total; COD = advance)
$amount_to_charge = number_format((float)$pay_now, 2, '.', '');

$postData = [
    "txnid"       => $order_id,
    "amount"      => $amount_to_charge,
    "firstname"   => $fname,
    "email"       => "demo@gmail.com",
    "phone"       => $mobno,
    "productinfo" => $productinfo,

    "surl"        => $success_url,
    "furl"        => $failure_url,

    "address1"    => substr((string)$street,   0, 100),
    "address2"    => substr((string)$landmark, 0, 100),
    "city"        => substr((string)$city,     0, 50),
    "state"       => "MH",
    "country"     => "India",
    "zipcode"     => $pincode,

    // UDFs for audit
    "udf1" => (string)$order_type,                                        // prepaid|cod
    "udf2" => (string)$qty,
    "udf3" => number_format((float)$item_mrp,            2, '.', ''),     // MRP (single)
    "udf4" => number_format((float)$cod_advance_amount,  2, '.', ''),     // COD advance total (if cod)
    "udf5" => number_format((float)$cod_remaining_amount,2, '.', ''),     // COD remaining total (if cod)
];

// =======================
// Initiate payment & redirect
// =======================
$response = $eb->initiatePaymentAPI($postData);
$respArr  = is_string($response) ? json_decode($response, true) : $response;

if (is_array($respArr) && isset($respArr['status']) && (int)$respArr['status'] === 1 && !empty($respArr['access_key'])) {
    $redirectUrl = rtrim($payment_url, '/') . '/' . $respArr['access_key'];
    echo "<script>window.location.href=" . json_encode($redirectUrl) . ";</script>";
    exit;
}

http_response_code(502);
echo "<h3 style='color:#d32f2f'>Error generating payment link</h3>";
if (isset($respArr['error_desc'])) {
    echo "<p><b>Gateway says:</b> " . htmlspecialchars($respArr['error_desc']) . "</p>";
}
if (isset($respArr['data'])) {
    $dataOut = is_string($respArr['data']) ? $respArr['data'] : json_encode($respArr['data'], JSON_PRETTY_PRINT);
    echo "<pre style='white-space:pre-wrap;background:#f8f8f8;padding:12px;border-radius:6px;'>" . htmlspecialchars($dataOut) . "</pre>";
}
?>
