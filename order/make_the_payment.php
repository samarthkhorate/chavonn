<?php 
require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
session_start();

// Check if POST data is available, if not, redirect to index.php
if (!isset($_POST['full_name']) || !isset($_POST['mobile_number']) || !isset($_POST['street_name']) || !isset($_POST['landmark']) || !isset($_POST['city_name']) || !isset($_POST['taluka']) || !isset($_POST['district']) || !isset($_POST['pincode']) || !isset($_POST['quantity']) || !isset($_POST['paymentMethod'])) {
    header("Location: index.php");
    exit();
}

// Get the referral code if present
$referal = isset($_POST['referal']) ? $_POST['referal'] : 'organic';  // Default to 'organic' if no referral is provided

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
$single_book_cost = 270;
extract($_POST);

// Calculate total amounts based on quantity and payment method
$total_of_book = $quantity * $single_book_cost;
if ($paymentMethod == "online") {
    $value_charges = 0;
    $defualt_payment_status = "pending";
} else {
    $value_charges = 40;
    $defualt_payment_status = "cash on delivery";
}
$sum_of_total = $value_charges + $total_of_book;

date_default_timezone_set("Asia/Kolkata");

// Get the current date and time
$current_date = date("d-m-Y"); // Format: DD-MM-YYYY
$current_time = date("h:i:s A"); // Format: HH:MM:SS pm

// Check if payment method is COD or online and insert into the database accordingly
if ($paymentMethod == "cod") {
    // Insert directly for COD orders
    $cod_query = "INSERT INTO `tbl_orders` 
        (`id`, `order_id`, `fname`, `mobno`, `street`, `landmark`, `city`, `taluka`, `district`, `pincode`, `qty`, `mode_of_payment`, 
         `value_books`, `value_charges`, `total_amount`, `order_date`, `order_time`, `payment_status`, `razorpay_id`, `affilate_id`) 
        VALUES 
        (NULL, '$page_tracking_id', '$full_name', '$mobile_number', '$street_name', '$landmark', '$city_name', '$taluka', '$district', 
         '$pincode', '$quantity', '$paymentMethod', '$total_of_book', '$value_charges', '$sum_of_total', '$current_date', 
         '$current_time', '$defualt_payment_status', 'COD', '$referal')";

    if (mysqli_query($con, $cod_query)) {
        header("Location: ../thank_you.php?page_tracking=$page_tracking_id");
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
                '1_name' => $full_name,
                '2_mobile_no' => $mobile_number,
                '3_qty' => $quantity,
                '4_street' => $street_name,
                '5_landmrk' => $landmark,
                '6_city' => $city_name,
                '7_tal' => $taluka,
                '8_dist' => $district,
                '9_pincd' => $pincode,
                '10_o_date' => $current_date,
                '12_o_time' => $current_time
            ]
        ]);

        $order_id = $order->id;

        // Insert into the database after Razorpay order creation
        $online_query = "INSERT INTO `tbl_orders` 
            (`id`, `order_id`, `fname`, `mobno`, `street`, `landmark`, `city`, `taluka`, `district`, `pincode`, `qty`, `mode_of_payment`, 
             `value_books`, `value_charges`, `total_amount`, `order_date`, `order_time`, `payment_status`, `razorpay_id`, `affilate_id`) 
            VALUES 
            (NULL, '$order_id', '$full_name', '$mobile_number', '$street_name', '$landmark', '$city_name', '$taluka', '$district', 
             '$pincode', '$quantity', '$paymentMethod', '$total_of_book', '$value_charges', '$sum_of_total', '$current_date', 
             '$current_time', '$defualt_payment_status', 'Not Generated', '$referal')";

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
