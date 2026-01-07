<?php
include("../config.php");
include("sidebar.php");

ini_set('display_errors', 1);
error_reporting(E_ALL);

// 1️⃣ Fetch pending orders older than 24 hours
$query = "
    SELECT * 
    FROM tbl_orders 
    WHERE payment_status IN ('success', 'paid')
      AND (ship_shipment_id IS NULL OR ship_shipment_id = '')
    ORDER BY id ASC
";


$result = mysqli_query($con, $query);
if (!$result) {
    die("SQL Error: " . mysqli_error($con));
}

$total_pending = mysqli_num_rows($result);
?>

<div class="container-fluid px-4">
  <h2 class="mt-4 mb-3">
    <i class="fa fa-truck text-success me-2"></i> Pending Orders
  </h2>

  <!-- Total Count and Ship Button -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h5>Total Pending Orders: 
        <span class="badge bg-danger"><?php echo $total_pending; ?></span>
      </h5>
    </div>

    <div>
      <a href="https://chavonn.in/cron_send_orders_to_neoship.php" 
         target="_blank" 
         class="btn btn-primary btn-sm">
         <i class="fa fa-paper-plane"></i> Send Orders to Neoship
      </a>
    </div>
  </div>

  <!-- Orders Table -->
  <div class="card shadow-sm p-3 mb-4">
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-success">
          <tr>
            <th>ID</th>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Mobile</th>
            <th>City</th>
            <th>Total Amount</th>
            <th>Created At</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php
        if ($total_pending > 0) {
            while ($r = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                  <td><?php echo htmlspecialchars($r['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars($r['order_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars($r['fname'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars($r['mobno'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars($r['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                  <td>₹<?php echo number_format((float)($r['total_amount'] ?? 0), 2); ?></td>
                  <td><?php echo htmlspecialchars($r['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                  <td>
                    <span class="badge bg-warning text-dark">
                      ⚠️ Not yet sent for Shipping
                    </span>
                  </td>
                  <td>
                    <a href="edit_order.php?id=<?php echo $r['id']; ?>" class="btn btn-sm btn-warning">
                      <i class="fa fa-edit"></i> Edit Order
                    </a>
                  </td>
                </tr>
                <?php
            }
        } else {
            echo "<tr><td colspan='9' class='text-center text-muted'>
                    <i class='fa fa-check-circle text-success'></i> 
                    No pending orders older than 24 hours found.
                  </td></tr>";
        }
        ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

