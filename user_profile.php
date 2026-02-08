<?php
session_start();

/* ===== DB CONNECTION ===== */
$conn = new mysqli("localhost","root","","event_management");
if($conn->connect_error){
  die("Database connection failed");
}

/* ===== AUTH CHECK ===== */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer'){
  header("Location: ../login.php");
  exit;
}

$user_id = $_SESSION['user_id'];

/* ===== USERS TABLE ===== */
$userQ = $conn->prepare("SELECT name,email FROM users WHERE id=?");
$userQ->bind_param("i",$user_id);
$userQ->execute();
$user = $userQ->get_result()->fetch_assoc();

/* ===== ORGANIZERS TABLE ===== */
$orgQ = $conn->prepare("SELECT * FROM organizers WHERE user_id=?");
$orgQ->bind_param("i",$user_id);
$orgQ->execute();
$organizer = $orgQ->get_result()->fetch_assoc();

if(!$organizer){
  die("Organizer profile not found");
}

/* ===== EVENTS TABLE ===== */
$eventQ = $conn->prepare("
  SELECT title,event_date,status 
  FROM events 
  WHERE organizer_id=?
  ORDER BY id DESC
");
$eventQ->bind_param("i",$organizer['id']);
$eventQ->execute();
$events = $eventQ->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Organizer Profile</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
:root{
  --primary:#00e6e6;
  --bg:#0f2027;
  --card:#112a33;
  --muted:#9fbfc7;
}
html{
    overflow-x:hidden;
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
  animation:fade .9s ease;
}
@keyframes fade{
  from{opacity:0;transform:translateY(30px)}
  to{opacity:1}
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
.header h2{margin:0}
.role{opacity:.8}

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
  animation:up .8s ease;
}
@keyframes up{
  from{opacity:0;transform:translateY(20px)}
  to{opacity:1}
}
.stat{
  display:flex;
  justify-content:space-between;
  margin:12px 0;
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
  background:rgba(0,230,230,.18);
  color:var(--primary);
  font-size:12px;
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
  transition:.3s;
}
.btn:hover{
  transform:translateY(-3px);
  box-shadow:0 10px 25px rgba(0,230,230,.35);
}
@media(max-width:900px){
  .grid{grid-template-columns:1fr}
}
</style>
</head>

<body>
<div class="container">

  <!-- HEADER -->
  <div class="header">
 <img src="organizer/uploads/profile_pics/<?= $organizer['profile_pic']; ?>" alt="Profile">
    <div>
      <h2><?php echo htmlspecialchars($organizer['full_name']); ?></h2>
      <div class="role">Organizer • <?php echo $organizer['status']; ?></div>
      <p><?php echo htmlspecialchars($user['email']); ?></p>
    </div>
  </div>

  <!-- BODY -->
  <div class="grid">

    <!-- LEFT -->
    <div class="card">
      <h3>Organizer Info</h3>
      <div class="stat">Company <span><?php echo $organizer['company_name']; ?></span></div>
      <div class="stat">Mobile <span><?php echo $organizer['mobile']; ?></span></div>
      <div class="stat">Website <span><?php echo $organizer['website']; ?></span></div>
      <div class="stat">GST <span><?php echo $organizer['gst_number']; ?></span></div>
      <a href="edit_profile.php" class="btn">Edit Profile</a>
    </div>

    <!-- RIGHT -->
    <div class="card">
      <h3>My Events</h3>
      <?php if($events->num_rows == 0): ?>
        <p style="color:#9fbfc7">No events created yet</p>
      <?php endif; ?>

      <?php while($ev = $events->fetch_assoc()): ?>
        <div class="event">
          <span><?php echo $ev['title']; ?> (<?php echo $ev['event_date']; ?>)</span>
          <span class="badge"><?php echo $ev['status']; ?></span>
        </div>
      <?php endwhile; ?>
    </div>

  </div>

</div>
</body>
</html>
