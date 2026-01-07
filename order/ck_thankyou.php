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
include '../config.php'; // Database connection

// Get order_id from URL
$order_id = isset($_GET['oid']) ? $_GET['oid'] : '';

if (!$order_id) {
    echo "<h3>Invalid Order ID!</h3>";
    exit;
}

// Fetch order details from tbl_ck_orders
$query = "SELECT * FROM `tbl_ck_orders` WHERE `order_id` = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "s", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $order = mysqli_fetch_assoc($result);
} else {
    echo "<h3>Order not found!</h3>";
    exit;
}

// Convert YYYY-MM-DD to DD-MM-YYYY format
$formatted_edd = date("d-m-Y", strtotime($order['edd']));

// Updated Marathi message
$marathi_message = "
आयुष् बदलुन टाकणरे पुस्तक ऑ्डर केल्याद्दल आभार, अेक्षा आहे क हे पुस्तक तुम्ही संपूरण वाचाल आणि त्याची अंबलजावणी आपल्ा आयुष्यात राल.

आपण ऑर्डर केलेले हे पुस्तक आपल्या पत्त्याव " . htmlspecialchars($formatted_edd) . " पर्यंत िळुन जाईल.

ीर धरा, खचून जाऊ नका, शेव हीच सुरुवा आहे लक्षात ेवा आणि नवी सुरुवात कर ♥️";
?>

<!DOCTYPE html>
<html lang="mr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ऑ्डर धन्वाद</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file -->
    <style>
        body {
            font-family: 'Noto Sans Devanagari', 'Mangal', 'Arial', sans-serif;
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 30px;
            max-width: 600px;
            text-align: center;
            border-radius: 15px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.8s ease-in-out;
        }
        h2 {
            color: #d63384;
            font-weight: bold;
            margin-bottom: 15px;
        }
        p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 15px;
            color: #555;
        }
        .order-details {
            margin-top: 20px;
            border-top: 2px solid #d63384;
            padding-top: 20px;
            text-align: left;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            font-size: 16px;
        }
        th {
            background: #d63384;
            color: white;
            text-align: left;
        }
        .contact-link {
            text-decoration: none;
            background: #d63384;
            color: white;
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 16px;
            display: inline-block;
            margin-top: 20px;
            transition: 0.3s ease;
        }
        .contact-link:hover {
            background: #a02468;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>धन्यवद, <?php echo htmlspecialchars($order['shipping_first_name']); ?>!</h2>
        
        <p><?php echo nl2br($marathi_message); ?></p>

        <div class="order-details">
            <h3>🛒 Your Order Details</h3>
<table>
    <tr><th>Order ID</th><td><?php echo htmlspecialchars($order['order_id']); ?></td></tr>
    <tr><th>Payment Type</th><td><?php echo htmlspecialchars($order['payment_type']); ?></td></tr>
    <tr><th>Payment Status</th><td><?php echo htmlspecialchars($order['payment_status']); ?></td></tr>
    <tr><th>Total Amount</th><td>₹<?php echo htmlspecialchars($order['total_amount_payable']); ?></td></tr>
    <tr><th>Delivery Address</th><td>
        <?php echo htmlspecialchars($order['shipping_address_line1']) . ", " . 
                   htmlspecialchars($order['shipping_city']) . ", " . 
                   htmlspecialchars($order['shipping_state']) . " - " . 
                   htmlspecialchars($order['shipping_pincode']); ?>
    </td></tr>
    <tr><th>Phone</th><td><?php echo htmlspecialchars($order['shipping_phone']); ?></td></tr>
    <tr><th>Estimated Delivery Date</th><td><?php echo htmlspecialchars($formatted_edd); ?></td></tr>
    <tr><th>PG Transaction ID</th><td><?php echo htmlspecialchars($order['pg_transaction_id']); ?></td></tr>
</table>

        </div>
    </div>
</body>
</html>
