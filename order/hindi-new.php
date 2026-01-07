<!DOCTYPE html>

<!-- =========================================================
* Sneat - Bootstrap 5 HTML Admin Template - Pro | v1.0.0
==============================================================

* Product Page: https://themeselection.com/products/sneat-bootstrap-html-admin-template/
* Created by: ThemeSelection
* License: You must have a valid license purchased in order to legally use the theme for your project.
* Copyright ThemeSelection (https://themeselection.com)

=========================================================
 -->
<!-- beautify ignore:start -->
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Order Form</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="assets/js/config.js"></script>
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-PT6J4XZVS0"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-PT6J4XZVS0');
</script>
    <!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '904324298463906');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=904324298463906&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
  </head>
<style type="text/css">
  .content-wrapper {
  display: flex;
  align-items: center; /* Centers vertically */
  justify-content: center; /* Centers horizontally */
  min-height: 100vh; /* Ensures it takes full height */
  margin: 0;
}

.container-xxl {
  max-width: 600px; /* Adjust width as needed */
  width: 100%;
  padding: 20px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  background-color: #fff; /* Optional for better visibility */
  border-radius: 8px; /* Optional for rounded edges */
}
.footer-container {
  border: none;
  box-shadow: none;
  background-color: transparent;
}

.footer-container {
  border: none;
  background-color: transparent;
  font-size: 0.9rem;
  color: #6c757d; /* Muted text color */
  flex-direction: column; /* Ensures content stacks in the center */
  text-align: center;
}

.footer-link {
  color: #007bff; /* Link color */
  text-decoration: none;
  font-weight: 600;
}

.footer-link:hover {
  text-decoration: underline;
  color: #0056b3; /* Darker shade on hover */
}

</style>
<body>
 
<div class="content-wrapper">
  <div class="container-xxl">
    <div class="row">
      <div class="col-xxl">

     <div class="card mb-4"><br>
<h1 style="
    font-size: 20px;
    font-family: 'Bebas Neue', 'Noto Sans Devanagari', 'Mukta', sans-serif;
    font-weight: 700;
    letter-spacing: 0px;
    text-transform: uppercase;
    text-align: center;
    color: #D32F2F;
    margin-top: 10px; /* Adds space above h1 */
    margin-bottom: 3px; /* Reduces space below h1 */
">
    हिंदुस्थान की सबसे लोकप्रिय किताब!
</h1>

<div class="card-header d-flex flex-column align-items-center justify-content-center">
  <h5 class="mb-0" style="margin-top: 5px; text-align: center;">
    ऑर्डर करने के लिए अपनी पूरी जानकारी भरें
  </h5>
</div>


   




          </div>
          <div class="card-body">

<form id="myForm" onsubmit="return validateForm(event)" method="post" action="order_place.php">
  <!-- Full Name -->
  <div class="row mb-3">
<label class="col-sm-2 col-form-label" for="full-name">Full Name / पूरा नाम</label>
    <div class="col-sm-10">
      <div class="input-group input-group-merge">
        <span id="full-name-icon" class="input-group-text"><i class="bx bx-user"></i></span>
        <input
          type="text"
          class="form-control"
          id="full-name"
          name="full_name"
          placeholder="John Doe"
          title="Only alphabets and spaces are allowed."
          
        />
      </div>
      <div class="error-message" id="full-name-error" style="color: red;"></div>
    </div>
  </div>

  <!-- Mobile Number -->
  <div class="row mb-3">
<label class="col-sm-2 col-form-label" for="mobile-number">Mobile No / मोबाईल नंबर</label>
    <div class="col-sm-10">
      <div class="input-group input-group-merge">
        <span id="mobile-number-icon" class="input-group-text"><i class="bx bx-phone"></i></span>
        <input
          type="text"
          class="form-control"
          id="mobile-number"
          name="mobile_number"
          placeholder="6587998941"
          title="Enter a valid 10-digit mobile number."
          
        />
      </div>
      <div class="error-message" id="mobile-number-error" style="color: red;"></div>
    </div>
  </div>

  <!-- Road/Street Name -->
  <div class="row mb-3">
