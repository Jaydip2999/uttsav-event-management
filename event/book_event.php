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

<!-- ICONS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body{
  background:radial-gradient(circle at top,#0f2f36,#020617 60%);
  font-family:Poppins,sans-serif;
  color:#e5e7eb;
  min-height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
}

.card{
  width:420px;
  background:rgba(255,255,255,.06);
  border:1px solid rgba(255,255,255,.1);
  border-radius:22px;
  padding:32px;
  box-shadow:0 30px 60px rgba(0,0,0,.5);
  animation:fade .6s ease;
}

h2{
  color:#22d3ee;
  margin-bottom:12px;
}

.row{
  display:flex;
  align-items:center;
  gap:10px;
  font-size:14px;
  color:#cbd5e1;
  margin:8px 0;
}

.row i{
  color:#38bdf8;
}

.badge{
  display:inline-block;
  padding:6px 14px;
  border-radius:20px;
  font-size:12px;
  margin-bottom:15px;
}

.badge.ok{background:rgba(34,197,94,.15);color:#22c55e;}
.badge.warn{background:rgba(249,115,22,.15);color:#f97316;}

button{
  width:100%;
  margin-top:20px;
  padding:14px;
  border:none;
  border-radius:16px;
  font-weight:600;
  cursor:pointer;
  background:linear-gradient(90deg,#22d3ee,#06b6d4);
  color:#020617;
  font-size:15px;
  transition:.3s;
}

button:hover{
  transform:translateY(-2px);
  box-shadow:0 0 25px rgba(34,211,238,.6);
}

a{
  display:block;
  text-align:center;
  margin-top:18px;
  color:#38bdf8;
  text-decoration:none;
  font-size:14px;
}

.success{
  color:#22c55e;
  font-weight:600;
  text-align:center;
}

@keyframes fade{
  from{opacity:0;transform:translateY(20px)}
  to{opacity:1}
}
</style>
</head>

<body>

<div class="card">

<?php if(isset($success)){ ?>

  <span class="badge ok"><i class="fa fa-check-circle"></i> Registered</span>
  <h2>Seat Confirmed 🎉</h2>

  <div class="row"><i class="fa fa-calendar"></i><?= date("d M Y",strtotime($event['event_date'])) ?></div>
  <div class="row"><i class="fa fa-clock"></i><?= $event['event_time'] ?></div>
  <div class="row"><i class="fa fa-location-dot"></i><?= htmlspecialchars($event['location']) ?></div>

  <p class="success">You are officially registered!</p>
  <a href="event_full_details.php?id=<?= $event_id ?>">← Back to Event</a>

<?php } elseif($already){ ?>

  <span class="badge warn"><i class="fa fa-info-circle"></i> Already Booked</span>
  <h2>No Worries </h2>
  <p class="success">You already registered for this event.</p>
  <a href="event_details.php?id=<?= $event_id ?>">← Back to Event</a>

<?php } else { ?>

  <span class="badge ok"><i class="fa fa-chair"></i> Slots Available</span>
  <h2>Confirm Your Seat</h2>

  <div class="row"><i class="fa fa-ticket"></i><?= htmlspecialchars($event['title']) ?></div>
  <div class="row"><i class="fa fa-users"></i><?= $available ?> seats left</div>

  <form method="POST">
    <button name="confirm">
      <i class="fa fa-bolt"></i> Register Now
    </button>
  </form>

<?php } ?>

</div>

</body>
</html>
