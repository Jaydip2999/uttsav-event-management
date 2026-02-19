<?php
session_start();
require_once "../includes/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* Fetch organizer */
$org_stmt = $conn->prepare("SELECT id FROM organizers WHERE user_id = ?");
$org_stmt->bind_param("i",$user_id);
$org_stmt->execute();
$organizer = $org_stmt->get_result()->fetch_assoc();

if(!$organizer){
    die("Unauthorized");
}

$editMode = false;
$event = null;
$success = $error = "";

/* ===== CHECK EDIT MODE ===== */
if(isset($_GET['id'])){
    $editMode = true;
    $event_id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT * FROM events WHERE id=? AND organizer_id=?");
    $stmt->bind_param("ii",$event_id,$organizer['id']);
    $stmt->execute();
    $event = $stmt->get_result()->fetch_assoc();

    if(!$event){
        die("Event not found");
    }
}

/* ===== FORM SUBMIT ===== */
if(isset($_POST['submit'])){

    $title       = $_POST['title'];
    $description = $_POST['description'];
    $event_date  = $_POST['event_date'];
    $event_time  = $_POST['event_time'];
    $location    = $_POST['location'];
    $category    = $_POST['category'];
    $price       = $_POST['price'] ?? 0;
    $total_slots = $_POST['total_slots'];

    $h1 = $_POST['highlight1'];
    $h2 = $_POST['highlight2'];
    $h3 = $_POST['highlight3'];
    $h4 = $_POST['highlight4'];

    /* Image */
    if($editMode){
        $image = $event['image'];
    } else {
        $image = "";
    }

    if(!empty($_FILES['image']['name'])){
        $uploadDir = "../assets/images/events/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
        $image = $imageName;
    }
        /* Payment QR Image */
if($editMode){
    $payment_qr = $event['payment_qr'];
} else {
    $payment_qr = "";
}

if(!empty($_FILES['payment_qr']['name'])){
    $qrUploadDir = "../assets/images/payment_qr/";
    if (!is_dir($qrUploadDir)) {
        mkdir($qrUploadDir, 0777, true);
    }
    $qrName = time() . "_qr_" . basename($_FILES['payment_qr']['name']);
    move_uploaded_file($_FILES['payment_qr']['tmp_name'], $qrUploadDir . $qrName);
    $payment_qr = $qrName;
}

    if($editMode){

        /* UPDATE */
        $stmt = $conn->prepare("
            UPDATE events SET
            title=?, description=?, image=?,
            event_date=?, event_time=?, location=?,
            category=?, price=?, total_slots=?,
            highlight1=?, highlight2=?, highlight3=?, highlight4=?,
            status='pending'
            WHERE id=? AND organizer_id=?
        ");

        $stmt->bind_param(
            "sssssssiissssii",
            $title,$description,$image,
            $event_date,$event_time,$location,
            $category,$price,$total_slots,
            $h1,$h2,$h3,$h4,
            $event_id,$organizer['id']
        );

    }else{

        /* INSERT */
        $stmt = $conn->prepare("
            INSERT INTO events
            (organizer_id, title, description, image, event_date, event_time,
             location, category, price, total_slots, booked_slots, status,
             highlight1, highlight2, highlight3, highlight4)
            VALUES (?,?,?,?,?,?,?,?,?, ?,0,'pending', ?,?,?,?)
        ");

        $stmt->bind_param(
            "isssssssiissss",
            $organizer['id'],
            $title,$description,$image,
            $event_date,$event_time,$location,
            $category,$price,$total_slots,
            $h1,$h2,$h3,$h4
        );
    }

    if($stmt->execute()){
        $success = $editMode 
            ? "Event updated! Waiting for admin approval."
            : "Event submitted! Waiting for admin approval.";
    }else{
        $error = "Something went wrong!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= $editMode ? "Edit Event" : "Create Event" ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/style.css">
<link rel="stylesheet" href="event.css">
<link rel="icon" type="image/png" href="/php/event-management-system/assets/images/logo.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="event-form-page">

<?php include "../includes/header.php"; ?>

<div class="event-form-container">

<a href="javascript:history.back()" class="back-btn">
<i class="fa-solid fa-arrow-left-long"></i> Back
</a>
<h2>
<i class="fa-solid fa-calendar-plus"></i>
<?= $editMode ? "Edit Event" : "Create New Event" ?>
</h2>

<?php if($success) echo "<div class='alert success'>$success</div>"; ?>
<?php if($error) echo "<div class='alert error'>$error</div>"; ?>

<form method="POST" enctype="multipart/form-data">

<div class="form-grid">

<div class="full input-group">
<label>Event Title</label>
<input type="text" name="title"
value="<?= $editMode ? htmlspecialchars($event['title']) : '' ?>" required>
</div>

<div class="full input-group">
<label>Description</label>
<textarea name="description" required><?= $editMode ? htmlspecialchars($event['description']) : '' ?></textarea>
</div>

<div class="input-group">
<label>Date</label>
<input type="date" name="event_date"
value="<?= $editMode ? $event['event_date'] : '' ?>"
min="<?= date('Y-m-d'); ?>" required>
</div>

<div class="input-group">
<label>Time</label>
<input type="time" name="event_time"
value="<?= $editMode ? $event['event_time'] : '' ?>" required>
</div>

<div class="full input-group">
<label>Location</label>
<input type="text" name="location"
value="<?= $editMode ? htmlspecialchars($event['location']) : '' ?>" required>
</div>

<div class="input-group">
<label>Category</label>
<select name="category" required>
<option value="">Select</option>
<?php
$categories = ["Music","Technology","Sports","Education","Business","Others"];
foreach($categories as $cat){
$selected = ($editMode && $event['category']==$cat) ? "selected" : "";
echo "<option value='$cat' $selected>$cat</option>";
}
?>
</select>
</div>

<div class="input-group">
<label>Price (₹)</label>
<input type="number" name="price"
value="<?= $editMode ? $event['price'] : 0 ?>">
</div>

<div class="input-group">
<label>Total Slots</label>
<input type="number" name="total_slots"
value="<?= $editMode ? $event['total_slots'] : '' ?>"
min="1" required>
</div><div class="full upload-area">
  <input type="file" id="eventImage" name="image" required>
  <label for="eventImage">
    <i class="fa-solid fa-cloud-arrow-up"></i>
    Click to Upload Event Image
  </label>
</div>

<div class="full highlights">
<label>Event Highlights</label>
<input type="text" name="highlight1" placeholder="Highlight 1"
value="<?= $editMode ? htmlspecialchars($event['highlight1']) : '' ?>">
<input type="text" name="highlight2" placeholder="Highlight 2"
value="<?= $editMode ? htmlspecialchars($event['highlight2']) : '' ?>">
<input type="text" name="highlight3" placeholder="Highlight 3"
value="<?= $editMode ? htmlspecialchars($event['highlight3']) : '' ?>">
<input type="text" name="highlight4" placeholder="Highlight 4"
value="<?= $editMode ? htmlspecialchars($event['highlight4']) : '' ?>">
</div>

</div>

<button type="submit" name="submit">
<i class="fa-solid fa-paper-plane"></i>
<?= $editMode ? "Update Event" : "Submit Event" ?>
</button>

</form>
</div>

<?php include "../includes/footer.php"; ?>
<script src="../assets/script.js"></script>
</body>
</html>
