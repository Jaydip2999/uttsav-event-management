<?php
session_start();

// NOT LOGGED IN
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

// NOT ADMIN
if($_SESSION['role'] !== 'admin'){
    header("Location: ../index.php");
    exit;
}
include "../includes/db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin page</title>
   <link rel="stylesheet" href="admin.css">
</head>
<body>
    <?php

$users = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM users"))['c'];
$orgs = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM users WHERE role='organizer'"))['c'];
$pending = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM organizers WHERE status='pending'"))['c'];
?>
<?php require"admin_sidebar.php";?>
<div class="main">
<h2>Welcome, <?=$_SESSION['user_name']?></h2>

<div class="card">Total Users: <?=$users?></div>
<div class="card">Total Organizers: <?=$orgs?></div>
<div class="card">Pending Requests: <?=$pending?></div>

</div>
</body>
</html>