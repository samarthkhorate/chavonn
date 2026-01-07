<?php
include("../include/config.php");
include("sidebar.php");

if (!isset($_GET['id'])) { die("Invalid Request"); }

$id = intval($_GET['id']);
$order = mysqli_fetch_assoc(mysqli_query($con, "SELECT o.*, c.channel_name, p.product_name 
    FROM tbl_orders o 
    LEFT JOIN tbl_channel c ON o.channel_id = c.channel_id 
    LEFT JOIN tbl_products p ON o.product_sku = p.product_sku_code 
    WHERE o.id = '$id'"));

if (!$order) {
    die("<div class='alert alert-danger'>Order not found!</div>");
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = trim($_POST['fname']);
    $mobno = trim($_POST['mobno']);
    $street = trim($_POST['street']);
    $landmark = trim($_POST['landmark']);
    $city = trim($_POST['city']);
    $taluka = trim($_POST['taluka']);
    $district = trim($_POST['district']);
    $pincode = trim($_POST['pincode']);

    $stmt = $con->prepare("UPDATE tbl_orders SET fname=?, mobno=?, street=?, landmark=?, city=?, taluka=?, district=?, pincode=? WHERE id=?");
    $stmt->bind_param("ssssssssi", $fname, $mobno, $street, $landmark, $city, $taluka, $district, $pincode, $id);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Order updated successfully!'); window.location='unverified_orders.php';</script>";
        exit;
    } else {
        echo "<div class='alert alert-danger'>Update failed: " . $stmt->error . "</div>";
    }
}
?>

<h2 class="mb-4"><i class="fa fa-edit text-warning me-2"></i>Edit Order #<?php echo htmlspecialchars($order['order_id']); ?></h2>

<div class="card shadow-sm p-4 mb-5">
    <!-- Non-editable details -->
    <div class="mb-4">
        <h5 class="text-success"><i class="fa fa-info-circle me-2"></i>Order Summary</h5>
        <div class="row g-3">
            <div class="col-md-3"><strong>Product:</strong> <?php echo htmlspecialchars($order['product_name']); ?></div>
            <div class="col-md-3"><strong>SKU:</strong> <?php echo htmlspecialchars($order['product_sku']); ?></div>
            <div class="col-md-3"><strong>Qty:</strong> <?php echo htmlspecialchars($order['qty']); ?></div>
            <div class="col-md-3"><strong>Total:</strong> ₹<?php echo number_format($order['total_amount'],2); ?></div>

            <div class="col-md-3"><strong>Payment Mode:</strong> <?php echo htmlspecialchars($order['mode_of_payment']); ?></div>
            <div class="col-md-3"><strong>Channel:</strong> <?php echo htmlspecialchars($order['channel_name']); ?></div>
            <div class="col-md-3"><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></div>
            <div class="col-md-3"><strong>Status:</strong> <?php echo ($order['is_verified']=='1') ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-danger">Unverified</span>'; ?></div>
        </div>
    </div>

    <hr>

    <!-- Editable Form -->
    <form method="POST" id="editOrderForm" novalidate>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="fname" id="fname" class="form-control" value="<?php echo htmlspecialchars($order['fname']); ?>" required>
                <div class="invalid-feedback">Enter valid name (letters and spaces only).</div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                <input type="text" name="mobno" id="mobno" class="form-control" maxlength="10" value="<?php echo htmlspecialchars($order['mobno']); ?>" required>
                <div class="invalid-feedback">Enter valid 10-digit mobile number.</div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Street <span class="text-danger">*</span></label>
                <input type="text" name="street" id="street" class="form-control" value="<?php echo htmlspecialchars($order['street']); ?>" required>
                <div class="invalid-feedback">Enter valid street name.</div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Landmark <span class="text-danger">*</span></label>
                <input type="text" name="landmark" id="landmark" class="form-control" value="<?php echo htmlspecialchars($order['landmark']); ?>" required>
                <div class="invalid-feedback">Enter valid landmark.</div>
            </div>

            <div class="col-md-4">
                <label class="form-label">City <span class="text-danger">*</span></label>
                <input type="text" name="city" id="city" class="form-control" value="<?php echo htmlspecialchars($order['city']); ?>" required>
                <div class="invalid-feedback">Enter valid city.</div>
            </div>

            <div class="col-md-4">
                <label class="form-label">Taluka <span class="text-danger">*</span></label>
                <input type="text" name="taluka" id="taluka" class="form-control" value="<?php echo htmlspecialchars($order['taluka']); ?>" required>
                <div class="invalid-feedback">Enter valid taluka.</div>
            </div>

            <div class="col-md-4">
                <label class="form-label">District <span class="text-danger">*</span></label>
                <input type="text" name="district" id="district" class="form-control" value="<?php echo htmlspecialchars($order['district']); ?>" required>
                <div class="invalid-feedback">Enter valid district.</div>
            </div>

            <div class="col-md-3">
                <label class="form-label">Pincode <span class="text-danger">*</span></label>
                <input type="text" name="pincode" id="pincode" maxlength="6" class="form-control" value="<?php echo htmlspecialchars($order['pincode']); ?>" required>
                <div class="invalid-feedback">Enter valid 6-digit pincode.</div>
            </div>

            <div class="col-12 text-end mt-4">
                <button type="submit" id="submitBtn" class="btn btn-primary px-4" disabled>
                    <i class="fa fa-save"></i> Save Changes
                </button>
                <a href="unverified_orders.php" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>

<script>
// Validation patterns (same as add form)
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
    if (!regex.test(input.value.trim())) {
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
