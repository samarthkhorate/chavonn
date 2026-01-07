<?php
include("../include/config.php");
include("sidebar.php");
// Fetch product SKUs
$products = mysqli_query($con, "SELECT product_sku_code, product_name, product_prepaid_price, product_mrp FROM tbl_products ORDER BY product_name ASC");
// Fetch channels
$channels = mysqli_query($con, "SELECT channel_id, channel_name FROM tbl_channel WHERE channel_active=1 ORDER BY channel_name ASC");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = trim($_POST['fname']);
    $mobno = trim($_POST['mobno']);
    $street = trim($_POST['street']);
    $landmark = trim($_POST['landmark']);
    $city = trim($_POST['city']);
    $taluka = trim($_POST['taluka']);
    $district = trim($_POST['district']);
    $pincode = trim($_POST['pincode']);
    $qty = intval($_POST['qty']);
    $channel_id = intval($_POST['channel_id']);
    $sku = mysqli_real_escape_string($con, $_POST['product_sku']);
    $payment_mode = mysqli_real_escape_string($con, $_POST['payment_mode']);

    // Validate product
    $product = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM tbl_products WHERE product_sku_code='$sku'"));
    if (!$product) {
        echo "<script>alert('Invalid product selected.');</script>";
    } else {
        $order_id = "OD" . time();
        $total = $product['product_prepaid_price'] * $qty;

        $stmt = $con->prepare("INSERT INTO tbl_orders 
        (order_id, fname, mobno, street, landmark, city, taluka, district, pincode, product_sku, item_mrp, qty, mode_of_payment, total_amount, order_date, order_time, payment_status, channel_id, created_at, is_verified)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,CURDATE(),CURTIME(),'success',?,CURRENT_TIMESTAMP,'0')");
        $stmt->bind_param("sssssssssssisii", $order_id, $fname, $mobno, $street, $landmark, $city, $taluka, $district, $pincode, $sku, $product['product_mrp'], $qty, $payment_mode, $total, $channel_id);

        if ($stmt->execute()) {

            // ==================== WhatsApp API Notification (Console.Neotechking.com) ====================
            $order_details = [
                'mobno' => $mobno,
                'fname' => $fname,
                'product_sku' => $sku,
                'order_id' => $order_id,
                'total_amount' => $total
            ];

            $mobile = $order_details['mobno'];
            $customer_name = urlencode($order_details['fname']);
            $product_name = urlencode($order_details['product_sku']);
            $order_id_encoded = urlencode($order_details['order_id']);
            $amount = urlencode($order_details['total_amount']);

            // API URL
            $api_url = "https://console.neotechking.com/restapi/request.php?authkey=564779968434662c"
                . "&mobile={$mobile}"
                . "&country_code=91"
                . "&wid=17115"
                . "&1={$customer_name}"
                . "&2={$product_name}"
                . "&3={$order_id_encoded}"
                . "&4={$amount}";

            // Try API Call
            $response = @file_get_contents($api_url);
            $status = 'fail'; // default

            if ($response !== false) {
                $result = json_decode($response, true);
                
                if (isset($result['Message']) && $result['Message'] === 'Submitted Successfully') {
                    $status = 'success';
                    
                    // Update main order record
                    $update_msg = "UPDATE tbl_orders SET pending_msg = 1 WHERE order_id = '" . $con->real_escape_string($order_id) . "'";
                    $con->query($update_msg);
                }
            }

            // Log every attempt
            $log_query = "INSERT INTO tbl_order_msg_logs (order_id, mobile, api_url, api_response, status)
                          VALUES (
                            '" . $con->real_escape_string($order_id) . "',
                            '" . $con->real_escape_string($mobile) . "',
                            '" . $con->real_escape_string($api_url) . "',
                            '" . $con->real_escape_string($response ?? 'No response') . "',
                            '" . $con->real_escape_string($status) . "'
                          )";
            $con->query($log_query);
            // =====================================================================

            echo "<script>alert('✅ Order added successfully (Unverified). Redirecting to Dashboard...'); window.location='add_manual_order.php';</script>";
            exit;
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }
    }
}

?>

<h2 class="mb-3"><i class="fa fa-cart-plus text-success me-2"></i> Add Manual Order</h2>

