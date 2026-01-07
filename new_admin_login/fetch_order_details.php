<?php
include("../config.php");

function h($v){ return htmlspecialchars((string)$v ?? '', ENT_QUOTES, 'UTF-8'); }
function moneyINR($v){ return is_numeric($v) ? '₹'.number_format((float)$v, 2) : h($v); }
function yn($v){ 
  $yes = '<span class="badge bg-success">Yes</span>';
  $no  = '<span class="badge bg-secondary">No</span>';
  if ($v === null || $v === '') return $no;
  if (is_numeric($v)) return ((int)$v ? $yes : $no);
  $v = strtolower(trim((string)$v));
  return in_array($v, ['1','true','yes','y']) ? $yes : $no;
}
function dt($v){ return $v ? date('d M Y, h:i A', strtotime($v)) : ''; }
function prettyJSON($v){
  if ($v === null || $v === '') return '';
  $decoded = json_decode($v, true);
  if (json_last_error() === JSON_ERROR_NONE) {
    return '<pre class="mb-0" style="white-space:pre-wrap">'.h(json_encode($decoded, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)).'</pre>';
  }
  return '<pre class="mb-0" style="white-space:pre-wrap">'.h($v).'</pre>';
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

$q = mysqli_query($con, "
  SELECT o.*, c.channel_name 
  FROM tbl_orders o 
  LEFT JOIN tbl_channel c ON o.channel_id = c.channel_id 
  WHERE o.id = {$id}
");

if (!$q || mysqli_num_rows($q) == 0) {
  echo "<div class='alert alert-danger'>Order not found!</div>";
  exit;
}
$order = mysqli_fetch_assoc($q);

// Derived helpers
$amounts = [
  'Item MRP'            => moneyINR($order['item_mrp']),
  'Quantity'            => h($order['qty']),
  'Total Amount'        => moneyINR($order['total_amount']),
  'COD Advance Amount'  => moneyINR($order['cod_advance_amount']),
  'COD Remaining Amount'=> moneyINR($order['cod_remaining_amount']),
];

$basic = [
  'Order ID'         => h($order['order_id']),
  'Order Type'       => h($order['order_type']),
  'Mode of Payment'  => h($order['mode_of_payment']),
  'Payment Status'   => h($order['payment_status']),
  'Order Date'       => h($order['order_date']),
  'Order Time'       => h($order['order_time']),
  'Channel'          => h($order['channel_name']),
  'Affiliate ID'     => h($order['affilate_id']),
  'Shiprocket Account' => h($order['shiprocket_account']),
];

$customer = [
  'Customer Name'   => h($order['fname']),
  'Mobile'          => h($order['mobno']),
  'Street'          => h($order['street']),
  'Landmark'        => h($order['landmark']),
  'City'            => h($order['city']),
  'Taluka'          => h($order['taluka']),
  'District'        => h($order['district']),
  'Pincode'         => h($order['pincode']),
];

$product = [
  'Product SKU'     => h($order['product_sku']),
  'Product Info (PG)' => h($order['pg_productinfo']),
];

$pgCore = [
  'Bank Ref No'         => h($order['bank_ref_no']),
  'PG Txn ID'           => h($order['pg_txnid']),
  'PG Amount'           => moneyINR($order['pg_amount']),
  'PG Status'           => h($order['pg_status']),
  'PG Error Message'    => h($order['pg_error_message']),
  'PG Payment Source'   => h($order['pg_payment_source']),
  'PG Mode'             => h($order['pg_mode']),
  'PG Added On'         => h($order['pg_addedon']),
  'PG EasePay ID'       => h($order['pg_easepayid']),
  'PG Net Amount Debit' => moneyINR($order['pg_net_amount_debit']),
  'PG Cashback %'       => h($order['pg_cash_back_percentage']),
  'PG Deduction %'      => h($order['pg_deduction_percentage']),
];

$pgCard = [
  'PG Card Category'    => h($order['pg_card_category']),
  'PG Unmapped Status'  => h($order['pg_unmappedstatus']),
  'PG Card Num (masked)'=> h($order['pg_cardnum']),
  'PG UPI VPA'          => h($order['pg_upi_va']),
  'PG Card Type'        => h($order['pg_card_type']),
  'PG Bank Code'        => h($order['pg_bankcode']),
  'Name on Card'        => h($order['pg_name_on_card']),
  'Bank Name'           => h($order['pg_bank_name']),
  'Issuing Bank'        => h($order['pg_issuing_bank']),
  'PG Type'             => h($order['pg_pg_type']),
  'Auth Code'           => h($order['pg_auth_code']),
  'Auth Ref Num'        => h($order['pg_auth_ref_num']),
];

$pgMeta = [
  'Email'               => h($order['pg_email']),
  'First Name'          => h($order['pg_firstname']),
  'PG Key'              => h($order['pg_key']),
  'Merchant Logo'       => h($order['pg_merchant_logo']),
  'Success URL'         => h($order['pg_surl']),
  'Failure URL'         => h($order['pg_furl']),
  'Hash'                => h($order['pg_hash']),
];

$pgUdfs = [
  'UDF1' => h($order['pg_udf1']),
  'UDF2' => h($order['pg_udf2']),
  'UDF3' => h($order['pg_udf3']),
  'UDF4' => h($order['pg_udf4']),
  'UDF5' => h($order['pg_udf5']),
  'UDF6' => h($order['pg_udf6']),
  'UDF7' => h($order['pg_udf7']),
  'UDF8' => h($order['pg_udf8']),
  'UDF9' => h($order['pg_udf9']),
  'UDF10'=> h($order['pg_udf10']),
];

$shipment = [
  'Ship Order ID'       => h($order['ship_odr_id']),
  'Channel Order ID'    => h($order['ship_ch_odr_id']),
  'Shipment ID'         => h($order['ship_shipment_id']),
  'AWB Code'            => h($order['ship_awb_code']),
  'Courier Name'        => h($order['ship_courier_name']),
  'Shipment Status'     => h($order['ship_status']),
  'Ship Last Updated'   => h($order['ship_last_updated']),
];

$sys = [
  'Channel ID'          => h($order['channel_id']),
  'Created At'          => h($order['created_at']),
  'Updated At'          => h($order['updated_at']),
  'Is Verified'         => yn($order['is_verified']),
];

$msgs = [
  'Pending Msg'         => nl2br(h($order['pending_msg'])),
  'Dispatch Msg'        => nl2br(h($order['dispatch_msg'])),
  'Out For Delivery Msg'=> nl2br(h($order['out_for_delivery_msg'])),
];

?>
<div class="row">
  <div class="col-md-6">
    <h5 class="mt-2">🧾 Basic Details</h5>
    <table class="table table-sm">
      <?php foreach ($basic as $k=>$v): ?>
        <tr><th style="width:40%"><?php echo h($k); ?>:</th><td><?php echo $v; ?></td></tr>
      <?php endforeach; ?>
    </table>

    <h5 class="mt-3">👤 Customer</h5>
    <table class="table table-sm">
      <?php foreach ($customer as $k=>$v): ?>
        <tr><th style="width:40%"><?php echo h($k); ?>:</th><td><?php echo $v; ?></td></tr>
      <?php endforeach; ?>
    </table>

    <h5 class="mt-3">🛍️ Product</h5>
    <table class="table table-sm">
      <?php foreach ($product as $k=>$v): ?>
        <tr><th style="width:40%"><?php echo h($k); ?>:</th><td><?php echo $v; ?></td></tr>
      <?php endforeach; ?>
    </table>

    <h5 class="mt-3">💰 Amounts</h5>
    <table class="table table-sm">
      <?php foreach ($amounts as $k=>$v): ?>
        <tr><th style="width:40%"><?php echo h($k); ?>:</th><td><?php echo $v; ?></td></tr>
      <?php endforeach; ?>
    </table>

    <h5 class="mt-3">📦 Shipment</h5>
    <table class="table table-sm">
      <?php foreach ($shipment as $k=>$v): ?>
        <tr><th style="width:40%"><?php echo h($k); ?>:</th><td><?php echo $v; ?></td></tr>
      <?php endforeach; ?>
    </table>
  </div>

  <div class="col-md-6">
    <h5 class="mt-2">💳 Payment Gateway (Core)</h5>
    <table class="table table-sm">
      <?php foreach ($pgCore as $k=>$v): ?>
        <tr><th style="width:45%"><?php echo h($k); ?>:</th><td><?php echo $v; ?></td></tr>
      <?php endforeach; ?>
    </table>

    <h5 class="mt-3">💳 Payment Gateway (Card/UPI/Bank)</h5>
    <table class="table table-sm">
      <?php foreach ($pgCard as $k=>$v): ?>
        <tr><th style="width:45%"><?php echo h($k); ?>:</th><td><?php echo $v; ?></td></tr>
      <?php endforeach; ?>
    </table>

    <h5 class="mt-3">🧾 Payment Gateway (Meta)</h5>
    <table class="table table-sm">
      <?php foreach ($pgMeta as $k=>$v): ?>
        <tr><th style="width:45%"><?php echo h($k); ?>:</th><td><?php echo $v; ?></td></tr>
      <?php endforeach; ?>
    </table>

    <h5 class="mt-3">🔧 UDFs</h5>
    <table class="table table-sm">
      <?php foreach ($pgUdfs as $k=>$v): ?>
        <tr><th style="width:45%"><?php echo h($k); ?>:</th><td><?php echo $v; ?></td></tr>
      <?php endforeach; ?>
    </table>

    <h5 class="mt-3">🧩 Meta (JSON)</h5>
    <div class="border rounded p-2" style="background:#fafafa">
      <?php echo prettyJSON($order['meta']); ?>
    </div>

    <h5 class="mt-3">⚙️ System</h5>
    <table class="table table-sm">
      <?php foreach ($sys as $k=>$v): ?>
        <tr><th style="width:45%"><?php echo h($k); ?>:</th><td><?php echo $v; ?></td></tr>
      <?php endforeach; ?>
    </table>

    <h5 class="mt-3">✉️ Status Messages</h5>
    <table class="table table-sm">
      <?php foreach ($msgs as $k=>$v): ?>
        <tr><th style="width:45%"><?php echo h($k); ?>:</th><td><?php echo $v; ?></td></tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