<label class="col-sm-2 col-form-label" for="street-name">Road/Street Name / सड़क / गली नाम</label>
    <div class="col-sm-10">
      <div class="input-group input-group-merge">
        <span id="street-name-icon" class="input-group-text"><i class="bx bx-map"></i></span>
        <input
          type="text"
          class="form-control"
          id="street-name"
          name="street_name"
          placeholder="Street Name"
          title="Only alphabets and spaces are allowed."
        />
      </div>
      <div class="error-message" id="street-name-error" style="color: red;"></div>
    </div>
  </div>

  <!-- Landmark -->
  <div class="row mb-3">
<label class="col-sm-2 col-form-label" for="landmark">Landmark / निकट का स्थान</label>
    <div class="col-sm-10">
      <div class="input-group input-group-merge">
        <span id="landmark-icon" class="input-group-text"><i class="bx bx-map-pin"></i></span>
        <input
          type="text"
          class="form-control"
          id="landmark"
          name="landmark"
          placeholder="Landmark"
          title="Only alphabets and spaces are allowed."
          
        />
      </div>
      <div class="error-message" id="landmark-error" style="color: red;"></div>
    </div>
  </div>

  <!-- Village/City Name -->
  <div class="row mb-3">
<label class="col-sm-2 col-form-label" for="city-name">Village/City Name / गाँव या शहर का नाम</label>
    <div class="col-sm-10">
      <div class="input-group input-group-merge">
        <span id="city-name-icon" class="input-group-text"><i class="bx bx-buildings"></i></span>
        <input
          type="text"
          class="form-control"
          id="city-name"
          name="city_name"
          placeholder="City Name"
          title="Only alphabets and spaces are allowed."
          
        />
      </div>
      <div class="error-message" id="city-name-error" style="color: red;"></div>
    </div>
  </div>

  <!-- Taluka -->
  <div class="row mb-3">
<label class="col-sm-2 col-form-label" for="taluka">Taluka / तहसील</label>
    <div class="col-sm-10">
      <div class="input-group input-group-merge">
        <span id="taluka-icon" class="input-group-text"><i class="bx bx-location-plus"></i></span>
        <input
          type="text"
          class="form-control"
          id="taluka"
          name="taluka"
          placeholder="Taluka Name"
          title="Only alphabets and spaces are allowed."
          
        />
      </div>
      <div class="error-message" id="taluka-error" style="color: red;"></div>
    </div>
  </div>

  <!-- District -->
  <div class="row mb-3">
<label class="col-sm-2 col-form-label" for="district">District / जिला</label>
    <div class="col-sm-10">
      <div class="input-group input-group-merge">
        <span id="district-icon" class="input-group-text"><i class="bx bx-flag"></i></span>
        <input
          type="text"
          class="form-control"
          id="district"
          name="district"
          placeholder="District Name"
          title="Only alphabets and spaces are allowed."
          
        />
      </div>
      <div class="error-message" id="district-error" style="color: red;"></div>
    </div>
  </div>

  <!-- Pincode -->
  <div class="row mb-3">
<label class="col-sm-2 col-form-label" for="pincode">Pincode / पिनकोड</label>
    <div class="col-sm-10">
      <div class="input-group input-group-merge">
        <span id="pincode-icon" class="input-group-text"><i class="bx bx-mail-send"></i></span>
        <input
          type="text"
          class="form-control"
          id="pincode"
          name="pincode"
          placeholder="123456"
          title="Enter a valid 6-digit pincode."
          
        />
      </div>
      <div class="error-message" id="pincode-error" style="color: red;"></div>
    </div>
  </div>



    <!-- Book Details -->
 <div class="row mb-3">
    <label class="col-sm-2 col-form-label">Product</label>
    <div class="col-sm-10 d-flex align-items-center">
      <img src="https://vaibhavdhus.com/assets/images/book.png" alt="The End Is The Beginning" width="100" height="100" class="me-3" />
      <div>
