<?php
include("../include/config.php");

if (!isset($_GET['id'])) die("Invalid request.");
$id = intval($_GET['id']);
mysqli_query($con, "UPDATE tbl_orders SET is_verified='1' WHERE id='$id'");
header("Location: unverified_orders.php");
exit;
?>
