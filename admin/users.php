<?php
require "admin_check.php";
require "../includes/db.php";

$q = mysqli_query($conn,"SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Users</title>

<link rel="stylesheet" href="admin.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
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
          <i class="fa-solid fa-users"></i> Users
        </h1>
        <p>All registered users</p>
      </div>

      <!-- USERS GRID -->
      <div class="data-grid">

        <?php while($u=mysqli_fetch_assoc($q)): ?>
        <div class="data-card">

          <div class="data-card-header">
            <strong><?= htmlspecialchars($u['name']) ?></strong>
            <span class="badge success">
              <i class="fa-solid fa-user-check"></i> Active
            </span>
          </div>

          <div class="data-meta">
            <i class="fa-solid fa-envelope"></i>
            <?= htmlspecialchars($u['email']) ?>
          </div>

          <div class="data-actions">
            <a href="delete_user.php?id=<?= $u['id'] ?>"
               onclick="return confirm('Delete this user?')"
               class="btn btn-danger">
              <i class="fa-solid fa-trash"></i> Delete
            </a>
          </div>

        </div>
        <?php endwhile; ?>

      </div>

    </div>
  </div>

</div>

</body>
</html>
