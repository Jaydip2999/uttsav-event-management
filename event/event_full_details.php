<?php
session_start();
require "../includes/db.php";

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($event_id <= 0) die("Invalid Event ID");

/* FETCH EVENT + ORGANIZER */
$sql = "
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
WHERE e.id = $event_id
";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) die("Event not found");
$event = mysqli_fetch_assoc($result);

/* ORGANIZER IMAGE */
$orgImage = "../assets/images/default-user.png"; 

if(!empty($event['organizer_image'])){
    $possiblePath = __DIR__ . "/../organizer/uploads/profile_pics/" . $event['organizer_image'];
    if(file_exists($possiblePath)){
        $orgImage = "../organizer/uploads/profile_pics/" . $event['organizer_image'];
    }
}


/* EVENT IMAGE */
$eventImage = "../assets/images/default-event.jpg";
if (!empty($event['image']) && file_exists(__DIR__."/../assets/images/events/".$event['image'])) {
    $eventImage = "../assets/images/events/".$event['image'];
}


/* ===== GUEST COUNT ===== */

$maxGuests = isset($event['total_slots']) ? (int)$event['total_slots'] : 0;
$bookedGuests = isset($event['booked_slots']) ? (int)$event['booked_slots'] : 0;

$availableSlots = max(0, $maxGuests - $bookedGuests);

$bookingQuery = mysqli_query(
  $conn,
  "SELECT COUNT(*) AS total FROM bookings WHERE event_id = $event_id"
);

if($bookingQuery){
  $bookingData = mysqli_fetch_assoc($bookingQuery);
  $bookedGuests = (int)$bookingData['total'];
}

$availableSlots = max(0, $maxGuests - $bookedGuests);

/* ===== EVENT STATUS CHECK ===== */

// Current datetime
$currentDateTime = new DateTime("now");
$eventDateTime = new DateTime($event['event_date'].' '.$event['event_time']);

// Past event?
$isPastEvent = $eventDateTime < $currentDateTime;

// Booking closed condition
$isBookingClosed = ($availableSlots <= 0 || $isPastEvent);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($event['title']); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
<style>
/* ===== RESET ===== *//* ===== GLOBAL ===== */
*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:'Poppins',sans-serif;
}

html, body{
  width:100%;
  overflow-x:hidden;
  background: #020617;
}

