<?php
require_once("admin_check.php");
$u = mysqli_query($conn,"SELECT * FROM users");
?>
<!DOCTYPE html>
<html>
<head>
<title>Users</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>

<?php require "admin_sidebar.php"; ?>

<div class="layout">
  <div class="main">
    <h2>Users</h2>

    <div class="table-wrapper">
      <table>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
        </tr>

        <?php while($r=mysqli_fetch_assoc($u)){ ?>
        <tr>
          <td><?=$r['id']?></td>
          <td><?=htmlspecialchars($r['name'])?></td>
          <td><?=htmlspecialchars($r['email'])?></td>
          <td><?=$r['role']?></td>
        </tr>
        <?php } ?>
      </table>
    </div>

  </div>
</div>

</body>
</html>
