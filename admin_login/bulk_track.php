<?php
include '../config.php';
include 'generate_tokan.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Fetch Process</title>
</head>
<body>
    <!-- Initial message -->
    <h1 id="status-heading">Data fetch process is started...</h1>

    <?php
    // Fetch all orders with a valid `ship_shipment_id`
$query = "SELECT id, ship_shipment_id, order_id 
          FROM tbl_old_orders 
          WHERE ship_shipment_id IS NOT NULL 
          AND ship_shipment_id != '' 
          AND ship_status != 'Delivered'";


// ONLY NEW
/*$query = "SELECT id, ship_shipment_id, order_id 
          FROM tbl_old_orders 
          WHERE ship_shipment_id IS NOT NULL 
          AND ship_shipment_id != '' 
          AND ship_status = 'NEW' 
          AND ship_status != 'Delivered'";
*/

    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $order_id = $row['id'];
            $shipment_id = $row['ship_shipment_id'];
            $shipment_order_id = $row['order_id'];

            // Call the Shiprocket tracking API
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
                    'Authorization: Bearer ' . $shiprocket_token
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            // Parse the API response
            $responseArray = json_decode($response, true);
            if (isset($responseArray['tracking_data']['shipment_track'][0]['current_status'])) {
                $current_status = $responseArray['tracking_data']['shipment_track'][0]['current_status'];

                // Update the database with the current status
                $update_query = "UPDATE tbl_old_orders SET ship_status = '" . mysqli_real_escape_string($con, $current_status) . "' WHERE id = $order_id";
                if (mysqli_query($con, $update_query)) {
                    echo "Order ID $shipment_order_id updated with status: $current_status<br>";
                } else {
                    echo "Failed to update Order ID $shipment_order_id: " . mysqli_error($con) . "<br>";
                }
            } else {
                echo "Failed to fetch status for Shipment ID $shipment_id<br>";
            }
        }

        // When processing is complete, update the status message
        echo "<script>document.getElementById('status-heading').innerText = 'All data fetched successfully!';</script>";
    } else {
        // No shipments found
        echo "<script>document.getElementById('status-heading').innerText = 'No shipments found for tracking.';</script>";
    }
    ?>
</body>
</html>
