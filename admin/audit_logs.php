<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Logs</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>

<?php
require_once("admin_check.php");
$q=mysqli_query($conn,"SELECT * FROM admin_logs ORDER BY id DESC");
require "admin_sidebar.php";
?>

<div class="layout">
  <div class="main">
    <h2>Admin Logs</h2>

    <div class="table-wrapper">
      <table>
        <tr>
          <th>Action</th>
          <th>Date</th>
        </tr>

        <?php while($l=mysqli_fetch_assoc($q)){ ?>
        <tr>
          <td><?=$l['action']?></td>
          <td><?=$l['created_at']?></td>
        </tr>
        <?php } ?>
      </table>
    </div>
  </div>
</div>

</body>
</html>
