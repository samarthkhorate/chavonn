<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Chav Onn Spices – Pure • Authentic • Traditional</title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">

<!-- Bootstrap -->
<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root {
  --primary-green: #2e7d32;
  --dark-green: #1b5e20;
  --light-green: #e8f5e9;
  --accent-green: #4caf50;
  --lime-green: #8bc34a;
  --forest-green: #0f2f1b;
  --bg-light: #f9fff9;
  --text-dark: #333333;
  --text-light: #ffffff;
  --card-bg: #ffffff;
  --shadow: rgba(46, 125, 50, 0.15);
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html, body {
  width: 100%;
  overflow-x: hidden;
  font-family: 'Poppins', sans-serif;
  background-color: var(--bg-light);
  color: var(--text-dark);
}

h1, h2, h3, h4, h5 {
  font-family: 'Playfair Display', serif;
  color: var(--forest-green);
}

.container {
  max-width: 100%;
  padding-left: 15px;
  padding-right: 15px;
}

.row {
  margin-left: -15px;
  margin-right: -15px;
}

.col-lg-4, .col-md-6, .col-lg-6, .col-lg-5, .col-lg-7, .col-md-6 {
  padding-left: 15px;
  padding-right: 15px;
}

/* POPUP MODAL STYLES */
.popup-modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.85);
  z-index: 9999;
  animation: fadeIn 0.5s ease;
}

