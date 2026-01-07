<?php
header("Content-Type: application/json");
date_default_timezone_set('Asia/Kolkata'); // Set timezone

// Function to generate HMAC-SHA256 in Base64
function generateHmacSha256($secretKey, $payload) {
    return base64_encode(hash_hmac('sha256', $payload, $secretKey, true));
}

// Validate request parameters
if (!isset($_GET['variantId']) || !isset($_GET['quantity'])) {
    echo json_encode(["error" => "Missing variantId or quantity"], JSON_PRETTY_PRINT);
    exit;
}

// API Credentials
$apiKey = "hMnaAuOnp1QbqaId";  // Replace with your API Key
$secretKey = "3zSj8VJkNJw65mV064bu5DzlzldOkLTO"; // Replace with your API Secret

// Order Data
$variantId = $_GET['variantId']; 
$quantity = intval($_GET['quantity']);
$redirectUrl = "https://vaibhavdhus.com/order/ck_thankyou.php"; 
$timestamp = gmdate("Y-m-d\TH:i:s\Z"); 

// JSON Payload
$payload = json_encode([
    "cart_data" => [
        "items" => [
            [
                "variant_id" => $variantId,
                "quantity" => $quantity
            ]
        ]
    ],
    "redirect_url" => $redirectUrl,
    "timestamp" => $timestamp
], JSON_UNESCAPED_SLASHES);

// Generate HMAC Signature in Base64
$hmacSignature = generateHmacSha256($secretKey, $payload);

// Initialize cURL Request
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://checkout-api.shiprocket.com/api/v1/access-token/checkout",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => [
        "X-Api-Key: $apiKey",
        "X-Api-HMAC-SHA256: $hmacSignature",
        "Content-Type: application/json"
    ]
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$error = curl_error($curl);
curl_close($curl);

// Handle cURL errors
if ($error) {
    echo json_encode(["status_code" => 500, "error" => $error], JSON_PRETTY_PRINT);
    exit;
}

// Return response as JSON
echo json_encode([
    "status_code" => $httpCode,
    "response" => json_decode($response, true)
], JSON_PRETTY_PRINT);
?>