<div class="card p-4 shadow-sm mb-5">
  <form method="POST" id="manualOrderForm" novalidate>
    <div class="row g-3">

      <!-- Channel -->
      <div class="col-md-4">
        <label class="form-label">Select Channel / Agent <span class="text-danger">*</span></label>
        <select name="channel_id" id="channel_id" class="form-select" required>
          <option value="">-- Select Channel --</option>
          <?php while($c=mysqli_fetch_assoc($channels)){ ?>
            <option value="<?php echo $c['channel_id']; ?>"><?php echo htmlspecialchars($c['channel_name']); ?></option>
          <?php } ?>
        </select>
        <div class="invalid-feedback">Select a valid channel.</div>
      </div>

      <!-- Product SKU -->
      <div class="col-md-4">
        <label class="form-label">Select Product SKU <span class="text-danger">*</span></label>
        <select name="product_sku" id="product_sku" class="form-select" required>
          <option value="">-- Select Product --</option>
          <?php while($p=mysqli_fetch_assoc($products)){ ?>
            <option value="<?php echo htmlspecialchars($p['product_sku_code']); ?>">
              <?php echo htmlspecialchars($p['product_name']); ?> (₹<?php echo $p['product_prepaid_price']; ?>)
            </option>
          <?php } ?>
        </select>
        <div class="invalid-feedback">Select a valid product.</div>
      </div>

      <!-- Payment -->
      <div class="col-md-4">
        <label class="form-label">Payment Mode <span class="text-danger">*</span></label>
        <select name="payment_mode" id="payment_mode" class="form-select" required>
          <option value="upi_qr_payment">UPI QR Payment</option>
          <option value="cod">Cash on Delivery (COD)</option>
        </select>
      </div>

      <!-- Name -->
      <div class="col-md-6">
        <label class="form-label">Full Name <span class="text-danger">*</span></label>
        <input type="text" name="fname" id="fname" class="form-control" required>
        <div class="invalid-feedback">Enter valid name (letters and spaces only).</div>
      </div>

      <!-- Mobile -->
      <div class="col-md-6">
        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
        <input type="text" name="mobno" id="mobno" maxlength="10" class="form-control" required>
        <div class="invalid-feedback">Enter 10-digit mobile number.</div>
      </div>

      <!-- Street -->
      <div class="col-md-6">
        <label class="form-label">Street <span class="text-danger">*</span></label>
        <input type="text" name="street" id="street" class="form-control" required>
        <div class="invalid-feedback">Enter valid street name.</div>
      </div>

      <!-- Landmark -->
      <div class="col-md-6">
        <label class="form-label">Landmark <span class="text-danger">*</span></label>
        <input type="text" name="landmark" id="landmark" class="form-control" required>
        <div class="invalid-feedback">Enter valid landmark.</div>
      </div>

      <!-- City -->
      <div class="col-md-4">
        <label class="form-label">City <span class="text-danger">*</span></label>
        <input type="text" name="city" id="city" class="form-control" required>
        <div class="invalid-feedback">Enter valid city.</div>
      </div>

      <!-- Taluka -->
      <div class="col-md-4">
        <label class="form-label">Taluka <span class="text-danger">*</span></label>
        <input type="text" name="taluka" id="taluka" class="form-control" required>
        <div class="invalid-feedback">Enter valid taluka.</div>
      </div>

      <!-- District -->
      <div class="col-md-4">
        <label class="form-label">District <span class="text-danger">*</span></label>
        <input type="text" name="district" id="district" class="form-control" required>
        <div class="invalid-feedback">Enter valid district.</div>
      </div>

      <!-- Pincode -->
      <div class="col-md-3">
        <label class="form-label">Pincode <span class="text-danger">*</span></label>
        <input type="text" name="pincode" id="pincode" maxlength="6" class="form-control" required>
        <div class="invalid-feedback">Enter 6-digit pincode.</div>
      </div>

      <!-- Quantity -->
      <div class="col-md-3">
        <label class="form-label">Quantity <span class="text-danger">*</span></label>
        <input type="number" name="qty" id="qty" class="form-control" value="1" min="1" required>
        <div class="invalid-feedback">Enter a valid quantity.</div>
      </div>

      <!-- Submit -->
      <div class="col-12 mt-4 text-end">
        <button type="submit" id="submitBtn" class="btn btn-success px-4" disabled>
          <i class="fa fa-save"></i> Save Manual Order
        </button>
      </div>
    </div>
  </form>
</div>

<script>
// Real-time field validation + enable submit only if all valid
const fields = {
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
  const regex = fields[id];
  if (!regex) return true;
  if (!regex.test(input.value.trim())) {
    input.classList.add('is-invalid');
    return false;
  } else {
    input.classList.remove('is-invalid');
    return true;
  }
}

function validateSelect(id) {
  const input = document.getElementById(id);
  if (input.value === '') {
    input.classList.add('is-invalid');
    return false;
  } else {
    input.classList.remove('is-invalid');
    return true;
  }
}

function checkAllValid() {
  let allValid = true;
  for (let key in fields) if (!validateField(key)) allValid = false;
  if (!validateSelect('channel_id')) allValid = false;
  if (!validateSelect('product_sku')) allValid = false;
  const qty = document.getElementById('qty');
  if (parseInt(qty.value) < 1) { qty.classList.add('is-invalid'); allValid = false; } else qty.classList.remove('is-invalid');
  document.getElementById('submitBtn').disabled = !allValid;
  return allValid;
}

// Add real-time listeners
document.addEventListener('input', e => {
  if (fields[e.target.id]) validateField(e.target.id);
  checkAllValid();
});

document.addEventListener('change', e => {
  if (['channel_id','product_sku'].includes(e.target.id)) validateSelect(e.target.id);
  checkAllValid();
});
</script>

<style>
.is-invalid { border-color: #dc3545 !important; }
.invalid-feedback { display: block; }
</style>

</body>
</html>