<p><strong>अंतः अस्ति प्रारंभः (The End is the Beginning)</strong></p>
        <p>Price: ₹320</p>
        <!-- Quantity Box -->
        <div class="input-group input-group-sm">
          <button
            class="btn btn-primary"
            id="decreaseQty"
            onclick="updateQuantity(-1, event)"
          >-</button>
          <input
            type="number"
            id="quantity"
            name="quantity"
            class="form-control text-center"
            value="1"
            min="1"
            readonly
          />
          <button
            class="btn btn-primary"
            id="increaseQty"
            onclick="updateQuantity(1, event)"
          >+</button>
        </div>
      </div>
    </div>
  </div>

<!-- Payment Options -->
<div class="row mb-3">
  <label class="col-sm-2 col-form-label">Select Language</label>
  <div class="col-sm-10">
    <div class="form-check">
      <input
        class="form-check-input"
        type="radio"
        name="paymentMethod"
        id="payOnline"
        value="online"
        checked
        onclick="updateSummary()"
      />
      <label class="form-check-label" for="payCOD">Hindi Edition</label>
    </div>
    <div class="form-check">
      <input
        class="form-check-input"
        type="radio"
        name="paymentMethod"
        id="payCOD"
        value="cod"
        onclick="updateSummary()"
      />
            <label class="form-check-label" for="payOnline">Marathi Edition</label>
    </div>
  </div>
</div>

<!-- Order Summary -->
<div class="row mb-3">
  <label class="col-sm-2 col-form-label">Order Summary</label>
  <div class="col-sm-10">
    <div id="order-summary">
      <p>Book Charges: ₹<span id="bookCharges">320</span></p>
      <p><strong style="color: green;">Free Home Delivery</strong></p>

      <p><strong>Total: ₹<span id="totalAmount">320</span></strong></p>
    </div>
  </div>
</div>


  <!-- Submit Button -->
  <div class="row justify-content-end">
    <div class="col-sm-10">
      <button type="submit" class="btn btn-primary" id="submit-button">Pay 320 Online and Place Order</button>
    </div>
  </div>
</div>

<script>
  const bookPrice = 270; // Price of one book
  const deliveryFee = 40; // Delivery charge for COD
  let quantity = 1; // Default quantity
  let paymentMethod = "online"; // Default payment method

  // Update quantity and recalculate
  function updateQuantity(change, event) {
    event.preventDefault(); // Prevent the page from scrolling to the top
    quantity = Math.max(1, quantity + change); // Ensure quantity is at least 1
    document.getElementById("quantity").value = quantity;
    updateSummary();
  }

  // Update order summary dynamically
  function updateSummary() {
    // Calculate book charges
    const bookCharges = bookPrice * quantity;
    document.getElementById("bookCharges").textContent = bookCharges;

    // Determine delivery charges
    const isCOD = document.getElementById("payCOD").checked;
    const deliveryCharges = isCOD ? deliveryFee : 0;
    document.getElementById("deliveryCharges").textContent = deliveryCharges;

    // Calculate total amount
    const totalAmount = bookCharges + deliveryCharges;
    document.getElementById("totalAmount").textContent = totalAmount;

    // Update button text
    const paymentText = isCOD ? "COD" : "Online";
    document.getElementById("submit-button").textContent = `Pay ₹${totalAmount} ${paymentText} and Place Order`;
  }

  // Call updateSummary on load to initialize
  window.onload = updateSummary;
</script>
            </form>

          </div>

        </div>
                  <!-- Footer Section -->
<!-- Footer Section -->
<div class="footer-container container-xxl d-flex justify-content-center text-center py-3 mt-3">
<div>
    For book/order-related queries, kindly contact: <a href="tel:7499454645" class="footer-link">7499454645</a><br>
    © Copyright 2024 Vaibhav Dhus. All Rights Reserved.<br>
    Developed by: <a href="https://shubhsairajinfotech.com/" target="_blank" class="footer-link"><strong>Shubhsairaj Infotech Private Limited</strong></a><br>
