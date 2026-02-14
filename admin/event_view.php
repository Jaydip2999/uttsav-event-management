<?php
include '../includes/db.php';

/* ================================VALIDATE EVENT ID ================================ */

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Event ID");
}

$event_id = intval($_GET['id']);

/* ================================ FETCH EVENT ================================ */

$event_query = mysqli_query($conn, "
    SELECT e.*, 
           o.full_name AS organizer_name, 
           o.email, 
           o.mobile, 
           o.profile_pic
    FROM events e
    LEFT JOIN organizers o ON e.organizer_id = o.id
    WHERE e.id = $event_id
");

if (mysqli_num_rows($event_query) == 0) {
    die("Event not found");
}

$event = mysqli_fetch_assoc($event_query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Event Details</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="layout">
    <?php include 'admin_sidebar.php'; ?>
    <div class="main">
        <div class="container">
            <div class="page-top">
                <h2>Event Details</h2>
                <a href="event_pending_list.php" class="back-btn">← Back</a>
            </div>

            <div class="details-wrapper">
                <!-- ================= EVENT DETAILS ===================== -->

                <div class="details-card">

                    <!-- EVENT HEADER -->
                    <div class="details-header">

                        <img src="../assets/images/events/<?php echo $event['image']; ?>" 
                             alt="Event Image" 
                             class="details-img">

                        <div>
                            <h2 class="details-title">
                                <?php echo htmlspecialchars($event['title']); ?>
                            </h2>

                            <!-- STATUS BADGE -->
                            <?php if($event['status'] == 'approved'): ?>
                                <span class="status-badge status-approved">Approved</span>
                            <?php elseif($event['status'] == 'pending'): ?>
                                <span class="status-badge status-pending">Pending</span>
                            <?php else: ?>
                                <span class="status-badge status-rejected">Rejected</span>
                            <?php endif; ?>
                        </div>

                    </div>

                    <!-- EVENT INFO ROWS -->
                    <div class="details-row">
                        <strong>Date:</strong>
                        <?php echo date("d M Y", strtotime($event['event_date'])); ?>
                    </div>

                    <div class="details-row">
                        <strong>Time:</strong>
                        <?php echo htmlspecialchars($event['event_time']); ?>
                    </div>

                    <div class="details-row">
                        <strong>Location:</strong>
                        <?php echo htmlspecialchars($event['location']); ?>
                    </div>

                    <div class="details-row">
                        <strong>Category:</strong>
                        <?php echo htmlspecialchars($event['category']); ?>
                    </div>

                    <div class="details-row">
                        <strong>Price:</strong>
                        ₹<?php echo number_format($event['price']); ?>
                    </div>

                    <div class="details-row">
                        <strong>Total Seats:</strong>
                        <?php echo $event['total_slots']; ?>
                    </div>

                    <div class="details-row">
                        <strong>Booked Seats:</strong>
                        <?php echo $event['booked_slots']; ?>
                    </div>

                    <div class="details-row">
                        <strong>Remaining Seats:</strong>
                        <?php echo $event['total_slots'] - $event['booked_slots']; ?>
                    </div>

                    <div class="details-row">
                        <strong>Description:</strong><br>
                        <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                    </div>

                    <!-- HIGHLIGHTS -->
                    <?php if(!empty($event['highlights'])): ?>
                        <div class="details-row">
                            <strong>Highlights:</strong>
                            <ul class="details-highlights">
                                <?php 
                                $highlights = explode(",", $event['highlights']);
                                foreach($highlights as $h): ?>
                                    <li><?php echo htmlspecialchars(trim($h)); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <!-- ================= ACTION BUTTONS ==================== -->

                    <div class="actions">

                    <?php if($event['status'] == 'pending'): ?>

                        <!-- APPROVE -->
                        <form method="POST" action="event_action.php">
                            <input type="hidden" name="id" value="<?= $event['id'] ?>">
                            <input type="hidden" name="type" value="approve">
                            <button type="submit" class="btn btn-success"
                                onclick="return confirm('Approve this event?')">
                                Approve
                            </button>
                        </form>

                        <!-- REJECT -->
                        <form method="POST" action="event_action.php">
                            <input type="hidden" name="id" value="<?= $event['id'] ?>">
                            <input type="hidden" name="type" value="reject">
                            <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Reject this event?')">
                                Reject
                            </button>
                        </form>

                    <?php elseif($event['status'] == 'approved'): ?>

                        <!-- STOP -->
                        <?php if(!$event['is_closed']): ?>
                        <form method="POST" action="event_action.php">
                            <input type="hidden" name="id" value="<?= $event['id'] ?>">
                            <input type="hidden" name="type" value="stop">
                            <button type="submit" class="btn btn-primary"
                                onclick="return confirm('Stop this event?')">
                                Stop Event
                            </button>
                        </form>
                        <?php endif; ?>

                        <!-- DELETE -->
                        <form method="POST" action="event_action.php">
                            <input type="hidden" name="id" value="<?= $event['id'] ?>">
                            <input type="hidden" name="type" value="delete">
                            <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Delete this event permanently?')">
                                Delete
                            </button>
                        </form>

                    <?php elseif($event['status'] == 'rejected'): ?>

                        <form method="POST" action="event_action.php">
                            <input type="hidden" name="id" value="<?= $event['id'] ?>">
                            <input type="hidden" name="type" value="delete">
                            <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Delete this rejected event?')">
                                Delete
                            </button>
                        </form>

                    <?php endif; ?>

                    </div>

                </div>

                <!-- ================= ORGANIZER DETAILS ================= -->

                <div class="details-card">

                    <h3 class="details-title">Organizer Details</h3>

                    <div class="organizer-box">

                        <img src="../organizer/uploads/profile_pics/<?php echo $event['profile_pic']; ?>" 
                             alt="Organizer"
                             class="details-img">

                        <div>
                            <div class="details-row">
                                <strong>Name:</strong>
                                <?php echo htmlspecialchars($event['organizer_name']); ?>
                            </div>

                            <div class="details-row">
                                <strong>Email:</strong>
                                <?php echo htmlspecialchars($event['email']); ?>
                            </div>

                            <div class="details-row">
                                <strong>Phone:</strong>
                                <?php echo htmlspecialchars($event['mobile']); ?>
                            </div>
                        </div>

                    </div>

                </div>

            </div> 

        </div>
    </div>

</div>

</body>
</html>
