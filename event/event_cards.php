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
  <div class="category" data-cat="wedding">Wedding</div>
  <div class="category" data-cat="corporate">Corporate</div>
  <div class="category" data-cat="birthday">Birthday</div>
</div>

<?php
require "../includes/db.php";
$result = mysqli_query($conn,"SELECT * FROM events WHERE status='approved'");
?>

<div class="events-wrapper" id="eventList">
<?php while($row=mysqli_fetch_assoc($result)){ ?>
  <div class="event-card" data-cat="<?= $row['category'] ?>">
    <div class="event-img">
      <img src="uploads/<?= $row['image'] ?>">
    </div>
    <div class="event-body">
      <h3><?= htmlspecialchars($row['title']) ?></h3>
      <p><?= htmlspecialchars($row['description']) ?></p>

      <div class="event-meta">
        <span><i data-lucide="map-pin"></i><?= htmlspecialchars($row['location']) ?></span>
        <span><i data-lucide="calendar-days"></i><?= date("d M Y",strtotime($row['event_date'])) ?></span>
      </div>

      <a href="event_details.php?id=<?= $row['id'] ?>">
        <button class="event-btn">View Details</button>
      </a>
    </div>
  </div>
<?php } ?>
</div>

</section>

<?php
// include"../includes/header.php";  
?>

<?php include"../includes/footer.php"; ?>
<script src="../assets/script.js"></script>

</body>
</html>
