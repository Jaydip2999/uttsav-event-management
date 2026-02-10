<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Organizers</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>

<?php
require_once("admin_check.php");

$q=mysqli_query($conn,"
SELECT o.*,u.email 
FROM organizers o
JOIN users u ON o.user_id=u.id
ORDER BY o.id DESC
");

require "admin_sidebar.php";
?>

<div class="layout">
  <div class="main">
    <h2>Organizers</h2>

    <div class="table-wrapper">
      <table>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Status</th>
          <th>License</th>
          <th>Action</th>
        </tr>

        <?php while($r=mysqli_fetch_assoc($q)){ ?>
        <tr>
          <td><?=htmlspecialchars($r['full_name'])?></td>
          <td><?=htmlspecialchars($r['email'])?></td>
          <td><?=strtoupper($r['status'])?></td>
          <td><?=strtoupper($r['license_status'])?></td>
          <td>
            <a class="btn reject" href="license_action.php?id=<?=$r['id']?>&type=suspend">Suspend</a>
            <a class="btn reject" href="license_action.php?id=<?=$r['id']?>&type=cancel">Cancel</a>
          </td>
        </tr>
        <?php } ?>
      </table>
    </div>
  </div>
</div>

</body>
</html>
