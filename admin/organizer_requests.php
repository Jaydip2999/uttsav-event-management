<?php
require_once("admin_check.php");

$q = mysqli_query($conn,"
SELECT o.*, u.name, u.email 
FROM organizers o
JOIN users u ON o.user_id = u.id
WHERE o.status='pending'
ORDER BY o.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Organizer Requests</title>
<link rel="stylesheet" href="admin.css">
</head>
<body>

<?php require"admin_sidebar.php";?>

<div class="main">
<h2>Pending Organizer Requests</h2>

<table>
<tr>
<th>Name</th>
<th>Email</th>
<th>Company</th>
<th>ID Proof</th>
<th>Action</th>
</tr>

<?php if(mysqli_num_rows($q)>0){ while($r=mysqli_fetch_assoc($q)){ ?>
<tr>
<td><?=htmlspecialchars($r['full_name'])?></td>
<td><?=htmlspecialchars($r['email'])?></td>
<td><?=htmlspecialchars($r['company_name'])?></td>
<td><a href="../organizer/<?=htmlspecialchars($r['profile_pic'])?>" target="_blank">View</a></td>
<td>
<a class="btn approve"
href="approve_organizer.php?id=<?=$r['id']?>&uid=<?=$r['user_id']?>"
onclick="return confirm('Approve this organizer?')">Approve</a>

<a class="btn reject"
href="reject_organizer.php?id=<?=$r['id']?>"
onclick="return confirm('Reject this organizer?')">Reject</a>

</td>
</tr>
<?php } } else { ?>
<tr><td colspan="5">No pending requests</td></tr>
<?php } ?>

</table>
</div>
</body>
</html>
