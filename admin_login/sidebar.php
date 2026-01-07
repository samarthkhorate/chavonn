<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['admin_username'])) {
    header("Location: index.php");
    exit;
}
$admin_user = htmlspecialchars($_SESSION['admin_username']);
$_SESSION['last_activity'] = time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pravara Admin Dashboard</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<style>
body {
  background: #f3f6f4;
  font-family: 'Poppins', sans-serif;
  margin: 0;
}
.sidebar {
  width: 250px;
  height: 100vh;
  position: fixed;
  top: 0; left: 0;
  background: linear-gradient(180deg, #2e7d32, #1b5e20);
  color: white;
  display: flex;
  flex-direction: column;
}
.sidebar .logo {
  font-size: 1.4rem;
  font-weight: 700;
  padding: 20px;
  border-bottom: 1px solid rgba(255,255,255,0.2);
  text-align: center;
}
.sidebar a {
  padding: 14px 20px;
  display: block;
  color: white;
  text-decoration: none;
  transition: 0.3s;
}
.sidebar a:hover {
  background: rgba(255,255,255,0.15);
}
.sidebar-footer {
  margin-top: auto;
  padding: 15px;
  font-size: 13px;
  text-align: center;
  background: rgba(255,255,255,0.1);
}
.main {
  margin-left: 260px;
  padding: 25px;
}
.card {
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
footer {
  text-align: center;
  padding: 15px;
  color: #666;
  font-size: 13px;
  margin-top: 30px;
}
</style>
</head>
<body>
<div class="sidebar">
  <div class="logo">Pravara Admin</div>
  <a href="dashboard.php"><i class="fa fa-chart-line me-2"></i> Dashboard</a>
  <a href="add_manual_order.php"><i class="fa fa-box me-2"></i>Orders Add</a>
  <a href="unverified_orders.php"><i class="fa fa-box me-2"></i>Orders Verification</a>
  <a href="orders_search.php"><i class="fa fa-box me-2"></i>Orders View</a>
  <a href="pending_ship_orders.php"><i class="fa fa-box me-2"></i>Shiprocket API</a>
  <a href="admin-call-management.php"><i class="fa fa-phone me-2"></i> Call Management</a>
  <a href="whatsapp_temp_orders.php"><i class="fa fa-phone me-2"></i> Pending WP Orders</a>.
    <a href="whatsapp_dashboard.php"><i class="fa fa-users me-2"></i>Whatsapp Dashboard</a>

<!--   <a href="json_data.php"><i class="fa fa-file-code me-2"></i> JSON Data</a>
  <a href="shiprocket_logs.php"><i class="fa fa-truck me-2"></i> Shiprocket Logs</a>
  <a href="channels.php"><i class="fa fa-users me-2"></i> Channels</a>
  <a href="admin_logs_view.php"><i class="fa fa-history me-2"></i> Admin Logs</a> -->
  <a href="logout.php"><i class="fa fa-sign-out-alt me-2"></i> Logout</a>
  <div class="sidebar-footer">
    Developed by <br>
    <a href="https://www.neotechking.com" target="_blank" style="color:#b2fab4;text-decoration:none;">NeotechKing Global Solutions Pvt. Ltd.</a>
  </div>
</div>
<div class="main">