.popup-content {
  position: relative;
  background: linear-gradient(135deg, #ffffff, var(--light-green));
  width: 90%;
  max-width: 500px;
  margin: 50px auto;
  border-radius: 25px;
  overflow: hidden;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
  animation: slideUp 0.6s ease;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { transform: translateY(50px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.popup-header {
  background: linear-gradient(135deg, var(--forest-green), var(--dark-green));
  padding: 25px;
  text-align: center;
  position: relative;
}

.popup-header h3 {
  color: var(--text-light);
  font-size: 28px;
  margin-bottom: 10px;
  font-weight: 800;
}

.popup-badge {
  background: linear-gradient(135deg, var(--accent-green), var(--lime-green));
  color: var(--forest-green);
  padding: 8px 20px;
  border-radius: 20px;
  font-weight: 700;
  font-size: 16px;
  display: inline-block;
  margin: 10px 0;
}

.popup-body {
  padding: 30px;
}

.popup-image {
  width: 100%;
  height: 250px;
  object-fit: cover;
  border-radius: 15px;
  margin-bottom: 20px;
  border: 3px solid var(--light-green);
}

.popup-price {
  text-align: center;
  margin: 20px 0;
}

.popup-original-price {
  color: #999;
  text-decoration: line-through;
  font-size: 20px;
  display: block;
}

.popup-discounted-price {
  color: var(--primary-green);
  font-size: 36px;
  font-weight: 800;
  display: block;
  margin: 10px 0;
}

.popup-discount {
  background: linear-gradient(135deg, var(--lime-green), var(--accent-green));
  color: white;
  padding: 8px 20px;
  border-radius: 20px;
  font-weight: 700;
  font-size: 16px;
  display: inline-block;
}

.popup-features {
  list-style: none;
  padding: 0;
  margin: 25px 0;
}

.popup-features li {
  padding: 10px 0;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 10px;
}

.popup-features li i {
  color: var(--primary-green);
  font-size: 18px;
}

.popup-buttons {
  display: flex;
  gap: 15px;
  margin-top: 25px;
}

.popup-buy-btn {
  flex: 1;
  background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
  color: white;
  border: none;
  padding: 16px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 18px;
  text-decoration: none;
  text-align: center;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
}

.popup-buy-btn:hover {
  background: linear-gradient(135deg, var(--dark-green), var(--forest-green));
  transform: translateY(-3px);
  box-shadow: 0 8px 20px var(--shadow);
  color: white;
}

.popup-close-btn {
  flex: 1;
  background: rgba(0, 0, 0, 0.1);
  color: var(--text-dark);
  border: 2px solid var(--light-green);
  padding: 16px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 18px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.popup-close-btn:hover {
  background: var(--light-green);
  transform: translateY(-3px);
}

.popup-close {
  position: absolute;
  top: 15px;
  right: 15px;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  font-size: 20px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  z-index: 10;
}

.popup-close:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: rotate(90deg);
}

@media (max-width: 768px) {
  .popup-content {
    width: 95%;
    margin: 20px auto;
  }
  
  .popup-header h3 {
    font-size: 24px;
  }
  
  .popup-buttons {
    flex-direction: column;
  }
  
  .popup-image {
    height: 200px;
  }
}

/* Header & Navigation */
.navbar {
  background: linear-gradient(135deg, var(--forest-green), var(--dark-green));
  padding: 18px 0;
  box-shadow: 0 4px 20px rgba(15, 47, 27, 0.2);
  position: sticky;
  top: 0;
  z-index: 1000;
  width: 100%;
}

.navbar .container {
  width: 100%;
}

.navbar-brand {
  font-size: 32px;
  font-weight: 800;
  background: linear-gradient(90deg, #ffffff, var(--light-green));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  letter-spacing: 1px;
  display: flex;
  align-items: center;
  gap: 10px;
  white-space: nowrap;
}

.navbar-brand::before {
  content: "🌿";
  font-size: 28px;
}

.nav-link {
  color: rgba(255, 255, 255, 0.9) !important;
  font-weight: 600;
  font-size: 16px;
  margin: 0 10px;
  padding: 8px 16px !important;
  border-radius: 6px;
  transition: all 0.3s ease;
}

.nav-link:hover {
  color: var(--text-light) !important;
  background-color: rgba(255, 255, 255, 0.15);
  transform: translateY(-2px);
}

.navbar-toggler {
  border: 2px solid var(--light-green);
  color: var(--light-green) !important;
}

.navbar-toggler-icon {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%23e8f5e9' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}

/* Hero Section */
.hero {
  background: linear-gradient(135deg, var(--light-green) 0%, #ffffff 100%);
  padding: 100px 0;
  position: relative;
  overflow: hidden;
  width: 100%;
}

.hero::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 100%;
  background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100" opacity="0.05"><path d="M20,20 Q40,5 60,20 T100,20 L100,80 Q80,95 60,80 T20,80 Z" fill="%231b5e20"/></svg>');
  background-size: 150px;
}

.hero h1 {
  font-size: 52px;
  font-weight: 800;
  line-height: 1.2;
  margin-bottom: 24px;
  color: var(--forest-green);
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
  word-wrap: break-word;
}

.hero h1 span {
  color: var(--primary-green);
  position: relative;
}

.hero h1 span::after {
  content: "";
  position: absolute;
  bottom: -5px;
  left: 0;
  width: 100%;
  height: 3px;
  background: linear-gradient(90deg, var(--accent-green), var(--lime-green));
  border-radius: 2px;
}

.hero p {
  font-size: 18px;
  color: #444;
  line-height: 1.8;
  margin-bottom: 32px;
  max-width: 100%;
  word-wrap: break-word;
}

.hero-btn {
  background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
  color: var(--text-light);
  padding: 16px 40px;
  border-radius: 50px;
  font-weight: 700;
  font-size: 18px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  box-shadow: 0 8px 20px var(--shadow);
  transition: all 0.3s ease;
  border: none;
  position: relative;
  overflow: hidden;
  width: auto;
  white-space: nowrap;
}

.hero-btn::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: 0.5s;
}

.hero-btn:hover::before {
  left: 100%;
}

.hero-btn:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 25px rgba(46, 125, 50, 0.3);
}

/* Products Section */
.products-section {
  padding: 100px 0;
  background: linear-gradient(to bottom, #ffffff, var(--bg-light));
  width: 100%;
}

.section-title {
  font-size: 42px;
  font-weight: 800;
  text-align: center;
  margin-bottom: 60px;
  position: relative;
  padding-bottom: 20px;
  word-wrap: break-word;
}

.section-title::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 100px;
  height: 4px;
  background: linear-gradient(90deg, var(--primary-green), var(--lime-green));
  border-radius: 2px;
}

/* Product Cards */
.product-card {
  background: var(--card-bg);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 10px 30px var(--shadow);
  transition: all 0.4s ease;
  border: 1px solid rgba(46, 125, 50, 0.1);
  height: 100%;
  display: flex;
  flex-direction: column;
  width: 100%;
}

.product-card:hover {
  transform: translateY(-12px);
  box-shadow: 0 20px 40px rgba(46, 125, 50, 0.25);
  border-color: var(--accent-green);
}

.card-img-container {
  height: 260px;
  overflow: hidden;
  position: relative;
  width: 100%;
}

.card-img-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
  display: block;
}

.product-card:hover .card-img-container img {
  transform: scale(1.08);
}

.card-body {
  padding: 25px;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  width: 100%;
}

.card-title {
  font-size: 20px;
  font-weight: 700;
  color: var(--forest-green);
  margin-bottom: 15px;
  min-height: 48px;
  word-wrap: break-word;
}

.price-container {
  margin-bottom: 20px;
  width: 100%;
}

.original-price {
  color: #999;
  text-decoration: line-through;
  font-size: 16px;
  display: inline-block;
}

.discounted-price {
  color: var(--primary-green);
  font-size: 26px;
  font-weight: 800;
  margin-left: 10px;
  display: inline-block;
}

.discount-badge {
  background: linear-gradient(135deg, var(--lime-green), var(--accent-green));
  color: white;
  padding: 4px 12px;
  border-radius: 15px;
  font-size: 14px;
  font-weight: 600;
  display: inline-block;
  margin-left: 10px;
  white-space: nowrap;
}

.card-btn {
  background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
  color: white;
  border: none;
  padding: 14px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 16px;
  width: 100%;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  margin-top: auto;
}

.card-btn:hover {
  background: linear-gradient(135deg, var(--dark-green), var(--forest-green));
  transform: translateY(-3px);
  box-shadow: 0 8px 15px var(--shadow);
}

/* About Section */
.about-section {
  padding: 100px 0;
  background: linear-gradient(135deg, var(--light-green) 0%, #ffffff 100%);
  position: relative;
  width: 100%;
}

.about-content {
  max-width: 100%;
  margin: 0 auto;
  text-align: center;
  padding: 0 15px;
}

.about-features {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 30px;
  margin-top: 50px;
  width: 100%;
}

.feature-item {
  background: white;
  padding: 30px;
  border-radius: 20px;
  box-shadow: 0 10px 30px var(--shadow);
  transition: transform 0.3s ease;
  width: 100%;
}

.feature-item:hover {
  transform: translateY(-10px);
}

.feature-icon {
  font-size: 40px;
  color: var(--primary-green);
  margin-bottom: 20px;
}

.feature-title {
  font-size: 22px;
  color: var(--forest-green);
  margin-bottom: 15px;
  word-wrap: break-word;
}

/* Contact Section */
.contact-section {
  padding: 100px 0;
  background: linear-gradient(135deg, var(--forest-green), var(--dark-green));
  color: var(--text-light);
  width: 100%;
}

.contact-section h2 {
  color: var(--text-light);
}

.contact-info {
  padding-right: 0;
  width: 100%;
}

.contact-item {
  display: flex;
  align-items: flex-start;
  margin-bottom: 25px;
  gap: 20px;
  width: 100%;
}

.contact-icon {
  width: 60px;
  height: 60px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  flex-shrink: 0;
}

.contact-form input,
.contact-form textarea {
  background: rgba(255, 255, 255, 0.1);
  border: 2px solid rgba(255, 255, 255, 0.2);
  color: white;
  padding: 15px;
  border-radius: 12px;
  margin-bottom: 20px;
  transition: all 0.3s ease;
  width: 100%;
  max-width: 100%;
}

.contact-form input:focus,
.contact-form textarea:focus {
  background: rgba(255, 255, 255, 0.15);
  border-color: var(--light-green);
  box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
  outline: none;
}

.contact-form input::placeholder,
.contact-form textarea::placeholder {
  color: rgba(255, 255, 255, 0.7);
}

.submit-btn {
  background: linear-gradient(135deg, var(--accent-green), var(--lime-green));
  color: var(--forest-green);
  border: none;
  padding: 16px 40px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 18px;
  width: 100%;
  transition: all 0.3s ease;
  display: block;
}

.submit-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

/* Footer */
.footer {
  background: var(--forest-green);
  color: var(--text-light);
  padding: 60px 0 30px;
  width: 100%;
}

.footer-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 40px;
  margin-bottom: 40px;
  width: 100%;
}

.footer-logo {
  font-size: 32px;
  font-weight: 800;
  margin-bottom: 20px;
  background: linear-gradient(90deg, var(--light-green), var(--lime-green));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  word-wrap: break-word;
}

.footer-links h4,
.footer-social h4 {
  color: var(--light-green);
  margin-bottom: 25px;
  font-size: 20px;
  word-wrap: break-word;
}

.footer-links ul {
  list-style: none;
  padding: 0;
  width: 100%;
}

.footer-links li {
  margin-bottom: 12px;
  width: 100%;
}

.footer-links a {
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  transition: color 0.3s ease;
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
}

.footer-links a:hover {
  color: var(--light-green);
  padding-left: 5px;
}

.social-icons {
  display: flex;
  gap: 15px;
  margin-top: 20px;
  flex-wrap: wrap;
}

.social-icon {
  width: 45px;
  height: 45px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 18px;
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.social-icon:hover {
  background: var(--primary-green);
  transform: translateY(-5px);
}

.copyright {
  text-align: center;
  padding-top: 30px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  color: rgba(255, 255, 255, 0.7);
  font-size: 15px;
  width: 100%;
  word-wrap: break-word;
}

/* Responsive Design */
@media (max-width: 768px) {
  .hero h1 {
    font-size: 36px;
    line-height: 1.3;
  }
  
  .section-title {
    font-size: 32px;
    padding-left: 15px;
    padding-right: 15px;
  }
  
  .hero-btn,
  .card-btn,
  .submit-btn {
    padding: 14px 30px;
    width: 100%;
    text-align: center;
    display: block;
  }
  
  .contact-info {
    padding-right: 0;
    margin-bottom: 50px;
  }
  
  .hero {
    padding: 70px 0;
  }
  
  .products-section,
  .about-section,
  .contact-section {
    padding: 70px 0;
  }
  
  .navbar-brand {
    font-size: 26px;
  }
  
  .card-title {
    font-size: 18px;
  }
  
  .discounted-price {
    font-size: 22px;
  }
  
  .discount-badge {
    font-size: 12px;
    padding: 3px 8px;
  }
  
  .contact-item {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
  
  .contact-icon {
    width: 50px;
    height: 50px;
    font-size: 20px;
  }
  
  .footer-content {
    gap: 30px;
  }
  
  .col-lg-4, .col-md-6 {
    width: 100%;
    max-width: 100%;
  }
  
  .product-card {
    max-width: 100%;
  }
}

@media (max-width: 576px) {
  .hero h1 {
    font-size: 28px;
  }
  
  .section-title {
    font-size: 26px;
  }
  
  .hero p {
    font-size: 16px;
  }
  
  .navbar-brand {
    font-size: 22px;
  }
  
  .card-img-container {
    height: 220px;
  }
  
  .footer-logo {
    font-size: 26px;
  }
  
  .about-features {
    grid-template-columns: 1fr;
  }
}

/* Fix for overflow issues */
img {
  max-width: 100%;
  height: auto;
  display: block;
}

.form-control {
  max-width: 100%;
}

/* Animation for scroll reveal */
.reveal {
  opacity: 0;
  transform: translateY(30px);
  transition: all 0.8s ease;
}

.reveal.active {
  opacity: 1;
  transform: translateY(0);
}

/* Ensure no horizontal overflow */
section, div, header, footer, nav {
  max-width: 100vw;
  box-sizing: border-box;
}
</style>
</head>
<body>

<!-- POPUP MODAL FOR MINI MASALA BOX -->
<div class="popup-modal" id="miniMasalaPopup">
  <div class="popup-content">
    <button class="popup-close" onclick="closePopup()">×</button>
    
    <div class="popup-header">
      <h3>Limited Time Offer! 🎁</h3>
      <div class="popup-badge">Most Popular Choice</div>
      <p style="color: rgba(255, 255, 255, 0.9); margin-top: 10px;">Chavonn Mini Masala Box</p>
    </div>
    
    <div class="popup-body">
      <img src="order/product_images/mini-masala-box.jpg" alt="Chavonn Mini Masala Box" class="popup-image" onerror="this.src='https://images.unsplash.com/photo-1590771129823-8c72c5b3c0c0?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60'">
      
      <div class="popup-price">
        <span class="popup-original-price">₹399.00</span>
        <span class="popup-discounted-price">₹299.00</span>
        <span class="popup-discount">Save ₹100 (25% OFF)</span>
      </div>
      
      <ul class="popup-features">
        <li><i class="fas fa-check-circle"></i> 6 Essential Indian Spices</li>
        <li><i class="fas fa-check-circle"></i> Perfect for Beginners</li>
        <li><i class="fas fa-check-circle"></i> 100% Natural & Pure</li>
        <li><i class="fas fa-check-circle"></i> Traditional Grinding</li>
        <li><i class="fas fa-check-circle"></i> Free Recipe E-Book</li>
      </ul>
      
      <div class="popup-buttons">
        <a href="order/index.php?pg_sku=22" class="popup-buy-btn">
          <i class="fas fa-bolt"></i> Buy Now
        </a>
        <button class="popup-close-btn" onclick="closePopup()">
          <i class="fas fa-times"></i> Close
        </button>
      </div>
      
      <p style="text-align: center; margin-top: 20px; color: #666; font-size: 14px;">
        <i class="fas fa-shield-alt"></i> Free shipping on orders above ₹499
      </p>
    </div>
  </div>
</div>

<!-- Header -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="#">
      Chav Onn Spices
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="#home">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#products">Products</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#about">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#contact">Contact</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero Section -->
<section id="home" class="hero">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-12 reveal">
        <h1>Pure & Authentic <span>Indian Spices</span></h1>
        <p>
          Hand-picked ingredients • Traditional grinding •  
          No chemicals • No preservatives • Farm to Kitchen
        </p>
        <a href="#products" class="hero-btn">
          <i class="fas fa-shopping-bag"></i> Shop Our Spices
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Products Section -->
<section id="products" class="products-section">
  <div class="container">
    <h2 class="section-title reveal">Our Premium Masalas</h2>
    <div class="row g-4">

      <?php
      include 'config.php';
      $query = "SELECT * FROM tbl_products";
      $result = mysqli_query($con, $query);

      if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          $name = htmlspecialchars($row['product_name']);
          $mrp = $row['product_mrp'];
          $price = $row['product_prepaid_price'];
          $sku = urlencode($row['product_sku_code']);
          $img = $row['product_image_path'];
          
          // Calculate discount percentage
          $discount = 0;
          if ($mrp > 0 && $price < $mrp) {
            $discount = round((($mrp - $price) / $mrp) * 100);
          }
      ?>

      <div class="col-lg-4 col-md-6 reveal">
        <div class="product-card">
          <div class="card-img-container">
            <img src="order/product_images/<?php echo $img; ?>" alt="<?php echo $name; ?>">
            <!-- OVERLAY REMOVED -->
          </div>
          <div class="card-body">
            <h5 class="card-title"><?php echo $name; ?></h5>
            <div class="price-container">
              <?php if ($mrp > $price): ?>
                <span class="original-price">₹<?php echo number_format($mrp, 2); ?></span>
              <?php endif; ?>
              <span class="discounted-price">₹<?php echo number_format($price, 2); ?></span>
              <?php if ($discount > 0): ?>
                <span class="discount-badge">Save ₹<?php echo number_format($mrp - $price, 2); ?></span>
              <?php endif; ?>
            </div>
            <a href="order/index.php?pg_sku=<?php echo $sku; ?>" class="card-btn">
              <i class="fas fa-cart-plus"></i> Buy Now
            </a>
          </div>
        </div>
      </div>

      <?php 
        }
      } else { 
      ?>
      
      <div class="col-12 text-center py-5">
        <div class="alert alert-info" style="background: var(--light-green); color: var(--forest-green); border: none; border-radius: 15px; padding: 30px;">
          <i class="fas fa-info-circle fa-3x mb-4"></i>
          <h3>No Products Available</h3>
          <p class="mb-0">We're currently updating our spice collection. Please check back soon!</p>
        </div>
      </div>
      
      <?php } ?>

    </div>
  </div>
</section>

<!-- About Section -->
<section id="about" class="about-section">
  <div class="container">
    <h2 class="section-title reveal">About Chavonn Spices</h2>
    <div class="about-content reveal">
      <p style="font-size: 18px; line-height: 1.8; margin-bottom: 40px;">
        Chavonn Spices is a heritage Indian spice brand dedicated to preserving authentic flavors 
        using traditional grinding methods passed down through generations. We believe in the purity 
        of nature's gifts and the wisdom of traditional cooking.
      </p>
      
      <div class="about-features">
        <div class="feature-item reveal">
          <div class="feature-icon">
            <i class="fas fa-leaf"></i>
          </div>
          <h4 class="feature-title">100% Natural</h4>
          <p>No chemicals, preservatives, or artificial additives. Just pure, natural spices.</p>
        </div>
        
        <div class="feature-item reveal">
          <div class="feature-icon">
            <i class="fas fa-seedling"></i>
          </div>
          <h4 class="feature-title">Direct Farmer Sourcing</h4>
          <p>We work directly with farmers to ensure the highest quality ingredients.</p>
        </div>
        
        <div class="feature-item reveal">
          <div class="feature-icon">
            <i class="fas fa-mortar-pestle"></i>
          </div>
          <h4 class="feature-title">Traditional Grinding</h4>
          <p>Slow-ground using traditional methods to preserve aroma and flavor.</p>
        </div>
        
        <div class="feature-item reveal">
          <div class="feature-icon">
            <i class="fas fa-award"></i>
          </div>
          <h4 class="feature-title">Trusted Quality</h4>
          <p>Trusted by thousands of Indian kitchens for authentic taste.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Contact Section -->
<section id="contact" class="contact-section">
  <div class="container">
    <div class="row">
      <div class="col-lg-5 reveal">
        <div class="contact-info">
          <h2 class="mb-4">Get in Touch</h2>
          <p class="mb-5" style="color: rgba(255, 255, 255, 0.9);">
            Have questions about our spices? We're here to help you bring authentic flavors to your kitchen.
          </p>
          
          <div class="contact-item">
            <div class="contact-icon">
              <i class="fas fa-phone"></i>
            </div>
            <div>
              <h5>Call Us</h5>
              <p>+91 7249407268 , 7218475630</p>
            </div>
          </div>
          
          <div class="contact-item">
            <div class="contact-icon">
              <i class="fas fa-envelope"></i>
            </div>
            <div>
              <h5>Email Us</h5>
              <p>chavonn.india@gmail.com</p>
            </div>
          </div>
          
          <div class="contact-item">
            <div class="contact-icon">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <div>
              <h5>Visit Us</h5>
              <p>College Road, Kowad - 416508, Kolhapur, Maharashtra, India</p>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-7 reveal">
        <form class="contact-form" action="send_contact.php" method="POST">
          <div class="row">
            <div class="col-md-6">
              <input type="text" class="form-control" name="name" placeholder="Your Name" required>
            </div>
            <div class="col-md-6">
              <input type="email" class="form-control" name="email" placeholder="Your Email" required>
            </div>
          </div>
          <input type="text" class="form-control" name="subject" placeholder="Subject">
          <textarea class="form-control" name="message" rows="5" placeholder="Your Message" required></textarea>
          <button type="submit" class="submit-btn">
            <i class="fas fa-paper-plane"></i> Send Message
          </button>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="footer">
  <div class="container">
    <div class="footer-content">
      <div class="footer-brand">
        <div class="footer-logo">Chavonn Spices</div>
        <p style="color: rgba(255, 255, 255, 0.8);">
          Bringing authentic Indian flavors to your kitchen since 2019. 
          Pure ingredients, traditional methods, unmatched quality.
        </p>
      </div>
      
      <div class="footer-links">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="#home"><i class="fas fa-chevron-right"></i> Home</a></li>
          <li><a href="#products"><i class="fas fa-chevron-right"></i> Products</a></li>
          <li><a href="#about"><i class="fas fa-chevron-right"></i> About Us</a></li>
          <li><a href="#contact"><i class="fas fa-chevron-right"></i> Contact</a></li>
        </ul>
      </div>
      
      <div class="footer-social">
        <h4>Follow Us</h4>
        <p style="color: rgba(255, 255, 255, 0.8); margin-bottom: 20px;">
          Stay connected for updates and recipes
        </p>
        <div class="social-icons">
          <a href="https://www.facebook.com/share/1AgAnR5uw4/" class="social-icon"><i class="fab fa-facebook-f"></i></a>
          <a href="https://www.instagram.com/chavonn.india?igsh=ZXRxZjF3NDI2d3h1" class="social-icon"><i class="fab fa-instagram"></i></a>
          <a href="https://wa.me/qr/NG7ENDKZOP7WG1" class="social-icon"><i class="fab fa-whatsapp"></i></a>
        </div>
      </div>
    </div>
    
    <div class="copyright">
      <p>© 2025 <b>Chavonn Spices</b> | Pure Taste • Honest Ingredients • Traditional Excellence</p>
    </div>
  </div>
</footer>

<!-- Bootstrap JS -->
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
// Scroll reveal animation
function reveal() {
  const reveals = document.querySelectorAll('.reveal');
  
  for (let i = 0; i < reveals.length; i++) {
    const windowHeight = window.innerHeight;
    const elementTop = reveals[i].getBoundingClientRect().top;
    const elementVisible = 150;
    
    if (elementTop < windowHeight - elementVisible) {
      reveals[i].classList.add('active');
    }
  }
}

window.addEventListener('scroll', reveal);
reveal(); // Initial check

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    
    const targetId = this.getAttribute('href');
    if (targetId === '#') return;
    
    const targetElement = document.querySelector(targetId);
    if (targetElement) {
      window.scrollTo({
        top: targetElement.offsetTop - 80,
        behavior: 'smooth'
      });
    }
  });
});

