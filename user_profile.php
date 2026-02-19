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
$userResult = $conn->query("SELECT id, name, email FROM users WHERE id=$user_id");
$user = $userResult->fetch_assoc();

/* ===== ORGANIZER ===== */
$orgResult = $conn->query("SELECT * FROM organizers WHERE user_id=$user_id");
$organizer = $orgResult->fetch_assoc();

$isOrganizer = ($organizer && isset($organizer['id']));
/* ===== ORGANIZER EARNINGS ===== */
$totalRevenue = 0;
$adminCommission = 0;
$organizerEarning = 0;

if($isOrganizer){

    $org_id = $organizer['id'];

    $earnQuery = $conn->query("
        SELECT SUM(b.total_price) as total
        FROM bookings b
        JOIN events e ON e.id = b.event_id
        WHERE e.organizer_id = $org_id
        AND b.status = 'confirmed'
    ");

    $earnData = $earnQuery->fetch_assoc();
    $totalRevenue = $earnData['total'] ?? 0;

    $adminCommission = $totalRevenue * 0.30;
    $organizerEarning = $totalRevenue * 0.70;
}
/* ===== APPROVED WITHDRAWALS ===== */
$totalWithdrawn = 0;
$availableBalance = $organizerEarning;

if($isOrganizer){

    $withdrawQuery = $conn->query("
        SELECT SUM(amount) as totalWithdraw
        FROM withdraw_requests
        WHERE organizer_id = $org_id
        AND status = 'approved'
    ");

    $withdrawData = $withdrawQuery->fetch_assoc();
    $totalWithdrawn = $withdrawData['totalWithdraw'] ?? 0;

    $availableBalance = $organizerEarning - $totalWithdrawn;
}

/* ===== MY CREATED EVENTS ===== */
$myEvents = null;
if($isOrganizer){
    $org_id = $organizer['id'];
  $myEvents = $conn->query("
    SELECT id, title, event_date,status, is_closed
    FROM events
    WHERE organizer_id=$org_id
    ORDER BY event_date DESC
");

}

/* ===== REGISTERED EVENTS ===== */
$registeredEvents = $conn->query("
    SELECT e.title, e.event_date, b.status
    FROM bookings b
    JOIN events e ON e.id = b.event_id
    WHERE b.user_id=$user_id
    ORDER BY b.booking_date DESC
");

/* ===== NOTIFICATIONS ===== */
$notifications = $conn->query("
    SELECT *
    FROM notifications
    WHERE user_id=$user_id
    ORDER BY created_at DESC
    limit 3
");
/* ===== EVENT ACTIONS (OPEN / CLOSE / DELETE) ===== */
if($isOrganizer && $_SERVER['REQUEST_METHOD'] === 'POST'){

    $event_id = intval($_POST['event_id']);
    $org_id   = $organizer['id'];

    /* VERIFY EVENT BELONGS TO THIS ORGANIZER */
    $check = $conn->query("SELECT id, is_closed FROM events WHERE id=$event_id AND organizer_id=$org_id");

    if($check && $check->num_rows > 0){

        $eventData = $check->fetch_assoc();

        /* TOGGLE OPEN / CLOSE */
        if(isset($_POST['toggle_status'])){

            $newValue = ($eventData['is_closed'] == 0) ? 1 : 0;

            $conn->query("UPDATE events SET is_closed=$newValue WHERE id=$event_id");
        }

        /* DELETE EVENT */
        if(isset($_POST['delete_event'])){

            $conn->query("DELETE FROM bookings WHERE event_id=$event_id");
            $conn->query("DELETE FROM events WHERE id=$event_id");
        }
    }

    header("Location: user_profile.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
<link rel="icon" type="image/png" href="/php/event-management-system/assets/images/logo.png">
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

<!-- LEFT --><div class="mp-card mp-profile-card">

  <h3 class="mp-section-title">
      <?= $isOrganizer ? 'Organizer Overview' : 'User Overview'; ?>
  </h3>

  <div class="mp-info-box">
      <?php if($isOrganizer): ?>
          <div class="mp-stat">
              <span>Company</span>
              <strong><?= htmlspecialchars($organizer['company_name']); ?></strong>
          </div>
          <div class="mp-stat">
              <span>Mobile</span>
              <strong><?= htmlspecialchars($organizer['mobile']); ?></strong>
          </div>
      <?php else: ?>
          <div class="mp-stat">
              <span>Name</span>
              <strong><?= htmlspecialchars($user['name']); ?></strong>
          </div>
          <div class="mp-stat">
              <span>Email</span>
              <strong><?= htmlspecialchars($user['email']); ?></strong>
          </div>
      <?php endif; ?>
  </div>

  <a href="edit_profile.php" class="mp-btn mp-full-btn">Edit Profile</a>

  <?php if($isOrganizer): ?>

  <div class="mp-divider"></div>

  <h4 class="mp-subtitle">Earnings</h4>

  <div class="mp-stat">
      <span>Total Revenue</span>
      <strong>₹<?= number_format($totalRevenue,2); ?></strong>
  </div>

  <div class="mp-stat">
      <span>Your Earning (70%)</span>
      <strong style="color:#00e6e6;">
          ₹<?= number_format($organizerEarning,2); ?>
      </strong>
  </div>

  <div class="mp-wallet-highlight">
      <span>Available Balance</span>
      <h2>₹<?= number_format($availableBalance,2); ?></h2>
      <a href="wallet.php" class="mp-btn mp-full-btn">
          Manage Wallet
      </a>
  </div>

  <?php endif; ?>

</div>

<!-- RIGHT -->
 <div class="mp-card mp-events-card">
<h3 class="mp-section-title">
    <?= $isOrganizer ? 'My Events' : 'Booked Events'; ?>
</h3>

<div class="mp-scroll">

<?php if($isOrganizer): ?>

    <?php if($myEvents->num_rows == 0): ?>
        <p class="mp-muted">No events created</p>
    <?php else: ?>
        <?php while($ev = $myEvents->fetch_assoc()): ?>
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
                    <form method="POST" class="mp-action-form">
                   <input type="hidden" name="event_id" value="<?= $ev['id']; ?>">
                 <!-- OPEN / CLOSE -->
                <button type="submit" 
                name="toggle_status" 
                class="mp-small-btn"
                onclick="return confirm('Are you sure to change event status?');">
                <?= ($ev['is_closed'] == '0') ? 'Close' : 'Open'; ?>
                </button>
                     <!-- DELETE -->
                <button type="submit" 
            name="delete_event" 
            class="mp-small-btn danger"
            onclick="return confirm('Are you sure to delete this event?');">
                Delete
                </button>

                </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

<?php endif; ?>

</div>

<!-- REGISTERED EVENTS -->
<div class="mp-card mp-full">
<h3>Registered Events</h3>

<?php if($registeredEvents->num_rows == 0): ?>
  <p class="mp-muted">No registered events</p>
<?php else: ?>
<div class="mp-scroll">
<?php while($re = $registeredEvents->fetch_assoc()): ?>
  <div class="mp-event">
    <span>
      <?= htmlspecialchars($re['title']); ?> 
      (<?= $re['event_date']; ?>)
    </span>

    <span class="mp-badge <?= $re['status']; ?>">
      <?= ucfirst($re['status']); ?>
    </span>
  </div>
<?php endwhile; ?>
</div>
<?php endif; ?>

</div>

</div>


</div>


<!-- NOTIFICATIONS -->
<?php if($notifications && $notifications->num_rows > 0): ?>
<div class="mp-card mp-full">
<h3>Notifications</h3>

<div class="mp-scroll">
<?php while($noti = $notifications->fetch_assoc()): ?>
  <div class="mp-event">
    <div>
      <strong><?= htmlspecialchars($noti['title']); ?></strong>
      <small><?= date("d M Y, h:i A", strtotime($noti['created_at'])); ?></small>
      <p><?= htmlspecialchars($noti['message']); ?></p>
    </div>

    <?php if($noti['is_read'] == 0): ?>
      <span class="mp-badge pending">New</span>
    <?php endif; ?>
  </div>
  
<?php endwhile; ?>
</div>
</div>
<?php endif; ?>
</div>
</body>
</html>
