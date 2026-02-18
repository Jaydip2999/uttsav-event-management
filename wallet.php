<?php
session_start();
require "includes/db.php";

/* ================= AUTH ================= */
if(!isset($_SESSION['user_id'])){
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* ================= CHECK ORGANIZER ================= */
$orgQ = $conn->query("SELECT * FROM organizers WHERE user_id=$user_id");
$organizer = $orgQ->fetch_assoc();

if(!$organizer){
    die("Access Denied");
}

$org_id = $organizer['id'];

/* ================= TOTAL EARNINGS ================= */
$earnQ = $conn->query("
    SELECT SUM(b.total_price) as total
    FROM bookings b
    JOIN events e ON e.id=b.event_id
    WHERE e.organizer_id=$org_id
    AND b.status='confirmed'
");
$earnData = $earnQ->fetch_assoc();
$totalRevenue = $earnData['total'] ?? 0;
$organizerEarning = $totalRevenue * 0.70;

/* ================= TOTAL WITHDRAWN ================= */
$withdrawQ = $conn->query("
    SELECT SUM(amount) as totalWithdraw
    FROM withdraw_requests
    WHERE organizer_id=$org_id
    AND status='approved'
");
$withdrawData = $withdrawQ->fetch_assoc();
$totalWithdrawn = $withdrawData['totalWithdraw'] ?? 0;

$availableBalance = $organizerEarning - $totalWithdrawn;

$error = "";

/* ================= WITHDRAW REQUEST ================= */
if(isset($_POST['withdraw_request'])){

    $amount = floatval($_POST['amount']);
    $upi = $conn->real_escape_string($_POST['upi']);

    if($amount <= 0){
        $error = "Enter valid withdraw amount.";
    }
    elseif($amount < 100){
        $error = "Minimum withdraw amount is ₹100.";
    }
    elseif($amount > $availableBalance){
        $error = "Insufficient balance! Max withdraw: ₹" . number_format($availableBalance,2);
    }
    else{
        $conn->query("
            INSERT INTO withdraw_requests
            (organizer_id, amount, upi_id, status, requested_at)
            VALUES ($org_id, $amount, '$upi', 'pending', NOW())
        ");

        header("Location: wallet.php?success=1");
        exit;
    }
}

/* ================= FETCH HISTORY ================= */
$history = $conn->query("
    SELECT * FROM withdraw_requests
    WHERE organizer_id=$org_id
    ORDER BY requested_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Wallet</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="mp-page">

<div class="mp-container">

<a href="user_profile.php" class="back-btn">← Back</a>

<!-- ================= WALLET OVERVIEW ================= -->
<div class="mp-card mp-full">
<h2>Wallet Overview</h2>

<div class="wallet-overview">

  <div class="wallet-box">
    <h4>Total Earnings (70%)</h4>
    <h2>₹<?= number_format($organizerEarning,2); ?></h2>
  </div>

  <div class="wallet-box">
    <h4>Total Withdrawn</h4>
    <h2>₹<?= number_format($totalWithdrawn,2); ?></h2>
  </div>

  <div class="wallet-box green">
    <h4>Available Balance</h4>
    <h2>₹<?= number_format($availableBalance,2); ?></h2>
  </div>

</div>
</div>

<!-- ================= WITHDRAW FORM ================= -->
<div class="mp-card mp-full">
<h3>Request Withdraw</h3>

<?php if(!empty($error)): ?>
    <div class="error-msg"><?= $error; ?></div>
<?php endif; ?>

<?php if(isset($_GET['success'])): ?>
    <div class="success-msg">
        Withdraw request submitted successfully!
    </div>
<?php endif; ?>

<form method="POST" class="withdraw-form">
    <input type="number" step="0.01" name="amount"
           placeholder="Enter Amount (Min ₹100)" required>

    <input type="text" name="upi"
           placeholder="Enter UPI ID / GPay Number" required>

    <button type="submit" name="withdraw_request"
            class="mp-btn">
        Submit Request
    </button>
</form>

</div>

<!-- ================= WITHDRAW HISTORY ================= -->
<div class="mp-card mp-full">
<h3>Withdraw History</h3>

<?php if($history->num_rows == 0): ?>
    <p>No withdraw history yet.</p>
<?php else: ?>
<?php while($row = $history->fetch_assoc()): ?>
<div class="wallet-history-item">
    <span>
        ₹<?= number_format($row['amount'],2); ?>
        • <?= date("d M Y", strtotime($row['requested_at'])); ?>
    </span>

    <span class="mp-badge <?= $row['status']; ?>">
        <?= ucfirst($row['status']); ?>
    </span>
</div>
<?php endwhile; ?>
<?php endif; ?>

</div>

</div>

<?php if(!empty($error)): ?>
<?php endif; ?>

</body>
</html>
