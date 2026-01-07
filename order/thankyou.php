<?php
// thankyou.php — DB-only (no Easebuzz calls)

date_default_timezone_set('Asia/Kolkata');
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

// DB only
require_once __DIR__ . '/../config.php';

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$order_id = $_GET['order_id'] ?? '';
$error    = '';
$order    = [];

if ($order_id !== '') {
    $sql = "SELECT * FROM tbl_orders WHERE order_id = '".$con->real_escape_string($order_id)."' LIMIT 1";
    $res = $con->query($sql);
    if ($res && $res->num_rows) {
        $order = $res->fetch_assoc();
    } else {
        $error = "Order not found with ID: " . h($order_id);
    }
} else {
    $error = "No order ID provided in the URL.";
}

$payment_status = strtolower($order['payment_status'] ?? 'pending');
switch ($payment_status) {
    case 'success':
        $status_icon  = 'fa-check-circle';
        $status_title = 'Order Confirmed Successfully!';
        $status_class = 'success';
        break;
    case 'pending':
    case 'userpending':
    case '':
        $status_icon  = 'fa-clock';
        $status_title = 'Payment Pending';
        $status_class = 'pending';
        break;
    case 'failed':
    case 'dropped':
    case 'cancelled':
        $status_icon  = 'fa-exclamation-circle';
        $status_title = 'Payment ' . ucfirst($payment_status);
        $status_class = 'failed';
        break;
    default:
        $status_icon  = 'fa-info-circle';
        $status_title = 'Payment Status: ' . ucfirst($payment_status);
        $status_class = 'other';
        break;
}

$total_amount_disp = number_format((float)($order['total_amount'] ?? 0), 2);
$pg_amount_disp    = number_format((float)($order['pg_amount'] ?? 0), 2);

