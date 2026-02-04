<?php
require_once("admin_check.php");

if(!isset($_GET['id'])){
    header("Location: organizer_requests.php");
    exit;
}

$organizer_id = intval($_GET['id']);

mysqli_query($conn,
"UPDATE organizers SET status='rejected' WHERE id=$organizer_id");

header("Location: organizer_requests.php");
exit;
?>