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

/* ===== MY CREATED EVENTS  ===== */
$myEvents = null;
if($isOrganizer){
$myQ = $conn->prepare("
    SELECT id, title, event_date, status
    FROM events
    WHERE organizer_id=?
    ORDER BY event_date DESC
");
    $myQ->bind_param("i",$organizer['id']);
    $myQ->execute();
    $myEvents = $myQ->get_result();
}

/* ===== REGISTERED EVENTS ===== */
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
<link rel="stylesheet" href="assets/style.css">
</head>

<body class="mp-page">
<div class="mp-container">
<a href="index.php" class="back-btn">← Back</a>

<!-- HEADER -->
<div class="mp-header">
<?php if($isOrganizer): ?>
  <img class="mp-avatar" src="organizer/uploads/profile_pics/<?= htmlspecialchars($organizer['profile_pic']); ?>">
  <div class="mp-info">
    <h2><?= htmlspecialchars($organizer['full_name']); ?></h2>
    <p>Organizer • <?= htmlspecialchars($organizer['status']); ?></p>
    <p><?= htmlspecialchars($user['email']); ?></p>
  </div>
<?php else: ?>
  <img class="mp-avatar" src="user/uploads/profile_pics/default.png">
  <div class="mp-info">
    <h2><?= htmlspecialchars($user['name']); ?></h2>
    <p>User</p>
    <p><?= htmlspecialchars($user['email']); ?></p>
  </div>
<?php endif; ?>
</div>

<div class="mp-grid">

<!-- LEFT -->
<div class="mp-card">
  <h3><?= $isOrganizer ? 'Organizer Info' : 'User Info'; ?></h3>

  <?php if($isOrganizer): ?>
    <div class="mp-stat">Company <span><?= $organizer['company_name']; ?></span></div>
    <div class="mp-stat">Mobile <span><?= $organizer['mobile']; ?></span></div>
  <?php else: ?>
    <div class="mp-stat">Name <span><?= $user['name']; ?></span></div>
    <div class="mp-stat">Email <span><?= $user['email']; ?></span></div>
  <?php endif; ?>

  <a href="edit_profile.php" class="mp-btn">Edit Profile</a>
</div>

<!-- RIGHT -->
<div class="mp-card">
<h3><?= $isOrganizer ? 'My Events' : 'Booked Events'; ?></h3>
<div class="mp-scroll">
<?php if($isOrganizer && $myEvents->num_rows==0): ?>
  <p class="mp-muted">No events created</p>
<?php endif; ?>

<?php if($isOrganizer): ?>
<?php while($ev=$myEvents->fetch_assoc()): ?>
  <div class="mp-event">

    <div class="mp-event-left">
      <strong><?= htmlspecialchars($ev['title']); ?></strong>
      <small><?= $ev['event_date']; ?></small>
    </div>

    <div class="mp-event-right">
      <span class="mp-badge <?= $ev['status']; ?>">
        <?= ucfirst($ev['status']); ?>
      </span>

      <a href="event/event_form.php?id=<?= $ev['id']; ?>" 
         class="mp-manage-btn">
         Edit
      </a>
    </div>

  </div>
<?php endwhile; ?>
</div>
<?php endif; ?>
</div>
<?php if($isOrganizer): ?>

<?php endif; ?>

</div>

<!-- REGISTERED EVENTS -->
<div class="mp-card mp-full">
<h3>Registered Events</h3>

<?php if($registeredEvents->num_rows==0): ?>
  <p class="mp-muted">No registered events</p>
<?php else: ?>
<div class="mp-scroll">
<?php while($re=$registeredEvents->fetch_assoc()): ?>
  <div class="mp-event">
    <span><?= htmlspecialchars($re['title']); ?> (<?= $re['event_date']; ?>)</span>
    <span class="mp-badge <?= $re['status']; ?>">
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