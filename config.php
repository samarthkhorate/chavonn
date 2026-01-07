<?php
// Database configuration
$host = '217.21.84.103';       // Database host
$dbname = 'u139090234_db1';  // Database name
$username = 'u139090234_usr1';  // Database username
$password = 'Abhi@#9865312';  // Database password
define('DB_HOST', '217.21.84.103');  // Database host
define('DB_USER', 'u139090234_usr1');       // Database username
define('DB_PASS', 'Abhi@#9865312');           // Database password
define('DB_NAME', 'u139090234_db1');  // Database name
// Establishing the database connection
$con = new mysqli($host, $username, $password, $dbname);

// Checking the connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Uncomment this line for debugging (remove in production)
// echo "Connected successfully";
$api_key = 'rzp_live_5G1cQunOS5weFv';
$api_secret = 'HDtu8kq1eE2eHR35ArVw6CCq';


$api_key1 = 'rzp_live_5G1cQunOS5weFv';
$api_secret1 = 'HDtu8kq1eE2eHR35ArVw6CCq';
 

$razorpay_config1 = array(
    'api_key1' => 'rzp_live_5G1cQunOS5weFv',
    'api_secret1' => 'HDtu8kq1eE2eHR35ArVw6CCq',
);

$razorpay_config = array(
    'api_key' => 'rzp_live_5G1cQunOS5weFv',
    'api_secret' => 'HDtu8kq1eE2eHR35ArVw6CCq',
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
