<?php
include("../config.php");
session_start();

/* ============================
   AUTH & SESSION
============================ */
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
mysqli_query(
    $con,
    "INSERT INTO admin_logs (admin_username, ip_address, action)
     VALUES ('$username', '$ip', 'Dashboard Visit')"
);

include("sidebar.php");

/* ============================
   DATES
============================ */
$today     = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));

/* ============================
   BASIC METRICS (PAID ONLY)
============================ */
$total_orders = mysqli_fetch_assoc(
    mysqli_query($con, "SELECT COUNT(*) AS c FROM tbl_orders WHERE payment_status='paid'")
)['c'] ?? 0;

$total_revenue = mysqli_fetch_assoc(
    mysqli_query($con, "SELECT SUM(total_amount) AS s FROM tbl_orders WHERE payment_status='paid'")
)['s'] ?? 0;

$todays_orders = mysqli_fetch_assoc(
    mysqli_query($con, "
        SELECT COUNT(*) AS c, SUM(total_amount) AS s
        FROM tbl_orders
        WHERE DATE(created_at)='$today'
          AND payment_status='paid'
    ")
);

$todays_order_count = $todays_orders['c'] ?? 0;
$todays_sales       = $todays_orders['s'] ?? 0;

/* ============================
   CHANNEL-WISE ANALYTICS
============================ */
$channels = [];
$q_channels = mysqli_query($con, "
    SELECT channel_id, channel_name 
    FROM tbl_channel 
    ORDER BY channel_name
");

while ($ch = mysqli_fetch_assoc($q_channels)) {
    $channels[$ch['channel_id']] = [
        'channel_name'      => $ch['channel_name'],
        'today_orders'      => 0,
        'yesterday_orders'  => 0,
        'avg_7days'         => 0
    ];
}

/* TODAY (PAID ONLY) */
$q1 = mysqli_query($con, "
    SELECT channel_id, COUNT(*) AS c
    FROM tbl_orders
    WHERE DATE(created_at)='$today'
      AND payment_status='paid'
    GROUP BY channel_id
");
while ($r = mysqli_fetch_assoc($q1)) {
    if (isset($channels[$r['channel_id']])) {
        $channels[$r['channel_id']]['today_orders'] = (int)$r['c'];
    }
}

/* YESTERDAY (PAID ONLY) */
$q2 = mysqli_query($con, "
    SELECT channel_id, COUNT(*) AS c
    FROM tbl_orders
    WHERE DATE(created_at)='$yesterday'
      AND payment_status='paid'
    GROUP BY channel_id
");
while ($r = mysqli_fetch_assoc($q2)) {
    if (isset($channels[$r['channel_id']])) {
        $channels[$r['channel_id']]['yesterday_orders'] = (int)$r['c'];
    }
}

/* LAST 7 DAYS AVG (PAID ONLY) */
$q3 = mysqli_query($con, "
    SELECT channel_id, ROUND(COUNT(*)/7,2) AS avg_orders
    FROM tbl_orders
    WHERE payment_status='paid'
      AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY channel_id
");
while ($r = mysqli_fetch_assoc($q3)) {
    if (isset($channels[$r['channel_id']])) {
        $channels[$r['channel_id']]['avg_7days'] = (float)$r['avg_orders'];
    }
}

/* ============================
   PRODUCT-WISE SALES (PAID ONLY)
============================ */
$products_sales = mysqli_query($con, "
    SELECT 
        p.product_name,
        p.product_sku_code,
        SUM(o.qty) AS total_qty,
        SUM(o.total_amount) AS total_sales
    FROM tbl_orders o
    LEFT JOIN tbl_products p
      ON o.product_sku COLLATE utf8mb4_unicode_ci
       = p.product_sku_code COLLATE utf8mb4_unicode_ci
    WHERE o.payment_status='paid'
    GROUP BY o.product_sku
    ORDER BY total_qty DESC
");

/* Top 5 Products */
$top_labels = [];
$top_qty    = [];
$i = 0;
mysqli_data_seek($products_sales, 0);
while ($row = mysqli_fetch_assoc($products_sales)) {
    if ($i < 5) {
        $top_labels[] = $row['product_name'] ?: 'Unknown';
        $top_qty[]    = (int)$row['total_qty'];
        $i++;
    }
}

/* ============================
   LAST 7 DAYS TREND (PAID ONLY)
============================ */
$chart_q = mysqli_query($con, "
    SELECT DATE(created_at) AS d, COUNT(*) AS cnt
    FROM tbl_orders
    WHERE payment_status='paid'
      AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY d
    ORDER BY d
");

$chart_labels = [];
$chart_data   = [];
while ($r = mysqli_fetch_assoc($chart_q)) {
    $chart_labels[] = $r['d'];
    $chart_data[]   = (int)$r['cnt'];
}

/* ============================
   ADMIN LOGS
============================ */
$recent_logs = mysqli_query($con, "
    SELECT admin_username, action, timestamp
    FROM admin_logs
    ORDER BY timestamp DESC
    LIMIT 8
");
?>

<h1 class="mb-4">📊 BePerfect Group Dashboard</h1>

<!-- KPI CARDS -->
<div class="row g-4 mb-4">
  <div class="col-md-3">
    <div class="card text-white bg-success p-3">
      <h6>Total Paid Orders</h6>
      <h3><?= number_format($total_orders); ?></h3>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-white bg-info p-3">
      <h6>Total Revenue</h6>
      <h3>₹<?= number_format($total_revenue,2); ?></h3>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-white bg-primary p-3">
      <h6>Today's Orders</h6>
      <h3><?= $todays_order_count; ?></h3>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-white bg-warning p-3">
      <h6>Today's Sales</h6>
      <h3>₹<?= number_format($todays_sales,2); ?></h3>
    </div>
  </div>
</div>

<!-- CHANNEL TABLE -->
<div class="card mt-4 p-3">
  <h5>Channel-wise Orders (Paid)</h5>
  <div class="table-responsive mt-3">
    <table class="table table-bordered table-striped">
      <thead class="table-success">
        <tr>
          <th>Channel</th>
          <th>Today</th>
          <th>Yesterday</th>
          <th>7-Day Avg</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($channels as $ch) { ?>
        <tr>
          <td><?= htmlspecialchars($ch['channel_name']); ?></td>
          <td><?= $ch['today_orders']; ?></td>
          <td><?= $ch['yesterday_orders']; ?></td>
          <td><?= $ch['avg_7days']; ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>

<!-- PRODUCT SALES -->
<div class="card mt-4 p-3">
  <h5>Product-wise Sales (Paid)</h5>
  <div class="table-responsive">
    <table id="productSales" class="table table-striped">
      <thead class="table-success">
        <tr>
          <th>Product</th>
          <th>SKU</th>
          <th>Qty Sold</th>
          <th>Total Sales ₹</th>
        </tr>
      </thead>
      <tbody>
        <?php mysqli_data_seek($products_sales,0);
        while ($r = mysqli_fetch_assoc($products_sales)) { ?>
        <tr>
          <td><?= htmlspecialchars($r['product_name']); ?></td>
          <td><?= htmlspecialchars($r['product_sku_code']); ?></td>
          <td><?= (int)$r['total_qty']; ?></td>
          <td><?= number_format($r['total_sales'],2); ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ADMIN LOGS -->
<div class="card mt-4 p-3">
  <h5>Recent Admin Activity</h5>
  <ul class="list-group list-group-flush">
    <?php while ($l = mysqli_fetch_assoc($recent_logs)) { ?>
      <li class="list-group-item">
        <?= htmlspecialchars($l['admin_username']); ?> —
        <?= $l['action']; ?>
        <span class="float-end text-muted"><?= $l['timestamp']; ?></span>
      </li>
    <?php } ?>
  </ul>
</div>

<footer class="mt-5 text-center">
  Developed by <a href="https://www.neotechking.com" target="_blank">NeotechKing Global Solutions Pvt. Ltd.</a>
</footer>

<script>
$(document).ready(function(){
  $('#productSales').DataTable();

  new Chart(document.getElementById("ordersChart"), {
    type: 'line',
    data: {
      labels: <?= json_encode($chart_labels); ?>,
      datasets: [{
        label: 'Paid Orders',
        data: <?= json_encode($chart_data); ?>,
        borderColor: '#2e7d32',
        backgroundColor: 'rgba(46,125,50,0.1)',
        fill: true
      }]
    }
  });

  new Chart(document.getElementById("topProductsChart"), {
    type: 'bar',
    data: {
      labels: <?= json_encode($top_labels); ?>,
      datasets: [{
        data: <?= json_encode($top_qty); ?>,
        backgroundColor: '#43a047'
      }]
    },
    options:{plugins:{legend:{display:false}}}
  });
});
</script>
