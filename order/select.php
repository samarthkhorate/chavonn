<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Select Edition | आवृत्ती निवडा</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <style>
    body {
      background-color: #f8f9fa;
    }
    .edition-card {
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s, box-shadow 0.3s;
      padding: 20px;
      text-align: center;
    }
    .edition-card:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }
    .btn-custom {
      background: linear-gradient(45deg, #ff7e5f, #feb47b);
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: bold;
      transition: 0.3s;
      animation: pulse 1.5s infinite;
    }
    .btn-custom:hover {
      background: linear-gradient(45deg, #ff6a3d, #fe9a57);
    }
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }
    .book-container {
      display: flex;
      justify-content: center;
      gap: 10px;
    }
    .book-container img {
      width: 150px;
      height: 150px;
      border-radius: 8px;
    }
    .book2-container {
      display: flex;
      justify-content: center;
      gap: 10px;
    }
    .book2-container img {
      width: 150px;
      height: 180px;
      border-radius: 8px;
    }
    .card-body {
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container text-center py-5">
    <h1 class="mb-4">Select Edition | आवृत्ती निवडा</h1>
    <div class="row justify-content-center">
      <!-- Marathi Part 1 -->
      <div class="col-md-6 col-12 mb-4">
        <div class="card edition-card">
          <h4 class="mb-3">Marathi Edition - Part 1 | मराठी आवृत्ती - भाग १</h4>
          <div class="book-container">
            <div><img src="https://vaibhavdhus.com/assets/images/book.png" alt="Marathi Edition Part 1" /></div>
          </div>
          <div class="card-body">
            <a href="order-form.php" class="btn btn-custom">Select Marathi Part 1</a>
          </div>
        </div>
      </div>

      <!-- Marathi Part 2 -->
      <div class="col-md-6 col-12 mb-4">
        <div class="card edition-card">
          <h4 class="mb-3">Marathi Edition - Part 2 | मराठी आवृत्ती - भाग २</h4>
          <div class="book2-container">
            <div><img src="marathi_part_2_front.jpg" alt="Marathi Part 2 Front" /></div>
            <div><img src="marathi_part_2_back.jpg" alt="Marathi Part 2 Back" /></div>
          </div>
          <div class="card-body">
            <a href="order-part-two.php" class="btn btn-custom">Select Marathi Part 2</a>
          </div>
        </div>
      </div>

      <!-- Hindi Part 1 -->
      <div class="col-md-6 col-12 mb-4">
        <div class="card edition-card">
          <h4 class="mb-3">Hindi Edition - Part 1 | हिंदी आवृत्ती - भाग १</h4>
          <div class="book-container">
            <div><img src="hindi-front.png" alt="Hindi Front" /></div>
            <div><img src="hindi-back.png" alt="Hindi Back" /></div>
          </div>
          <div class="card-body">
            <a href="order-form-hindi.php" class="btn btn-custom">Select Hindi Part 1</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
