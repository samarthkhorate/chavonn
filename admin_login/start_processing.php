<?php
session_start();
include '../config.php';
include 'generate_tokan.php';

// Trigger order processing when the page is first loaded
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['start_processing']) && $_GET['start_processing'] == 'true') {
    // Initialize session variables for tracking progress
    $_SESSION['current_order'] = 0;
    $_SESSION['total_orders'] = 0;

    // Query to fetch all orders with payment_status = 'paid'
    $query = "SELECT * FROM `tbl_old_orders` WHERE payment_status = 'paid'";
    $result = mysqli_query($con, $query);
    $total_orders = mysqli_num_rows($result); // Total number of orders

    $_SESSION['total_orders'] = $total_orders; // Store total orders for tracking progress

    if ($result && $total_orders > 0) {
        while ($order = mysqli_fetch_assoc($result)) {
            // Fetch order details
            $id = $order['id'];
            $order_id = $order['order_id'];
            $razorpay_id = $order['razorpay_id'];
            $street = $order['street'];
            $landmark = $order['landmark'];
            $city = $order['city'];
            $taluka = $order['taluka'];
            $district = $order['district'];
            $pincode = $order['pincode'];
            $mobno = $order['mobno'];
            $value_books = $order['value_books'];
            $value_charges = $order['value_charges'];
            $total_amount = $order['total_amount'];
            $qty = $order['qty'];

            // Create DateTime object from the original format
            $date = DateTime::createFromFormat('d-m-Y', $order['order_date']);
            $formatted_date = $date->format('Y-m-d');

            // Combine street and landmark
            $address_line_1 = $street . " & " . $landmark;
            $address_line_2 = "City: " . $city . ", Taluka: " . $taluka . ", District: " . $district;

            // Calculate total weight
            $weight_per_qty = 0.195;  // Weight per unit in grams
            $total_weight = $qty * $weight_per_qty;

            // Initialize cURL for Shiprocket API
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/orders/create/adhoc',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode([
                    'order_id' => $order_id,
                    'order_date' => $formatted_date,
                    'pickup_location' => 'Primary',
                    'comment' => $razorpay_id,
                    'reseller_name' => 'Vaibhav Dhus',
                    'billing_customer_name' => $order['fname'],
                    'billing_address' => $address_line_1,
                    'billing_address_2' => $address_line_2,
                    'billing_city' => $district,
                    'billing_pincode' => $pincode,
                    'billing_state' => 'Maharashtra',
                    'billing_country' => 'India',
                    'billing_phone' => $mobno,
                    'shipping_is_billing' => true,
                    'order_items' => [
                        [
                            'name' => 'The End is the Beginning',
                            'sku' => 'book001',
                            'units' => $qty,
                            'selling_price' => $value_books,
                            'hsn' => 441122
                        ]
                    ],
                    'payment_method' => 'Prepaid',
                    'shipping_charges' => $value_charges,
                    'sub_total' => $total_amount,
                    'length' => 21,
                    'breadth' => 15,
                    'height' => 0.6,
                    'weight' => $total_weight
                ]),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $shiprocket_token
                ],
            ));

            // Execute the cURL request and check for errors
            $response = curl_exec($curl);
            if (!$response) {
                error_log("Error with cURL request: " . curl_error($curl)); // Log cURL error
                continue; // Skip to next order if error occurs
            }
            curl_close($curl);

            // Decode the response into an array
            $data = json_decode($response, true);
            if (!$data) {
                error_log("Error decoding response for order ID: $order_id - Response: $response"); // Log decoding error
                continue; // Skip to next order if response decoding fails
            }

            // If Shiprocket returns a valid order ID, update the database
            if (isset($data['order_id'])) {
                $update_query = "UPDATE tbl_old_orders SET 
                    ship_odr_id = '" . mysqli_real_escape_string($con, $data['order_id']) . "',
                    ship_ch_odr_id = '" . mysqli_real_escape_string($con, $data['channel_order_id']) . "',
                    ship_shipment_id = '" . mysqli_real_escape_string($con, $data['shipment_id']) . "',
                    ship_status = '" . mysqli_real_escape_string($con, $data['status']) . "',
                    ship_status_code = '" . mysqli_real_escape_string($con, $data['status_code']) . "',
                    ship_onboarding = '" . mysqli_real_escape_string($con, $data['onboarding_completed_now']) . "',
                    ship_awb_code = '" . mysqli_real_escape_string($con, $data['awb_code']) . "',
                    ship_courier_id = '" . mysqli_real_escape_string($con, $data['courier_company_id']) . "',
                    ship_courier_name = '" . mysqli_real_escape_string($con, $data['courier_name']) . "',
                    ship_new_channel = '" . mysqli_real_escape_string($con, $data['new_channel']) . "'
                    WHERE id = " . $id;
                if (!mysqli_query($con, $update_query)) {
                    error_log("Error updating database for order ID: $order_id - " . mysqli_error($con)); // Log DB update error
                }
            } else {
                error_log("Invalid order ID or missing data for order ID: $order_id - Response: " . json_encode($data)); // Log missing order ID
            }

            // Increment the current order counter
            $_SESSION['current_order']++;
        }
    }

    // After processing all orders, mark the process as complete
    echo json_encode(['status' => 'processing_completed']);
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Processing</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<h1>Order Processing</h1>

<!-- Button to Start Processing Orders -->
<button id="startButton">Start Fetching Orders</button>

<!-- Current Order Status -->
<p id="currentOrderStatus">Current Order: 0</p>

<script>
$(document).ready(function() {
    // Handle "Start Fetching Orders" button click
    $("#startButton").click(function() {
        // Disable the button to prevent multiple clicks
        $(this).prop('disabled', true);

        // Start the processing of orders using GET request
        window.location.href = window.location.href.split('?')[0] + '?start_processing=true';

        // Start updating the current order status
        updateCurrentOrder();
    });

    // Function to update the current order number
    function updateCurrentOrder() {
        let interval = setInterval(function() {
            $.get(window.location.href.split('?')[0] + '?status_check=true', function(response) {
                let currentOrder = response.current_order || 0;
                let totalOrders = response.total_orders || 0;

                $("#currentOrderStatus").text(`Current Order: ${currentOrder} / ${totalOrders}`);

                // Stop the interval when all orders are processed
                if (currentOrder >= totalOrders) {
                    clearInterval(interval);
                    $("#currentOrderStatus").text("All orders processed successfully!");
                }
            }, 'json');
        }, 1000); // Update every 1 second
    }
});
</script>

</body>
</html>
