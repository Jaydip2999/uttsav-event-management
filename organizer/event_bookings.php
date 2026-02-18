<?php
session_start();
require "../includes/db.php";

if($_SESSION['role'] != 'organizer'){
    die("Access Denied");
}

$org_id = $_SESSION['user_id'];

$data = mysqli_query($conn,"
    SELECT b.*, u.name, e.title
    FROM bookings b
    JOIN users u ON b.user_id=u.id
    JOIN events e ON b.event_id=e.id
    WHERE e.organizer_id=$org_id
    ORDER BY b.id DESC
");
?>

<h2>My Event Bookings</h2>

<table border="1" cellpadding="8">
<tr>
<th>User</th>
<th>Event</th>
<th>Transaction</th>
<th>Status</th>
</tr>

<?php while($row=mysqli_fetch_assoc($data)){ ?>
<tr>
<td><?= $row['name'] ?></td>
<td><?= $row['title'] ?></td>
<td><?= $row['transaction_id'] ?></td>
<td>
<?php
if($row['status']=='pending') echo "⏳ Pending";
elseif($row['status']=='confirmed') echo " Confirmed";
else echo " Rejected";
?>
</td>
</tr>
<?php } ?>
</table>
