<?php

// Function to generate HMAC-SHA256 signature
function generateHmacSha256($secretKey, $message) {
    return base64_encode(hash_hmac('sha256', $message, $secretKey, true));
}

// ✅ API Credentials (Replace with actual values)
$apiKey = "hMnaAuOnp1QbqaId";  // API Key
$secretKey = "3zSj8VJkNJw65mV064bu5DzlzldOkLTO"; // Secret Key (Replace with actual secret key)

// ✅ Order Data (Customize as needed)
$variantId = "1244539923890450"; // Product Variant ID
$quantity = 1; // Quantity
$redirectUrl = "https://test-checkout.requestcatcher.com/test?key=val"; // Redirect URL
$timestamp = gmdate("Y-m-d\TH:i:s.v\Z"); // ISO 8601 timestamp

// ✅ JSON Payload (Properly formatted)
$payloadArray = [
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
];

$payload = json_encode($payloadArray, JSON_UNESCAPED_SLASHES);

// ✅ Generate HMAC-SHA256 Signature
$hmacSignature = generateHmacSha256($secretKey, $payload);

// ✅ Initialize cURL
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://fastrr-api-dev.pickrr.com/api/v1/access-token/checkout",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => [
        "X-Api-Key: $apiKey",
        "X-Api-HMAC-SHA256: $hmacSignature",
        "Content-Type: application/json"
    ]
]);

// ✅ Execute cURL Request
$response = curl_exec($curl);

// ✅ Error Handling
if (curl_errno($curl)) {
    echo 'cURL Error: ' . curl_error($curl);
}

// ✅ Close cURL Connection
curl_close($curl);

// ✅ Display API Response
echo $response;
?>
