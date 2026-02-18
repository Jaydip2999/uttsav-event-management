<?php
require_once("admin_check.php");
require "../includes/db.php";

/* ================= VALIDATE EVENT ID ================= */

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Event ID");
}

$event_id = (int)$_GET['id'];

/* ================= FETCH EVENT (Prepared Statement) ================= */

$stmt = $conn->prepare("
    SELECT e.*, 
           o.full_name AS organizer_name, 
           o.email, 
           o.mobile, 
           o.profile_pic
    FROM events e
    LEFT JOIN organizers o ON e.organizer_id = o.id
    WHERE e.id = ?
");

$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Event not found");
}

$event = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Event Details</title>
<link rel="stylesheet" href="admin.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

<div class="layout">

    <?php include 'admin_sidebar.php'; ?>

    <div class="main">
     <a href="javascript:history.back()" class="back-btn">
        <i class="fa-solid fa-arrow-left-long"></i> Back
    </a>
        <!-- PAGE TITLE (Dashboard Style) -->
        <h2 class="dash-title">
            <i class="fa-solid fa-calendar-check"></i> Event Details
        </h2>
    
        <div class="details-wrapper">

            <!-- ================= EVENT DETAILS ================= -->
            <div class="details-card">

                <div class="details-header">

                    <img src="../assets/images/events/<?= htmlspecialchars($event['image']) ?>" 
                         class="details-img">

                    <div>
                        <h2 class="details-title">
                            <?= htmlspecialchars($event['title']); ?>
                        </h2>

                        <?php if($event['status'] == 'approved'): ?>
                            <span class="status-badge status-approved">Approved</span>
                        <?php elseif($event['status'] == 'pending'): ?>
                            <span class="status-badge status-pending">Pending</span>
                        <?php else: ?>
                            <span class="status-badge status-rejected">Rejected</span>
                        <?php endif; ?>

                    </div>

                </div>

                <div class="details-row"><strong>Date:</strong>
                    <?= date("d M Y", strtotime($event['event_date'])); ?>
                </div>

                <div class="details-row"><strong>Time:</strong>
                    <?= htmlspecialchars($event['event_time']); ?>
                </div>

                <div class="details-row"><strong>Location:</strong>
                    <?= htmlspecialchars($event['location']); ?>
                </div>

                <div class="details-row"><strong>Category:</strong>
                    <?= htmlspecialchars($event['category']); ?>
                </div>

                <div class="details-row"><strong>Price:</strong>
                    ₹<?= number_format($event['price']); ?>
                </div>

                <div class="details-row"><strong>Total Seats:</strong>
                    <?= $event['total_slots']; ?>
                </div>

                <div class="details-row"><strong>Booked Seats:</strong>
                    <?= $event['booked_slots']; ?>
                </div>

                <div class="details-row"><strong>Remaining Seats:</strong>
                    <?= $event['total_slots'] - $event['booked_slots']; ?>
                </div>

                <div class="details-row">
                    <strong>Description:</strong><br>
                    <?= nl2br(htmlspecialchars($event['description'])); ?>
                </div>

                <?php if(!empty($event['highlights'])): ?>
                <div class="details-row">
                    <strong>Highlights:</strong>
                    <ul class="details-highlights">
                        <?php 
                        $highlights = explode(",", $event['highlights']);
                        foreach($highlights as $h): ?>
                            <li><?= htmlspecialchars(trim($h)); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- ================= ACTION BUTTONS ================= -->
                <!-- ================= ACTION BUTTONS ================= -->
<div class="actions">

<?php
$status    = $event['status'];      // pending / approved / rejected
$is_closed = $event['is_closed'];   // 0 / 1
?>

<!-- APPROVE BUTTON -->
<?php if($status != 'approved'): ?>
<form method="POST" action="event_action.php">
    <input type="hidden" name="id" value="<?= $event['id'] ?>">
    <input type="hidden" name="type" value="approve">
    <button class="btn btn-success"
        onclick="return confirm('Approve this event?')">
        Approve
    </button>
</form>
<?php endif; ?>


<!-- REJECT BUTTON -->
<?php if($status != 'rejected'): ?>
<form method="POST" action="event_action.php">
    <input type="hidden" name="id" value="<?= $event['id'] ?>">
    <input type="hidden" name="type" value="reject">
    <button class="btn btn-danger"
        onclick="return confirm('Reject this event?')">
        Reject
    </button>
</form>
<?php endif; ?>


<!-- CLOSE BUTTON (Only if approved & not closed) -->
<?php if($status == 'approved' && $is_closed == 0): ?>
<form method="POST" action="event_action.php">
    <input type="hidden" name="id" value="<?= $event['id'] ?>">
    <input type="hidden" name="type" value="stop">
    <button class="btn btn-primary"
        onclick="return confirm('Close this event?')">
        Close Event
    </button>
</form>
<?php endif; ?>


<!-- REOPEN BUTTON (Only if approved & closed) -->
<?php if($status == 'approved' && $is_closed == 1): ?>
<form method="POST" action="event_action.php">
    <input type="hidden" name="id" value="<?= $event['id'] ?>">
    <input type="hidden" name="type" value="reopen">
    <button class="btn btn-warning"
        onclick="return confirm('Reopen this event?')">
        Reopen Event
    </button>
</form>
<?php endif; ?>


<!-- DELETE BUTTON (Always visible) -->
<form method="POST" action="event_action.php">
    <input type="hidden" name="id" value="<?= $event['id'] ?>">
    <input type="hidden" name="type" value="delete">
    <button class="btn btn-dark"
        onclick="return confirm('Delete permanently?')">
        Delete
    </button>
</form>

</div>
</div>
            <!-- ================= ORGANIZER DETAILS ================= -->

            <div class="details-card">

                <h3 class="details-title">Organizer Details</h3>

                <div class="organizer-box">

                    <img src="../organizer/uploads/profile_pics/<?= htmlspecialchars($event['profile_pic']) ?>"
                         class="details-img">

                    <div>
                        <div class="details-row">
                            <strong>Name:</strong>
                            <?= htmlspecialchars($event['organizer_name']); ?>
                        </div>

                        <div class="details-row">
                            <strong>Email:</strong>
                            <?= htmlspecialchars($event['email']); ?>
                        </div>

                        <div class="details-row">
                            <strong>Phone:</strong>
                            <?= htmlspecialchars($event['mobile']); ?>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

<script src="../assets/script.js"></script>
</body>
</html>
