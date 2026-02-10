<?php
require_once("admin_check.php");

$id=(int)$_GET['id'];
$type=$_GET['type'];

$status = ($type=='suspend') ? 'suspended' : 'cancelled';

mysqli_query($conn,"UPDATE organizers SET license_status='$status' WHERE id=$id");

mysqli_query($conn,"INSERT INTO admin_logs(admin_id,action)
VALUES($_SESSION[user_id],'Organizer license $status')");

header("Location: organizers_list.php");
exit;
