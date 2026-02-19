<?php
require "admin_check.php";
require "../includes/db.php";

$q = mysqli_query($conn,"SELECT * FROM admin_logs ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Logs</title>

<link rel="stylesheet" href="admin.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link rel="icon" type="image/png" href="/php/event-management-system/assets/images/logo.png">
</head>
<body>

<div class="layout">

  <!-- SIDEBAR -->
  <?php include "admin_sidebar.php"; ?>

  <!-- MAIN -->
  <div class="main">
    <div class="container">

      <!-- PAGE HEADER -->
      <div class="page-header">
        <h1>
          <i class="fa-solid fa-clipboard-list"></i> Audit Logs
        </h1>
        <p>All admin activities history</p>
      </div>

      <!-- LOG GRID -->
      <div class="data-grid">

        <?php while($l=mysqli_fetch_assoc($q)): ?>
        <div class="log-card">

          <div class="log-action">
            <i class="fa-solid fa-bolt"></i>
            <?= htmlspecialchars($l['action']) ?>
          </div>

          <div class="log-time">
            <i class="fa-solid fa-clock"></i>
            <?= htmlspecialchars($l['created_at']) ?>
          </div>

        </div>
        <?php endwhile; ?>

      </div>

    </div>
  </div>

</div>

</body>
</html>
