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
$eventQ = mysqli_query($conn,"
    SELECT id,title,event_date,event_time,location,
           total_slots,booked_slots,status
    FROM events WHERE id = $event_id
");

if(mysqli_num_rows($eventQ) == 0) die("Event not found");
$event = mysqli_fetch_assoc($eventQ);

/* ===== CHECK ===== */
if(strtolower($event['status']) != 'approved') die("Event Closed");

$available = $event['total_slots'] - $event['booked_slots'];
if($available <= 0) die("Slots Full");

/* ===== ALREADY REGISTERED ===== */
$alreadyQ = mysqli_query($conn,"
    SELECT id FROM bookings
    WHERE user_id=$user_id 
      AND event_id=$event_id 
      AND status='confirmed'
");
$already = mysqli_num_rows($alreadyQ) > 0;

/* ===== REGISTER ===== */
if(isset($_POST['confirm']) && !$already){
    mysqli_begin_transaction($conn);
    try{
       mysqli_query($conn,"
        INSERT INTO bookings (user_id,event_id,status,booking_date)
        VALUES ($user_id,$event_id,'confirmed',NOW())
        ");


        mysqli_query($conn,"
            UPDATE events SET booked_slots = booked_slots + 1
            WHERE id = $event_id
        ");

        mysqli_commit($conn);
        $success = true;
    }catch(Exception $e){
        mysqli_rollback($conn);
        $error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Event Booking</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="event.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="ev-book-page">

<div class="ev-book-card">

<?php if(isset($success)){ ?>

  <span class="ev-badge ev-ok">
    <i class="fa fa-check-circle"></i> Registered
  </span>

  <h2 class="ev-title">Seat Confirmed 🎉</h2>

  <div class="ev-row">
    <i class="fa fa-calendar"></i>
    <?= date("d M Y",strtotime($event['event_date'])) ?>
  </div>

  <div class="ev-row">
    <i class="fa fa-clock"></i>
    <?= $event['event_time'] ?>
  </div>

  <div class="ev-row">
    <i class="fa fa-location-dot"></i>
    <?= htmlspecialchars($event['location']) ?>
  </div>

  <p class="ev-success">You are officially registered!</p>

  <a href="event_full_details.php?id=<?= $event_id ?>" class="ev-back">
    ← Back to Event
  </a>

<?php } elseif($already){ ?>

  <span class="ev-badge ev-warn">
    <i class="fa fa-info-circle"></i> Already Booked
  </span>

  <h2 class="ev-title">No Worries</h2>

  <p class="ev-success">
    You already registered for this event.
  </p>

  <a href="event_full_details.php?id=<?= $event_id ?>" class="ev-back">
    ← Back to Event
  </a>

<?php } else { ?>

  <span class="ev-badge ev-ok">
    <i class="fa fa-chair"></i> Slots Available
  </span>

  <h2 class="ev-title">Confirm Your Seat</h2>

  <div class="ev-row">
    <i class="fa fa-ticket"></i>
    <?= htmlspecialchars($event['title']) ?>
  </div>

  <div class="ev-row">
    <i class="fa fa-users"></i>
    <?= $available ?> seats left
  </div>

  <form method="POST">
    <button class="ev-btn" name="confirm">
      <i class="fa fa-bolt"></i> Register Now
    </button>
  </form>

<?php } ?>

</div>

</body>

</html>
