<?php
require_once("admin_check.php");

$id=(int)$_GET['id'];
$type=$_GET['type'];

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
  mysqli_query($conn,"DELETE FROM events WHERE id=$id");
}

mysqli_query($conn,"INSERT INTO admin_logs(admin_id,action)
VALUES($_SESSION[user_id],'Event action: $type')");

header("Location: event_pending_list.php");
exit;
