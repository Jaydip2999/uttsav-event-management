<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

require "../includes/db.php";

$event_id = $_GET['id'] ?? '';

if($event_id == '17'){
    echo "Invalid Event ID";
    exit;
}

$event_id = mysqli_real_escape_string($conn, $event_id);

$sql = "SELECT * FROM events WHERE id='$event_id'";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) == 0){
    echo "Event not found";
    exit;
}

$event = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Event Full Details</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:'Poppins',sans-serif;
}
body{
  background:#020617;
  color:#e5e7eb;
}

/* ===== HERO ===== */
.hero{
  height:75vh;
  background:url('assets/images/concert.png') center/cover no-repeat;
  position:relative;
  display:flex;
  align-items:flex-end;
}
.hero::after{
  content:'';
  position:absolute;
  inset:0;
  background:linear-gradient(to top,#020617 20%,transparent);
}
.hero-content{
  position:relative;
  padding:40px 8%;
}
.hero-content h1{
  font-size:46px;
  margin-bottom:10px;
}
.hero-meta span{
  margin-right:15px;
  font-size:15px;
  color:#c7d2fe;
}

/* ===== MAIN LAYOUT ===== */
.wrapper{
  padding:60px 8%;
  display:grid;
  grid-template-columns:2.7fr 1.3fr;
  gap:40px;
}

/* ===== CARD ===== */
.card{
  background:rgba(255,255,255,.06);
  border-radius:20px;
  padding:28px;
  margin-bottom:35px;
}

/* ===== TITLES ===== */
.section-title{
  font-size:26px;
  margin-bottom:15px;
  color:#a5b4fc;
}

/* ===== TEXT ===== */
.card p{
  line-height:1.9;
  font-size:15px;
  color:#d1d5db;
}

/* ===== HIGHLIGHTS ===== */
.highlights{
  display:grid;
  grid-template-columns:repeat(2,1fr);
  gap:15px;
}
.highlight{
  background:rgba(255,255,255,.08);
  padding:15px;
  border-radius:14px;
}
.highlight i{
  color:#6366f1;
  margin-right:8px;
}

/* ===== GALLERY ===== */
.gallery{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:15px;
}
.gallery img{
  width:100%;
  height:180px;
  object-fit:cover;
  border-radius:14px;
}

/* ===== SCHEDULE ===== */
.schedule li{
  list-style:none;
  padding:14px;
  background:rgba(255,255,255,.07);
  border-radius:14px;
  margin-bottom:12px;
}
.schedule span{
  color:#818cf8;
  font-weight:600;
}

/* ===== RULES ===== */
.rules li{
  margin-bottom:10px;
  list-style:none;
}
.rules li::before{
  content:"✔ ";
  color:#22c55e;
}

/* ===== ORGANIZER ===== */
.organizer{
  display:flex;
  gap:15px;
  align-items:center;
}
.organizer img{
  width:70px;
  height:70px;
  border-radius:50%;
}

/* ===== SIDEBAR ===== */
.sidebar{
  position:sticky;
  top:100px;
}
.info-row{
  display:flex;
  justify-content:space-between;
  margin-bottom:15px;
}
.price{
  font-size:34px;
  color:#22c55e;
  text-align:center;
}
.book-btn{
  width:100%;
  padding:15px;
  border:none;
  border-radius:14px;
  background:#6366f1;
  color:#fff;
  font-size:16px;
  cursor:pointer;
}
.book-btn:hover{opacity:.9}

/* ===== RESPONSIVE ===== */
@media(max-width:900px){
  .wrapper{
    grid-template-columns:1fr;
  }
  .hero-content h1{font-size:34px}
}
</style>
</head>

<body>
<!-- ===== HERO ===== -->
<section class="hero" style="background:url('uploads/<?php echo $event['image']; ?>') center/cover no-repeat;">
  <div class="hero-content">
    <h1><?php echo htmlspecialchars($event['title']); ?></h1>
    <div class="hero-meta">
      <span>
        <i class="fa fa-calendar"></i>
        <?php echo date("d M Y", strtotime($event['event_date'])); ?>
      </span>
      <span>
        <i class="fa fa-location-dot"></i>
        <?php echo htmlspecialchars($event['location']); ?>
      </span>
    </div>
  </div>
</section>

<!-- ===== CONTENT ===== -->
<section class="wrapper">

<!-- LEFT SIDE -->
<div>

  <!-- ABOUT -->
  <div class="card">
    <h3 class="section-title">About This Event</h3>
    <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
  </div>

  <!-- HIGHLIGHTS -->
  <div class="card">
    <h3 class="section-title">Why You Should Attend</h3>
    <div class="highlights">

      <?php if(!empty($event['highlight1'])){ ?>
        <div class="highlight"><i class="fa fa-star"></i> <?php echo $event['highlight1']; ?></div>
      <?php } ?>

      <?php if(!empty($event['highlight2'])){ ?>
        <div class="highlight"><i class="fa fa-star"></i> <?php echo $event['highlight2']; ?></div>
      <?php } ?>

      <?php if(!empty($event['highlight3'])){ ?>
        <div class="highlight"><i class="fa fa-star"></i> <?php echo $event['highlight3']; ?></div>
      <?php } ?>

      <?php if(!empty($event['highlight4'])){ ?>
        <div class="highlight"><i class="fa fa-star"></i> <?php echo $event['highlight4']; ?></div>
      <?php } ?>

    </div>
  </div>

  <!-- GALLERY (STATIC FOR NOW) -->
  <div class="card">
    <h3 class="section-title">Event Gallery</h3>
    <div class="gallery">
      <img src="assets/images/g1.jpg">
      <img src="assets/images/g2.jpg">
      <img src="assets/images/g3.jpg">
    </div>
  </div>

  <!-- ORGANIZER -->
  <div class="card">
    <h3 class="section-title">Organizer Details</h3>
    <div class="organizer">
      <img src="https://i.pravatar.cc/150">
      <div>
        <strong>Event Organizer</strong>
        <p>Professional event management team</p>
      </div>
    </div>
  </div>

</div>

<!-- RIGHT SIDEBAR -->
<div class="sidebar">
  <div class="card">
    <h3 class="section-title">Event Info</h3>

    <div class="info-row">
      <span>Date</span>
      <span><?php echo date("d M Y", strtotime($event['event_date'])); ?></span>
    </div>

    <div class="info-row">
      <span>Time</span>
      <span><?php echo date("h:i A", strtotime($event['event_time'])); ?></span>
    </div>

    <div class="info-row">
      <span>Location</span>
      <span><?php echo htmlspecialchars($event['location']); ?></span>
    </div>

    <div class="info-row">
      <span>Category</span>
      <span><?php echo htmlspecialchars($event['category']); ?></span>
    </div>

    <div class="price">
      ₹<?php echo ($event['price'] > 0) ? $event['price'] : 'Free'; ?>
    </div>

    <button class="book-btn">Book Your Seat</button>
  </div>
</div>

</section>

</body>
</html>
