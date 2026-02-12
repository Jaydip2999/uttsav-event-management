<?php
require "admin_check.php";
require "../includes/db.php";

if(!isset($_GET['id'])){
    header("Location: users.php");
    exit;
}

$user_id = (int)$_GET['id'];

// Optional: prevent admin from deleting themselves
if($user_id == $_SESSION['user_id']){
    die("You cannot delete your own account.");
}

// Optional: delete related data (events, bookings) if needed
mysqli_query($conn, "DELETE FROM events WHERE organizer_id=(SELECT id FROM organizers WHERE user_id=$user_id)");
mysqli_query($conn, "DELETE FROM bookings WHERE user_id=$user_id");

// Delete user
mysqli_query($conn, "DELETE FROM users WHERE id=$user_id");

// Log action
$admin_id = (int)$_SESSION['user_id'];
mysqli_query($conn, "INSERT INTO admin_logs(admin_id,action) VALUES($admin_id,'Deleted user ID $user_id')");

header("Location: users.php");
exit;
?>
