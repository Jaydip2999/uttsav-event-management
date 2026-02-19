<?php
require "admin_check.php";
require "../includes/db.php";

/* ===== Pending Requests ===== */
$pending = mysqli_query($conn,"
  SELECT o.*, u.name, u.email 
  FROM organizers o
  JOIN users u ON u.id = o.user_id
  WHERE o.status='pending'
");

/* ===== All Organizers ===== */
$all = mysqli_query($conn,"
  SELECT o.*, u.name, u.email 
  FROM organizers o
  JOIN users u ON u.id = o.user_id
  ORDER BY o.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Organizers</title>
<link rel="stylesheet" href="admin.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link rel="icon" type="image/png" href="/php/event-management-system/assets/images/logo.png">
</head>

<body>

<div class="layout">

<?php include "admin_sidebar.php"; ?>

<div class="main">

<!-- ================= Pending Requests ================= -->
<div class="page-header">
  <h1><i class="fa-solid fa-user-clock"></i> Pending Organizer Requests</h1>
</div>

<div class="data-grid">

<?php if(mysqli_num_rows($pending) > 0): ?>
  <?php while($r = mysqli_fetch_assoc($pending)): ?>

  <div class="data-card">

    <a href="organizer_view.php?id=<?= $r['id'] ?>" class="card-link">

      <div class="data-card-header">
        <strong><?= htmlspecialchars($r['name']) ?></strong>
        <span class="badge primary">Pending</span>
      </div>

      <div class="data-meta">
        <i class="fa-solid fa-envelope"></i>
        <?= htmlspecialchars($r['email']) ?>
      </div>

      <div class="data-meta">
        <i class="fa-solid fa-building"></i>
        <?= htmlspecialchars($r['company_name']) ?>
      </div>

    </a>

    <div class="data-actions">
      <a href="organizer_action.php?id=<?= $r['id'] ?>&action=approve"
         class="btn btn-success">
         Approve
      </a>

      <a href="organizer_action.php?id=<?= $r['id'] ?>&action=reject"
         class="btn btn-danger">
         Reject
      </a>
    </div>

  </div>

  <?php endwhile; ?>
<?php else: ?>
  <p>No pending requests.</p>
<?php endif; ?>

</div>


<!-- ================= All Organizers ================= -->
<div class="page-header" style="margin-top:40px;">
  <h1><i class="fa-solid fa-users"></i> All Organizers</h1>
</div>

<div class="data-grid">

<?php while($row = mysqli_fetch_assoc($all)): ?>
<div class="data-card">

  <div class="data-card-header">
    <strong><?= htmlspecialchars($row['name']) ?></strong>

    <?php if($row['status']=='approved'): ?>
      <span class="badge success">Approved</span>

    <?php elseif($row['status']=='pending'): ?>
      <span class="badge primary">Pending</span>

    <?php elseif($row['status']=='license_cancelled'): ?>
      <span class="badge warning">License Cancelled</span>

    <?php else: ?>
      <span class="badge danger">Rejected</span>
    <?php endif; ?>
  </div>

  <div class="data-meta">
    <i class="fa-solid fa-envelope"></i>
    <?= htmlspecialchars($row['email']) ?>
  </div>

  <div class="data-meta">
    <i class="fa-solid fa-building"></i>
    <?= htmlspecialchars($row['company_name']) ?>
  </div>

  <div class="data-actions">

    <?php if($row['status']=='approved'): ?>

      <a href="organizer_action.php?id=<?= $row['id'] ?>&action=cancel_license"
         onclick="return confirm('Cancel organizer license?')"
         class="btn btn-danger">
        <i class="fa-solid fa-ban"></i> Cancel License
      </a>

      <a href="organizer_action.php?id=<?= $row['id'] ?>&action=delete"
         onclick="return confirm('Delete organizer and all data?')"
         class="btn btn-danger">
        <i class="fa-solid fa-trash"></i> Delete
      </a>

    <?php elseif($row['status']=='pending'): ?>

      <a href="organizer_action.php?id=<?= $row['id'] ?>&action=approve"
         class="btn btn-success">
        Approve
      </a>

      <a href="organizer_action.php?id=<?= $row['id'] ?>&action=reject"
         class="btn btn-danger">
        Reject
      </a>

    <?php elseif($row['status']=='license_cancelled'): ?>

      <a href="organizer_action.php?id=<?= $row['id'] ?>&action=reactivate"
         onclick="return confirm('Reactivate license?')"
         class="btn btn-success">
        <i class="fa-solid fa-rotate-left"></i> Reactivate
      </a>

    <?php elseif($row['status']=='rejected'): ?>

      <a href="organizer_action.php?id=<?= $row['id'] ?>&action=approve"
         onclick="return confirm('Re-approve this organizer?')"
         class="btn btn-success">
        <i class="fa-solid fa-check"></i> Re-Approve
      </a>

    <?php endif; ?>

  </div>

</div>
<?php endwhile; ?>

</div>

</div>
</div>

</body>
</html>
