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

    /* Insert Event */
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
        $success = " Event submitted successfully! Waiting for admin approval.";
    }else{
        $error = " Something went wrong!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Submit New Event</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
  
<style>
:root{
  --primary:#00e6e6;
  --primary-dark:#00b3b3;
  --bg:#0f2027;
  --card:#1f2a33;
  --border:#334454;
  --text:#ffffff;
  --text-muted:#cbd5e1;
}

/* RESET */
*{
  box-sizing:border-box;
  font-family:'Poppins',sans-serif;
}

body{
  margin:0;
  background:var(--bg);
  padding:18px;
  color:var(--text);
}

/* ===== CARD ===== */
.wrapper{
  max-width:760px;
  margin:auto;
  background:var(--card);
  padding:28px;
  border-radius:18px;
  box-shadow:0 25px 50px rgba(0,0,0,.45);
  animation:fadeIn .5s ease;
  position:relative;
}

@keyframes fadeIn{
  from{opacity:0;transform:translateY(20px)}
  to{opacity:1;transform:none}
}

/* CLOSE */
.close-btn{
  position:absolute;
  top:18px;
  right:18px;
  font-size:18px;
  color:#cbd5e1;
  text-decoration:none;
}
.close-btn:hover{color:var(--primary)}

/* TITLE */
h2{
  text-align:center;
  font-size:22px;
  margin-bottom:22px;
  color:var(--primary);
}

/* ALERTS */
.success,.error{
  padding:12px 14px;
  border-radius:12px;
  margin-bottom:16px;
  font-size:14px;
}
.success{
  background:rgba(0,230,230,.18);
  color:var(--primary);
}
.error{
  background:rgba(255,0,0,.18);
  color:#ff4d4d;
}

/* GRID */
.form-grid{
  display:grid;
  grid-template-columns:repeat(2,1fr);
  gap:16px;
}
.full{grid-column:1/-1}

/* LABEL */
label{
  font-size:13px;
  margin-bottom:6px;
  display:block;
  color:var(--text-muted);
}

/* INPUTS */
input,textarea,select{
  width:100%;
  padding:11px 13px;
  border-radius:12px;
  border:1px solid var(--border);
  background:#1a2b35;
  color:var(--text);
  font-size:13px;
  transition:.25s;
}

textarea{min-height:90px}

input:focus,textarea:focus,select:focus{
  outline:none;
  border-color:var(--primary);
  box-shadow:0 0 0 3px rgba(0,230,230,.18);
}

/* FILE */
.custom-file input{display:none}

.file-label{
  display:flex;
  justify-content:center;
  align-items:center;
  gap:8px;
  padding:11px;
  border:2px dashed var(--border);
  border-radius:12px;
  cursor:pointer;
  font-size:13px;
  color:var(--text-muted);
  transition:.3s;
}
.file-label:hover{
  border-color:var(--primary);
  color:var(--primary);
}

/* HIGHLIGHTS */
.highlight-group input{margin-bottom:8px}

/* BUTTON */
button{
  margin-top:20px;
  width:100%;
  padding:13px;
  border:none;
  border-radius:14px;
  background:linear-gradient(90deg,rgb(22, 76, 87),rgba(163, 235, 249, 0.97));
  font-size:15px;
  font-weight:500;
  cursor:pointer;
  transition:.3s;
  color:white;
}
button:hover{
  transform:translateY(-2px);
  box-shadow:0 14px 26px rgba(0,230,230,.35);
}

/* MOBILE */
@media(max-width:700px){
  .form-grid{grid-template-columns:1fr}
  h2{font-size:20px}
}

</style>

</head>
<body><div class="wrapper">
  <a href="../index.php" class="close-btn">
    <i class="fa-solid fa-xmark"></i>
  </a>

  <h2>Submit New Event</h2>

  <?php if($success) echo "<div class='success'>$success</div>"; ?>
  <?php if($error) echo "<div class='error'>$error</div>"; ?>

  <form method="POST" enctype="multipart/form-data">
    <div class="form-grid">

      <div class="full">
        <label>Event Title *</label>
        <input type="text" name="title" required>
      </div>

      <div class="full">
        <label>Description *</label>
        <textarea name="description" required></textarea>
      </div>

      <div>
        <label>Date *</label>
        <input type="date" name="event_date" min="<?= date('Y-m-d'); ?>" required>
      </div>

      <div>
        <label>Time *</label>
        <input type="time" name="event_time" required>
      </div>

      <div class="full">
        <label>Location *</label>
        <input type="text" name="location" required>
      </div>

      <div>
        <label>Category *</label>
        <select name="category" required>
          <option value="">Select</option>
          <option>Music</option>
          <option>Technology</option>
          <option>Sports</option>
          <option>Education</option>
          <option>Business</option>
        </select>
      </div>

      <div>
        <label>Price (₹)</label>
        <input type="number" name="price" value="0">
      </div>

      <div>
        <label>Total Slots *</label>
        <input type="number" name="total_slots" min="1" required>
      </div>

      <div class="full">
        <label>Event Image *</label>
        <div class="custom-file">
          <input type="file" name="image" id="image" required>
          <label for="image" class="file-label">
            <i class="fa fa-upload"></i> Select Image
          </label>
        </div>
      </div>

      <div class="full highlight-group">
        <label>Event Highlights</label>
        <input type="text" name="highlight1" placeholder="Highlight 1">
        <input type="text" name="highlight2" placeholder="Highlight 2">
        <input type="text" name="highlight3" placeholder="Highlight 3">
        <input type="text" name="highlight4" placeholder="Highlight 4">
      </div>

    </div>

    <button name="submit">Submit Event</button>
  </form>
</div>


</body>
</html>
