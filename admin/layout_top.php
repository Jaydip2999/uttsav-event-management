<?php
require "admin_check.php";
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel</title>

<link rel="stylesheet" href="admin.css">
<link rel="stylesheet"
 href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="layout">

<?php include "admin_sidebar.php"; ?>

<div class="main">

<div class="topbar">
    <button id="toggleBtn"><i class="fa fa-bars"></i></button>
    <h2>Admin Panel</h2>
</div>
