<?php
include("../include/config.php");
include("sidebar.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch all records from tbl_temp_orders
$query = "SELECT * FROM tbl_temp_orders ORDER BY id DESC";
$result = mysqli_query($con, $query);
if (!$result) {
    die("<pre>❌ MySQL Error: " . mysqli_error($con) . "</pre>");
}
?>

<div class="container-fluid px-4">
  <h2 class="mt-4 mb-3">
    <i class="fa fa-whatsapp text-success me-2"></i> WhatsApp Chatbot Orders (Temporary)
  </h2>

  <div class="card shadow-sm p-3 mb-4">
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-success">
          <tr>
            <th>ID</th>
            <th>User Mobile</th>
            <th>Full Name</th>
            <th>Street</th>
            <th>Landmark</th>
            <th>City</th>
            <th>Taluka</th>
            <th>District</th>
            <th>Pincode</th>
            <th>Product Short Name</th>
            <th>Quantity</th>
            <th>Payment Mode</th>
            <th>Created At</th>
            <th>Updated At</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if (mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
                  echo "<tr>";
                  echo "<td>" . htmlspecialchars($row['id'] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
                  echo "<td>" . htmlspecialchars($row['user_mobile'] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
                  echo "<td>" . htmlspecialchars($row['fname'] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
                  echo "<td>" . htmlspecialchars($row['street'] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
                  echo "<td>" . htmlspecialchars($row['landmark'] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
                  echo "<td>" . htmlspecialchars($row['city'] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
                  echo "<td>" . htmlspecialchars($row['taluka'] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
                  echo "<td>" . htmlspecialchars($row['district'] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
                  echo "<td>" . htmlspecialchars($row['pincode'] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
                  echo "<td>" . htmlspecialchars($row['product_short_name'] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
                  echo "<td>" . htmlspecialchars($row['qty'] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
                  echo "<td>" . htmlspecialchars($row['payment_mode'] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
                  echo "<td>" . htmlspecialchars($row['created_at'] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
                  echo "<td>" . htmlspecialchars($row['updated_at'] ?? '', ENT_QUOTES, 'UTF-8') . "</td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='14' class='text-center text-muted'>
                    <i class='fa fa-info-circle'></i> No temporary orders received yet.
                    </td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
