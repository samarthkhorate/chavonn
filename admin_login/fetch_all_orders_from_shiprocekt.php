<?php
include '../config.php';
//include 'generate_tokan.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Shipments Data Fetch</title>
</head>
<body>
    <!-- Initial message -->
    <h1 id="status-heading">Fetching shipment data...</h1>

    <?php
    // Fetch all orders with a valid `ship_shipment_id`
    $query = "SELECT id, ship_shipment_id FROM tbl_old_orders 
              WHERE ship_shipment_id IS NOT NULL 
              AND ship_status != 'Delivered' 
              AND ship_status != 'RTO Delivered' 
              AND ship_status != 'Undelivered-EN-ROUTE' 
              AND ship_status != 'RTO IN INTRANSIT' 
              AND ship_status != 'RTO Initiated' 
              AND ship_status != 'Undelivered-AT DESTINATION HUB' 
              AND ship_status != 'Undelivered-AT SOURCE HUB' 
              AND ship_status != 'Undelivered-EN-ROUTE' 
              AND ship_status != 'Undelivered-EN-ROUTE' 
              AND ship_status != 'Out for Delivery' 
              AND ship_status != 'REACHED AT DESTINATION HUB' 
              AND ship_status != 'Undelivered' 
              AND ship_status != 'Lost' 
              AND ship_status != 'need_to_shift' 
              AND payment_status = 'paid' ";

    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $order_id = $row['id'];
            $shipment_id = $row['ship_shipment_id'];
            $retry_count = 0;

            do {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://apiv2.shiprocket.in/v1/external/courier/track/shipment/$shipment_id",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $shiprocket_token,
                    ),
                ));

                $response = curl_exec($curl);
                $response_decoded = json_decode ($response,true);
                echo "<pre>";
                print_r($response_decoded);
                if (curl_errno($curl)) {
                    echo "Error for Shipment ID $shipment_id: " . curl_error($curl) . "<br>";
                    break;
                }

                // Decode the response
                $responseArray = json_decode($response, true);

                $current_status = $responseArray['tracking_data']['shipment_track'][0]['current_status'] ?? '';
                $awb_code = $responseArray['tracking_data']['shipment_track'][0]['awb_code'] ?? '';
                $courier_company_id = $responseArray['tracking_data']['shipment_track'][0]['courier_company_id'] ?? '';
                $courier_name = $responseArray['tracking_data']['shipment_track'][0]['courier_name'] ?? '';

                if (!empty($current_status) && !empty($awb_code) && !empty($courier_company_id) && !empty($courier_name)) {
                    // Update the database with the fetched data
                    $update_query = "UPDATE tbl_old_orders SET 
                                        ship_status = '" . mysqli_real_escape_string($con, $current_status) . "', 
                                        ship_awb_code = '" . mysqli_real_escape_string($con, $awb_code) . "', 
                                        ship_courier_id = '" . mysqli_real_escape_string($con, $courier_company_id) . "', 
                                        ship_courier_name = '" . mysqli_real_escape_string($con, $courier_name) . "' 
                                    WHERE id = $order_id";

                    if (mysqli_query($con, $update_query)) {
                        echo "Order ID $order_id updated successfully.<br>";
                    } else {
                        
                                    
                    }
                    break;
                } else {
                    $update_query123 = "UPDATE tbl_old_orders SET ship_status = 'need_to_shift' WHERE id = $order_id";
                        
                                     if (mysqli_query($con, $update_query123)) {
                                                                 echo "update Order ID $order_id: " . mysqli_error($con) . "marked as need_to_shift <br>";

                                         
                                     }
                    echo "Blank data received for Shipment ID $shipment_id. Retrying...<br>";
                }

                curl_close($curl);
                $retry_count++;
            } while ($retry_count < 1);

            if ($retry_count == 1) {
                echo "Failed to fetch valid data for Shipment ID $shipment_id after 3 attempts.<br>";
            }
        }

        // Update the status message
        echo "<script>document.getElementById('status-heading').innerText = 'All shipment data fetched successfully!';</script>";
    } else {
        echo "<script>document.getElementById('status-heading').innerText = 'No shipments found for tracking.';</script>";
    }
    ?>
</body>
</html>
