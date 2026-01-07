<?php
require('razorpay-php/Razorpay.php'); // Ensure this path is correct

use Razorpay\Api\Api;

include '../config.php';
// Replace with your test or live keys


$order_id = $_GET['order_id'] ?? ''; // e.g., order_Mz1lqVb9DNZTXh

if (empty($order_id)) {
    die("Missing Razorpay order_id in URL");
}

try {
$api = new Api($api_key, $api_secret);

    $order = $api->order->fetch($order_id);

    echo "<h3>Order Details:</h3>";
    echo "Order ID: " . $order['id'] . "<br>";
    echo "Amount: ₹" . ($order['amount'] / 100) . "<br>";
    echo "Amount Paid: ₹" . ($order['amount_paid'] / 100) . "<br>";
    echo "Status: " . $order['status'] . "<br>";

    $payments = $order->payments();
    echo "<h3>Payments:</h3>";

    foreach ($payments['items'] as $p) {
        echo "Payment ID: " . $p['id'] . "<br>";
        echo "Status: " . $p['status'] . "<br>";
        echo "Captured: " . ($p['captured'] ? 'Yes' : 'No') . "<br>";
        echo "<hr>";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
