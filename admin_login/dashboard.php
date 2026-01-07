<?php
include("../include/config.php");
session_start();
if (!isset($_SESSION['admin_username'])) {
    header("Location: index.php");
    exit;
}

// Auto logout after 15 minutes
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 900)) {
    session_unset();
    session_destroy();
    header("Location: index.php?timeout=1");
    exit;
}
$_SESSION['last_activity'] = time();

$username = $_SESSION['admin_username'];

// Log dashboard visit
$ip = $_SERVER['REMOTE_ADDR'];
mysqli_query($con, "INSERT INTO admin_logs (admin_username, ip_address, action) VALUES ('$username', '$ip', 'Dashboard Visit')");
?>
<?php
include("sidebar.php");

// ---------------------
//  BASIC METRICS
// ---------------------
$total_orders = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS c FROM tbl_orders"))['c'] ?? 0;
$total_revenue = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(total_amount) AS s FROM tbl_orders WHERE payment_status='Success'"))['s'] ?? 0;
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));

// Today’s Orders + Revenue
$todays_orders = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS c, SUM(total_amount) AS s FROM tbl_orders WHERE DATE(created_at)='$today' AND payment_status='Success'"));
$todays_order_count = $todays_orders['c'] ?? 0;
$todays_sales = $todays_orders['s'] ?? 0;

// ---------------------
//  CHANNEL-WISE ANALYTICS
// ---------------------
$channels = [];
$q_channels = mysqli_query($con, "SELECT channel_id, channel_name FROM tbl_channel ORDER BY channel_name");
while ($ch = mysqli_fetch_assoc($q_channels)) {
    $cid = $ch['channel_id'];
    $channels[$cid] = [
        'channel_name' => $ch['channel_name'],
        'today_orders' => 0,
        'yesterday_orders' => 0,
        'avg_7days' => 0
    ];
}

// Today
$q1 = mysqli_query($con, "SELECT channel_id, COUNT(*) AS c FROM tbl_orders WHERE DATE(created_at)='$today' GROUP BY channel_id");
while ($r = mysqli_fetch_assoc($q1)) {
    $cid = $r['channel_id'];
    if (isset($channels[$cid])) $channels[$cid]['today_orders'] = (int)$r['c'];
}

// Yesterday
$q2 = mysqli_query($con, "SELECT channel_id, COUNT(*) AS c FROM tbl_orders WHERE DATE(created_at)='$yesterday' GROUP BY channel_id");
while ($r = mysqli_fetch_assoc($q2)) {
    $cid = $r['channel_id'];
    if (isset($channels[$cid])) $channels[$cid]['yesterday_orders'] = (int)$r['c'];
}

// 7-day Average
$q3 = mysqli_query($con, "SELECT channel_id, ROUND(COUNT(*)/7,2) AS avg_orders FROM tbl_orders WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY channel_id");
while ($r = mysqli_fetch_assoc($q3)) {
    $cid = $r['channel_id'];
    if (isset($channels[$cid])) $channels[$cid]['avg_7days'] = (float)$r['avg_orders'];
}

