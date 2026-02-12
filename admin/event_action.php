<?php
require_once("admin_check.php");
require "../includes/db.php";

if(!isset($_GET['id']) || !isset($_GET['type'])){
    header("Location: ../event/event_cards.php");
    exit;
}

$id   = (int)$_GET['id'];
$type = $_GET['type'];

$allowed = ['approve','reject','stop','delete'];

if(!in_array($type,$allowed)){
    header("Location: all_events.php");
    exit;
}

if($type=="approve"){
    mysqli_query($conn,"UPDATE events SET status='approved' WHERE id=$id");
}
elseif($type=="reject"){
    mysqli_query($conn,"UPDATE events SET status='rejected' WHERE id=$id");
}
elseif($type=="stop"){
    mysqli_query($conn,"UPDATE events SET is_closed=1 WHERE id=$id");
}
elseif($type=="delete"){

    // Optional: delete related bookings first
    mysqli_query($conn,"DELETE FROM bookings WHERE event_id=$id");

    mysqli_query($conn,"DELETE FROM events WHERE id=$id");
}

/* ===== Admin Log ===== */
$admin_id = (int)$_SESSION['user_id'];
$action   = "Event $type (ID: $id)";

mysqli_query($conn,"
INSERT INTO admin_logs(admin_id,action)
VALUES($admin_id,'$action')
");

header("Location: all_events.php");
exit;
?>
