<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>

<?php
require_once("admin_check.php");

$users = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM users"))['c'];
$orgs  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM organizers"))['c'];
$pending_org = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM organizers WHERE status='pending'"))['c'];
$events = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM events"))['c'];
$closed = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM events WHERE is_closed=1"))['c'];

require "admin_sidebar.php";
?>

<div class="layout">
  <div class="main">
    <h2>Welcome Admin</h2>

    <div class="card">Total Users: <?=$users?></div>
    <div class="card">Total Organizers: <?=$orgs?></div>
    <div class="card">Pending Organizer Requests: <?=$pending_org?></div>
    <div class="card">Total Events: <?=$events?></div>
    <div class="card">Closed Events: <?=$closed?></div>
  </div>
</div>

</body>
</html>
