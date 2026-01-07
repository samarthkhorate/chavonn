<?php
// log_action.php
include("../include/config.php");
header('Content-Type: application/json');

if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['admin_username'])) {
    http_response_code(403);
    echo json_encode(['status'=>'error','msg'=>'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $username = mysqli_real_escape_string($con, $_SESSION['admin_username']);
    $action = mysqli_real_escape_string($con, $_POST['action']);
    $ip = $_SERVER['REMOTE_ADDR'];

    $q = "INSERT INTO admin_logs (admin_username, ip_address, action) VALUES ('$username','$ip','$action')";
    if (mysqli_query($con, $q)) {
        echo json_encode(['status'=>'ok']);
    } else {
        echo json_encode(['status'=>'error','msg'=>mysqli_error($con)]);
    }
    exit;
}
echo json_encode(['status'=>'error','msg'=>'Invalid request']);
