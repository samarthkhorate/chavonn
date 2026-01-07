<?php
include 'config.php'; // Database connection

// Function to process JSON data
function processJsonData($jsonString, $con) {
    $jsonData = json_decode($jsonString, true);

    if (!$jsonData) {
        die("Invalid JSON Data");
    }

    $order_id = $jsonData['order_id'];
    $platform_order_id = $jsonData['platform_order_id'];
    $status = $jsonData['status'];
    $source = $jsonData['source'];
    $phone = $jsonData['phone'];
    $email = $jsonData['email'];
    $shipping_plan = $jsonData['shipping_plan'];
    $shipping_charges = $jsonData['shipping_charges'];
    $rto_prediction = $jsonData['rto_prediction'];
    $edd = isset($jsonData['edd']) ? $jsonData['edd'] : NULL;
    $payment_type = $jsonData['payment_type'];
    $payment_status = $jsonData['payment_status'];

    // Payment details
    $payment_gateway = $payment_method = $payment_amount = $pg_transaction_id = NULL;
    if (!empty($jsonData['payments'])) {
        $payment_gateway = $jsonData['payments'][0]['gateway'];
        $payment_method = $jsonData['payments'][0]['payment_method'];
        $payment_amount = $jsonData['payments'][0]['amount'];
        $pg_transaction_id = $jsonData['payments'][0]['pg_transaction_id'];
    }

    // Shipping details
    $shipping = $jsonData['shipping_address'];
    $shipping_first_name = $shipping['first_name'];
    $shipping_last_name = $shipping['last_name'];
    $shipping_phone = $shipping['phone'];
    $shipping_email = $shipping['email'];
    $shipping_address_line1 = $shipping['line1'];
    $shipping_address_line2 = $shipping['line2'];
    $shipping_city = $shipping['city'];
    $shipping_pincode = $shipping['pincode'];
    $shipping_state = $shipping['state'];
    $shipping_country = $shipping['country'];

    // Billing details
    $billing = $jsonData['billing_address'];
    $billing_first_name = $billing['first_name'];
    $billing_last_name = $billing['last_name'];
    $billing_phone = $billing['phone'];
    $billing_email = $billing['email'];
    $billing_address_line1 = $billing['line1'];
    $billing_address_line2 = $billing['line2'];
    $billing_city = $billing['city'];
    $billing_pincode = $billing['pincode'];
    $billing_state = $billing['state'];
    $billing_country = $billing['country'];

    // Order pricing details
    $subtotal_price = $jsonData['subtotal_price'];
    $total_discount = $jsonData['total_discount'];
    $total_amount_payable = $jsonData['total_amount_payable'];
    $cod_charges = isset($jsonData['cod_charges']) ? $jsonData['cod_charges'] : NULL;
    $loyalty_points_applied = isset($jsonData['loyalty_points_applied']) ? $jsonData['loyalty_points_applied'] : NULL;
    $discount_detail = isset($jsonData['discount_detail']) ? $jsonData['discount_detail'] : NULL;
    $tags = implode(',', $jsonData['tags']);

    // Cart details
    $variant_id = $quantity = NULL;
    if (!empty($jsonData['cart_data']['items'])) {
        $variant_id = $jsonData['cart_data']['items'][0]['variant_id'];
        $quantity = $jsonData['cart_data']['items'][0]['quantity'];
    }

    // Insert full JSON data into tbl_ck_webhooks
    $jsonDataEscaped = mysqli_real_escape_string($con, json_encode($jsonData));
    $sql1 = "INSERT INTO tbl_ck_webhooks (order_id, json_data) VALUES ('$order_id', '$jsonDataEscaped')";
    mysqli_query($con, $sql1);

    // Insert separated values into tbl_ck_orders
    $sql2 = "INSERT INTO tbl_ck_orders (
        order_id, platform_order_id, status, source, phone, email, shipping_plan, shipping_charges,
        rto_prediction, edd, payment_type, payment_status, payment_gateway, payment_method,
        payment_amount, pg_transaction_id, shipping_first_name, shipping_last_name, shipping_phone,
        shipping_email, shipping_address_line1, shipping_address_line2, shipping_city, shipping_pincode,
        shipping_state, shipping_country, billing_first_name, billing_last_name, billing_phone,
        billing_email, billing_address_line1, billing_address_line2, billing_city, billing_pincode,
        billing_state, billing_country, subtotal_price, total_discount, total_amount_payable, cod_charges,
        loyalty_points_applied, discount_detail, tags, variant_id, quantity
    ) VALUES (
        '$order_id', '$platform_order_id', '$status', '$source', '$phone', '$email', '$shipping_plan',
        '$shipping_charges', '$rto_prediction', " . ($edd ? "'$edd'" : "NULL") . ", '$payment_type',
        '$payment_status', " . ($payment_gateway ? "'$payment_gateway'" : "NULL") . ",
        " . ($payment_method ? "'$payment_method'" : "NULL") . ", " . ($payment_amount ? "'$payment_amount'" : "NULL") . ",
        " . ($pg_transaction_id ? "'$pg_transaction_id'" : "NULL") . ", '$shipping_first_name',
        '$shipping_last_name', '$shipping_phone', '$shipping_email', '$shipping_address_line1',
        " . ($shipping_address_line2 ? "'$shipping_address_line2'" : "NULL") . ", '$shipping_city',
        '$shipping_pincode', '$shipping_state', '$shipping_country', '$billing_first_name',
        '$billing_last_name', '$billing_phone', '$billing_email', '$billing_address_line1',
        " . ($billing_address_line2 ? "'$billing_address_line2'" : "NULL") . ", '$billing_city',
        '$billing_pincode', '$billing_state', '$billing_country', '$subtotal_price', '$total_discount',
        '$total_amount_payable', " . ($cod_charges ? "'$cod_charges'" : "NULL") . ",
        " . ($loyalty_points_applied ? "'$loyalty_points_applied'" : "NULL") . ",
        " . ($discount_detail ? "'$discount_detail'" : "NULL") . ", '$tags', '$variant_id', '$quantity'
    )";

    mysqli_query($con, $sql2);
}

// Example usage
$jsonData = file_get_contents('php://input'); // Read JSON from request
processJsonData($jsonData, $con);
?>
