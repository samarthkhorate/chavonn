<?php
include("../config.php");

$where = [];
if (!empty($_POST['order_id'])) $where[] = "o.order_id LIKE '%".mysqli_real_escape_string($con,$_POST['order_id'])."%'";
if (!empty($_POST['mobno'])) $where[] = "o.mobno LIKE '%".mysqli_real_escape_string($con,$_POST['mobno'])."%'";
if (!empty($_POST['fname'])) $where[] = "o.fname LIKE '%".mysqli_real_escape_string($con,$_POST['fname'])."%'";
if (!empty($_POST['payment_status'])) $where[] = "o.payment_status='".mysqli_real_escape_string($con,$_POST['payment_status'])."'";

$filter = count($where) ? "WHERE ".implode(" AND ", $where) : "";
$q = "
  SELECT o.*, c.channel_name 
  FROM tbl_orders o 
  LEFT JOIN tbl_channel c ON o.channel_id=c.channel_id 
  $filter
  ORDER BY o.id DESC
  LIMIT 500
";
$res = mysqli_query($con, $q);

if (!$res || mysqli_num_rows($res) == 0){
  echo "<div class='p-3 text-center text-muted'>No matching records found 🔍</div>";
  exit;
}

echo '<table class="table table-bordered table-striped table-hover align-middle" id="ordersResult">';
echo '<thead class="table-success"><tr>
<th>ID</th><th>Order ID</th><th>Customer</th><th>Mobile</th>
<th>Amount</th><th>Payment</th><th>Channel</th><th>Created</th><th>Action</th>
</tr></thead><tbody>';
while($r = mysqli_fetch_assoc($res)){
  echo '<tr>
  <td>'.$r['id'].'</td>
  <td>'.htmlspecialchars($r['order_id']).'</td>
  <td>'.htmlspecialchars($r['fname']).'</td>
  <td>'.htmlspecialchars($r['mobno']).'</td>
  <td>₹'.number_format($r['total_amount'],2).'</td>
  <td>'.$r['payment_status'].'</td>
  <td>'.($r['channel_name'] ?? '-').'</td>
  <td>'.$r['created_at'].'</td>
  <td><button class="btn btn-sm btn-primary" onclick="viewOrderDetails('.$r['id'].')"><i class="fa fa-eye"></i> View</button></td>
  </tr>';
}
echo '</tbody></table>';
?>

<script>
$(document).ready(function(){
  $('#ordersResult').DataTable({pageLength:25, order:[[0,'desc']]});
});
</script>
