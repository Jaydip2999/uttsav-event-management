<?php

require "admin_check.php";
require "../includes/db.php";

/* ===== APPROVE ===== */
if(isset($_GET['approve'])){
    $bid = (int)$_GET['approve'];

    $b = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT * FROM bookings WHERE id=$bid
    "));

    if($b && $b['status']=='pending'){

    mysqli_begin_transaction($conn);

    // 1️⃣ Confirm booking
    mysqli_query($conn,"
        UPDATE bookings 
        SET status='confirmed' 
        WHERE id=$bid
    ");

    // 2️⃣ Increase event slot
    mysqli_query($conn,"
        UPDATE events 
        SET booked_slots = booked_slots + 1
        WHERE id=".$b['event_id']."
    ");

    // 3️⃣ Calculate commission
    $total_amount = $b['total_price'];
    $admin_commission = $total_amount * 0.30;

    // 4️⃣ Insert into admin_wallet
    $stmt = $conn->prepare("
        INSERT INTO admin_wallet 
        (amount, status, requested_at) 
        VALUES (?, 'credited', NOW())
    ");
    $stmt->bind_param("d", $admin_commission);
    $stmt->execute();

    mysqli_commit($conn);
}


    header("Location: manage_bookings.php");
    exit;
}

/* ===== REJECT ===== */
if(isset($_GET['reject'])){
    $bid = (int)$_GET['reject'];

    mysqli_query($conn,"
        UPDATE bookings 
        SET status='rejected'
        WHERE id=$bid
    ");

    header("Location: manage_bookings.php");
    exit;
}

/* ===== FETCH BOOKINGS ===== */
$q = mysqli_query($conn,"
    SELECT b.*, u.name, e.title 
    FROM bookings b
    JOIN users u ON b.user_id=u.id
    JOIN events e ON b.event_id=e.id
    ORDER BY b.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Bookings</title>

<link rel="stylesheet" href="admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="layout">
<?php require "admin_sidebar.php"; ?>

<div class="main">
<div class="container">

<div class="page-header">
<h1>
<i class="fa-solid fa-ticket"></i> Manage Bookings
</h1>
<p>Approve or reject event bookings</p>
</div>

<div class="data-grid">

<?php if(mysqli_num_rows($q) > 0): ?>
<?php while($row = mysqli_fetch_assoc($q)): ?>

<div class="data-card">

<div class="data-card-header">
<strong><?= htmlspecialchars($row['title']) ?></strong>

<?php
if($row['status']=='pending'){
    echo '<span class="badge primary">Pending</span>';
}
elseif($row['status']=='confirmed'){
    echo '<span class="badge success">Confirmed</span>';
}
else{
    echo '<span class="badge danger">Rejected</span>';
}
?>
</div>

<div class="data-meta">
<i class="fa-solid fa-user"></i>
<?= htmlspecialchars($row['name']) ?>
</div>

<?php if(!empty($row['transaction_id'])): ?>
<div class="data-meta">
<i class="fa-solid fa-money-bill"></i>
Txn: <?= htmlspecialchars($row['transaction_id']) ?>
</div>
<?php endif; ?>

<?php if($row['status']=='pending'): ?>
<div style="margin-top:15px;">
<a href="?approve=<?= $row['id'] ?>" class="btn btn-success">
<i class="fa-solid fa-check"></i> Approve
</a>

<a href="?reject=<?= $row['id'] ?>" class="btn btn-danger">
<i class="fa-solid fa-xmark"></i> Reject
</a>
</div>
<?php endif; ?>

</div>

<?php endwhile; ?>

<?php else: ?>

<div class="data-card">
<div class="data-meta">
No bookings found.
</div>
</div>

<?php endif; ?>

</div>
</div>
</div>
</div>

</body>
</html>