// Navbar background on scroll
window.addEventListener('scroll', function() {
  const navbar = document.querySelector('.navbar');
  if (window.scrollY > 50) {
    navbar.style.boxShadow = '0 4px 20px rgba(15, 47, 27, 0.3)';
  } else {
    navbar.style.boxShadow = '0 4px 20px rgba(15, 47, 27, 0.2)';
  }
});

// Fix for mobile overflow
document.addEventListener('DOMContentLoaded', function() {
  // Prevent horizontal scrolling
  document.body.style.overflowX = 'hidden';
  
  // Ensure all elements are within viewport
  const allElements = document.querySelectorAll('*');
  allElements.forEach(element => {
    element.style.maxWidth = '100%';
  });
});

// Mini Masala Box Popup Functions
function showPopup() {
  // Check if popup was shown today using localStorage
  const today = new Date().toDateString();
  const lastShown = localStorage.getItem('popupLastShown');
  
  // Show popup only once per day
  if (lastShown !== today) {
    setTimeout(() => {
      document.getElementById('miniMasalaPopup').style.display = 'block';
      document.body.style.overflow = 'hidden';
      localStorage.setItem('popupLastShown', today);
    }, 1500); // Show after 1.5 seconds
  }
}

function closePopup() {
  document.getElementById('miniMasalaPopup').style.display = 'none';
  document.body.style.overflow = 'auto';
}

// Close popup when clicking outside
window.onclick = function(event) {
  const popup = document.getElementById('miniMasalaPopup');
  if (event.target === popup) {
    closePopup();
  }
}

// Show popup when page loads
window.addEventListener('load', showPopup);

// Optional: Add a button to manually open the popup (for testing)
function openPopup() {
  document.getElementById('miniMasalaPopup').style.display = 'block';
  document.body.style.overflow = 'hidden';
}
</script>

</body>
</html>