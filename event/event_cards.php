<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Explore Events</title>
<link rel="stylesheet" href="../assets/style.css">
<script src="https://unpkg.com/lucide@latest"></script>
<link rel="stylesheet" href="event.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" href="/php/event-management-system/assets/images/logo.png">

</head>
<body>
  
<?php
session_start();
 include "../includes/header.php";?>
<section class="events-page">

<div class="events-header">
  <h1>Explore Events</h1>
  <div class="search-box">
    <i data-lucide="search"></i>
    <input type="text" id="searchInput" placeholder="Search events...">
  </div>
</div>

<div class="categories">
  <div class="category active" data-cat="all">All</div>
  <div class="category" data-cat="today">Today</div>
  <div class="category" data-cat="week">This Week</div>
  <div class="category" data-cat="month">This Month</div>
  <div class="category" data-cat="past">Past Events</div>

  <div class="category" data-cat="wedding">Wedding</div>
  <div class="category" data-cat="corporate">Corporate</div>
  <div class="category" data-cat="birthday">Birthday</div>
</div>

<?php
require "../includes/db.php";
$result = mysqli_query($conn,"
SELECT events.* 
FROM events
JOIN users ON events.organizer_id = users.id
WHERE events.status='approved'
AND users.status='active'
");

?>

<div class="events-wrapper" id="eventList">
<?php while($row=mysqli_fetch_assoc($result)){ ?>
 <div class="event-card"
     data-cat="<?= $row['category'] ?>"
     data-date="<?= $row['event_date'] ?>">

  <div class="event-img">
    <img src="../assets/images/events/<?= htmlspecialchars($row['image']) ?>" alt="Event Image">
  </div>

  <div class="event-body">
    <h3><?= htmlspecialchars($row['title']) ?></h3>
    <p><?= htmlspecialchars($row['description']) ?></p>

    <div class="event-meta">
      <span><i data-lucide="map-pin"></i><?= htmlspecialchars($row['location']) ?></span>
      <span><i data-lucide="calendar-days"></i>
        <?= date("d M Y", strtotime($row['event_date'])) ?>
      </span>
    </div>

<?php
// Get real booked slots from bookings table
$event_id = $row['id'];

$stmt = $conn->prepare("
    SELECT COALESCE(SUM(quantity),0) AS total_booked
    FROM bookings
    WHERE event_id = ?
    AND status IN ('confirmed','pending')
");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_assoc();

$booked = (int)$data['total_booked'];
$available = max(0, $row['total_slots'] - $booked);
?>

<div class="slot-info">
  <span class="total"><?= $row['total_slots'] ?></span>
  <span class="available">/ <?= $available ?></span>
</div>
    <a href="event_full_details.php?id=<?= $row['id'] ?>">
      <button class="event-btn">View Details</button>
    </a>
  </div>
</div>

<?php } ?>
</div>

</section>

<?php include"../includes/footer.php"; ?>
<script src="../assets/script.js"></script>

</body>
</html>
