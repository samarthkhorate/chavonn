<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../config.php");

$alert = '';

if (!$con) {
    die("<script>alert('Database connection failed: " . addslashes(mysqli_connect_error()) . "');</script>");
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $alert = "⚠️ Please enter both username and password!";
    } else {
        $query = "SELECT * FROM admins WHERE username='$username' AND password='$password'";
        $result = mysqli_query($con, $query);

        if (!$result) {
            $alert = "❌ Query Failed: " . mysqli_error($con);
        } else {
            if (mysqli_num_rows($result) == 1) {
                $_SESSION['admin_username'] = $username;
                $_SESSION['last_activity'] = time();

                echo "<script>alert('✅ Login Successful! Redirecting...'); window.location='dashboard.php';</script>";
                exit;
            } else {
                $alert = "❌ Invalid Username or Password!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login | BePerfect Group</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .alert {
      background: #ffebee;
      border: 1px solid #e53935;
      color: #c62828;
      padding: 10px;
      margin-top: 10px;
      border-radius: 6px;
      font-weight: bold;
      text-align: center;
    }
  </style>
</head>
<body class="login-body">
  <div class="login-container">
    <h2>BePerfect Group - Admin Login</h2>
    <form method="POST" autocomplete="off">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" name="login">Login</button>
    </form>

    <?php if (!empty($alert)) echo "<div class='alert'>$alert</div>"; ?>

    <footer>
      <p>Developed by <a href="https://www.neotechking.com" target="_blank">NeotechKing Global Solutions Pvt. Ltd.</a></p>
    </footer>
  </div>
</body>
</html>
