<?php
session_start();
include '../config.php';
if(isset($_POST['pti']) && isset($_POST['mobno'])){
    $traking_no=$_POST['pti'];
    $payment_status_updates="processing";
    mysqli_query($con,"update tbl_orders set payment_status='$payment_status_updates' where order_id ='$traking_no'");
    $_SESSION['OID']=$traking_no;
}


if(isset($_POST['pyid']) && isset($_SESSION['OID'])){
    $payment_id=$_POST['pyid']; 
    $traking_no=$_SESSION['OID'];
        $payment_status_updates1="PAID";

    mysqli_query($con,"update tbl_orders set payment_status='$payment_status_updates1',razorpay_id='$payment_id' where order_id ='$traking_no'");
}
?>