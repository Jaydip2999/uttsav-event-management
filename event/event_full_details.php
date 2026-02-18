<?php
session_start();
require "../includes/db.php";

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($event_id <= 0) die("Invalid Event ID");

/* ================= FETCH EVENT (SECURE) ================= */

$stmt = $conn->prepare("
SELECT 
    e.*,
    o.full_name AS organizer_name,
    o.email AS organizer_email,
    o.mobile AS organizer_mobile,
    o.profile_pic AS organizer_image,
    o.company_name AS organizer_company,
    o.status AS organizer_status
FROM events e
LEFT JOIN organizers o ON o.id = e.organizer_id
WHERE e.id = ?
AND e.status='approved'
");

$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) die("Event not found or not available");

$event = $result->fetch_assoc();

/* ================= ORGANIZER IMAGE ================= */

$orgImage = "../assets/images/default-user.png";

if(!empty($event['organizer_image'])){
    $possiblePath = __DIR__ . "/../organizer/uploads/profile_pics/" . $event['organizer_image'];
    if(file_exists($possiblePath)){
        $orgImage = "../organizer/uploads/profile_pics/" . $event['organizer_image'];
    }
}

/* ================= EVENT IMAGE ================= */

$eventImage = "../assets/images/default-event.jpg";

if (!empty($event['image'])) {
    $possibleEventPath = __DIR__."/../assets/images/events/".$event['image'];
    if(file_exists($possibleEventPath)){
        $eventImage = "../assets/images/events/".$event['image'];
    }
}

/* ================= BOOKING COUNT ================= */

$stmt2 = $conn->prepare("SELECT COUNT(*) as total FROM bookings WHERE event_id=?");
$stmt2->bind_param("i",$event_id);
$stmt2->execute();
$bookingResult = $stmt2->get_result()->fetch_assoc();

$bookedGuests = (int)$bookingResult['total'];
$maxGuests = (int)$event['total_slots'];
$availableSlots = max(0, $maxGuests - $bookedGuests);

/* ================= EVENT STATUS CHECK ================= */

$currentDateTime = new DateTime("now");
$eventDateTime = new DateTime($event['event_date'].' '.$event['event_time']);

$isPastEvent = $eventDateTime < $currentDateTime;
$isBookingClosed = (
    $availableSlots <= 0 || 
    $isPastEvent || 
    $event['is_closed'] == 1
);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($event['title']); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://unpkg.com/lucide@latest"></script>
<link rel="stylesheet" href="../assets/style.css">
<link rel="stylesheet" href="event.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
<?php include "../includes/header.php"; ?>

<div class="event-detail">

<section class="hero" style="background:url('<?= $eventImage ?>') center/cover no-repeat;">
<a href="javascript:history.back()" class="back-btn">
<i class="fa-solid fa-arrow-left-long"></i> Back
</a>
<div class="hero-overlay"></div>
<div class="hero-content">
<h1><?= htmlspecialchars($event['title']); ?></h1>
<div class="hero-meta">
<span><i class="fa fa-calendar"></i> <?= date("d M Y",strtotime($event['event_date'])); ?></span>
<span><i class="fa fa-location-dot"></i> <?= htmlspecialchars($event['location']); ?></span>
</div>
</div>
</section>

<section class="wrapper">
<div>

<div class="card">
<h3 class="section-title">About This Event</h3>
<p><?= nl2br(htmlspecialchars($event['description'])); ?></p>
</div>

<div class="card">
<h3 class="section-title">Why You Should Attend</h3>
<div class="highlights">
<?php for($i=1;$i<=4;$i++){
if(!empty($event["highlight$i"])){
echo "<div class='highlight'><i class='fa fa-star'></i> ".htmlspecialchars($event["highlight$i"])."</div>";
}} ?>
</div>
</div>

<div class="card">
<h3 class="section-title">Organizer Details</h3>
<div class="organizer compact">
<img src="<?= $orgImage; ?>" alt="Organizer">
<div>
<strong><?= htmlspecialchars($event['organizer_name']); ?></strong>
<?php if($event['organizer_status']=='approved'){ ?>
<span class="badge approved">✔ Verified</span>
<?php } ?>
<p class="company"><?= htmlspecialchars($event['organizer_company']); ?></p>
<p class="email">
<?= htmlspecialchars($event['organizer_email']); ?>
<p class="company"><?= htmlspecialchars($event['organizer_mobile']); ?></p>
</p>
</div>
</div>
</div>

</div>

<div class="sidebar">
<div class="card">
<h3 class="section-title">Event Info</h3>

<div class="info-row"><span>Date</span><span><?= date("d M Y",strtotime($event['event_date'])); ?></span></div>
<div class="info-row"><span>Time</span><span><?= htmlspecialchars($event['event_time']); ?></span></div>
<div class="info-row"><span>Category</span><span><?= htmlspecialchars($event['category']); ?></span></div>

<div class="info-row"><span>Total Guests</span><span><?= $maxGuests; ?></span></div>
<div class="info-row"><span>Booked</span><span><?= $bookedGuests; ?></span></div>

<div class="info-row">
<span>Available Slots</span>
<span>
<?php if($availableSlots > 0){ ?>
<?= $availableSlots; ?>
<?php } else { ?>
<strong style="color:#ef4444">Full</strong>
<?php } ?>
</span>
</div>

<div class="info-row">
<span>Status</span>
<span>
<?php if($isPastEvent){ ?>
<strong style="color:#ef4444">Event Closed</strong>
<?php } elseif($availableSlots <= 0){ ?>
<strong style="color:#f97316">Booking Full</strong>
<?php } elseif($event['is_closed']==1){ ?>
<strong style="color:#ef4444">Closed by Organizer</strong>
<?php } else { ?>
<strong style="color:#22c55e">Open</strong>
<?php } ?>
</span>
</div>

<div class="price">
₹<?= $event['price']>0 ? htmlspecialchars($event['price']) : 'Free'; ?>
</div>

<?php if(!$isBookingClosed): ?>
<a href="book_event.php?event_id=<?= $event['id'] ?>">
<button class="book-btn">
Book Your Seat <i class="fa fa-ticket"></i>
</button>
</a>
<?php else: ?>
<button class="book-btn" disabled style="opacity:.5;cursor:not-allowed">
<?= $isPastEvent ? 'Event Closed' : ($availableSlots <= 0 ? 'Slots Full' : 'Closed') ?>
<i class="fa fa-ticket"></i>
</button>
<?php endif; ?>

</div>
</div>

</section>
</div>

<?php include "../includes/footer.php"; ?>
<script src="../assets/script.js"></script>
</body>
</html>
