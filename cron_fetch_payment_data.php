<?php
ini_set('memory_limit', '512M'); // Increase PHP memory limit
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "config.php";
require('order/razorpay-php/Razorpay.php');
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

$to = strtotime("today 23:59:59"); // End of today
$from = $to - (4 * 24 * 60 * 60); // Subtract 4 days (4 * 24 hours * 60 minutes * 60 seconds)




// Set a fixed date range: from 10-06-2025 to 14-06-2025
/*$from = strtotime("2025-06-21 00:00:00");
$to   = strtotime("2025-06-24 23:59:59");
*/
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
foreach ($orders as $razorpay_order) {
    $razorpay_order_id = $razorpay_order['id'];

    // Check if this Razorpay order exists in our database with 'pending' status
    $check_query = $con->prepare("SELECT * FROM tbl_orders WHERE order_id = ? AND payment_status = 'pending'");
    $check_query->bind_param("s", $razorpay_order_id);
    $check_query->execute();
    $db_result = $check_query->get_result();

    if ($db_result->num_rows > 0) {
        $db_order = $db_result->fetch_assoc();

        // Check if amount_due is 0
        if ($razorpay_order['amount_due'] == 0) {
            // Fetch payments for the order
            try {
                $payments_response = $api->order->fetch($razorpay_order_id)->payments();

                foreach ($payments_response['items'] as $payment) {
                    if ($payment['status'] === 'captured') {
                        // Add this order for update
                        $updated_orders[] = [
                            'order_id' => $razorpay_order_id,
                            'razorpay_payment_id' => $payment['id']
                        ];
                        break; // Stop checking once a captured payment is found
                    }
                }
            } catch (Exception $e) {
                // Log any error while fetching payments
                $log_message = "Error fetching payments for order $razorpay_order_id: " . $e->getMessage();
                $log_type = "error";
                $stmt = $con->prepare("INSERT INTO tbl_logs (log_message, log_type) VALUES (?, ?)");
                $stmt->bind_param("ss", $log_message, $log_type);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    $check_query->close();
}

// Step 7: Update the records if the condition is met
if (count($updated_orders) > 0) {
    $update_query = "UPDATE tbl_orders SET payment_status = 'paid_but_need_to_fetch_info' WHERE order_id = ?";
    $update_stmt = $con->prepare($update_query);

    foreach ($updated_orders as $updated_order) {
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
    $error_message = "No captured payments found with amount_due as 0.";
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
