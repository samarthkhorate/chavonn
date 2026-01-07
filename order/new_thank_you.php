<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '904324298463906');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=904324298463906&ev=PageView&noscript=1"
/></noscript>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-PT6J4XZVS0"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-PT6J4XZVS0');
</script>
<!-- End Meta Pixel Code -->
<?php 

require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;

include "../config.php";

// Initialize Razorpay API
$api = new Api($api_key, $api_secret);

extract($_GET);

$payment_success = false;



// Fetch order details from the database
$query = "SELECT *
          FROM tbl_orders 
          WHERE order_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("s", $page_tracking);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("No order found with the provided ID.");
}

// Fetch data as an associative array
$order = $result->fetch_assoc();
//echo $order['total_amount'];
//print_r($order);
if($order['mode_of_payment']=='online'){

if(1==1){

$pay_status = $api->order->fetch($page_tracking);
   echo "<pre>";
print_r($pay_status);
echo "</pre>";
$pay_data = $api->order->fetch($page_tracking)->payments();
$pay_array = $pay_data['items'][0];
$razor_pay_id = $pay_array['id'];      // Razorpay payment ID
    $total_amt = $order['total_amount'];
    //echo $total_amt*100;
    $finale = $total_amt*100;
   echo "<pre>";
print_r($pay_array);
echo "</pre>";
$before_capture_status = $pay_array['status'];
if($before_capture_status=='authorized'){
    //echo "payment need to capture";
      $capturewala = $api->payment->fetch($razor_pay_id)->capture(array('amount'=>$finale,'currency' => 'INR'));

    /*echo "capture wala";
    print_r($capturewala);*/
}
  /*$capturewala = $api->payment->fetch($razor_pay_id)->capture(array('amount'=>$finale,'currency' => 'INR'));
    echo "capture wala";
    print_r($capturewala);*/


}





//$bank_tran_id = $pay_array['acquirer_data']['bank_transaction_id'];

// Fetch payment data from Razorpay
/*$paydata = $api->order->fetch($page_tracking)->payments();

$payarray0 = $paydata['items'][0];

echo "<pre>";
print_r($payarray0);
echo "</pre>";
*/
$paystatus_new = $api->order->fetch($page_tracking);

$paydata_new = $api->order->fetch($page_tracking)->payments();
$payarray_new = $paydata_new['items'][0];


$amt_requested = ($paystatus_new['amount'])/100;      // Requested amount
$amt_paid =  ($paystatus_new['amount_paid'])/100;     // Paid amount
$amt_due = ($paystatus_new['amount_due'])/100;        // Amount due (must be 0)
$pyt_status = $paystatus_new['status'];         // Payment status (must be 'paid')


//echo "<br>".$bank_tran_id;
/*echo "<br>".$razor_pay_id;
echo "<br>".$amt_requested;
echo "<br>".$amt_paid;
echo "<br>".$amt_due;
echo "<br>".$pyt_status;
echo "<br>".$order['total_amount'];*/


// Validate page tracking (order ID)
if (!$page_tracking) {
    die("Invalid request. Order ID is missing.");
}



// Validate payment success conditions
if ($order['total_amount'] == $amt_requested && 
    $amt_requested == $amt_paid && 
    $amt_due == 0 && 
    $pyt_status === 'paid') {
    $payment_success = true;

    // Optional: Add an update query here

    $update_query = "UPDATE tbl_orders 
                     SET payment_status = 'paid', razorpay_id = ? 
                     WHERE order_id = ?";
    $update_stmt = $con->prepare($update_query);
$update_stmt->bind_param("ss", $razor_pay_id, $page_tracking);
    $update_stmt->execute();
    $update_stmt->close();
}

$stmt->close();

}else{
    $payment_success=true;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Your Order</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

    <div class="container mt-5">
        <div class="card text-center">
            <div class="card-header <?= $payment_success ? 'bg-success' : 'bg-danger' ?> text-white">
                <h2><?= $payment_success ? 'Payment Successful!' : 'Payment Failed' ?></h2>
            </div>
            <div class="card-body">
                <h4 class="card-title"><?= $payment_success ? 'Thank You for Your Order' : 'Payment Verification Failed' ?></h4>
                <?php if ($payment_success): ?>
                    <p class="card-text">
                        Your order for the book <strong>"The End is the Beginning"</strong> has been successfully placed.
                    </p>
                    <h5>Order Details:</h5>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                            <th>Id</th>
                            <td><?= htmlspecialchars($order['id']) ?></td>
                        </tr>
                            <tr>
                                <th>Order ID</th>
                                <td><?= htmlspecialchars($order['order_id']) ?></td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td><?= htmlspecialchars($order['fname']) ?></td>
                            </tr>
                            <tr>
                                <th>Mobile</th>
                                <td><?= htmlspecialchars($order['mobno']) ?></td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>
                                    <?= htmlspecialchars($order['street']) ?>, <?= htmlspecialchars($order['landmark']) ?>,<br>
                                    <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['taluka']) ?>,<br>
                                    <?= htmlspecialchars($order['district']) ?>, PIN: <?= htmlspecialchars($order['pincode']) ?>
                                </td>
                            </tr>
                                                    <tr>
                            <th>Quantity</th>
                            <td><?= htmlspecialchars($order['qty']) ?></td>
                        </tr>
                        <tr>
                            <th>Payment Mode</th>
                            <td><?= htmlspecialchars($order['mode_of_payment']) ?></td>
                        </tr>
                        <tr>
                            <th>Books Value</th>
                            <td>₹<?= htmlspecialchars($order['value_books']) ?></td>
                        </tr>
                        <tr>
                            <th>COD Charges</th>
                            <td>₹<?= htmlspecialchars($order['value_charges']) ?></td>
                        </tr>
                        <tr>
                                <th>Total Amount</th>
                                <td>₹<?= htmlspecialchars($order['total_amount']) ?></td>
                            </tr>
                        <th>Order Date</th>
                            <td><?= htmlspecialchars($order['order_date']) ?></td>
                        </tr>
                        <tr>
                            <th>Order Time</th>
                            <td><?= htmlspecialchars($order['order_time']) ?></td>
                           <tr>
    <th>Payment Status</th>
    <td>
        <?php
        if (
            $order['total_amount'] == $amt_requested &&
            $amt_requested == $amt_paid &&
            $amt_due == 0 &&
            $order['payment_status'] === 'paid'
        ) {
            echo "PAYMENT_PAID";
        } else {
            echo htmlspecialchars($order['payment_status']);
        }
        ?>
    </td>
</tr>

                            <tr>
                                <th>Razorpay ID</th>
    <td>
        <?php
        if (
            $order['total_amount'] == $amt_requested &&
            $amt_requested == $amt_paid &&
            $amt_due == 0 &&
            $order['payment_status'] === 'paid'
        ) {
            echo $razor_pay_id;
        } else {
            echo htmlspecialchars($order['razorpay_id']);
        }
        ?>
    </td>                            </tr>
                            
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="card-text">
                        Unfortunately, we couldn't verify your payment. Please contact support with your order details.
                    </p>
                <?php endif; ?>
                <a href="index.php" class="btn btn-primary mt-3">Go Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>
<?php
if (!isset($_GET['reloaded'])) {
    // Parse the current query parameters
    $queryParams = $_GET;

    // Add the 'reloaded' parameter
    $queryParams['reloaded'] = 1;

    // Build the updated query string
    $newQueryString = http_build_query($queryParams);

    // Reload the page with the updated query parameters
    header("Location: " . $_SERVER['PHP_SELF'] . "?" . $newQueryString);
    exit;
}

// Your page logic here
?>
