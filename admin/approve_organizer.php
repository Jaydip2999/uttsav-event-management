<?php
require_once("admin_check.php");
require "../includes/db.php";
if(!isset($_GET['id']) || !isset($_GET['uid'])){
    header("Location: organizer_requests.php");
    exit;
}

$organizer_id = intval($_GET['id']);
$user_id      = intval($_GET['uid']);

// Organizer approve
mysqli_query($conn,
"UPDATE organizers SET status='approved' WHERE id=$organizer_id");

// User role change to organizer
mysqli_query($conn,
"UPDATE users SET role='organizer' WHERE id=$user_id");

header("Location: organizer_requests.php");
exit;
?>