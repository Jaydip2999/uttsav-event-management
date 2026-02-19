<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Messages</title>

<link rel="stylesheet" href="admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="icon" type="image/png" href="/php/event-management-system/assets/images/logo.png">
<style>
.card-link{
  text-decoration:none;
  color:inherit;
  display:block;
}
</style>
</head>
<body>

<?php
require "admin_check.php";
require "../includes/db.php";

/* ===== FETCH MESSAGES ===== */
$q = $conn->query("
    SELECT * FROM contact_messages 
    ORDER BY id DESC
");
?>

<div class="layout">
<?php require "admin_sidebar.php"; ?>

<div class="main">
<div class="container">

<div class="page-header">
<h1>
<i class="fa-solid fa-envelope"></i> Contact Messages
</h1>
<p>View messages sent by users</p>
</div>

<div class="data-grid">

<?php if($q->num_rows > 0): ?>
<?php while($m = $q->fetch_assoc()): ?>

<div class="data-card">

<div class="data-card-header">
<strong><?= htmlspecialchars($m['name']) ?></strong>
<span class="badge primary">New</span>
</div>

<div class="data-meta">
<i class="fa-solid fa-envelope"></i>
<?= htmlspecialchars($m['email']) ?>
</div>

<?php if(!empty($m['subject'])): ?>
<div class="data-meta">
<i class="fa-solid fa-tag"></i>
<?= htmlspecialchars($m['subject']) ?>
</div>
<?php endif; ?>

<div class="data-meta">
<i class="fa-solid fa-message"></i>
<?= nl2br(htmlspecialchars($m['message'])) ?>
</div>

<div class="data-meta">
<i class="fa-solid fa-calendar"></i>
<?= $m['created_at'] ?>
</div>

</div>

<?php endwhile; ?>

<?php else: ?>

<div class="data-card">
<div class="data-meta">
No messages found.
</div>
</div>

<?php endif; ?>

</div>
</div>
</div>
</div>

</body>
</html>
