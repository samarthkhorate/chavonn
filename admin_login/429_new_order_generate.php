
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Orders</title>
    <style>
        .progress-container {
            width: 100%;
            background-color: #f3f3f3;
            border-radius: 10px;
        }
        .progress-bar {
            height: 20px;
            width: 0;
            background-color: #4caf50;
            text-align: center;
            color: white;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <h2>Processing Orders</h2>
    <div class="progress-container">
        <div class="progress-bar" id="progress-bar">0%</div>
    </div>
    <p id="status">Starting...</p>

    <script>
        function updateProgress() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_progress.php', true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var progress = JSON.parse(xhr.responseText);
                    var percent = (progress.current_order / progress.total_orders) * 100;
                    document.getElementById('progress-bar').style.width = percent + '%';
                    document.getElementById('progress-bar').innerText = Math.round(percent) + '%';
                    document.getElementById('status').innerText = 'Processed Orders: ' + progress.current_order + ' / ' + progress.total_orders;
                    
                    if (progress.current_order < progress.total_orders) {
                        setTimeout(updateProgress, 1000);
                    } else {
                        document.getElementById('status').innerText = 'Processing Complete!';
                    }
                }
            };
            xhr.send();
        }

        window.onload = function () {
            updateProgress();
        };
    </script>
</body>
</html>

<?php

include '../config.php';
include 'generate_tokan.php';

// Query to fetch all orders with payment_status = 'paid'
$query = "SELECT * FROM `tbl_old_orders` WHERE `payment_status` = 'paid' AND `ship_status_code` = '429' ORDER BY `id` DESC;";

// Execute the query
$result = mysqli_query($con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    // Loop through the orders
    while ($order = mysqli_fetch_assoc($result)) {
        // Extract order details
        $id = $order['id'];
        $order_id = $order['order_id']; // Assuming the order_id is stored in tbl_old_orders
        $razorpay_id = $order['razorpay_id']; // Assuming razorpay_id is stored in tbl_old_orders
        $street = $order['street'];
        $landmark = $order['landmark'];
        $city = $order['city'];
        $taluka = $order['taluka'];
        $district = $order['district'];
        $pincode = $order['pincode'];
        $mobno = $order['mobno'];
        $value_books = $order['value_books']; // Price of books
        $value_charges = $order['value_charges']; // Shipping charges
        $total_amount = $order['total_amount']; // Total amount for the order
        $qty = $order['qty']; // Quantity of items

        // Create DateTime object from the original format
        $date = DateTime::createFromFormat('d-m-Y', $order['order_date']);

        // Format the date to YYYY-MM-DD
        $formatted_date = $date->format('Y-m-d');

        // Combine street and landmark
        $address_line_1 = $street . " & " . $landmark;

        // Combine city, taluka, and district
        $address_line_2 = "City : " . $city . ", Tal : " . $taluka . ", Dist : " . $district;
        $weight_per_qty = 0.195;  // Weight per unit in grams
        $updated_order_id = "apiv1_".$id."-".$order_id;

        // Calculate total weight
        $total_weight = $qty * $weight_per_qty;

        // Initialize cURL
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
            CURLOPT_POSTFIELDS =>'{
                "order_id": "' . $updated_order_id . '",
                "order_date": "' . $formatted_date . '",
                "pickup_location": "Primary",
                "comment": "' . $razorpay_id . '",
                "reseller_name": "Vaibhav Dhus",
                "billing_customer_name": "' . $order['fname'] . '",
                "billing_last_name": "",
                "billing_address": "' . $address_line_1 . '",
                "billing_address_2": "' . $address_line_2 . '",
                "billing_city": "' . $district . '",
                "billing_pincode": "' . $pincode . '",
                "billing_state": "Maharashtra",
                "billing_country": "India",
                "billing_email": "",
                "billing_phone": "' . $mobno . '",
                "shipping_is_billing": true,
                "order_items": [
                    {
                        "name": "The End is the Beginning",
                        "sku": "book001",
                        "units": ' . $qty . ',
                        "selling_price": ' . $value_books . ',
                        "discount": "",
                        "tax": "",
                        "hsn": 441122
                    }
                ],
                "payment_method": "Prepaid",
                "shipping_charges": ' . $value_charges . ',
                "giftwrap_charges": 0,
                "transaction_charges": 0,
                "total_discount": 0,
                "sub_total": ' . $total_amount . ',
                "length": 21,
                "breadth": 15,
                "height": 0.6,
                "weight": ' . $total_weight . '
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $shiprocket_token
            ),
        ));

        // Execute the cURL request
        $response = curl_exec($curl);

        curl_close($curl);

        // Decode the response into an array
        $data = json_decode($response, true);
echo "<pre>";
print_r($data);
echo "</pre>";
        // Check if data contains the expected values
            // Prepare the SQL update query
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
                WHERE id = " . $id; // Update order based on the order ID

            // Execute the update query
            if (mysqli_query($con, $update_query)) {
                echo "Order ID $order_id data updated successfully.<br>";
            } else {
                echo "Error updating record for Order ID $order_id: " . mysqli_error($con) . "<br>";
            }
        } 
    }


?>