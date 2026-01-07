<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../include/config.php");
date_default_timezone_set("Asia/Kolkata");

$id = $_POST['id'] ?? 0;
$status = $_POST['status'] ?? '';
$agent_no = $_POST['agent_no'] ?? '';

if (!empty($id) && !empty($status) && !empty($agent_no)) {
    $now = date("Y-m-d H:i:s");

    $sql = "UPDATE tbl_call_requests 
            SET status = ?, 
                agent_no = ?, 
                call_attended_datetime = ?
            WHERE id = ?";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("sisi", $status, $agent_no, $now, $id);

    if ($stmt->execute()) {
        header("Location: admin-call-management.php?msg=updated");
        exit;
    } else {
        echo "❌ Error updating record: " . $con->error;
    }
} else {
    echo "⚠️ Invalid data received.";
}
?>
