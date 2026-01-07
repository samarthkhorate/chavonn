<?php
include("../include/config.php");
include("sidebar.php");

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Function for field validation (same logic as form)
function validate_field($regex, $value) {
    return preg_match($regex, trim($value));
}

// ✅ Safe MySQL REGEXP validation condition (dash moved to end)
$validation_condition = "
    fname REGEXP '^[A-Za-z ]{2,}$' AND
    mobno REGEXP '^[0-9]{10}$' AND
    street REGEXP '^[A-Za-z0-9[:space:].,&-]{3,}$' AND
    city REGEXP '^[A-Za-z ]{2,}$' AND
    taluka REGEXP '^[A-Za-z ]{2,}$' AND
    district REGEXP '^[A-Za-z ]{2,}$' AND
    pincode REGEXP '^[0-9]{6}$'
";

// ✅ Verify all valid orders
if (isset($_POST['verify_all'])) {
    $verify_all = "
        UPDATE tbl_orders 
        SET is_verified='1' 
        WHERE is_verified='0' 
          AND $validation_condition
    ";
    mysqli_query($con, $verify_all) or die("SQL Error: " . mysqli_error($con));
    echo "<script>alert('✅ All valid orders verified successfully!'); window.location='unverified_orders.php';</script>";
    exit;
}

// ✅ Verify valid orders for selected channel
if (isset($_GET['verify_channel'])) {
    $cid = intval($_GET['verify_channel']);
    $verify_channel = "
        UPDATE tbl_orders 
        SET is_verified='1' 
        WHERE is_verified='0' 
          AND channel_id='$cid'
          AND $validation_condition
    ";
    mysqli_query($con, $verify_channel) or die("SQL Error: " . mysqli_error($con));
    echo "<script>alert('✅ Verified all valid orders for the selected channel!'); window.location='unverified_orders.php';</script>";
    exit;
}

// ✅ Fetch all unverified orders
$order_query = "
    SELECT o.*, c.channel_name 
    FROM tbl_orders o 
    LEFT JOIN tbl_channel c ON o.channel_id = c.channel_id
    WHERE o.is_verified='0' AND o.payment_status='success'
    ORDER BY o.id DESC 
";
$q = mysqli_query($con, $order_query) or die("SQL Error: " . mysqli_error($con));

// ✅ Fetch channels with only valid unverified orders
$channel_query = "
    SELECT 
        c.channel_id, 
        c.channel_name,
        COUNT(o.id) AS valid_orders,
        COALESCE(SUM(o.total_amount), 0) AS total_amount
    FROM tbl_channel c
    LEFT JOIN tbl_orders o 
      ON o.channel_id = c.channel_id 
     AND o.is_verified='0'
     AND o.payment_status='success'
     AND $validation_condition
    GROUP BY c.channel_id
    HAVING valid_orders > 0
    ORDER BY valid_orders DESC
";
$channel_summary = mysqli_query($con, $channel_query) or die("SQL Error: " . mysqli_error($con));
?>

<div class="container-fluid px-4">
    <h2 class="mt-4 mb-3">
        <i class="fa fa-exclamation-circle text-warning me-2"></i> Unverified Orders
    </h2>

    <!-- Channel-wise valid orders -->
    <div class="mb-4">
        <h5>📊 Channel-wise Pending Verification</h5>
        <div class="d-flex flex-wrap gap-2">
            <?php 
            $hasChannel = false;
            while ($ch = mysqli_fetch_assoc($channel_summary)) { 
                $hasChannel = true; ?>
                <form method="GET" style="display:inline;">
                    <input type="hidden" name="verify_channel" value="<?php echo $ch['channel_id']; ?>">
                    <button type="submit" class="btn btn-outline-success btn-sm">
                        <?php echo htmlspecialchars($ch['channel_name']); ?> - 
                        <?php echo $ch['valid_orders']; ?> Orders - ₹<?php echo number_format((float)($ch['total_amount'] ?? 0), 2); ?>
                    </button>
                </form>
            <?php }
            if (!$hasChannel) echo "<span class='text-muted'>No channels have valid unverified orders.</span>";
            ?>
        </div>
    </div>

    <!-- Verify All Valid Orders Button -->
    <form method="POST" class="mb-3">
        <button type="submit" name="verify_all" class="btn btn-success btn-sm">
            <i class="fa fa-check-circle"></i> Verify All Valid Orders
        </button>
    </form>

    <!-- Unverified Orders Table -->
    <div class="card shadow-sm p-3 mb-4">
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-success">
                    <tr>
                        <th>ID</th>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Mobile</th>
                        <th>Channel</th>
                        <th>Amount</th>
                        <th>Validation</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($r = mysqli_fetch_assoc($q)) {
                        $errors = [];

                        if (!validate_field('/^[A-Za-z\s]{2,}$/', $r['fname'])) $errors[] = "Name";
                        if (!validate_field('/^[0-9]{10}$/', $r['mobno'])) $errors[] = "Mobile";
                        if (!validate_field('/^[A-Za-z0-9\s\-.,&]{3,}$/', $r['street'])) $errors[] = "Street";
                        if (!validate_field('/^[A-Za-z\s]{2,}$/', $r['city'])) $errors[] = "City";
                        if (!validate_field('/^[A-Za-z\s]{2,}$/', $r['taluka'])) $errors[] = "Taluka";
                        if (!validate_field('/^[A-Za-z\s]{2,}$/', $r['district'])) $errors[] = "District";
                        if (!validate_field('/^[0-9]{6}$/', $r['pincode'])) $errors[] = "Pincode";

                        $errorCount = count($errors);
                        $status = ($errorCount == 0)
                            ? "<span class='badge bg-success'>Valid</span>"
                            : "<span class='badge bg-danger'>$errorCount Error(s)</span>";
                    ?>
                        <tr>
                            <td><?php echo $r['id']; ?></td>
                            <td><?php echo htmlspecialchars($r['order_id']); ?></td>
<td><?php echo htmlspecialchars($r['fname'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($r['mobno'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
<td><?php echo htmlspecialchars($r['channel_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
<td>₹<?php echo number_format((float)($r['total_amount'] ?? 0), 2); ?></td>
                            <td><?php echo $status; ?></td>
                            <td>
                                <?php if ($errorCount == 0) { ?>
                                    <button class="btn btn-success btn-sm" onclick="verifyOrder(<?php echo $r['id']; ?>)">
                                        <i class="fa fa-check"></i> Verify
                                    </button>
                                <?php } else { ?>
                                    <a href="edit_order.php?id=<?php echo $r['id']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function verifyOrder(id){
    if(confirm("Are you sure you want to verify this order?")){
        window.location = "verify_order_action.php?id=" + id;
    }
}
</script>
