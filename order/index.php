<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Order Now – Chavonn Spices</title>

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<!-- Bootstrap -->
<link rel="stylesheet" href="assets/vendor/css/core.css">
<link rel="stylesheet" href="assets/vendor/css/theme-default.css">

<style>
:root{
  --green:#2e7d32;
  --dark:#1b5e20;
  --light:#e8f5e9;
}

body{
  font-family:'Poppins',sans-serif;
  background:linear-gradient(135deg,#e8f5e9,#ffffff);
}

.container-xxl{
  max-width:700px;
}

.card{
  border-radius:18px;
  box-shadow:0 10px 30px rgba(0,0,0,.15);
  border:none;
}

.card-header{
  text-align:center;
  background:linear-gradient(90deg,var(--green),var(--dark));
  color:#fff;
  border-radius:18px 18px 0 0;
}

.card-header h1{
  font-size:22px;
  font-weight:700;
}

.card-body label{
  font-weight:600;
  color:var(--dark);
}

.input-group-text{
  background:var(--light);
  color:var(--dark);
}

.form-control{
  border-radius:8px;
}

.btn-primary{
  background:linear-gradient(90deg,var(--green),var(--dark));
  border:none;
  font-weight:600;
}

.order-summary{
  background:var(--light);
  padding:15px;
  border-radius:10px;
}

.footer-container{
  text-align:center;
  font-size:14px;
  margin-top:20px;
  color:#555;
}
.footer-link{
  color:var(--green);
  font-weight:600;
  text-decoration:none;
}
</style>

<?php 
include '../config.php';
$pg_sku = $_GET['pg_sku'];

$sql = "SELECT * FROM tbl_products WHERE product_sku_code = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $pg_sku);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
$product = $result->fetch_assoc();
?>

</head>
<body>

<div class="container-xxl d-flex justify-content-center align-items-center min-vh-100">
<div class="card w-100">

<div class="card-header">
<h1>Chavonn Spices – Order Form 🌿</h1>
<p>Pure • Authentic • Traditional Masalas</p>
</div>

<div class="card-body">

<form id="myForm" onsubmit="return validateForm(event)" method="post" action="lang_order_place.php">

<!-- FULL NAME -->
<div class="mb-3">
<label>Full Name</label>
<input type="text" class="form-control" name="full_name" id="full-name">
<div class="error-message" id="full-name-error" style="color:red"></div>
</div>

<!-- MOBILE -->
<div class="mb-3">
<label>Mobile Number</label>
<input type="text" class="form-control" name="mobile_number" id="mobile-number">
<div class="error-message" id="mobile-number-error" style="color:red"></div>
</div>

<!-- STREET -->
<div class="mb-3">
<label>Street Name</label>
<input type="text" class="form-control" name="street_name" id="street-name">
<div class="error-message" id="street-name-error" style="color:red"></div>
</div>

<!-- LANDMARK -->
<div class="mb-3">
<label>Landmark</label>
<input type="text" class="form-control" name="landmark" id="landmark">
<div class="error-message" id="landmark-error" style="color:red"></div>
</div>

<!-- CITY -->
<div class="mb-3">
<label>City</label>
<input type="text" class="form-control" name="city_name" id="city-name">
<div class="error-message" id="city-name-error" style="color:red"></div>
</div>

<!-- TALUKA -->
<div class="mb-3">
<label>Taluka</label>
<input type="text" class="form-control" name="taluka" id="taluka">
<div class="error-message" id="taluka-error" style="color:red"></div>
</div>

<!-- DISTRICT -->
<div class="mb-3">
<label>District</label>
<input type="text" class="form-control" name="district" id="district">
<div class="error-message" id="district-error" style="color:red"></div>
</div>

<!-- PINCODE -->
<div class="mb-3">
<label>Pincode</label>
<input type="text" class="form-control" name="pincode" id="pincode">
<div class="error-message" id="pincode-error" style="color:red"></div>
</div>

<!-- PRODUCT -->
<div class="mb-3">
<label>Product</label>
<div class="d-flex align-items-center">
<img src="product_images/<?php echo $product['product_image_path'];?>" width="80">
<div class="ms-3">
<strong><?php echo $product['product_name'];?></strong><br>
MRP ₹<?php echo $product['product_mrp'];?>
</div>
</div>
<input type="hidden" name="language" value="<?php echo $pg_sku;?>">
</div>

<!-- QUANTITY -->
<div class="mb-3">
<label>Quantity</label>
<input type="number" class="form-control" name="quantity" id="quantity" value="1" readonly>
</div>

<!-- PAYMENT -->
<div class="mb-3">
<label>Payment Mode</label><br>
<input type="radio" name="paymentMode" value="prepaid" checked onclick="updateSummary()"> Prepaid  
<br>
<input type="radio" name="paymentMode" value="cod" onclick="updateSummary()"> Cash on Delivery
</div>

<!-- SUMMARY -->
<div class="order-summary mb-3">
<p>MRP: ₹<span id="orderMrp"><?php echo $product['product_mrp'];?></span></p>
<p class="text-success" id="discountLine">
Discount: ₹<span id="discount"><?php echo $product['product_mrp'] - $product['product_prepaid_price'];?></span>
</p>
<p><strong>Total: ₹<span id="totalAmount"><?php echo $product['product_prepaid_price'];?></span></strong></p>
<p id="codNote" class="d-none text-muted">
Remaining COD: ₹<span id="codRemaining"><?php echo $product['product_cod_amount'];?></span>
</p>
</div>

<button type="submit" id="submit-button" class="btn btn-primary w-100">
Pay ₹<?php echo $product['product_prepaid_price'];?> & Place Order
</button>

</form>

</div>
</div>
</div>

<div class="footer-container">
📞 9588620512 | © 2025 <b>Chavonn Spices</b><br>
Developed by <a href="https://neotechking.com/" class="footer-link">Neotechking</a>
</div>

<script>
let mrp=<?php echo $product['product_mrp'];?>;
let prepaid=<?php echo $product['product_prepaid_price'];?>;
let cod=<?php echo $product['product_cod_amount'];?>;

function updateSummary(){
let mode=document.querySelector('input[name="paymentMode"]:checked').value;
if(mode==="prepaid"){
document.getElementById("discountLine").classList.remove("d-none");
document.getElementById("codNote").classList.add("d-none");
document.getElementById("totalAmount").innerText=prepaid;
document.getElementById("submit-button").innerText=`Pay ₹${prepaid} & Place Order`;
}else{
document.getElementById("discountLine").classList.add("d-none");
document.getElementById("codNote").classList.remove("d-none");
document.getElementById("totalAmount").innerText=prepaid;
document.getElementById("codRemaining").innerText=cod;
document.getElementById("submit-button").innerText=`Pay ₹${prepaid} Now & ₹${cod} on Delivery`;
}
}
</script>

</body>
</html>

<?php } else { echo "Invalid SKU"; } ?>
