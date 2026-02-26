<?php
require "admin_check.php";
require "../includes/db.php";

/* ================= FETCH WALLET DATA ================= */

// Get all wallet entries (earnings + withdrawals)
$wallets = $conn->query("
    SELECT * FROM admin_wallet 
    ORDER BY requested_at DESC
");

// Calculate current balance
$total_earning = 0;
while($row = $wallets->fetch_assoc()){
    $total_earning += $row['amount']; // positive & negative both
}

// Reset pointer
$wallets->data_seek(0);


/* ================= HANDLE WITHDRAW ================= */

$msg = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $amount = (float)($_POST['amount'] ?? 0);
    $upi = trim($_POST['upi'] ?? '');

    if($amount <= 0){
        $msg = "Enter valid amount!";
    }
    elseif($amount > $total_earning){
        $msg = "Insufficient balance!";
    }
    elseif(empty($upi)){
        $msg = "Enter UPI ID!";
    }
    else{

        mysqli_begin_transaction($conn);

        // Insert negative entry for withdrawal
        $stmt = $conn->prepare("
            INSERT INTO admin_wallet
            (amount, status, upi_id, requested_at, withdrawn_at)
            VALUES (?, 'withdrawn', ?, NOW(), NOW())
        ");

        $negative_amount = -$amount;
        $stmt->bind_param("ds", $negative_amount, $upi);
        $stmt->execute();

        mysqli_commit($conn);

        header("Location: admin_withdraw.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Wallet Overview</title>

<link rel="stylesheet" href="admin.css">
<link rel="stylesheet" href="wallet.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="icon" type="image/png" href="/php/event-management-system/assets/images/logo.png">
</head>
<body>

<div class="layout">
<?php require "admin_sidebar.php"; ?>

<div class="main">
<div class="container">

<div class="page-header">
<h1><i class="fa-solid fa-wallet"></i> Wallet Overview</h1>
<p>Manage Admin Earnings & Withdrawals</p>
</div>

<!-- ================= BALANCE CARD ================= -->
<div class="wallet-overview">
    <div class="wallet-box green">
        <h4>Available Balance</h4>
        <h2>₹<?= number_format($total_earning,2) ?></h2>
    </div>
</div>
<!-- ================= WITHDRAW SECTION ================= -->

<div class="withdraw-form">

    <form method="post">

        <input type="number"
               step="0.01"
               name="amount"
               placeholder="Enter withdrawal amount"
               max="<?= $total_earning ?>"
               required>

        <input type="text"
               name="upi"
               placeholder="Enter UPI ID / GPay Number"
               required>

        <button type="submit" class="btn btn-primary">
            Withdraw
        </button>

    </form>

    <?php if($msg): ?>
        <p class="success-msg"><?= $msg ?></p>
    <?php endif; ?>

</div>
<!-- ================= WALLET TABLE ================= -->
<div class="table-card">
<table class="admin-table">
<thead>
<tr>
<th>Amount</th>
<th>Type</th>
<th>UPI</th>
<th>Date</th>
</tr>
</thead>
<tbody>

<?php if(mysqli_num_rows($wallets) > 0): ?>
<?php while($row = $wallets->fetch_assoc()): ?>
<tr>
<td style="<?= $row['amount'] < 0 ? 'color:red;' : 'color:green;' ?>">
    ₹<?= number_format($row['amount'],2) ?>
</td>
<td>
    <?= $row['amount'] < 0 ? 'Withdraw' : 'Commission' ?>
</td>
<td>
    <?= $row['upi_id'] ?? '-' ?>
</td>
<td>
    <?= $row['requested_at'] ?>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
<td colspan="4">No transactions found</td>
</tr>
<?php endif; ?>

</tbody>
</table>
</div>

</div>
</div>
</div>

</body>
</html>
