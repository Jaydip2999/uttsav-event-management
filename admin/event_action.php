<?php
require_once("admin_check.php");
require "../includes/db.php";

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header("Location: event_pending_list.php");
    exit;
}

if(!isset($_POST['id']) || !isset($_POST['type'])){
    header("Location: event_pending_list.php");
    exit;
}

$id   = (int)$_POST['id'];
$type = $_POST['type'];

$allowed = ['approve','reject','stop','reopen','delete'];

if(!in_array($type,$allowed)){
    header("Location: event_pending_list.php");
    exit;
}

/* ================= GET EVENT + USER ================= */

$eventQ = mysqli_query($conn,"
    SELECT e.title, o.user_id
    FROM events e
    LEFT JOIN organizers o ON e.organizer_id = o.id
    WHERE e.id = $id
");

if(!$eventQ || mysqli_num_rows($eventQ) == 0){
    header("Location: event_pending_list.php");
    exit;
}

$event = mysqli_fetch_assoc($eventQ);
$event_title = mysqli_real_escape_string($conn, $event['title']);
$user_id     = (int)$event['user_id'];

$noti_title   = "";
$noti_message = "";

/* ================= ACTION LOGIC ================= */

if($type == "approve"){

    mysqli_query($conn,"UPDATE events SET status='approved', is_closed=0 WHERE id=$id");

    $noti_title   = "Event Approved";
    $noti_message = "Your event '$event_title' has been approved by admin.";
}

elseif($type == "reject"){

    mysqli_query($conn,"UPDATE events SET status='rejected', is_closed=0 WHERE id=$id");

    $noti_title   = "Event Rejected";
    $noti_message = "Your event '$event_title' was rejected by admin.";
}

elseif($type == "stop"){

    mysqli_query($conn,"UPDATE events SET is_closed=1 WHERE id=$id");

    $noti_title   = "Event Closed";
    $noti_message = "Your event '$event_title' has been closed by admin.";
}

elseif($type == "reopen"){

    mysqli_query($conn,"UPDATE events SET is_closed=0 WHERE id=$id");

    $noti_title   = "Event Reopened";
    $noti_message = "Your event '$event_title' has been reopened by admin.";
}

elseif($type == "delete"){

    mysqli_query($conn,"DELETE FROM events WHERE id=$id");

    $noti_title   = "Event Deleted";
    $noti_message = "Your event '$event_title' has been deleted by admin.";
}

/* ================= INSERT NOTIFICATION ================= */

if($user_id > 0){
$noti_title   = mysqli_real_escape_string($conn, $noti_title);
$noti_message = mysqli_real_escape_string($conn, $noti_message);

mysqli_query($conn,"
    INSERT INTO notifications (user_id, title, message, type)
    VALUES ($user_id, '$noti_title', '$noti_message', 'event')
");

}

/* ================= ADMIN LOG ================= */

if(isset($_SESSION['user_id'])){

    $admin_id = (int)$_SESSION['user_id'];
    $action   = "Event $type (ID: $id)";

    mysqli_query($conn,"
        INSERT INTO admin_logs (admin_id, action)
        VALUES ($admin_id, '$action')
    ");
}

header("Location: event_pending_list.php");
exit;
?>
