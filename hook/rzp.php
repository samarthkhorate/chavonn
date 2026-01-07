<?php
/**
 * Razorpay Webhook Handler
 * Signature verification REMOVED
 * Raw JSON stored in tbl_razorpay_webhooks
 */

ini_set('display_errors', 0);
error_reporting(0);

date_default_timezone_set('Asia/Kolkata');

require_once __DIR__ . '/../config.php';

/* ===========================
   READ RAW INPUT
=========================== */
$rawPayload = file_get_contents('php://input');
$headersArr = getallheaders();

if (empty($rawPayload)) {
    http_response_code(400);
    exit('Empty payload');
}

/* ===========================
   DECODE JSON
=========================== */
$data = json_decode($rawPayload, true);
if (!is_array($data)) {
    http_response_code(400);
    exit('Invalid JSON');
}

$event = $data['event'] ?? 'unknown';

/* ===========================
   EXTRACT IDS (SAFE)
=========================== */
$razorpay_order_id   = null;
$razorpay_payment_id = null;

if (isset($data['payload']['payment']['entity'])) {
    $payment = $data['payload']['payment']['entity'];
    $razorpay_payment_id = $payment['id'] ?? null;
    $razorpay_order_id   = $payment['order_id'] ?? null;
}

/* ===========================
   STORE RAW WEBHOOK
=========================== */
$payloadEsc = mysqli_real_escape_string($con, $rawPayload);
$headersEsc = mysqli_real_escape_string($con, json_encode($headersArr));

mysqli_query($con, "
    INSERT INTO tbl_razorpay_webhooks (
        event,
        razorpay_order_id,
        razorpay_payment_id,
        payload,
        headers
    ) VALUES (
        '".mysqli_real_escape_string($con, $event)."',
        '".mysqli_real_escape_string($con, (string)$razorpay_order_id)."',
        '".mysqli_real_escape_string($con, (string)$razorpay_payment_id)."',
        '$payloadEsc',
        '$headersEsc'
    )
");

/* ===========================
   HANDLE EVENTS
=========================== */
if ($event === 'payment.captured' && $razorpay_order_id) {

    $amount = ($payment['amount'] ?? 0) / 100;
    $method = $payment['method'] ?? null;
    $bank   = $payment['bank'] ?? null;
    $upi    = $payment['vpa'] ?? null;

    mysqli_query($con, "
        UPDATE tbl_orders SET
            payment_status = 'paid',
            pg_status = 'captured',
            bank_ref_no = '".mysqli_real_escape_string($con,$razorpay_payment_id)."',
            pg_amount = '$amount',
            pg_mode = '".mysqli_real_escape_string($con,$method)."',
            pg_bank_name = '".mysqli_real_escape_string($con,$bank)."',
            pg_upi_va = '".mysqli_real_escape_string($con,$upi)."',
            pg_addedon = NOW(),
            updated_at = NOW()
        WHERE pg_txnid = '".mysqli_real_escape_string($con,$razorpay_order_id)."'
    ");

} elseif ($event === 'payment.failed' && $razorpay_order_id) {

    $error_desc = $payment['error_description'] ?? 'Payment failed';

    mysqli_query($con, "
        UPDATE tbl_orders SET
            payment_status = 'failed',
            pg_status = 'failed',
            pg_error_message = '".mysqli_real_escape_string($con,$error_desc)."',
            updated_at = NOW()
        WHERE pg_txnid = '".mysqli_real_escape_string($con,$razorpay_order_id)."'
    ");
}

/* ===========================
   RESPOND OK
=========================== */
http_response_code(200);
echo json_encode(['status' => 'received']);
