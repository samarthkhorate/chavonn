<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://apiv2.shiprocket.in/v1/external/auth/login',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => '{
    "email": "help@beperfectgroup.in",
    "password": "Beperfect@#986532"
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);

// Decode the JSON response
$response_data = json_decode($response, true);

// Check if the token exists in the response and assign it to the variable
if (isset($response_data['token'])) {
    $shiprocket_token = $response_data['token'];
   // echo "Token: " . $shiprocket_token; // Optional: Print the token
} else {
    echo "Token not found in response.";
}

?>
