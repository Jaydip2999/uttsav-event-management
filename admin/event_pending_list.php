<?php
include 'admin_check.php';
require "../includes/db.php";
?>

<!DOCTYPE html>
<html>
<head>
<title>Organizer Events</title>
<link rel="stylesheet" href="admin.css">
</head>

<body>

<!-- ===== SIDEBAR ===== -->
<div class="sidebar">
<h2>ADMIN</h2>
<a href="../index.php">Dashboard</a>
<a href="organizer_requests.php">Organizer Requests</a>
<a href="event_pending_list.php">Events</a>
<a href="users.php">Users</a>
<a href="logout.php">Logout</a>
</div>

<!-- ===== MAIN CONTENT ===== -->
<div class="main">
  <div class="card">
    <h2>Organizer Added Events</h2>

    <table>
      <tr>
        <th>Event</th>
        <th>Organizer</th>
        <th>Category</th>
        <th>Date</th>
        <th>Status</th>
        <th class="center">Action</th>
      </tr>

      <?php
    $q = "
        SELECT events.*, organizers.full_name 
        FROM events
        LEFT JOIN organizers ON events.organizer_id = organizers.id
        ORDER BY events.id DESC
        ";


      $r = mysqli_query($conn,$q);

      while($row = mysqli_fetch_assoc($r)){
      ?>
      <tr>
        <td><?= htmlspecialchars($row['title']); ?></td>
       <td><?= $row['full_name'] ? htmlspecialchars($row['full_name']) : 'N/A'; ?></td>
       <td><?= $row['category'] ? htmlspecialchars($row['category']) : 'Uncategorized'; ?></td>
        <td><?= $row['event_date']; ?></td>
        <td><?= strtoupper($row['status']); ?></td>

        <td class="center">
          <?php if($row['status']=='pending'){ ?>
           <a class="btn approve" href="event_action.php?id=<?= $row['id']; ?>&type=approve">Approve</a>
            <a class="btn reject" href="event_action.php?id=<?= $row['id']; ?>&type=reject">Reject</a>
          <?php } else { ?>
            -
          <?php } ?>
        </td>
      </tr>
      <?php } ?>

    </table>
  </div>

</div>

</body>
</html>
