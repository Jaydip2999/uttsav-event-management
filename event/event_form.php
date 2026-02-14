<?php
session_start();
require_once "../includes/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* Fetch organizer */
$org_stmt = $conn->prepare("SELECT id FROM organizers WHERE user_id = ?");
$org_stmt->bind_param("i",$user_id);
$org_stmt->execute();
$organizer = $org_stmt->get_result()->fetch_assoc();

$success = $error = "";

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

    /* Image Upload */
    $uploadDir = "../assets/images/events/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $imageName = time() . "_" . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
    $image = $imageName;

    $stmt = $conn->prepare("
        INSERT INTO events
        (organizer_id, title, description, image, event_date, event_time, location, category, price,
         total_slots, booked_slots, status,
         highlight1, highlight2, highlight3, highlight4)
        VALUES (?,?,?,?,?,?,?,?,?, ?,0,'pending', ?,?,?,?)
    ");

    $stmt->bind_param(
        "isssssssiissss",
        $organizer['id'],
        $title,
        $description,
        $image,
        $event_date,
        $event_time,
        $location,
        $category,
        $price,
        $total_slots,
        $h1,$h2,$h3,$h4
    );

    if($stmt->execute()){
        $success = "Event submitted successfully! Waiting for admin approval.";
    }else{
        $error = "Something went wrong!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Submit Event</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="event.css">
<link rel="stylesheet" href="../assets/style.css">
<script src="https://unpkg.com/lucide@latest"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="event-form-page">
  <?php include"../includes/header.php";?>

<div class="event-form-container">

    <h2><i class="fa-solid fa-calendar-plus"></i> Create New Event</h2>

    <?php if($success) echo "<div class='alert success'>$success</div>"; ?>
    <?php if($error) echo "<div class='alert error'>$error</div>"; ?>

    <form method="POST" enctype="multipart/form-data">

        <div class="form-grid">

            <div class="full input-group">
                <label>Event Title</label>
                <input type="text" name="title" required>
            </div>

            <div class="full input-group">
                <label>Description</label>
                <textarea name="description" required></textarea>
            </div>

            <div class="input-group">
                <label>Date</label>
                <input type="date" name="event_date" min="<?= date('Y-m-d'); ?>" required>
            </div>

            <div class="input-group">
                <label>Time</label>
                <input type="time" name="event_time" required>
            </div>

            <div class="full input-group">
                <label>Location</label>
                <input type="text" name="location" required>
            </div>

            <div class="input-group">
                <label>Category</label>
                <select name="category" required>
                    <option value="">Select</option>
                    <option>Music</option>
                    <option>Technology</option>
                    <option>Sports</option>
                    <option>Education</option>
                    <option>Business</option>
                    <option>Others</option>
                </select>
            </div>

            <div class="input-group">
                <label>Price (₹)</label>
                <input type="number" name="price" value="0">
            </div>

            <div class="input-group">
                <label>Total Slots</label>
                <input type="number" name="total_slots" min="1" required>
            </div>

            <!-- Professional Upload UI -->
            <div class="full upload-area">
                <input type="file" name="image" id="image" required>
                <label for="image">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Click to upload event image</span>
                </label>
            </div>

            <div class="full highlights">
                <label>Event Highlights</label>
                <input type="text" name="highlight1" placeholder="Highlight 1">
                <input type="text" name="highlight2" placeholder="Highlight 2">
                <input type="text" name="highlight3" placeholder="Highlight 3">
                <input type="text" name="highlight4" placeholder="Highlight 4">
            </div>

        </div>

        <button type="submit" name="submit">
            <i class="fa-solid fa-paper-plane"></i> Submit Event
        </button>

    </form>

</div>
<?php include"../includes/footer.php";?>
<script src="../assets/script.js"></script>
</body>
</html>
