<?php
// new_admin_login/edit_order.php

// Throw mysqli errors as exceptions (easier to debug than blank screens)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

include("../config.php");
include("sidebar.php");

// --- Ensure UTF-8 everywhere and set a consistent connection collation ---
mysqli_set_charset($con, "utf8mb4");
mysqli_query($con, "SET collation_connection = 'utf8mb4_unicode_ci'");

// --- Validate id ---
if (!isset($_GET['id'])) {
    die("Invalid Request");
}
$id = (int)$_GET['id']; // numeric primary key

// ------------------------
// Fetch order (prepared)
// ------------------------
// We enforce collation in the JOIN conditions to avoid "illegal mix of collations".
// - product_sku vs product_sku_code: COLLATE utf8mb4_unicode_ci on both sides
// - channel_id: cast both to CHAR then COLLATE, so it works whether columns are INT or VARCHAR
$sql = "
    SELECT 
        o.*,
        c.channel_name,
        p.product_name
    FROM tbl_orders o
    LEFT JOIN tbl_channel  c 
        ON CAST(o.channel_id AS CHAR) COLLATE utf8mb4_unicode_ci 
         = CAST(c.channel_id AS CHAR) COLLATE utf8mb4_unicode_ci
    LEFT JOIN tbl_products p 
        ON o.product_sku COLLATE utf8mb4_unicode_ci 
         = p.product_sku_code COLLATE utf8mb4_unicode_ci
    WHERE o.id = ?
";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    die("<div class='alert alert-danger'>Order not found!</div>");
}

