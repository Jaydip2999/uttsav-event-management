<?php
include 'admin_check.php';
require "../includes/db.php";

if(!isset($_GET['id'], $_GET['type'])){
    header("Location:event_pending_list.php");
    exit;
}

$id   = (int) $_GET['id'];
$type = $_GET['type'];

if($type === "approve"){
    $status = "approved";
}
elseif($type === "reject"){
    $status = "rejected";
}
else{
    header("Location: event_pending_list.php");
    exit;
}

mysqli_query($conn, "UPDATE events SET status='$status' WHERE id=$id");

header("Location: event_pending_list.php");
exit;
?>
