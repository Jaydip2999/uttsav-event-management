<?php
require "includes/db.php"; 

$today = date("Y-m-d");

$query = "
    SELECT * FROM events 
    WHERE event_date >= '$today'
    AND status = 'approved'
    ORDER BY event_date ASC
    LIMIT 4
";


$result = mysqli_query($conn, $query);
?>

<section class="event-slider">

<?php
$active = "active"; 

if(mysqli_num_rows($result) > 0):
while($row = mysqli_fetch_assoc($result)):
?>

  <div class="slide <?= $active ?>">
    <img src="\php\Event-management-system\assets\images\events\<?= $row['image']; ?>" alt="<?= $row['title']; ?>">

    <div class="slide-content">
      <h2><?= htmlspecialchars($row['title']); ?></h2>
      <p><?= htmlspecialchars($row['description']); ?></p>

      <a href="event/event_full_details.php?id=<?= $row['id']; ?>" class="slide-btn">
        Explore Event
      </a>
    </div>
  </div>

<?php
$active = ""; 
endwhile;
else:
?>
  <p style="text-align:center;">No upcoming events found</p>
<?php endif; ?>

</section>
