<?php
require "admin_check.php";
require "../includes/db.php";

if(!isset($_GET['id'])){
    die("Invalid request");
}

$id = intval($_GET['id']);

$stmt = mysqli_prepare($conn,"
  SELECT o.*, u.name, u.email
  FROM organizers o
  JOIN users u ON u.id = o.user_id
  WHERE o.id = ?
");

mysqli_stmt_bind_param($stmt,"i",$id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)==0){
    die("Organizer not found");
}

$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Organizer Details</title>
<link rel="stylesheet" href="admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="icon" type="image/png" href="/php/event-management-system/assets/images/logo.png">
</head>

<body>

<div class="layout">

  <?php require "admin_sidebar.php"; ?>

  <div class="main">
   <a href="javascript:history.back()" class="back-btn">
<i class="fa-solid fa-arrow-left-long"></i> Back
</a>
    <!-- Page Title -->
    <h2 class="dash-title">
      <i class="fa-solid fa-user-tie"></i> Organizer Details
    </h2>

    <div class="details-wrapper">

      <div class="details-card">

        <!-- Header -->
        <div class="details-header">

          <img src="../organizer/uploads/profile_pics/<?= htmlspecialchars($data['profile_pic']) ?>"
               class="details-img">

          <div>
            <h2 class="details-title">
              <?= htmlspecialchars($data['name']) ?>
            </h2>

            <?php if($data['status']=='approved'): ?>
              <span class="status-badge status-approved">Approved</span>
            <?php elseif($data['status']=='pending'): ?>
              <span class="status-badge status-pending">Pending</span>
            <?php else: ?>
              <span class="status-badge status-rejected">Rejected</span>
            <?php endif; ?>
          </div>

        </div>

        <!-- Details -->
        <div class="details-row"><strong>Email:</strong> <?= htmlspecialchars($data['email']) ?></div>
        <div class="details-row"><strong>Company:</strong> <?= htmlspecialchars($data['company_name']) ?></div>
        <div class="details-row"><strong>GST No:</strong> <?= htmlspecialchars($data['gst_number']) ?></div>
        <div class="details-row"><strong>Address:</strong> <?= htmlspecialchars($data['address']) ?></div>
        <div class="details-row"><strong>Phone:</strong> <?= htmlspecialchars($data['mobile']) ?></div>
        <div class="details-row"><strong>Joined:</strong> <?= date("d M Y", strtotime($data['created_at'])) ?></div>

        <!-- Buttons -->
        <div class="actions">

          <?php if($data['status']=='pending'): ?>
            <a href="organizer_action.php?id=<?= $data['id'] ?>&action=approve"
               class="btn btn-success">Approve</a>

            <a href="organizer_action.php?id=<?= $data['id'] ?>&action=reject"
               class="btn btn-danger">Reject</a>
          <?php endif; ?>

          <?php if($data['status']=='approved'): ?>
            <a href="organizer_action.php?id=<?= $data['id'] ?>&action=cancel_license"
               class="btn btn-warning">Cancel License</a>

            <a href="organizer_action.php?id=<?= $data['id'] ?>&action=delete"
               class="btn btn-danger">Delete</a>
          <?php endif; ?>

        </div>

      </div>

    </div>

  </div>
</div>

<script src="../assets/script.js"></script>
</body>
</html>
