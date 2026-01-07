<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Chavonn Spices – Pure • Authentic • Traditional</title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<!-- Bootstrap -->
<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<style>
:root{
  --green:#2e7d32;
  --dark:#1b5e20;
  --light:#e8f5e9;
}

body{
  font-family:'Poppins',sans-serif;
  background:#f9fff9;
}

/* HEADER */
.header{
  background:#fff;
  padding:15px 0;
  box-shadow:0 4px 12px rgba(0,0,0,.08);
}
.navbar-brand{
  font-size:26px;
  font-weight:800;
  color:var(--dark);
}
.nav-link{
  color:var(--dark)!important;
  font-weight:600;
}
.nav-link:hover{
  color:var(--green)!important;
}

/* HERO */
.hero{
  background:linear-gradient(135deg,#e8f5e9,#ffffff);
  padding:80px 0;
}
.hero h1{
  font-size:42px;
  font-weight:800;
  color:var(--dark);
}
.hero p{
  font-size:18px;
  color:#444;
}
.hero .btn{
  background:linear-gradient(90deg,var(--green),var(--dark));
  color:#fff;
  padding:12px 30px;
  border-radius:8px;
  font-weight:600;
}

/* PRODUCTS */
.section-title{
  font-size:36px;
  font-weight:800;
  color:var(--dark);
  margin-bottom:40px;
}
.card{
  border:none;
  border-radius:18px;
  transition:.3s;
}
.card:hover{
  transform:translateY(-6px);
  box-shadow:0 10px 25px rgba(0,0,0,.15);
}
.price{
  color:var(--green);
  font-weight:700;
}
.buy-btn{
  background:linear-gradient(90deg,var(--green),var(--dark));
  color:#fff;
  padding:10px 25px;
  border-radius:8px;
  text-decoration:none;
  font-weight:600;
}

/* ABOUT */
.about{
  background:#ffffff;
  padding:80px 0;
}
.about p{
  font-size:17px;
  color:#444;
  line-height:1.7;
}

/* CONTACT */
.contact{
  background:linear-gradient(135deg,var(--dark),var(--green));
  color:#fff;
  padding:80px 0;
}
.contact input,
.contact textarea{
  background:transparent;
  border:1px solid #c8e6c9;
  color:#fff;
}
.contact input::placeholder,
.contact textarea::placeholder{
  color:#e0f2f1;
}

/* FOOTER */
footer{
  background:#0f2f1b;
  color:#fff;
  padding:25px 0;
}
</style>

</head>
<body>

<!-- HEADER -->
<nav class="navbar navbar-expand-lg header">
  <div class="container">
    <a class="navbar-brand" href="#">Chavonn Spices</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
      ☰
    </button>
    <div class="collapse navbar-collapse" id="menu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="#products">Products</a></li>
        <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
        <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6">
        <h1>Pure & Authentic Indian Spices 🌿</h1>
        <p>
          Hand-picked ingredients • Traditional grinding •  
          No chemicals • No preservatives
        </p>
        <a href="#products" class="btn">Shop Spices</a>
      </div>
      <div class="col-lg-6 text-center">
        <img src="assets/images/spices-hero.png" class="img-fluid" alt="Spices">
      </div>
    </div>
  </div>
</section>

<!-- PRODUCTS -->
<section id="products" class="py-5">
  <div class="container">
    <h2 class="section-title text-center">Our Premium Masalas</h2>
    <div class="row">

<?php
include 'config.php';
$query="SELECT * FROM tbl_products";
$result=mysqli_query($con,$query);

if($result && mysqli_num_rows($result)>0){
while($row=mysqli_fetch_assoc($result)){
$name=htmlspecialchars($row['product_name']);
$mrp=$row['product_mrp'];
$price=$row['product_prepaid_price'];
$sku=urlencode($row['product_sku_code']);
$img=$row['product_image_path'];
?>

<div class="col-lg-4 col-md-6 mb-4">
  <div class="card h-100 text-center p-3">
    <img src="order/product_images/<?php echo $img;?>" class="img-fluid mb-3" style="height:260px;object-fit:cover;">
    <h5><?php echo $name;?></h5>
    <p>
      <del>₹<?php echo number_format($mrp,2);?></del><br>
      <span class="price">₹<?php echo number_format($price,2);?></span>
    </p>
    <a href="order/index.php?pg_sku=<?php echo $sku;?>" class="buy-btn">
      Add to Cart
    </a>
  </div>
</div>

<?php }} else { ?>
<p class="text-center">No products available</p>
<?php } ?>

    </div>
  </div>
</section>

<!-- ABOUT -->
<section id="about" class="about">
  <div class="container text-center">
    <h2 class="section-title">About Chavonn Spices</h2>
    <p>
      Chavonn Spices is a heritage Indian spice brand dedicated to
      preserving authentic flavors using traditional grinding methods.
      <br><br>
      ✔ Direct farmer sourcing  
      ✔ Slow-ground for aroma  
      ✔ 100% natural ingredients  
      ✔ Trusted by Indian kitchens
    </p>
  </div>
</section>

<!-- CONTACT -->
<section id="contact" class="contact">
  <div class="container">
    <div class="row">
      <div class="col-lg-6">
        <h2>Get in Touch</h2>
        <p>
          📞 +91 XXXXXXXX<br>
          ✉ support@chavonnspices.com<br>
          📍 Pune, Maharashtra
        </p>
      </div>
      <div class="col-lg-6">
        <form action="send_contact.php" method="POST">
          <input class="form-control mb-3" name="name" placeholder="Your Name" required>
          <input class="form-control mb-3" name="email" placeholder="Email" required>
          <textarea class="form-control mb-3" name="message" placeholder="Message" required></textarea>
          <button class="btn btn-light w-100">Send Message</button>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="text-center">
  <p>© 2025 <b>Chavonn Spices</b> | Pure Taste • Honest Ingredients</p>
</footer>

<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
