<?php
require "../includes/db.php";
require_once("admin_check.php");

/* ================= APPROVE ================= */
if(isset($_GET['approve'])){
    $id = intval($_GET['approve']);

    $conn->query("
        UPDATE withdraw_requests
        SET status='approved'
        WHERE id=$id
    ");

    header("Location: withdraw.php?success=approved");
    exit;
}

/* ================= REJECT ================= */
if(isset($_GET['reject'])){
    $id = intval($_GET['reject']);

    $conn->query("
        UPDATE withdraw_requests
        SET status='rejected'
        WHERE id=$id
    ");

    header("Location: withdraw.php?success=rejected");
    exit;
}

/* ================= FETCH REQUESTS ================= */
$result = $conn->query("
    SELECT w.*, o.full_name
    FROM withdraw_requests w
    JOIN organizers o ON o.id = w.organizer_id
    ORDER BY w.requested_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Withdraw Requests</title>
<link rel="stylesheet" href="admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="icon" type="image/png" href="/php/event-management-system/assets/images/logo.png">
</head>
<body>

<div class="layout">

<?php require "admin_sidebar.php"; ?>

<div class="main">

<h2 class="dash-title">
<i class="fa-solid fa-money-bill-transfer"></i> Withdraw Requests
</h2>

<?php if(isset($_GET['success'])): ?>
<div class="success-msg">
Request <?= $_GET['success']; ?> successfully!
</div>
<?php endif; ?>

<div class="table-card">

<table class="admin-table">
<thead>
<tr>
<th>Organizer</th>
<th>Amount</th>
<th>UPI ID</th>
<th>Date</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row['full_name']); ?></td>
<td>₹<?= number_format($row['amount'],2); ?></td>
<td><?= htmlspecialchars($row['upi_id']); ?></td>
<td><?= date("d M Y", strtotime($row['requested_at'])); ?></td>

<td>
<span class="status-badge <?= $row['status']; ?>">
<?= ucfirst($row['status']); ?>
</span>
</td>

<td>
<?php if($row['status']=='pending'): ?>
<a href="?approve=<?= $row['id']; ?>" class="btn approve">
Approve
</a>
<a href="?reject=<?= $row['id']; ?>" class="btn reject">
Reject
</a>
<?php else: ?>
—
<?php endif; ?>
</td>

</tr>
<?php endwhile; ?>

</tbody>
</table>

</div>

</div>
</div>

</body>
</html>