body{
  margin:0;
  background: radial-gradient(circle at top,#0f2f36 0%,#020617 55%);
  font-family:'Poppins',sans-serif;
  color:#e5e7eb;
  padding-top:10vh;
}

/* ===== HERO ===== */
.hero{
  height:75vh;
  background:url('<?= $eventImage ?>') center/cover no-repeat;
  position:relative;
  display:flex;
  align-items:flex-end;
}

.hero-overlay{
  position:absolute;
  inset:0;
  background:linear-gradient(
    to top,
    rgba(2,6,23,.95) 20%,
    rgba(2,6,23,.45)
  );
}

.hero-content{
  position:relative;
  padding:0 8% 50px;
  animation:fadeUp .8s ease;
}

.hero h1{
  font-size:38px;
  font-weight:600;
  color:#f8fafc;
}

.hero-meta span{
  margin-right:18px;
  font-size:14px;
  color:#94a3b8;
}

/* ===== LAYOUT ===== */
.wrapper{
  padding:60px 8%;
  display:grid;
  grid-template-columns:2.6fr 1.4fr;
  gap:40px;
}

/* ===== CARD ===== */
.card{
  background:linear-gradient(145deg, rgba(42, 51, 53,0.15),rgba(23, 45, 53, 0.5));
  backdrop-filter:blur(14px);
  border:1px solid rgba(255,255,255,.08);
  border-radius:18px;
  padding:28px;
  margin-bottom:35px;
 box-shadow:0 25px 55px rgba(0,0,0,.45);
 backdrop-filter:blur(12px);
}


.section-title{
  font-size:22px;
  color: rgb(128, 231, 252);
  margin-bottom:16px;
}

.card p{
  line-height:1.9;
  color:#cbd5e1;
}

/* ===== HIGHLIGHTS ===== */
.highlights{
  display:grid;
  grid-template-columns:repeat(2,1fr);
  gap:14px;
}

.highlight{
  background:rgba(255,255,255,.05);
  border:1px solid rgba(255,255,255,.06);
  padding:14px;
  border-radius:14px;
  font-size:14px;
  color:#e5e7eb;
}

.highlight i{
  color:#cbd5e1;;
}


/* ===== ORGANIZER ===== */
.organizer.compact{
  display:flex;
  gap:14px;
  align-items:center;
  background:rgba(255,255,255,.05);
  border:1px solid rgba(255,255,255,.06);
  padding:16px;
  border-radius:16px;
}

.organizer img{
  width:62px;
  height:62px;
  border-radius:50%;
  object-fit:cover;
  border:2px solid #22d3ee;
}

.company{
  font-size:13px;
  color:#94a3b8;
  margin:2px 0;
}

.badge.approved{
  background:rgba(34,211,238,.15);
  color:#22d3ee;
  font-size:11px;
  padding:4px 12px;
  border-radius:20px;
}

/* ===== SIDEBAR ===== */
.sidebar{
  position:sticky;
  top:90px;
}

.info-row{
  display:flex;
  justify-content:space-between;
  font-size:14px;
  margin-bottom:14px;
  color:#cbd5e1;
}

/* ===== PRICE ===== */
.price{
  font-size:40px;
  font-weight:700;
  text-align:center;
  margin:26px 0;
  background:linear-gradient(135deg,#22d3ee,#06b6d4);
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
}

/* ===== BUTTON ===== */
.book-btn{
  width:100%;
  padding:15px;
  border:none;
   background:linear-gradient(90deg,#00e6e6,#00b3b3);
  color:#020617;
  border-radius:16px;
  font-size:16px;
  font-weight:600;
  cursor:pointer;
  transition:.35s ease;
}

.book-btn:hover{
  transform:translateY(-2px);
  box-shadow:0 0 30px rgba(34,211,238,.55);
}

/* ===== ANIMATION ===== */
@keyframes fadeUp{
  from{opacity:0;transform:translateY(25px)}
  to{opacity:1;transform:none}
}

/* ===== MOBILE ===== */
@media(max-width:768px){
  .wrapper{
    grid-template-columns:1fr;
    padding:35px 6%;
  }
  .hero h1{
    font-size:26px;
  }
  .highlights{
    grid-template-columns:1fr;
  }
  .card{
    font-size:11px;
  }
  body{
    margin-top:-10%;
  }
}

</style>
</head>

<body>
<?php include "../includes/header.php"; ?>

<section class="hero">
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
      <p class="email"><?= htmlspecialchars($event['organizer_email']); ?>
      <span class="company"><?= htmlspecialchars($event['organizer_mobile']); ?></span>
    </p>
    </div>
  </div>
</div>

</div>

<div class="sidebar">
<div class="card">
  <h3 class="section-title">Event Info</h3>
  <div class="info-row"><span>Date</span><span><?= date("d M Y",strtotime($event['event_date'])); ?></span></div>
  <div class="info-row"><span>Time</span><span><?= $event['event_time']; ?></span></div>
  <div class="info-row"><span>Category</span><span><?= $event['category']; ?></span></div>
  <div class="info-row">
  <span>Total Guests</span>
  <span><?= $maxGuests; ?></span>
</div>

<div class="info-row">
  <span>Booked</span>
  <span><?= $bookedGuests; ?></span>
</div>

<div class="info-row">
  <span>Available Slots</span>
  <span>
    <?php if($availableSlots > 0){ ?>
      <?= $availableSlots; ?>
    <?php }else{ ?>
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
    <?php } else { ?>
      <strong style="color:#22c55e">Open</strong>
    <?php } ?>
  </span>
</div>

  <div class="price">₹<?= $event['price']>0?$event['price']:'Free'; ?></div>

  <a href="book_event.php?event_id=<?= $event['id'] ?>">
  <button class="book-btn"
<?= ($isBookingClosed ? 'disabled style="opacity:.5;cursor:not-allowed"' : '') ?>>
<?= $isPastEvent ? 'Event Closed' : ($availableSlots <= 0 ? 'Slots Full' : 'Book Your Seat') ?> <i class="fa fa-ticket"></i>
</button>
</a>

</div>
</div>
</section>

<?php include "../includes/footer.php"; ?>
<script src="../assets/script.js"></script>
</body>
</html>
