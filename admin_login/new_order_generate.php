<?php

include '../config.php';
include 'generate_tokan.php';

// Query to fetch all orders with payment_status = 'paid'
$query = "SELECT * FROM tbl_old_orders WHERE payment_status = 'paid' AND (ship_shipment_id IS NULL OR ship_shipment_id = '') ORDER BY `id` ASC LIMIT 250;";

// Execute the query
$result = mysqli_query($con, $query);

if ($result && mysqli_num_rows($result) > 0) {
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
        $value_books = 270;
        $value_charges = $order['value_charges'];
        $qty = $order['qty'];
        $total_amount = $value_books * $qty + $value_charges;

        $date = DateTime::createFromFormat('d-m-Y', $order['order_date']);
        $formatted_date = $date ? $date->format('Y-m-d') : date('Y-m-d');

        $address_line_1 = "$street & $landmark";
        $address_line_2 = "City: $city, Tal: $taluka, Dist: $district";

        // Ensure the total address length does not exceed 190 characters
        if (strlen($address_line_1 . ' ' . $address_line_2) > 190) {
            $address_line_2 = "$city, $taluka, $district";
        }
        if (strlen($address_line_1 . ' ' . $address_line_2) > 190) {
            $address_line_2 = "$city";
        }

        $total_weight = $qty * 0.195;
        $length_ship = 21;
        $width_ship = 15;
        $height_ship = 1;
        $updated_order_id = "v3_$id-$order_id";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/orders/create/adhoc',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode(array(
                "channel_id" => "6003585",
                "order_id" => $updated_order_id,
                "order_date" => $formatted_date,
                "pickup_location" => "Work",
                "comment" => $razorpay_id,
                "reseller_name" => "Vaibhav Dhus Enterprises",
                "billing_customer_name" => $order['fname'],
                "billing_last_name" => "",
                "billing_address" => $address_line_1,
                "billing_address_2" => $address_line_2,
                "billing_city" => $district,
                "billing_pincode" => $pincode,
                "billing_state" => "Maharashtra",
                "billing_country" => "India",
                "billing_email" => "",
                "billing_phone" => $mobno,
                "shipping_is_billing" => true,
                "order_items" => array(
                    array(
                        "name" => "Marathi - Antah Asti Prarambh",
                        "sku" => "marathi-book-v2",
                        "units" => $qty,
                        "selling_price" => $value_books,
                        "hsn" => 441122
                    )
                ),
                "payment_method" => "Prepaid",
                "shipping_charges" => $value_charges,
                "sub_total" => $total_amount,
                "length" => $length_ship,
                "breadth" => $width_ship,
                "height" => $height_ship,
                "weight" => $total_weight
            )),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $shiprocket_token
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response, true);

        if (!empty($data['order_id'])) {
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
                ship_new_channel = '" . mysqli_real_escape_string($con, $data['new_channel']) . "',
                shiprocket_account = '2'
                WHERE id = " . $id;

            if (mysqli_query($con, $update_query)) {
                echo "Order ID $order_id data updated successfully.<br>";
            } else {
                echo "Error updating record for Order ID $order_id: " . mysqli_error($con) . "<br>";
            }
        } else {
            echo "Failed to process Order ID $order_id. Response: " . json_encode($data) . "<br>";
        }
    }
} else {
    echo "No orders to process.";
}

?>
