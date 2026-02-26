<?php
session_start();
include "../includes/db.php";
$organizer_id = $_SESSION['user_id'];

$imageName = time()."_".$_FILES['image']['name'];
move_uploaded_file($_FILES['image']['tmp_name'],"../uploads/".$imageName);

$sql = "INSERT INTO events
(organizer_id,title,short_description,description,image,event_date,event_time,location,price,category,
highlight1,highlight2,highlight3,highlight4,status)

VALUES (
'$organizer_id',
'$_POST[title]',
'$_POST[short_description]',
'$_POST[description]',
'uploads/$imageName',
'$_POST[event_date]',
'$_POST[event_time]',
'$_POST[location]',
'$_POST[price]',
'$_POST[category]',
'$_POST[highlight1]',
'$_POST[highlight2]',
'$_POST[highlight3]',
'$_POST[highlight4]',
'pending'
)";

mysqli_query($conn,$sql);

header("Location: admin/admin_dashboard.php?msg=Event submitted for approval");
