<?php
include "../config.php";
require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;

// Initialize Razorpay API
$api = new Api($api_key, $api_secret);

$error_message = "";
$payment_success = false;

try {
    // Step 1: Connect to the database
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($con->connect_error) {
        die("Database connection failed: " . $con->connect_error);
    }

    // Log the successful DB connection
    $log_message = "Database connection established.";
    $log_type = "info";
    $stmt = $con->prepare("INSERT INTO tbl_logs (log_message, log_type) VALUES (?, ?)");
    $stmt->bind_param("ss", $log_message, $log_type);
    $stmt->execute();
    $stmt->close();

    // Step 2: Fetch Razorpay Orders for the last 7 days (with pagination)
    $limit = 50; // Set the number of orders to fetch per page
    $skip = 0;   // Set the offset for pagination
    $orders = [];

    // Calculate timestamps for the last 7 days
    $to = time(); // Current timestamp
    $from = $to - (8 * 24 * 60 * 60);
    do {
        $response = $api->order->all([
            "count" => $limit,
            "skip" => $skip,
            "from" => $from,
            "to" => $to
        ]);

        // Directly merge the Razorpay orders without converting to array
        $orders = array_merge($orders, $response['items']);
        
        $skip += $limit; // Increase the offset for the next page
    } while (count($response['items']) === $limit); // Fetch until there are less than 'limit' orders returned

    // Log the fetched orders
    $log_message = "Fetched " . count($orders) . " Razorpay orders.";
    $log_type = "info";
    $stmt = $con->prepare("INSERT INTO tbl_logs (log_message, log_type) VALUES (?, ?)");
    $stmt->bind_param("ss", $log_message, $log_type);
    $stmt->execute();
    $stmt->close();

    // Step 3: Close the database connection for 3 seconds
    $con->close();
    sleep(3); // Wait for 3 seconds

    // Step 4: Reconnect to the database
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($con->connect_error) {
        die("Database connection failed: " . $con->connect_error);
    }

    // Step 5: Fetch all pending orders from the database
    $query = "SELECT * FROM tbl_orders WHERE payment_status = 'pending'";
    $result = $con->query($query);

    if ($result->num_rows > 0) {
        $updated_orders = []; // Array to store orders that need to be updated

        // Step 6: Compare each pending order with Razorpay orders
        while ($order = $result->fetch_assoc()) {
            $order_id = $order['order_id']; // Database order ID

            // Search for the order in the Razorpay orders array
            foreach ($orders as $razorpay_order) {
                if ($razorpay_order['id'] === $order_id) {
                    // Found the matching order, check if amount_due is 0
                    $amount_due = $razorpay_order['amount_due']; // Amount due from Razorpay order details

                    if ($amount_due == 0) {
                        // If amount_due is 0, store the order in the updated_orders array
                        $updated_orders[] = [
                            'order_id' => $order_id,
                            'razorpay_id' => $razorpay_order['id']
                        ];
                    }
                }
            }
        }

        // Step 7: Update the records if the condition is met
        if (count($updated_orders) > 0) {
            $update_query = "UPDATE tbl_orders SET payment_status = 'paid_but_need_to_fetch_info' WHERE order_id = ?";
            $update_stmt = $con->prepare($update_query);

            foreach ($updated_orders as $updated_order) {
                // Update the payment status for each order that meets the condition
                $update_stmt->bind_param("s", $updated_order['order_id']);
                $update_stmt->execute();
            }

            $update_stmt->close();
            $payment_success = true;

            // Log the successful update
            $log_message = "Updated " . count($updated_orders) . " orders to 'paid_but_need_to_fetch_info'.";
            $log_type = "success";
            $stmt = $con->prepare("INSERT INTO tbl_logs (log_message, log_type) VALUES (?, ?)");
            $stmt->bind_param("ss", $log_message, $log_type);
            $stmt->execute();
            $stmt->close();
        } else {
            $error_message = "No pending orders found with amount due as 0.";
            // Log the error
            $log_message = $error_message;
            $log_type = "error";
            $stmt = $con->prepare("INSERT INTO tbl_logs (log_message, log_type) VALUES (?, ?)");
            $stmt->bind_param("ss", $log_message, $log_type);
            $stmt->execute();
            $stmt->close();
        }

    } else {
        $error_message = "No pending orders found in the database.";
        // Log the error
        $log_message = $error_message;
        $log_type = "error";
        $stmt = $con->prepare("INSERT INTO tbl_logs (log_message, log_type) VALUES (?, ?)");
        $stmt->bind_param("ss", $log_message, $log_type);
        $stmt->execute();
        $stmt->close();
    }

    // Close the database connection after the update
    $con->close();

} catch (Exception $e) {
    // Log the error
    $error_message = "Error processing the payment: " . $e->getMessage();
    $log_message = $error_message;
    $log_type = "error";
    $stmt = $con->prepare("INSERT INTO tbl_logs (log_message, log_type) VALUES (?, ?)");
    $stmt->bind_param("ss", $log_message, $log_type);
    $stmt->execute();
    $stmt->close();
}
?>
