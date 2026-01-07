<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Products</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS (via CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins&display=swap" rel="stylesheet">
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
fbq('init', '718628090837177');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=718628090837177&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f9fa;
    }

    h2.section-title {
      font-family: 'Playfair Display', serif;
      font-size: 40px;
      font-weight: bold;
      color: #4a90e2;
      letter-spacing: 1px;
    }

    .card-img-top {
      width: 300px;
      height: 300px;
      object-fit: cover;
      border-top-left-radius: 20px;
      border-top-right-radius: 20px;
    }

    .card {
      border-radius: 20px;
    }

    .buy-now-btn {
      display: inline-block;
      background: linear-gradient(90deg, #FFD700, #FFA500);
      font-family: 'Poppins', sans-serif;
      font-size: 16px;
      font-weight: 600;
      color: black;
      padding: 10px 25px;
      border-radius: 8px;
      text-decoration: none;
      box-shadow: 2px 2px 12px rgba(255, 215, 0, 0.5);
      transition: all 0.3s ease;
    }

    .buy-now-btn:hover {
      background: linear-gradient(90deg, #FFA500, #FFD700);
      color: black;
      text-decoration: none;
    }
  </style>
</head>
<body>

  <!-- Services Section -->
  <section id="services" class="our-services section py-5">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="section-title">All Products</h2>
      </div>
      <div class="row">
        <?php
        include 'config.php';
        $query = "SELECT * FROM tbl_products";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            $productName = htmlspecialchars($row['product_name']);
            $productMRP = floatval($row['product_mrp']);
            $productPrepaid = floatval($row['product_prepaid_price']);
            $productSKU = urlencode($row['product_sku_code']);
            $productImage = $row['product_image_path'];

            // Calculate discount percentage
            $discountPercent = 0;
            if ($productMRP > 0 && $productPrepaid > 0 && $productMRP > $productPrepaid) {
              $discountPercent = round((($productMRP - $productPrepaid) / $productMRP) * 100);
            }
        ?>
        <div class="col-lg-4 col-md-6 mb-4 d-flex align-items-stretch">
          <div class="card shadow-sm border-0 w-100">
            <div class="text-center pt-3">
              <img src="order/product_images/<?php echo $productImage; ?>" 
                   alt="<?php echo $productName; ?>" 
                   class="card-img-top mx-auto">
            </div>
            <div class="card-body text-center">
              <h5 class="card-title" style="font-family: 'Playfair Display', serif; font-size: 22px; color: #df0050;">
                <?php echo $productName; ?>
              </h5>
              <p class="card-text" style="font-size: 16px; color: #555;">
                MRP: <s>₹<?php echo number_format($productMRP, 2); ?></s><br>
                Prepaid: <span style="color: green; font-weight: 600;">₹<?php echo number_format($productPrepaid, 2); ?></span>
                <?php if ($discountPercent > 0) { ?>
                  <br><span style="color: #df0050; font-size: 14px;">Save <?php echo $discountPercent; ?>%</span>
                <?php } ?>
              </p>
              <a href="order/index.php?pg_sku=<?php echo $productSKU; ?>" class="buy-now-btn">Buy Now</a>
            </div>
          </div>
        </div>
        <?php
          }
        } else {
          echo '<div class="col-12 text-center"><p>No products available at the moment.</p></div>';
        }
        ?>
      </div>
    </div>
  </section>

  <!-- Bootstrap JS (via CDN) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
