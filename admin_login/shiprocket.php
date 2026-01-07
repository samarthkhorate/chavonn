<?php
session_start();
include '../config.php';
include 'generate_tokan.php';

// Query to fetch all orders with payment_status = 'paid'
$query = "SELECT * FROM `tbl_old_orders` WHERE payment_status = 'paid'";
$result = mysqli_query($con, $query);
$total_orders = mysqli_num_rows($result); // Total number of orders

// Initialize a flag to track success
$all_updates_successful = true;

if ($result && $total_orders > 0) {
    while ($order = mysqli_fetch_assoc($result)) {
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
        $updated_id = 'api_'.$id.'_'.$order_id;
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
                'order_id' => $updated_id,
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

        // Execute the cURL request
        $response = curl_exec($curl);
        curl_close($curl);

        // Decode the response into an array
        $data = json_decode($response, true);

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

            // If the update fails, set the flag to false
            if (!mysqli_query($con, $update_query)) {
                $all_updates_successful = false;
            }
        }
    }

    // Check if all updates were successful
    if ($all_updates_successful) {
        echo "Shiprocket data fetched and sent successfully!";
    } else {
        echo "There was an error while updating some orders.";
    }
}
?>