// ------------------------
// Handle Update
// ------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic trimming/sanitization
    $fname    = trim($_POST['fname']    ?? '');
    $mobno    = trim($_POST['mobno']    ?? '');
    $street   = trim($_POST['street']   ?? '');
    $landmark = trim($_POST['landmark'] ?? '');
    $city     = trim($_POST['city']     ?? '');
    $taluka   = trim($_POST['taluka']   ?? '');
    $district = trim($_POST['district'] ?? '');
    $pincode  = trim($_POST['pincode']  ?? '');

    // Optional: lightweight validation (server-side mirror of your JS)
    if (
        $fname === '' || $mobno === '' || $street === '' || $landmark === '' ||
        $city === '' || $taluka === '' || $district === '' || $pincode === ''
    ) {
        echo "<div class='alert alert-danger'>Please fill all required fields.</div>";
    } else {
        // Prepared update
        $u = $con->prepare("
            UPDATE tbl_orders
               SET fname=?, mobno=?, street=?, landmark=?, city=?, taluka=?, district=?, pincode=?
             WHERE id=?
        ");
        $u->bind_param("ssssssssi", $fname, $mobno, $street, $landmark, $city, $taluka, $district, $pincode, $id);
        $u->execute();
        $u->close();

        echo "<script>alert('✅ Order updated successfully!'); window.location='pending_ship_orders.php';</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Edit Order</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3">

<h2 class="mb-4"><i class="fa fa-edit text-warning me-2"></i>Edit Order #<?php echo htmlspecialchars($order['order_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h2>

<div class="card shadow-sm p-4 mb-5">
    <!-- Non-editable details -->
    <div class="mb-4">
        <h5 class="text-success"><i class="fa fa-info-circle me-2"></i>Order Summary</h5>
        <div class="row g-3">
            <div class="col-md-3"><strong>Product:</strong> <?php echo htmlspecialchars($order['product_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="col-md-3"><strong>SKU:</strong> <?php echo htmlspecialchars($order['product_sku'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="col-md-3"><strong>Qty:</strong> <?php echo htmlspecialchars($order['qty'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="col-md-3"><strong>Total:</strong> ₹<?php echo number_format((float)($order['total_amount'] ?? 0), 2); ?></div>

            <div class="col-md-3"><strong>Payment Mode:</strong> <?php echo htmlspecialchars($order['mode_of_payment'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="col-md-3"><strong>Channel:</strong> <?php echo htmlspecialchars($order['channel_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="col-md-3"><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="col-md-3">
                <strong>Status:</strong>
                <?php
                    $verified = isset($order['is_verified']) ? (string)$order['is_verified'] : '0';
                    echo ($verified === '1')
                        ? '<span class="badge bg-success">Verified</span>'
                        : '<span class="badge bg-danger">Unverified</span>';
                ?>
            </div>
        </div>
    </div>

    <hr>

    <!-- Editable Form -->
    <form method="POST" id="editOrderForm" novalidate>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="fname" id="fname" class="form-control"
                       value="<?php echo htmlspecialchars($order['fname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                <div class="invalid-feedback">Enter valid name (letters and spaces only).</div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                <input type="text" name="mobno" id="mobno" class="form-control" maxlength="10"
                       value="<?php echo htmlspecialchars($order['mobno'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                <div class="invalid-feedback">Enter valid 10-digit mobile number.</div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Street <span class="text-danger">*</span></label>
                <input type="text" name="street" id="street" class="form-control"
                       value="<?php echo htmlspecialchars($order['street'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                <div class="invalid-feedback">Enter valid street name.</div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Landmark <span class="text-danger">*</span></label>
                <input type="text" name="landmark" id="landmark" class="form-control"
                       value="<?php echo htmlspecialchars($order['landmark'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                <div class="invalid-feedback">Enter valid landmark.</div>
            </div>

            <div class="col-md-4">
                <label class="form-label">City <span class="text-danger">*</span></label>
                <input type="text" name="city" id="city" class="form-control"
                       value="<?php echo htmlspecialchars($order['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                <div class="invalid-feedback">Enter valid city.</div>
            </div>

            <div class="col-md-4">
                <label class="form-label">Taluka <span class="text-danger">*</span></label>
                <input type="text" name="taluka" id="taluka" class="form-control"
                       value="<?php echo htmlspecialchars($order['taluka'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                <div class="invalid-feedback">Enter valid taluka.</div>
            </div>

            <div class="col-md-4">
                <label class="form-label">District <span class="text-danger">*</span></label>
                <input type="text" name="district" id="district" class="form-control"
                       value="<?php echo htmlspecialchars($order['district'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                <div class="invalid-feedback">Enter valid district.</div>
            </div>

            <div class="col-md-3">
                <label class="form-label">Pincode <span class="text-danger">*</span></label>
                <input type="text" name="pincode" id="pincode" maxlength="6" class="form-control"
                       value="<?php echo htmlspecialchars($order['pincode'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                <div class="invalid-feedback">Enter valid 6-digit pincode.</div>
            </div>

            <div class="col-12 text-end mt-4">
                <button type="submit" id="submitBtn" class="btn btn-primary px-4" disabled>
                    <i class="fa fa-save"></i> Save Changes
                </button>
                <a href="pending_ship_orders.php" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>

<script>
// Validation patterns (same as your original)
const patterns = {
    fname: /^[A-Za-z\s]{2,}$/,
    mobno: /^[0-9]{10}$/,
    street: /^[A-Za-z0-9\s\-\.,&]{3,}$/,
    landmark: /^[A-Za-z0-9\s\-\.,&]{3,}$/,
    city: /^[A-Za-z\s]{2,}$/,
    taluka: /^[A-Za-z\s]{2,}$/,
    district: /^[A-Za-z\s]{2,}$/,
    pincode: /^[0-9]{6}$/
};

function validateField(id) {
    const input = document.getElementById(id);
    const regex = patterns[id];
    if (!regex.test((input.value || '').trim())) {
        input.classList.add('is-invalid');
        return false;
    } else {
        input.classList.remove('is-invalid');
        return true;
    }
}

function checkAllValid() {
    let valid = true;
    for (let id in patterns) {
        if (!validateField(id)) valid = false;
    }
    document.getElementById('submitBtn').disabled = !valid;
}

// Real-time validation
document.querySelectorAll('input').forEach(inp => {
    inp.addEventListener('input', () => {
        validateField(inp.id);
        checkAllValid();
    });
});

// Initial check
checkAllValid();
</script>

<style>
.is-invalid { border-color: #dc3545 !important; }
.invalid-feedback { display: block; }
</style>

</body>
</html>
