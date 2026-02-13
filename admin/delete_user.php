<?php
require "admin_check.php";
require "../includes/db.php";

if(!isset($_GET['id'])){
    header("Location: users.php");
    exit;
}

$user_id = (int)$_GET['id'];

if($user_id == $_SESSION['user_id']){
    die("You cannot delete your own account.");
}

mysqli_begin_transaction($conn);

try{

    // Get organizer id (if user is organizer)
    $res = mysqli_query($conn,"SELECT id FROM organizers WHERE user_id=$user_id");
    $org = mysqli_fetch_assoc($res);

    if($org){
        $org_id = $org['id'];

        // Delete events
        mysqli_query($conn,"DELETE FROM events WHERE organizer_id=$org_id");

        // Delete organizer
        mysqli_query($conn,"DELETE FROM organizers WHERE id=$org_id");
    }

    // Delete bookings
    mysqli_query($conn,"DELETE FROM bookings WHERE user_id=$user_id");

    // Delete user
    mysqli_query($conn,"DELETE FROM users WHERE id=$user_id");

    // Log
    $admin_id = (int)$_SESSION['user_id'];
    mysqli_query($conn,"
        INSERT INTO admin_logs(admin_id,action)
        VALUES($admin_id,'Deleted user ID $user_id')
    ");

    mysqli_commit($conn);

}catch(Exception $e){
    mysqli_rollback($conn);
    die("Error: ".$e->getMessage());
}

header("Location: users.php");
exit;
?>
