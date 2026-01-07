<?php
include "../config.php";
require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;

// Initialize Razorpay API
$api = new Api($api_key, $api_secret);

$error_message = "";
$payment_success = false;


// You can also define your Razorpay API keys here

try {
    // Step 1: Connect to the database
    $con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($con->connect_error) {
        die("Database connection failed: " . $con->connect_error);
    }

    // Step 2: Fetch Razorpay Orders (with pagination)
    $limit = 50; // Set the number of orders to fetch per page (maximum is 50)
    $skip = 0;   // Set the offset for pagination, use 0 to fetch the first page
    $orders = [];
    
    do {
        $response = $api->order->all(array("count" => $limit, "skip" => $skip));
        $orders = array_merge($orders, $response['items']);
        $skip += $limit; // Increase the offset for the next page
    } while (count($response['items']) === $limit); // Keep fetching until there are less than 'limit' orders returned

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
        } else {
            $error_message = "No pending orders found with amount due as 0.";
        }

    } else {
        $error_message = "No pending orders found in the database.";
    }

    // Close the database connection after the update
    $con->close();

} catch (Exception $e) {
    // Log or display errors
    $error_message = "Error processing the payment: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capture Single Order</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2>Orders Paid but not Updated</h2>
            </div>
            <div class="card-body">
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                <?php elseif ($payment_success): ?>
                    <div class="alert alert-success">Payment status updated successfully for pending orders.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
