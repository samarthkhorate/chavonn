<?php
session_start();

// Check if the script is accessed via a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST)) {
    header("Location: index.php"); // Redirect to the homepage or a safe page
    exit();
}
extract($_POST);

require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
$ads_account = 2;
$d1 =  date('Y-m-d');
$d2 = date('H:i:s');
$date_n_time = $d1." - ".$d2;
$d3 = random_int(10,99);
$d4 = random_int(10,99);
$d5 = $d1."".$d3."".$d2."".$d4;

$k1 =  date('Ymd');
$k2 = date('His');
$k3 = random_int(10,99);
$k4 = random_int(10,99);

$page_tracking_id = $k1."".$k3."".$k2."".$k4;

include '../config.php';
$single_book_cost = 0;
if ($bookLanguage == 'marathi') {
    $single_book_cost = 270;
} elseif ($bookLanguage == 'marathi_part2') {
    $single_book_cost = 290;
} else {
    $single_book_cost = 320;
}

$paymentMethod = 'online';
/*
echo "<pre>";
print_r($_POST);
echo "</pre>";*/
$total_of_book = $quantity*$single_book_cost;
if($paymentMethod=="online"){
$value_charges=0;
$defualt_payment_status = "pending";
}else{
    $value_charges=40;
    $defualt_payment_status = "cash on delevery";
}
$sum_of_total = $value_charges+$total_of_book;

date_default_timezone_set("Asia/Kolkata");

// Get the current date and time
$current_date = date("d-m-Y"); // Format: DD-MM-YYYY
$current_time = date("h:i:s A"); // Format: HH:MM:SS pm

/*echo "<br>TOtal of Book : ".$total_of_book;
echo "<br>TOtal of Charges : ".$value_charges;
echo "<br>Complete Total : ".$sum_of_total;

echo "<br>Current Date: $current_date";
echo "<br>Current Time: $current_time";

echo "<br>Payment Status : ".$defualt_payment_status;
echo "<br>Page id ".$page_tracking_id ;*/
if ($paymentMethod == "cod") {
    // Insert directly for COD orders
    $cod_query = "INSERT INTO `tbl_orders` 
        (`id`, `order_id`, `fname`, `mobno`, `street`, `landmark`, `city`, `taluka`, `district`, `pincode`, `qty`, `mode_of_payment`, 
         `value_books`, `value_charges`, `total_amount`, `order_date`, `order_time`, `payment_status`, `razorpay_id`,`language`,`meta`) 
        VALUES 
        (NULL, '$page_tracking_id', '$full_name', '$mobile_number', '$street_name', '$landmark', '$city_name', '$taluka', '$district', 
         '$pincode', '$quantity', '$paymentMethod', '$total_of_book', '$value_charges', '$sum_of_total', '$current_date', 
         '$current_time', '$defualt_payment_status', 'COD','$bookLanguage','$ads_account')";

    if (mysqli_query($con, $cod_query)) {
        header("Location: ../thank_you2.php?page_tracking=$page_tracking_id");
        exit();
    }
} else {
    // Generate Razorpay order
    $api = new Api($api_key, $api_secret);

    try {
        $order = $api->order->create([
            'receipt' => $page_tracking_id,
            'amount' => $sum_of_total * 100, // Amount in paise
            'currency' => 'INR',
            'notes' => [
                '01_name' => $full_name,
                '02_mobile_no' => $mobile_number,
                '03_qty' => $quantity,
                '04_street' => $street_name,
                '05_landmrk' => $landmark,
                '06_city' => $city_name,
                '07_tal' => $taluka,
                '08_dist' => $district,
                '09_pincode' => $pincode,
                '10_o_date' => $current_date,
                '12_o_time' => $current_time,
                '13_book_lang' => $bookLanguage
            ]
        ]);

        $order_id = $order->id;

        // Insert only after Razorpay order creation
        $online_query = "INSERT INTO `tbl_orders` 
            (`id`, `order_id`, `fname`, `mobno`, `street`, `landmark`, `city`, `taluka`, `district`, `pincode`, `qty`, `mode_of_payment`, 
             `value_books`, `value_charges`, `total_amount`, `order_date`, `order_time`, `payment_status`, `razorpay_id`,`language`,`meta`) 
            VALUES 
            (NULL, '$order_id', '$full_name', '$mobile_number', '$street_name', '$landmark', '$city_name', '$taluka', '$district', 
             '$pincode', '$quantity', '$paymentMethod', '$total_of_book', '$value_charges', '$sum_of_total', '$current_date', 
             '$current_time', '$defualt_payment_status', 'Not Generated','$bookLanguage', '$ads_account')";

        if (mysqli_query($con, $online_query)) {
            $callback_url = "https://vaibhavdhus.com/order/thank_you.php?page_tracking=" . $order_id;
        } else {
            die("Database Error: " . mysqli_error($con));
        }
    } catch (Exception $e) {
        die("Razorpay Order Creation Failed: " . $e->getMessage());
    }
}
?>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function startPayment() {
    var options = {
        key: "<?php echo $api_key; ?>",
        amount: "<?php echo $order->amount; ?>",
        currency: "INR",
        name: "The End is The Beginning",
        description: "<?php echo $page_tracking_id; ?>",
        image: "https://vaibhavdhus.com/assets/images/book.png",
        order_id: "<?php echo $order_id; ?>",
        theme: {
            color: "#738276"
        },
        callback_url: "<?php echo $callback_url; ?>"
    };
    var rzp = new Razorpay(options);
    rzp.open();
}
startPayment();
</script>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-PT6J4XZVS0"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-PT6J4XZVS0');
</script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f0f4f8, #d9e4f5);
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .container {
            max-width: 500px;
            width: 100%;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .header {
            margin-bottom: 20px;
        }
        .header img {
            width: 80px;
            margin-bottom: 10px;
            animation: float 2s infinite;
        }
        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        .header h1 {
            font-size: 24px;
            color: #2c3e50;
            margin: 0;
        }
        .header p {
            font-size: 16px;
            color: #6c757d;
        }
        .payment-details {
            margin: 20px 0;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }
        .payment-details p {
            font-size: 16px;
            color: #495057;
            margin: 8px 0;
        }
        .payment-details span {
            font-weight: bold;
            color: #212529;
        }
        .button-container {
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 12px 20px;
            font-size: 16px;
            color: white;
            background: linear-gradient(135deg, #6a9c78, #4a7659);
            border: none;
            border-radius: 30px;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn:hover {
            background: linear-gradient(135deg, #4a7659, #6a9c78);
            transform: scale(1.05);
        }
        .note {
            font-size: 14px;
            color: #6c757d;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://vaibhavdhus.com/assets/images/book.png" alt="Logo">
            <h1>Complete Your Payment</h1>
            <p>Your purchase awaits. <br>Secure your order by completing the payment below.</p>
        </div>

        <div class="payment-details">
            <p>Order ID: <span><?php echo $page_tracking_id; ?></span></p>
            <p>Amount to Pay: <span>₹<?php echo number_format($sum_of_total, 2); ?></span></p>
            <p>Payment Method: <span><?php echo $paymentMethod === 'online' ? 'Online' : 'Cash on Delivery'; ?></span></p>
        </div>

        <div class="button-container">
<!--            <button class="btn" id="start-payment">Pay Now</button>
-->            <button onclick="startPayment()" class="btn" id="retry-payment">Retry Payment</button>
        </div>

        <p class="note">If the payment fails, click "Retry Payment" to try again. Need help? Contact us.</p>
    </div>
</body>
</html>