<!--    For development-related inquiries, contact the developer company at: <a href="tel:02269718142" class="footer-link">02269718142</a>
--></div>


</div>
      </div>
    </div>
  </div>
</div>
<script>
  function validateForm(event) {
    event.preventDefault(); // Prevent form submission

    // Clear any previous error messages
    clearErrors();

    let isValid = true;

    // Validate Full Name
    const fullName = document.getElementById('full-name');
    const fullNameError = document.getElementById('full-name-error');
    if (!fullName.value.match(/^[A-Za-z ]+$/)) {
      isValid = false;
      fullNameError.innerHTML = 'Please enter a valid full name (alphabets and spaces only). <br>(केवल अक्षर और स्पेस)';
    }

    // Validate Mobile Number
    const mobileNumber = document.getElementById('mobile-number');
    const mobileNumberError = document.getElementById('mobile-number-error');
    if (!mobileNumber.value.match(/^\d{10}$/)) {
      isValid = false;
      mobileNumberError.innerHTML = 'Please enter a valid mobile number (10 digits only)<br> (केवल 10 अंक)';
    }

    // Validate Road/Street Name
    const streetName = document.getElementById('street-name');
    const streetNameError = document.getElementById('street-name-error');
    if (!streetName.value.match(/^[A-Za-z0-9 ]+$/)) {
      isValid = false;
      streetNameError.innerHTML = 'Please enter a valid street name (alphabets and spaces only) <br>(केवल अक्षर और स्पेस)';
    }

    // Validate Landmark
    const landmark = document.getElementById('landmark');
    const landmarkError = document.getElementById('landmark-error');
    if (!landmark.value.match(/^[A-Za-z0-9 ]+$/)) {
      isValid = false;
      landmarkError.innerHTML = 'Please enter a valid landmark name (alphabets and spaces only)<br>(केवल अक्षर और स्पेस)';
    }

    // Validate City Name
    const cityName = document.getElementById('city-name');
    const cityNameError = document.getElementById('city-name-error');
    if (!cityName.value.match(/^[A-Za-z0-9 ]+$/)) {
      isValid = false;
      cityNameError.innerHTML = 'Please enter a valid city name (alphabets and spaces only)<br>(केवल अक्षर और स्पेस)';
    }

    // Validate Taluka
    const taluka = document.getElementById('taluka');
    const talukaError = document.getElementById('taluka-error');
    if (!taluka.value.match(/^[A-Za-z ]+$/)) {
      isValid = false;
      talukaError.innerHTML = 'Please enter a valid taluka name (alphabets and spaces only)<br> (केवल अक्षर और स्पेस)';
    }

    // Validate District
    const district = document.getElementById('district');
    const districtError = document.getElementById('district-error');
    if (!district.value.match(/^[A-Za-z ]+$/)) {
      isValid = false;
      districtError.innerHTML = 'Please enter a valid district name (alphabets and spaces only)<br>(केवल अक्षर और स्पेस)';
    }

    // Validate Pincode
    const pincode = document.getElementById('pincode');
    const pincodeError = document.getElementById('pincode-error');
    if (!pincode.value.match(/^\d{6}$/)) {
      isValid = false;
      pincodeError.innerHTML = 'Please enter a valid 6-digit pincode<br> (कृपया वैध 6 अंकों का पिनकोड दर्ज करें)';
    }

    // If all validations passed, submit the form
    if (isValid) {
      document.getElementById('myForm').submit();
    }

    return isValid;
  }

  function clearErrors() {
    const errorMessages = document.querySelectorAll('.error-message');
    errorMessages.forEach((message) => message.innerHTML = '');
  }
</script>





            <!-- / Content -->

            <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme">
             
            </footer>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/libs/popper/popper.js"></script>
    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="assets/js/main.js"></script>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
