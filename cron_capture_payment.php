<?php

require('order/razorpay-php/Razorpay.php');
use Razorpay\Api\Api;

include "config.php";

// Initialize Razorpay API
$api = new Api($api_key, $api_secret);

// Fetch all orders that need payment capture or update
$query = "SELECT order_id, value_prepaid_total FROM tbl_orders WHERE payment_status = 'paid_but_need_to_fetch_info'";
$stmt = $con->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("No payments pending capture or update.");
}

// Loop through each order
while ($order = $result->fetch_assoc()) {
    $order_id = $order['order_id'];
    $value_prepaid_total = $order['value_prepaid_total'];
    $order_timestamp = date('Y-m-d H:i:s'); // Using created_at as order timestamp

    //echo "Processing Order ID: $order_id<br>";

    // Fetch payment details from Razorpay using the order_id
    try {
        $pay_data = $api->order->fetch($order_id)->payments();
        if (empty($pay_data['items'])) {
           // echo "No payments found for Order ID: $order_id.<br>";
            continue; // Move to the next order
        }

        $pay_array = $pay_data['items'][0];
        $razor_pay_id = $pay_array['id'];
        $before_capture_status = $pay_array['status'];

        // If payment is authorized but not captured, capture it
        if ($before_capture_status === 'authorized') {
            $amount_in_paise = $value_prepaid_total * 100;
            $capture_response = $api->payment->fetch($razor_pay_id)->capture(['amount' => $amount_in_paise, 'currency' => 'INR']);

            // Update payment status in the database to 'paid' after capture
            $update_query = "UPDATE tbl_orders SET payment_status = 'paid', razorpay_id = ? WHERE order_id = ?";
            $update_stmt = $con->prepare($update_query);
            $update_stmt->bind_param("ss", $razor_pay_id, $order_id);
            $update_stmt->execute();

            // Insert the log entry into the payment_capture_logs table
            $log_query = "INSERT INTO payment_capture_logs (order_id, razorpay_id, capture_timestamp, order_timestamp) 
                          VALUES (?, ?, ?, ?)";
            $log_stmt = $con->prepare($log_query);
            $capture_timestamp = date("Y-m-d H:i:s");  // Current timestamp when capture occurs
            $log_stmt->bind_param("ssss", $order_id, $razor_pay_id, $capture_timestamp, $order_timestamp);
            $log_stmt->execute();

          //  echo "Payment captured successfully for Order ID: $order_id<br>";
        } else {
            // Check the page tracking (order ID) for already captured payments
            $paystatus_new = $api->order->fetch($order_id);
            $paydata_new = $paystatus_new->payments();
            $payarray_new = $paydata_new['items'][0];

            $amt_requested = ($paystatus_new['amount']) / 100;  // Requested amount
            $amt_paid = ($paystatus_new['amount_paid']) / 100; // Paid amount
            $amt_due = ($paystatus_new['amount_due']) / 100;   // Amount due (must be 0)
            $pyt_status = $paystatus_new['status'];            // Payment status (must be 'paid')

            // Validate the conditions to ensure payment is complete and correct
            if ($order['value_prepaid_total'] == $amt_requested && 
                $amt_requested == $amt_paid && 
                $amt_due == 0 && 
                $pyt_status === 'paid') {
                // Update the database as payment has been captured and validated
                $update_query = "UPDATE tbl_orders SET payment_status = 'paid', razorpay_id = ? WHERE order_id = ?";
                $update_stmt = $con->prepare($update_query);
                $update_stmt->bind_param("ss", $razor_pay_id, $order_id);
                $update_stmt->execute();

                // Insert the log entry into the payment_capture_logs table
                $log_query = "INSERT INTO payment_capture_logs (order_id, razorpay_id, capture_timestamp, order_timestamp) 
                              VALUES (?, ?, ?, ?)";
                $log_stmt = $con->prepare($log_query);
                $capture_timestamp = date("Y-m-d H:i:s");  // Current timestamp when capture occurs
                $log_stmt->bind_param("ssss", $order_id, $razor_pay_id, $capture_timestamp, $order_timestamp);
                $log_stmt->execute();

               // echo "Payment successfully validated and updated for Order ID: $order_id<br>";
            } else {
              //  echo "Payment conditions not met for Order ID: $order_id.<br>";
            }
        }
    } catch (Exception $e) {
      //  echo "Error for Order ID: $order_id - " . $e->getMessage() . "<br>";
    }
}

$stmt->close();
?>
