<?php
include("../include/config.php");

$id = (int)$_POST['id'];
$q = mysqli_query($con, "
  SELECT o.*, c.channel_name 
  FROM tbl_orders o 
  LEFT JOIN tbl_channel c ON o.channel_id=c.channel_id 
  WHERE o.id=$id
");

if (!$q || mysqli_num_rows($q)==0) {
  echo "<div class='alert alert-danger'>Order not found!</div>";
  exit;
}

$order = mysqli_fetch_assoc($q);
?>

<div class="row">
  <div class="col-md-6">
    <h5>🧾 Basic Details</h5>
    <table class="table table-sm">
      <tr><th>Order ID:</th><td><?php echo htmlspecialchars($order['order_id']); ?></td></tr>
      <tr><th>Customer:</th><td><?php echo htmlspecialchars($order['fname']); ?></td></tr>
      <tr><th>Mobile:</th><td><?php echo htmlspecialchars($order['mobno']); ?></td></tr>
      <tr><th>Amount:</th><td>₹<?php echo number_format($order['total_amount'],2); ?></td></tr>
      <tr><th>Payment:</th><td><?php echo htmlspecialchars($order['payment_status']); ?></td></tr>
      <tr><th>Channel:</th><td><?php echo htmlspecialchars($order['channel_name']); ?></td></tr>
      <tr><th>Created At:</th><td><?php echo $order['created_at']; ?></td></tr>
    </table>
  </div>
  <div class="col-md-6">
    <h5>📍 Shipping Details</h5>
    <table class="table table-sm">
      <tr><th>Street:</th><td><?php echo htmlspecialchars($order['street']); ?></td></tr>
      <tr><th>Landmark:</th><td><?php echo htmlspecialchars($order['landmark']); ?></td></tr>
      <tr><th>City:</th><td><?php echo htmlspecialchars($order['city']); ?></td></tr>
      <tr><th>District:</th><td><?php echo htmlspecialchars($order['district']); ?></td></tr>
      <tr><th>Pincode:</th><td><?php echo htmlspecialchars($order['pincode']); ?></td></tr>
      <tr><th>Taluka:</th><td><?php echo htmlspecialchars($order['taluka']); ?></td></tr>
    </table>
  </div>
</div>

<hr>
<h5>💳 Payment Info</h5>
<table class="table table-sm">
  <tr><th>PG TXN ID</th><td><?php echo htmlspecialchars($order['pg_txnid']); ?></td></tr>
  <tr><th>PG Mode</th><td><?php echo htmlspecialchars($order['pg_mode']); ?></td></tr>
  <tr><th>Bank Ref</th><td><?php echo htmlspecialchars($order['bank_ref_no']); ?></td></tr>
  <tr><th>Bank Name</th><td><?php echo htmlspecialchars($order['pg_bank_name']); ?></td></tr>
</table>

<hr>
<h5>📦 Shipment Info</h5>
<table class="table table-sm">
  <tr><th>AWB Code</th><td><?php echo htmlspecialchars($order['ship_awb_code']); ?></td></tr>
  <tr><th>Courier</th><td><?php echo htmlspecialchars($order['ship_courier_name']); ?></td></tr>
  <tr><th>Status</th><td><?php echo htmlspecialchars($order['ship_status']); ?></td></tr>
</table>
