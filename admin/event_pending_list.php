<?php
require_once("admin_check.php");
?>
<!DOCTYPE html>
<html>
<head>
<title>Organizer Events</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>

<?php include "admin_sidebar.php"; ?>

<div class="layout">
  <div class="main">

    <h2>Organizer Added Events</h2>

    <div class="table-wrapper">
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
              <a class="btn approve" href="event_action.php?id=<?=$row['id']?>&type=approve">Approve</a>
              <a class="btn reject" href="event_action.php?id=<?=$row['id']?>&type=reject">Reject</a>
            <?php } else { ?>
              -
            <?php } ?>
          </td>
        </tr>
        <?php } ?>
      </table>
    </div>

  </div>
</div>

</body>
</html>
