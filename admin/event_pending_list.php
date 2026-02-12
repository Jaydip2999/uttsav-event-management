<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Events</title>

<link rel="stylesheet" href="admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<?php
require "admin_check.php";
require "../includes/db.php";
?>

<div class="layout">

  <?php require "admin_sidebar.php"; ?>

  <div class="main">
    <div class="container">

      <div class="page-header">
        <h1>
          <i class="fa-solid fa-calendar-days"></i> All Events
        </h1>
        <p>View and manage all organizer events</p>
      </div>

<?php
$status = $_GET['status'] ?? 'all';

$allowed_status = ['all','pending','approved','rejected'];

if(!in_array($status,$allowed_status)){
    $status = 'all';
}

$where = "";
if($status != 'all'){
    $where = "WHERE e.status='$status'";
}

$q = mysqli_query($conn,"
SELECT e.*, o.company_name
FROM events e
JOIN organizers o ON o.id=e.organizer_id
$where
ORDER BY e.id DESC
");
?>

<div class="actions" style="margin-bottom:20px;">
  <a href="?status=all" class="btn btn-primary">All</a>
  <a href="?status=pending" class="btn btn-primary">Pending</a>
  <a href="?status=approved" class="btn btn-success">Approved</a>
  <a href="?status=rejected" class="btn btn-danger">Rejected</a>
</div>

<div class="data-grid">

<?php if(mysqli_num_rows($q) > 0): ?>

<?php while($e=mysqli_fetch_assoc($q)): ?>

<div class="data-card">

  <div class="data-card-header">
    <strong><?= htmlspecialchars($e['title']) ?></strong>

    <?php if($e['status'] == 'pending'): ?>
      <span class="badge primary">Pending</span>
    <?php elseif($e['status'] == 'approved'): ?>
      <span class="badge success">
        <?= $e['is_closed'] ? 'Closed' : 'Approved' ?>
      </span>
    <?php else: ?>
      <span class="badge danger">Rejected</span>
    <?php endif; ?>
  </div>

  <div class="data-meta">
    <i class="fa-solid fa-building"></i>
    <?= htmlspecialchars($e['company_name']) ?>
  </div>

  <div class="data-meta">
    <i class="fa-solid fa-calendar"></i>
    <?= htmlspecialchars($e['event_date']) ?>
  </div>

  <?php if(!empty($e['location'])): ?>
  <div class="data-meta">
    <i class="fa-solid fa-location-dot"></i>
    <?= htmlspecialchars($e['location']) ?>
  </div>
  <?php endif; ?>


  <!-- ================= ACTIONS ================= -->
  <div class="data-actions">

  <?php if($e['status'] == 'pending'): ?>

    <a href="event_action.php?id=<?= $e['id'] ?>&type=approve"
       class="btn btn-success"
       onclick="return confirm('Approve this event?')">
       <i class="fa-solid fa-check"></i> Approve
    </a>

    <a href="event_action.php?id=<?= $e['id'] ?>&type=reject"
       class="btn btn-danger"
       onclick="return confirm('Reject this event?')">
       <i class="fa-solid fa-xmark"></i> Reject
    </a>

  <?php endif; ?>


  <?php if($e['status'] == 'approved' && $e['is_closed']==0): ?>

    <a href="event_action.php?id=<?= $e['id'] ?>&type=stop"
       class="btn btn-warning"
       onclick="return confirm('Close this event?')">
       <i class="fa-solid fa-ban"></i> Close
    </a>

  <?php endif; ?>


  <a href="event_action.php?id=<?= $e['id'] ?>&type=delete"
     class="btn btn-danger"
     onclick="return confirm('Delete this event permanently?')">
     <i class="fa-solid fa-trash"></i> Delete
  </a>

  </div>

</div>

<?php endwhile; ?>

<?php else: ?>

<div class="data-card">
  <div class="data-meta">
    No events found.
  </div>
</div>

<?php endif; ?>

</div>
</div>
</div>
</div>

</body>
</html>