// ---------------------
//  PRODUCT-WISE SALES
// ---------------------
$products_sales = mysqli_query($con, "
    SELECT p.product_name, p.product_sku_code, 
           SUM(o.qty) AS total_qty, 
           SUM(o.total_amount) AS total_sales
    FROM tbl_orders o
    LEFT JOIN tbl_products p ON o.product_sku = p.product_sku_code
    GROUP BY o.product_sku
    ORDER BY total_qty DESC
");

// Top 5 Products for chart
$top_labels = [];
$top_qty = [];
$i = 0;
mysqli_data_seek($products_sales, 0);
while ($row = mysqli_fetch_assoc($products_sales)) {
    if ($i < 5) {
        $top_labels[] = $row['product_name'] ?: 'Unknown';
        $top_qty[] = (int)$row['total_qty'];
        $i++;
    }
}

// ---------------------
//  LAST 7 DAYS TREND
// ---------------------
$chart_q = mysqli_query($con, "
  SELECT DATE(created_at) as d, COUNT(*) as cnt 
  FROM tbl_orders 
  WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
  GROUP BY d ORDER BY d
");
$chart_labels = [];
$chart_data = [];
while ($r = mysqli_fetch_assoc($chart_q)) {
  $chart_labels[] = $r['d'];
  $chart_data[] = (int)$r['cnt'];
}

// ---------------------
//  RECENT ADMIN LOGS
// ---------------------
$recent_logs = mysqli_query($con, "SELECT admin_username, action, timestamp FROM admin_logs ORDER BY timestamp DESC LIMIT 8");
?>

<h1 class="mb-4">📊 Pravara Health Care Dashboard</h1>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
  <div class="col-md-3">
    <div class="card text-white bg-success p-3">
      <h6>Total Orders</h6><h3><?php echo number_format($total_orders); ?></h3>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-white bg-info p-3">
      <h6>Total Revenue</h6><h3>₹<?php echo number_format($total_revenue,2); ?></h3>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-white bg-primary p-3">
      <h6>Today's Orders</h6><h3><?php echo $todays_order_count; ?></h3>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-white bg-warning p-3">
      <h6>Today's Sales</h6><h3>₹<?php echo number_format($todays_sales,2); ?></h3>
    </div>
  </div>
</div>

<!-- Charts Row -->
<div class="row g-4">
  <div class="col-lg-8">
    <div class="card p-3">
      <h5 class="mb-3"><i class="fa fa-line-chart text-success me-2"></i> Orders (Last 7 Days)</h5>
      <canvas id="ordersChart" height="100"></canvas>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card p-3">
      <h5 class="mb-3"><i class="fa fa-star text-success me-2"></i> Top 5 Products</h5>
      <canvas id="topProductsChart" height="100"></canvas>
    </div>
  </div>
</div>

<!-- Channel-wise Orders -->
<div class="card mt-4 p-3">
  <h5><i class="fa fa-network-wired text-success me-2"></i> Channel-wise Orders (Today vs Yesterday vs Avg)</h5>
  <div class="table-responsive mt-3">
    <table class="table table-bordered align-middle table-striped">
      <thead class="table-success">
        <tr>
          <th>Channel Name</th>
          <th>Today's Orders</th>
          <th>Yesterday's Orders</th>
          <th>7-Day Avg/Day</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($channels as $ch){ ?>
          <tr>
            <td><?php echo htmlspecialchars($ch['channel_name']); ?></td>
            <td><?php echo $ch['today_orders']; ?></td>
            <td><?php echo $ch['yesterday_orders']; ?></td>
            <td><?php echo $ch['avg_7days']; ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Product-wise Sales Summary -->
<div class="card mt-4 p-3">
  <h5><i class="fa fa-box text-success me-2"></i> Lifetime Product-wise Sales</h5>
  <div class="table-responsive mt-3">
    <table id="productSales" class="table table-hover table-striped">
      <thead class="table-success">
        <tr><th>Product</th><th>SKU</th><th>Total Qty Sold</th><th>Total Sales (₹)</th></tr>
      </thead>
      <tbody>
        <?php mysqli_data_seek($products_sales,0); while($r=mysqli_fetch_assoc($products_sales)){ ?>
          <tr>
            <td><?php echo htmlspecialchars($r['product_name']); ?></td>
            <td><?php echo htmlspecialchars($r['product_sku_code']); ?></td>
            <td><?php echo (int)$r['total_qty']; ?></td>
            <td><?php echo number_format($r['total_sales'],2); ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Admin Logs -->
<div class="card mt-4 p-3">
  <h5><i class="fa fa-clock text-success me-2"></i> Recent Admin Activity</h5>
  <ul class="list-group list-group-flush">
    <?php while($l=mysqli_fetch_assoc($recent_logs)){ ?>
      <li class="list-group-item"><?php echo htmlspecialchars($l['admin_username'])." — ".$l['action']." <span class='text-muted float-end'>".$l['timestamp']."</span>"; ?></li>
    <?php } ?>
  </ul>
</div>

<footer class="mt-5 text-center">
  Developed by <a href="https://www.neotechking.com" target="_blank">NeotechKing Global Solutions Pvt. Ltd.</a>
</footer>

</div> <!-- main -->
</body>

<script>
$(document).ready(function(){
  $('#productSales').DataTable();

  // Orders chart
  new Chart(document.getElementById("ordersChart"), {
    type: 'line',
    data: {
      labels: <?php echo json_encode($chart_labels); ?>,
      datasets: [{
        label: 'Orders',
        data: <?php echo json_encode($chart_data); ?>,
        fill: true,
        borderColor: '#2e7d32',
        backgroundColor: 'rgba(46,125,50,0.1)',
        tension: 0.4
      }]
    },
    options: { responsive: true }
  });

  // Top products
  new Chart(document.getElementById("topProductsChart"), {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($top_labels); ?>,
      datasets: [{
        label: 'Qty Sold',
        data: <?php echo json_encode($top_qty); ?>,
        backgroundColor: '#43a047'
      }]
    },
    options: { responsive: true, plugins:{legend:{display:false}} }
  });
});
</script>
</html>
