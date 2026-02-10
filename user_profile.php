<?php
session_start();

/* ===== DB CONNECTION ===== */
$conn = new mysqli("localhost","root","","event_management");
if($conn->connect_error){
    die("Database connection failed");
}

/* ===== AUTH CHECK ===== */
if(!isset($_SESSION['user_id'])){
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* ===== USER ===== */
$userQ = $conn->prepare("SELECT id, name, email FROM users WHERE id=?");
$userQ->bind_param("i",$user_id);
$userQ->execute();
$user = $userQ->get_result()->fetch_assoc();

/* ===== ORGANIZER ===== */
$orgQ = $conn->prepare("SELECT * FROM organizers WHERE user_id=?");
$orgQ->bind_param("i",$user_id);
$orgQ->execute();
$organizer = $orgQ->get_result()->fetch_assoc();

$isOrganizer = ($organizer && isset($organizer['id']));

/* ===== MY CREATED EVENTS (ONLY ORGANIZER) ===== */
$myEvents = null;
if($isOrganizer){
    $myQ = $conn->prepare("
        SELECT title, event_date, status
        FROM events
        WHERE organizer_id=?
        ORDER BY event_date DESC
    ");
    $myQ->bind_param("i",$organizer['id']);
    $myQ->execute();
    $myEvents = $myQ->get_result();
}

/* ===== REGISTERED EVENTS (USER + ORGANIZER BOTH) ===== */
$regQ = $conn->prepare("
    SELECT 
        e.title,
        e.event_date,
        b.status
    FROM bookings b
    JOIN events e ON e.id = b.event_id
    WHERE b.user_id=?
    ORDER BY b.booking_date DESC
");
$regQ->bind_param("i",$user_id);
$regQ->execute();
$registeredEvents = $regQ->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
:root{
  --primary:#00e6e6;
  --bg:#0f2027;
  --card:#112a33;
  --muted:#9fbfc7;
}
*{box-sizing:border-box;font-family:Poppins}
body{
  margin:0;
  background:radial-gradient(circle at top,var(--bg),#000);
  color:#fff;
}
.container{
  max-width:1200px;
  margin:30px auto;
  padding:20px;
}
.back-btn{
  display:inline-flex;
  align-items:center;
  gap:8px;
  margin-bottom:20px;
  padding:10px 18px;
  border-radius:14px;
  background:rgba(255,255,255,0.08);
  color:#fff;
  text-decoration:none;
}
.header{
  display:flex;
  gap:30px;
  align-items:center;
  flex-wrap:wrap;
  padding:30px;
  border-radius:22px;
  background:linear-gradient(135deg,var(--primary),#00b3b3);
}
.header img{
  width:150px;
  height:150px;
  border-radius:50%;
  object-fit:cover;
  border:5px solid #08161b;
}
.grid{
  display:grid;
  grid-template-columns:300px 1fr;
  gap:25px;
  margin-top:30px;
}

.card{
  background:var(--card);
  border-radius:18px;
  padding:25px;
  margin-top:15px;
}
.stat{
  display:flex;
  justify-content:space-between;
  margin:10px 0;
  font-size:14px;
  color:var(--muted);
}
.event{
  display:flex;
  justify-content:space-between;
  padding:12px 0;
  border-bottom:1px solid #1b3b44;
}
.badge{
  padding:4px 12px;
  border-radius:20px;
  font-size:12px;
  background:rgba(0,230,230,.18);
  color:var(--primary);
}
.btn{
  display:inline-block;
  margin-top:18px;
  padding:12px 20px;
  border-radius:14px;
  background:linear-gradient(135deg,var(--primary),#00b3b3);
  color:#000;
  font-weight:600;
  text-decoration:none;
}

/* STATUS COLORS */
.approved{background:#dcfce7;color:#16a34a}
.pending{background:#fff7ed;color:#f97316}
.rejected{background:#fee2e2;color:#ef4444}
.registered{background:#dcfce7;color:#16a34a}
.cancelled{background:#fee2e2;color:#ef4444}

/* SCROLL */
.scroll-box{
  max-height:220px;
  overflow-y:auto;
  padding-right:8px;
}
.scroll-box::-webkit-scrollbar{
  width:6px;
}
.scroll-box::-webkit-scrollbar-thumb{
  background:var(--primary);
  border-radius:10px;
}

/* RESPONSIVE */
@media(max-width:900px){
  .grid{grid-template-columns:1fr}
}
@media(max-width:600px){
  .header{justify-content:center;text-align:center}
  .event{flex-direction:column;gap:6px}
}
</style>
</head>

<body>
<div class="container">
<a href="index.php" class="back-btn">← Back</a>

<!-- HEADER -->
<div class="header">
<?php if($isOrganizer): ?>
  <img src="organizer/uploads/profile_pics/<?= htmlspecialchars($organizer['profile_pic']); ?>">
  <div>
    <h2><?= htmlspecialchars($organizer['full_name']); ?></h2>
    <p>Organizer • <?= htmlspecialchars($organizer['status']); ?></p>
    <p><?= htmlspecialchars($user['email']); ?></p>
  </div>
<?php else: ?>
  <img src="user/uploads/profile_pics/default.png">
  <div>
    <h2><?= htmlspecialchars($user['name']); ?></h2>
    <p>User</p>
    <p><?= htmlspecialchars($user['email']); ?></p>
  </div>
<?php endif; ?>
</div>

<div class="grid">

<!-- LEFT -->
<div class="card">
<?php if($isOrganizer): ?>
  <h3>Organizer Info</h3>
  <div class="stat">Company <span><?= $organizer['company_name']; ?></span></div>
  <div class="stat">Mobile <span><?= $organizer['mobile']; ?></span></div>
<?php else: ?>
  <h3>User Info</h3>
  <div class="stat">Name <span><?= $user['name']; ?></span></div>
  <div class="stat">Email <span><?= $user['email']; ?></span></div>
<?php endif; ?>
<a href="edit_profile.php" class="btn">Edit Profile</a>
</div>

<!-- RIGHT -->
<div class="card">
<h3><?= $isOrganizer ? 'My Events' : 'Booked Events'; ?></h3>

<?php if($isOrganizer && $myEvents->num_rows==0): ?>
  <p style="color:#9fbfc7">No events created</p>
<?php endif; ?>

<?php if($isOrganizer): ?>
<?php while($ev=$myEvents->fetch_assoc()): ?>
  <div class="event">
    <span><?= $ev['title']; ?> (<?= $ev['event_date']; ?>)</span>
    <span class="badge <?= $ev['status']; ?>"><?= $ev['status']; ?></span>
  </div>
<?php endwhile; ?>
<?php endif; ?>
</div>
</div>

<!-- REGISTERED EVENTS -->
<div class="card full-width">
  <h3>Registered Events</h3>

  <?php if($registeredEvents->num_rows==0): ?>
    <p style="color:#9fbfc7">No registered events</p>
  <?php else: ?>
  <div class="scroll-box">
    <?php while($re=$registeredEvents->fetch_assoc()): ?>
      <div class="event">
        <span><?= htmlspecialchars($re['title']); ?> (<?= $re['event_date']; ?>)</span>
        <span class="badge <?= $re['status']; ?>">
          <?= ucfirst($re['status']); ?>
        </span>
      </div>
    <?php endwhile; ?>
  </div>
  <?php endif; ?>
</div>

</div>
</body>
</html>