$is_cod            = (strtolower($order['order_type'] ?? '') === 'cod');
$cod_adv           = (float)($order['cod_advance_amount'] ?? 0);
$cod_rem           = (float)($order['cod_remaining_amount'] ?? 0);
$cod_adv_disp      = number_format($cod_adv, 2);
$cod_rem_disp      = number_format($cod_rem, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Confirmation - Be Perfect Group</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:linear-gradient(135deg,#f8f9fa 0%,#e9ecef 100%);color:#333;line-height:1.6;padding:15px;min-height:100vh}
.container{max-width:900px;margin:20px auto;background:#fff;border-radius:16px;box-shadow:0 12px 36px rgba(0,0,0,.08);overflow:hidden}
.header{padding:40px 20px;text-align:center;position:relative;overflow:hidden;color:#fff}
.header.success{background:linear-gradient(135deg,#4CAF50 0%,#2E7D32 100%)}
.header.pending{background:linear-gradient(135deg,#FF9800 0%,#EF6C00 100%)}
.header.failed{background:linear-gradient(135deg,#F44336 0%,#C62828 100%)}
.header.other{background:linear-gradient(135deg,#9E9E9E 0%,#616161 100%)}
.header::after{content:'';position:absolute;inset:0;background:url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100" opacity="0.05"><defs><pattern id="g" width="100" height="100" patternUnits="userSpaceOnUse"><circle fill="white" cx="25" cy="25" r="2"/><circle fill="white" cx="75" cy="75" r="2"/><circle fill="white" cx="75" cy="25" r="1"/><circle fill="white" cx="25" cy="75" r="1"/></pattern></defs><rect width="100" height="100" fill="url(%23g)"/></svg>')}
.header-content{position:relative;z-index:2}
.header i{font-size:70px;margin-bottom:20px;display:block;filter:drop-shadow(0 4px 8px rgba(0,0,0,.2))}
.header h1{font-size:32px;margin-bottom:12px;font-weight:600;letter-spacing:.5px}
.header p{font-size:18px;opacity:.9;max-width:600px;margin:0 auto}
.content{padding:30px}
.section{margin-bottom:30px;padding:25px;background:#f9fafb;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.03);border:1px solid #eaeaea}
.section h2{color:#2c3e50;margin-bottom:20px;display:flex;align-items:center;font-size:20px;font-weight:600;padding-bottom:12px;border-bottom:1px solid #eaeaea}
.section h2 i{margin-right:12px;color:#4CAF50;font-size:22px}
.info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px}
.info-item{margin-bottom:15px}
.info-item strong{display:block;color:#546e7a;font-size:14px;margin-bottom:6px;font-weight:500;letter-spacing:.3px}
.info-item span{font-size:16px;color:#263238;font-weight:500}
.status-badge{display:inline-block;padding:8px 16px;border-radius:50px;font-size:14px;font-weight:600;letter-spacing:.3px}
.status-success{background:#e8f5e9;color:#2E7D32}
.status-pending{background:#fff3e0;color:#EF6C00}
.status-failed{background:#ffebee;color:#C62828}
.status-other{background:#f5f5f5;color:#616161}
.shipping-timeline{display:flex;justify-content:space-between;margin:30px 0;position:relative}
.shipping-timeline::before{content:'';position:absolute;top:20px;left:0;right:0;height:3px;background:#e0e0e0;z-index:1}
.timeline-step{text-align:center;position:relative;z-index:2;flex:1}
.timeline-icon{width:40px;height:40px;background:#fff;color:#9e9e9e;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:18px;border:2px solid #e0e0e0;box-shadow:0 4px 8px rgba(0,0,0,.05)}
.timeline-step.active .timeline-icon{background:#4CAF50;color:#fff;border-color:#4CAF50}
.timeline-text{font-size:13px;color:#9e9e9e;font-weight:500}
.timeline-step.active .timeline-text{color:#4CAF50;font-weight:600}
.btn{padding:14px 28px;border-radius:10px;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;transition:all .3s ease;box-shadow:0 4px 8px rgba(0,0,0,.1);border:none;cursor:pointer;font-size:16px}
.btn i{margin-right:10px}
.btn-primary{background:#4CAF50;color:#fff}
.btn-primary:hover{background:#45a049;transform:translateY(-2px);box-shadow:0 6px 12px rgba(0,0,0,.15)}
.error-container{text-align:center;padding:50px 20px}
.error-icon{font-size:70px;color:#f44336;margin-bottom:25px}
.support{text-align:center;margin-top:40px;padding:25px;background:linear-gradient(135deg,#f1f8e9 0%,#e8f5e9 100%);border-radius:12px;border-left:4px solid #4CAF50}
.support h3{margin-bottom:15px;color:#2E7D32;font-weight:600}
.support p{margin-bottom:10px;color:#424242}
.status-message{text-align:center;padding:20px;margin:20px 0;border-radius:12px;font-size:17px;font-weight:500}
.status-message.success{background:#e8f5e9;color:#2E7D32;border-left:4px solid #4CAF50}
.status-message.pending{background:#fff3e0;color:#EF6C00;border-left:4px solid #FF9800}
.status-message.failed{background:#ffebee;color:#C62828;border-left:4px solid #F44336}
.status-message.other{background:#f5f5f5;color:#616161;border-left:4px solid #9E9E9E}
footer{margin-top:40px;padding:20px;text-align:center;background:#f8f9fa;border-top:1px solid #eaeaea;font-size:14px;color:#546e7a}
footer a{color:#4CAF50;text-decoration:none;font-weight:500}
footer a:hover{text-decoration:underline}
@media (max-width:768px){
  .header{padding:30px 15px}
  .header h1{font-size:26px}
  .content{padding:20px}
  .section{padding:20px}
  .shipping-timeline{flex-direction:column;align-items:flex-start;gap:25px}
  .shipping-timeline::before{display:none}
  .timeline-step{display:flex;align-items:center;gap:15px;text-align:left;width:100%}
  .timeline-icon{margin:0;flex-shrink:0}
}
</style>

<?php if ($payment_status === 'success'): ?>
<!-- Safe FB/GA events based on DB values only -->
<script>
window.fbq = window.fbq || function(){};
window.gtag = window.gtag || function(){};
fbq('track','Purchase',{
  value: <?php echo $total_amount_disp; ?>,
  currency:'INR',
  content_ids:['<?php echo h($order['product_sku']); ?>'],
  content_type:'product',
  contents:[{id:'<?php echo h($order['product_sku']); ?>',quantity:<?php echo (int)$order['qty']; ?>}],
  order_id:'<?php echo h($order['order_id']); ?>'
});
gtag('event','purchase',{
  transaction_id:'<?php echo h($order['order_id']); ?>',
  value: <?php echo $total_amount_disp; ?>,
  currency:'INR',
  items:[{
    item_id:'<?php echo h($order['product_sku']); ?>',
    item_name:'<?php echo h($order['product_sku']); ?>',
    quantity:<?php echo (int)$order['qty']; ?>,
    price: <?php
      $q = max(1,(int)$order['qty']);
      echo number_format(((float)$order['total_amount'])/$q, 2, '.', '');
    ?>
  }]
});
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=24082302058111904&ev=Purchase&noscript=1"/></noscript>
<?php endif; ?>
</head>
<body>
<div class="container">
<?php if ($error): ?>
  <div class="error-container">
    <div class="error-icon"><i class="fas fa-exclamation-circle"></i></div>
    <h2>Order Not Found</h2>
    <p><?php echo $error; ?></p>
    <div style="margin-top:25px;">
      <a href="/" class="btn btn-primary"><i class="fas fa-home"></i> Return to Home</a>
    </div>
  </div>
<?php else: ?>
  <div class="header <?php echo $status_class; ?>">
    <div class="header-content">
      <i class="fas <?php echo $status_icon; ?>"></i>
      <h1><?php echo h($status_title); ?></h1>
      <p>Thank you for shopping with Be Perfect Group</p>
    </div>
  </div>

  <div class="content">
    <!-- Status Message -->
    <?php if ($payment_status === 'success'): ?>
      <div class="status-message success"><i class="fas fa-check-circle"></i> Your payment was successful and your order is confirmed.</div>
    <?php elseif ($payment_status === 'pending' || $payment_status === 'userpending' || $payment_status === ''): ?>
      <div class="status-message pending"><i class="fas fa-clock"></i> Your payment is still processing. This may take a few moments.</div>
    <?php elseif (in_array($payment_status, ['failed','dropped','cancelled'], true)): ?>
      <div class="status-message failed"><i class="fas fa-exclamation-circle"></i> Your payment was not successful. Please try again or contact support.</div>
    <?php else: ?>
      <div class="status-message other"><i class="fas fa-info-circle"></i> Your payment status: <?php echo ucfirst(h($payment_status)); ?></div>
    <?php endif; ?>

    <!-- Order Summary -->
    <div class="section">
      <h2><i class="fas fa-receipt"></i> Order Summary</h2>
      <div class="info-grid">
        <div class="info-item"><strong>Order ID</strong><span><?php echo h($order['order_id']); ?></span></div>
        <div class="info-item"><strong>Order Date</strong><span><?php echo $order['order_date'] ? date('F j, Y', strtotime($order['order_date'])) : date('F j, Y'); ?></span></div>
        <div class="info-item"><strong>Customer Name</strong><span><?php echo h($order['fname']); ?></span></div>
        <div class="info-item"><strong>Mobile Number</strong><span><?php echo h($order['mobno']); ?></span></div>
        <div class="info-item"><strong>Product</strong><span><?php echo h($order['product_sku']); ?></span></div>
        <div class="info-item"><strong>Quantity</strong><span><?php echo (int)$order['qty']; ?> units</span></div>
        <div class="info-item">
          <strong>Total Amount<?php echo $is_cod ? ' (Advance + Remaining)' : ''; ?></strong>
          <span>
            ₹<?php echo $total_amount_disp; ?>
            <?php if ($is_cod): ?>
              <small style="display:block;color:#607d8b;margin-top:4px;">₹<?php echo $cod_adv_disp; ?> + ₹<?php echo $cod_rem_disp; ?></small>
            <?php endif; ?>
          </span>
        </div>
        <div class="info-item">
          <strong>Payment Status</strong>
          <span class="status-badge
            <?php
              echo $payment_status==='success' ? 'status-success' :
                   (in_array($payment_status,['pending','userpending',''],true) ? 'status-pending' :
                   (in_array($payment_status,['failed','dropped','cancelled'],true) ? 'status-failed' : 'status-other'));
            ?>">
            <?php echo ucfirst(h($payment_status ?: 'pending')); ?>
          </span>
        </div>
      </div>

      <?php if ($is_cod): ?>
        <div class="info-grid" style="margin-top:10px">
          <div class="info-item">
            <strong>Amount Paid Now (Advance)</strong>
            <span>₹<?php echo $pg_amount_disp > 0 ? $pg_amount_disp : $cod_adv_disp; ?></span>
          </div>
          <div class="info-item">
            <strong>Amount Payable on Delivery</strong>
            <span>₹<?php echo $cod_rem_disp; ?></span>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <?php if ($payment_status === 'success'): ?>
    <!-- Shipping (visible once paid) -->
    <div class="section">
      <h2><i class="fas fa-truck"></i> Shipping Information</h2>
      <p>Your order will be processed and shipped within the next 24 hours. You will receive your order within 5–6 working days.</p>
      <div class="shipping-timeline">
        <div class="timeline-step active"><div class="timeline-icon"><i class="fas fa-check"></i></div><div class="timeline-text">Order Confirmed</div></div>
        <div class="timeline-step"><div class="timeline-icon"><i class="fas fa-box"></i></div><div class="timeline-text">Processing</div></div>
        <div class="timeline-step"><div class="timeline-icon"><i class="fas fa-shipping-fast"></i></div><div class="timeline-text">Shipped</div></div>
        <div class="timeline-step"><div class="timeline-icon"><i class="fas fa-home"></i></div><div class="timeline-text">Delivered</div></div>
      </div>
      <div class="info-grid">
        <div class="info-item">
          <strong>Shipping Address</strong>
          <span>
            <?php
              $parts = array_filter([
                $order['street'] ?? '',
                $order['landmark'] ?? '',
                ($order['city'] ?? '').($order['taluka'] ? ', '.$order['taluka'] : ''),
                ($order['district'] ?? '').' - '.($order['pincode'] ?? '')
              ], fn($x)=>trim($x)!=='');
              echo h(implode(', ', $parts));
            ?>
          </span>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Payment Info -->
    <div class="section">
      <h2><i class="fas fa-credit-card"></i> Payment Information</h2>
      <div class="info-grid">
        <div class="info-item"><strong>Payment Method</strong><span><?php echo h($order['pg_mode'] ?: 'UPI'); ?></span></div>
        <div class="info-item"><strong>Transaction ID</strong><span><?php echo h($order['bank_ref_no'] ?: 'Processing...'); ?></span></div>
        <div class="info-item"><strong>Amount Paid</strong>
          <span>
            ₹<?php
              if ($is_cod) {
                  echo $pg_amount_disp > 0 ? $pg_amount_disp : $cod_adv_disp;
              } else {
                  echo $pg_amount_disp > 0 ? $pg_amount_disp : $total_amount_disp;
              }
            ?>
          </span>
        </div>
      </div>
    </div>

    <!-- Support -->
    <div class="support">
      <h3><i class="fas fa-headset"></i> Need Help?</h3>
      <p>If you have any questions about your order, please contact our customer support team.</p>
      <p>Phone: +91 9588620512</p>
    </div>

    <!-- Footer -->
    <footer>
      <p>© <?php echo date('Y'); ?> <a href="https://beperfectgroup.in/" target="_blank" rel="noopener">BePerfect Group</a>. All Rights Reserved.</p>
      <p>Developed by <a href="https://neotechking.com" target="_blank" rel="noopener">Neotechking Global Solutions Private Limited</a></p>
    </footer>
  </div>
<?php endif; ?>
</div>
<?php
if (isset($con) && $con instanceof mysqli) { $con->close(); }
?>
</body>
</html>
