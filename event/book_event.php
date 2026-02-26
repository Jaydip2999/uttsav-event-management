<?php
session_start();
require "../includes/db.php";

/* ===== AUTH ===== */
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

$user_id  = $_SESSION['user_id'];
$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;
if($event_id <= 0) die("Invalid Event");

/* ===== FETCH EVENT ===== */
$eventQ = mysqli_query($conn,"SELECT * FROM events WHERE id = $event_id");
if(mysqli_num_rows($eventQ) == 0) die("Event not found");
$event = mysqli_fetch_assoc($eventQ);

/* ===== CHECK STATUS ===== */
if(strtolower($event['status']) != 'approved') die("Event Closed");
/* ===== REAL BOOKED SLOTS ===== */
$slotQ = mysqli_prepare($conn,"
    SELECT COALESCE(SUM(quantity),0) AS total_booked
    FROM bookings
    WHERE event_id = ?
    AND status IN ('confirmed','pending')
");
mysqli_stmt_bind_param($slotQ,"i",$event_id);
mysqli_stmt_execute($slotQ);
$res = mysqli_stmt_get_result($slotQ);
$slotData = mysqli_fetch_assoc($res);

$booked = (int)$slotData['total_booked'];
$available = max(0, $event['total_slots'] - $booked);

if($available <= 0) die("Slots Full");

/* ===== REGISTER ===== */
if(isset($_POST['confirm'])){

    $quantity = (int)$_POST['quantity'];
$latestQ = mysqli_prepare($conn,"
    SELECT total_slots FROM events WHERE id = ?
");
mysqli_stmt_bind_param($latestQ,"i",$event_id);
mysqli_stmt_execute($latestQ);
$resLatest = mysqli_stmt_get_result($latestQ);
$latest = mysqli_fetch_assoc($resLatest);

/* Real-time booked again */
$bookQ = mysqli_prepare($conn,"
    SELECT COALESCE(SUM(quantity),0) AS total_booked
    FROM bookings
    WHERE event_id = ?
    AND status IN ('confirmed','pending')
");
mysqli_stmt_bind_param($bookQ,"i",$event_id);
mysqli_stmt_execute($bookQ);
$resBook = mysqli_stmt_get_result($bookQ);
$bookData = mysqli_fetch_assoc($resBook);

$available = max(0, $latest['total_slots'] - $bookData['total_booked']);
    if($quantity <= 0 || $quantity > $available){
        $error = "Only $available seats left";
    } else {

        $total_price = $event['price'] * $quantity;

        /* ===== FREE EVENT ===== */
        if($event['price'] <= 0){

          mysqli_begin_transaction($conn);
try{

    mysqli_query($conn,"
        INSERT INTO bookings (user_id,event_id,quantity,total_price,status)
        VALUES ($user_id,$event_id,$quantity,0,'confirmed')
    ");

    mysqli_commit($conn);
    $success = true;

}catch(Exception $e){
    mysqli_rollback($conn);
    $error = "Something went wrong";
}
        }

        /* ===== PAID EVENT ===== */
        else{

            $txn = mysqli_real_escape_string($conn,$_POST['transaction_id']);

            mysqli_begin_transaction($conn);
            try{

                mysqli_query($conn,"
                    INSERT INTO bookings (user_id,event_id,quantity,total_price,transaction_id,status)
                    VALUES ($user_id,$event_id,$quantity,$total_price,'$txn','pending')
                ");

                // Reserve seats immediately
                mysqli_query($conn,"
                    UPDATE events 
                    SET booked_slots = booked_slots + $quantity
                    WHERE id = $event_id
                ");

                mysqli_commit($conn);
                $payment_pending = true;

            }catch(Exception $e){
                mysqli_rollback($conn);
                $error = "Something went wrong";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Event Booking</title>
<link rel="stylesheet" href="event.css">
</head>
<body class="ev-book-page">

<div class="ev-book-card">

<?php if(isset($success)){ ?>

<h2>Seat Confirmed </h2>
<p class="ev-success">You are registered successfully.</p>

<a href="event_full_details.php?id=<?= $event_id ?>" class="ev-back-btn">
    ← Back to Event
</a>

<a href="../index.php" class="ev-dashboard-btn">
    Go to Dashboard
</a>

<?php } elseif(isset($payment_pending)){ ?>

<h2>Payment Submitted </h2>
<p class="ev-pending">Waiting for Admin Approval.</p>

<a href="event_full_details.php?id=<?= $event_id ?>" class="ev-back-btn">
    ← Back to Event
</a>

<?php } else { ?>

<h2><?= htmlspecialchars($event['title']) ?></h2>
<p>Seats Left: <?= $available ?></p>

<?php if($event['price'] <= 0): ?>

<!-- FREE EVENT -->
<form method="POST">

    <input type="number" 
           name="quantity" 
           min="1" 
           max="<?= $available ?>" 
           placeholder="Enter number of tickets"
           required>

    <button name="confirm">Register Now</button>
</form>

<?php else: ?>

<!-- PAID EVENT -->

<p>Price per Ticket: ₹<?= $event['price'] ?></p>

<form method="POST">

    <input type="number" 
           id="qty"
           name="quantity" 
           min="1" 
           max="<?= $available ?>" 
           placeholder="Enter number of tickets"
           required>

    <p>Total Price: ₹<span id="total">0</span></p>

    <img src="../assets/images/gpay_qr.png" width="200">

    <input type="text" 
           name="transaction_id" 
           placeholder="Enter Transaction ID" 
           required>

    <button name="confirm">Submit Payment</button>

    <a href="event_full_details.php?id=<?= $event_id ?>" class="ev-back-btn">
        ← Back to Event
    </a>

</form>

<script>
document.getElementById('qty').addEventListener('input', function(){
    let price = <?= $event['price'] ?>;
    let qty = this.value;
    document.getElementById('total').innerText = price * qty;
});
</script>

<?php endif; ?>

<?php } ?>

</div>

</body>
</html>
