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

$allowed = ['approve','reject','stop','delete'];

if(!in_array($type,$allowed)){
    header("Location: event_pending_list.php");
    exit;
}

/* Check event exists */
$check = $conn->prepare("SELECT id FROM events WHERE id=?");
$check->bind_param("i",$id);
$check->execute();
$result = $check->get_result();

if($result->num_rows == 0){
    header("Location: all_events.php");
    exit;
}

/* Perform action */
if($type == "approve"){
    $stmt = $conn->prepare("UPDATE events SET status='approved' WHERE id=?");
}
elseif($type == "reject"){
    $stmt = $conn->prepare("UPDATE events SET status='rejected' WHERE id=?");
}
elseif($type == "stop"){
    $stmt = $conn->prepare("UPDATE events SET is_closed=1 WHERE id=?");
}
elseif($type == "delete"){
    $stmt = $conn->prepare("DELETE FROM events WHERE id=?");
}

$stmt->bind_param("i",$id);
$stmt->execute();

/* Admin Log */
$admin_id = (int)$_SESSION['user_id'];
$action   = "Event $type (ID: $id)";

$log = $conn->prepare("INSERT INTO admin_logs(admin_id,action) VALUES(?,?)");
$log->bind_param("is",$admin_id,$action);
$log->execute();

header("Location: event_pending_list.php");
exit;
?>
