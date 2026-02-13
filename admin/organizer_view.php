<?php
require "admin_check.php";
require "../includes/db.php";

if(!isset($_GET['id'])){
    die("Invalid request");
}

$id = intval($_GET['id']);

$q = mysqli_query($conn,"
  SELECT o.*, u.name, u.email
  FROM organizers o
  JOIN users u ON u.id = o.user_id
  WHERE o.id = $id
");

if(mysqli_num_rows($q)==0){
    die("Organizer not found");
}

$data = mysqli_fetch_assoc($q);
?>

<!DOCTYPE html>
<html>
<head>
<title>Organizer Details</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="layout">
<?php include "admin_sidebar.php"; ?>

<div class="main">
<div class="container">

<div class="page-top">
<h2>Organizer Details</h2>
<a href="organizers_list.php" class="back-btn">← Back</a>
</div>

<div class="details-wrapper">

<div class="details-card">

<div class="details-header">
<img src="../organizer/uploads/profile_pics/<?= $data['profile_pic'] ?>"
     class="details-img">

<div>
<h2 class="details-title"><?= htmlspecialchars($data['name']) ?></h2>

<?php if($data['status']=='approved'): ?>
<span class="status-badge status-approved">Approved</span>
<?php elseif($data['status']=='pending'): ?>
<span class="status-badge status-pending">Pending</span>
<?php else: ?>
<span class="status-badge status-rejected">Rejected</span>
<?php endif; ?>

</div>
</div>

<div class="details-row">
<strong>Email:</strong>
<?= htmlspecialchars($data['email']) ?>
</div>

<div class="details-row">
<strong>Company:</strong>
<?= htmlspecialchars($data['company_name']) ?>
</div>

<div class="details-row">
<strong>Phone:</strong>
<?= htmlspecialchars($data['mobile']) ?>
</div>

<div class="details-row">
<strong>Joined:</strong>
<?= date("d M Y", strtotime($data['created_at'])) ?>
</div>

<?php if($data['status']=='pending'): ?>
<div class="actions">
<a href="organizer_action.php?id=<?= $data['id'] ?>&action=approve"
   class="btn btn-success">Approve</a>

<a href="organizer_action.php?id=<?= $data['id'] ?>&action=reject"
   class="btn btn-danger">Reject</a>
</div>
<?php endif; ?>

<?php if($data['status']=='approved'): ?>
<div class="actions">
<a href="organizer_action.php?id=<?= $data['id'] ?>&action=cancel_license"
   class="btn btn-danger">Cancel License</a>

<a href="organizer_action.php?id=<?= $data['id'] ?>&action=delete"
   class="btn btn-danger">Delete</a>
</div>
<?php endif; ?>

</div>

</div>
</div>
</div>
</div>

</body>
</html>
