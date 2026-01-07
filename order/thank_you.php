<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
date_default_timezone_set('Asia/Kolkata');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/razorpay-php/Razorpay.php';

use Razorpay\Api\Api;

/* ===========================
   VALIDATE INPUT
=========================== */
$order_id = $_GET['order_id'] ?? '';
if ($order_id === '') die("Invalid Order ID");

/* ===========================
   FETCH ORDER
=========================== */
$q = mysqli_query($con, "SELECT * FROM tbl_orders WHERE order_id='$order_id' LIMIT 1");
if (!$q || mysqli_num_rows($q) === 0) die("Order not found");
$order = mysqli_fetch_assoc($q);

/* ===========================
   VERIFY RAZORPAY PAYMENT
=========================== */
$api = new Api($api_key, $api_secret);
$paid = false;
$payment_id = '';

try {
    if ($order['pg_txnid']) {
        $rpOrder = $api->order->fetch($order['pg_txnid']);
        $payments = $rpOrder->payments();

        foreach ($payments['items'] ?? [] as $p) {
            if ($p['status'] === 'captured') {
                $paid = true;
                $payment_id = $p['id'];

                mysqli_query($con, "
                    UPDATE tbl_orders SET
                        payment_status='paid',
                        pg_status='captured',
                        bank_ref_no='$payment_id',
                        pg_amount='".($p['amount']/100)."',
                        pg_addedon=NOW()
                    WHERE order_id='$order_id'
                ");
                break;
            }
        }
    }
} catch (Exception $e) {}

/* Reload updated order */
$q = mysqli_query($con, "SELECT * FROM tbl_orders WHERE order_id='$order_id'");
$order = mysqli_fetch_assoc($q);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Order Status</title>

<!-- Meta Pixel Base -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init','718628090837177');
fbq('track','PageView');
</script>

<style>
:root{
    --green:#28a745;
    --red:#dc3545;
    --dark:#2c3e50;
}
body{
    margin:0;
    background:#f1f3f6;
    font-family:Poppins,Arial,sans-serif;
}
.container{
    max-width:480px;
    margin:auto;
    padding:20px;
}
.card{
    background:#fff;
    border-radius:14px;
    padding:22px;
    box-shadow:0 10px 30px rgba(0,0,0,.12);
}
.header{
    text-align:center;
}
.header .icon{
    font-size:46px;
    margin-bottom:10px;
}
.success{color:var(--green)}
.fail{color:var(--red)}
.section{
    margin-top:18px;
    border-top:1px dashed #ddd;
    padding-top:15px;
}
.row{
    display:flex;
    justify-content:space-between;
    font-size:14px;
    margin:8px 0;
}
.row span:first-child{color:#666}
.btn{
    width:100%;
    margin-top:20px;
    padding:14px;
    border:none;
    border-radius:30px;
    font-size:16px;
    cursor:pointer;
    background:linear-gradient(135deg,#6a9c78,#4a7659);
    color:#fff;
}
.footer-note{
    margin-top:15px;
    text-align:center;
    font-size:13px;
    color:#777;
}
</style>
</head>

<body>

<div class="container">
<div class="card">

<div class="header">
<?php if ($paid): ?>
    <div class="icon success">✅</div>
    <h2 class="success">Payment Successful</h2>
    <p>Your order has been placed successfully.</p>

    <!-- Meta Pixel Purchase -->
    <script>
    fbq('track','Purchase',{
        value: <?= json_encode((float)$order['pg_amount']) ?>,
        currency:'INR'
    });
    </script>

<?php else: ?>
    <div class="icon fail">❌</div>
    <h2 class="fail">Payment Pending</h2>
    <p>If amount was deducted, it will auto-refund.</p>
<?php endif; ?>
</div>

<div class="section">
<div class="row"><span>Order ID</span><span><?= htmlspecialchars($order['order_id']) ?></span></div>
<div class="row"><span>Status</span><span><?= htmlspecialchars($order['payment_status']) ?></span></div>
<div class="row"><span>Amount Paid</span><span>₹<?= number_format((float)$order['pg_amount'],2) ?></span></div>
<div class="row"><span>Total Amount</span><span>₹<?= number_format((float)$order['total_amount'],2) ?></span></div>
</div>

<div class="section">
<div class="row"><span>Name</span><span><?= htmlspecialchars($order['fname']) ?></span></div>
<div class="row"><span>Mobile</span><span><?= htmlspecialchars($order['mobno']) ?></span></div>
</div>

<div class="section">
<p style="font-size:14px;color:#555;">
<?= htmlspecialchars($order['street']) ?>,<br>
<?= htmlspecialchars($order['landmark']) ?>,<br>
<?= htmlspecialchars($order['city']) ?> – <?= htmlspecialchars($order['pincode']) ?>
</p>
</div>

<button class="btn" onclick="location.href='../index.php'">Go to Home</button>

<div class="footer-note">
Need help? Contact our support team.
</div>

</div>
</div>

</body>
</html>
