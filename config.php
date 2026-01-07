<?php
// Database configuration
$host = '217.21.84.103';       // Database host
$dbname = 'u139090234_chavonn_db';  // Database name
$username = 'u139090234_chavoon_user';  // Database username
$password = 'Chavoon@#986532';  // Database password
define('DB_HOST', '217.21.84.103');  // Database host
define('DB_USER', 'u139090234_chavoon_user');       // Database username
define('DB_PASS', 'Chavoon@#986532');           // Database password
define('DB_NAME', 'u139090234_chavonn_db');  // Database name
// Establishing the database connection
$con = new mysqli($host, $username, $password, $dbname);

// Checking the connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Uncomment this line for debugging (remove in production)
// echo "Connected successfully";
$api_key = 'rzp_live_S150dRy7Wvo1q9';
$api_secret = '4en7sUTCRfRl5VOAP4St5WTO';


$api_key1 = 'rzp_live_S150dRy7Wvo1q9';
$api_secret1 = '4en7sUTCRfRl5VOAP4St5WTO';
 

$razorpay_config1 = array(
    'api_key1' => 'rzp_live_S150dRy7Wvo1q9',
    'api_secret1' => '4en7sUTCRfRl5VOAP4St5WTO',
);

$razorpay_config = array(
    'api_key' => 'rzp_live_S150dRy7Wvo1q9',
    'api_secret' => '4en7sUTCRfRl5VOAP4St5WTO',
);




// Environment settings
// To switch between environments, simply change the value below to "test" or "prod".
$ENV = "prod";

// Merchant key and salt based on the environment
if ($ENV === "prod") {
    $MERCHANT_KEY = "6EWO575JNK";
    $SALT         = "5MQ7PQPHTE";
    $payment_url = "    https://pay.easebuzz.in/pay/";
} else { // "test" environment
    $MERCHANT_KEY = "SXSP7JME4";
    $SALT         = "2WWWMHZKR";
    $payment_url = "https://testpay.easebuzz.in/pay/";
}
?>
