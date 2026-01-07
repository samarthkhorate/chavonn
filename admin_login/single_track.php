<?php
include '../config.php';
include 'generate_tokan.php';

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/courier/track/shipment/701767941',
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

// Decode JSON response to an associative array
$responseArray = json_decode($response, true);

// Print the array
echo "<pre>";
print_r($responseArray);
echo "</pre>";
?>
