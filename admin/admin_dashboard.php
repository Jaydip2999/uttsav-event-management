<?php
require "../includes/db.php";
require_once("admin_check.php");

/* ================= BASIC STATS ================= */

$users = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM users"))['c'];

$orgs  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM organizers"))['c'];

$pending_org = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) c FROM organizers WHERE status='pending'
"))['c'];

$events = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) c FROM events
"))['c'];

$closed = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) c FROM events WHERE is_closed=1
"))['c'];

/* ================= REVENUE CALCULATION ================= */

$revenueQ = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT SUM(total_price) as total
    FROM bookings
    WHERE status='confirmed'
"));

$totalRevenue = $revenueQ['total'] ?? 0;

$platformEarning = $totalRevenue * 0.30;
$organizerShare  = $totalRevenue * 0.70;

/* ================= EVENT-WISE REVENUE ================= */

$eventRevenue = mysqli_query($conn,"
    SELECT e.title,
           SUM(b.total_price) as total
    FROM bookings b
    JOIN events e ON e.id = b.event_id
    WHERE b.status='confirmed'
    GROUP BY e.id
    ORDER BY total DESC
");

$admin_fee = 0.3; // 30% commission
$total_earning = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT SUM(amount)*$admin_fee as earning FROM admin_wallet")
)['earning'];

// Agar null ho to 0 set karo
$total_earning = $total_earning ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>

<link rel="stylesheet" href="admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="icon" type="image/png" href="/php/event-management-system/assets/images/logo.png">
</head>
<body>

<div class="layout">

<?php require "admin_sidebar.php"; ?>

<div class="main">

<!-- ================= DASHBOARD TITLE ================= -->
<h2 class="dash-title">
<i class="fa-solid fa-house"></i> Admin Dashboard
</h2>
<!-- ================= BASIC STATS ================= -->
<div class="stats-grid">

  <div class="stat-card users">
    <div class="icon"><i class="fa-solid fa-users"></i></div>
    <div>
      <h4>Total Users</h4>
      <p><?= $users ?></p>
      <span>Registered users</span>
    </div>
  </div>

  <div class="stat-card organizers">
    <div class="icon"><i class="fa-solid fa-user-tie"></i></div>
    <div>
      <h4>Organizers</h4>
      <p><?= $orgs ?></p>
      <span>Active organizers</span>
    </div>
  </div>

  <div class="stat-card pending">
    <div class="icon"><i class="fa-solid fa-clock"></i></div>
    <div>
      <h4>Pending Requests</h4>
      <p><?= $pending_org ?></p>
      <span>Need approval</span>
    </div>
  </div>

  <div class="stat-card events">
    <div class="icon"><i class="fa-solid fa-calendar-days"></i></div>
    <div>
      <h4>Total Events</h4>
      <p><?= $events ?></p>
      <span>All events</span>
    </div>
  </div>

  <div class="stat-card closed">
    <div class="icon"><i class="fa-solid fa-lock"></i></div>
    <div>
      <h4>Closed Events</h4>
      <p><?= $closed ?></p>
      <span>Completed</span>
    </div>
  </div>

  <div class="stat-card earnings">
    <div class="icon"><i class="fa-solid fa-wallet"></i></div>
    <div>
      <h4>Admin Earnings</h4>
      <p>₹<?= number_format($platformEarning,2) ?></p>
      <span>Total commission</span>
    </div>
  </div>

</div>
<!-- ================= REVENUE OVERVIEW ================= -->
<h2 class="dash-title" style="margin-top:40px;">
<i class="fa-solid fa-chart-line"></i> Revenue Overview
</h2>
<div class="stats-grid">

  <div class="stat-card revenue">
    <div class="icon"><i class="fa-solid fa-money-bill-wave"></i></div>
    <div>
      <h4>Total Revenue</h4>
      <p>₹<?= number_format($totalRevenue,2) ?></p>
      <span>Confirmed bookings</span>
    </div>
  </div>

  <div class="stat-card commission">
    <div class="icon"><i class="fa-solid fa-coins"></i></div>
    <div>
      <h4>Platform Earnings (30%)</h4>
      <p>₹<?= number_format($platformEarning,2) ?></p>
      <span>Your commission</span>
    </div>
  </div>

  <div class="stat-card share">
    <div class="icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
    <div>
      <h4>Organizer Share (70%)</h4>
      <p>₹<?= number_format($organizerShare,2) ?></p>
      <span>Payout to organizers</span>
    </div>
  </div>

</div>

<!-- ================= EVENT WISE REVENUE ================= -->
<h2 class="dash-title" style="margin-top:40px;">
<i class="fa-solid fa-chart-column"></i> Event-wise Earnings
</h2>

<div class="table-card">

<table class="admin-table">

<thead>
<tr>
<th>Event</th>
<th>Total Revenue</th>
<th>Platform (30%)</th>
<th>Organizer (70%)</th>
</tr>
</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($eventRevenue)): 
    $platform = $row['total'] * 0.30;
    $organizer = $row['total'] * 0.70;
?>

<tr>
<td><?= htmlspecialchars($row['title']); ?></td>
<td>₹<?= number_format($row['total'],2); ?></td>
<td style="color:#22d3ee;">₹<?= number_format($platform,2); ?></td>
<td style="color:#00e676;">₹<?= number_format($organizer,2); ?></td>
</tr>

<?php endwhile; ?>

</tbody>
</table>

</div>

</div>
</div>

</body>
</html>
