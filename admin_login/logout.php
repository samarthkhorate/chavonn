<?php
include("../include/config.php");
if (!isset($_SESSION)) session_start();

if (isset($_SESSION['admin_username'])) {
    $username = mysqli_real_escape_string($con, $_SESSION['admin_username']);
    $ip = $_SERVER['REMOTE_ADDR'];
    mysqli_query($con, "INSERT INTO admin_logs (admin_username, ip_address, action) VALUES ('$username','$ip','Logout')");
}

session_unset();
session_destroy();
header("Location: index.php");
exit;
