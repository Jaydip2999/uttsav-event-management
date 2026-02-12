<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>
<body>

<?php
require_once("admin_check.php");

$users = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM users"))['c'];
$orgs  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM organizers"))['c'];
$pending_org = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM organizers WHERE status='pending'"))['c'];
$events = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM events"))['c'];
$closed = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM events WHERE is_closed=1"))['c'];

?><div class="layout">
  
<?php require "admin_sidebar.php"; ?>
  <div class="main">

    <h2 class="dash-title">
      <i class="fa-solid fa-gauge"></i> Admin Dashboard
    </h2>

    <div class="stats-grid">

      <div class="stat-card users">
        <div class="icon"><i class="fa-solid fa-users"></i></div>
        <div>
          <h4>Total Users</h4>
          <p><?=$users?></p>
          <span>Registered users</span>
        </div>
      </div>

      <div class="stat-card organizers">
        <div class="icon"><i class="fa-solid fa-user-tie"></i></div>
        <div>
          <h4>Organizers</h4>
          <p><?=$orgs?></p>
          <span>Active organizers</span>
        </div>
      </div>

      <div class="stat-card pending">
        <div class="icon"><i class="fa-solid fa-clock"></i></div>
        <div>
          <h4>Pending Requests</h4>
          <p><?=$pending_org?></p>
          <span>Need approval</span>
        </div>
      </div>

      <div class="stat-card events">
        <div class="icon"><i class="fa-solid fa-calendar-check"></i></div>
        <div>
          <h4>Total Events</h4>
          <p><?=$events?></p>
          <span>All events</span>
        </div>
      </div>

      <div class="stat-card closed">
        <div class="icon"><i class="fa-solid fa-lock"></i></div>
        <div>
          <h4>Closed Events</h4>
          <p><?=$closed?></p>
          <span>Completed</span>
        </div>
      </div>

    </div>

  </div>
</div>

<script src="../assets/script.js"></script>

</body>
</html>
