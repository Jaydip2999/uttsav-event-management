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

/* Global */
*{
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background: var(--bg);
    padding:24px;
    color: var(--text);
}

/* ===== Card ===== */
.wrapper{
    max-width:900px;
    margin:auto;
    background: var(--card);
    padding:40px;
    border-radius:22px;
    box-shadow:0 30px 60px rgba(0,0,0,.4);
    animation:cardIn .6s ease;
}

.close-btn{
    position: relative;
    top:-15px;
    right:-98%;
    width:36px;
    height:36px;
    color: var(--text);
}

@keyframes cardIn{
    from{opacity:0; transform:translateY(30px);}
    to{opacity:1; transform:translateY(0);}
}

/* ===== Heading ===== */
h2{
    text-align:center;
    font-size:26px;
    margin-bottom:30px;
    color: var(--primary);
}

/* ===== Alerts ===== */
.success{
    padding:14px 16px;
    border-radius:14px;
    margin-bottom:20px;
    font-weight:500;
    animation:fadeUp .4s ease;
    background: rgba(0,230,230,0.2);
    color: var(--primary);
}

.error{
    padding:14px 16px;
    border-radius:14px;
    margin-bottom:20px;
    font-weight:500;
    animation:fadeUp .4s ease;
    background: rgba(255,0,0,0.2);
    color: #ff4d4d;
}

@keyframes fadeUp{
    from{opacity:0; transform:translateY(10px);}
    to{opacity:1; transform:translateY(0);}
}

/* ===== Grid ===== */
.form-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:22px;
}

.full{
    grid-column:1 / -1;
}

/* ===== Labels ===== */
label{
    font-size:14px;
    font-weight:500;
    margin-bottom:6px;
    display:block;
    color: var(--text-muted);
}

/* ===== Inputs ===== */
input, textarea, select{
    width:100%;
    padding:14px 16px;
    border-radius:14px;
    border:1px solid var(--border);
    background:#1a2b35;
    color: var(--text);
    font-size:14px;
    transition:all .3s ease;
}

input:hover, textarea:hover, select:hover{
    background:#22303c;
    border-color: var(--primary);
}

input:focus, textarea:focus, select:focus{
    outline:none;
    border-color: var(--primary);
    box-shadow:0 0 0 4px rgba(0,230,230,.2);
    transform:translateY(-1px);
}

input[type=file]{
    padding:12px;
}

/* ===== Button ===== */
button{
    margin-top:30px;
    width:100%;
    padding:16px;
    border:none;
    border-radius:16px;
    background:linear-gradient(135deg, var(--primary), var(--primary-dark));
    color:#fff;
    font-size:16px;
    font-weight:500;
    cursor:pointer;
    transition:all .35s ease;
}

button:hover{
    transform:translateY(-3px);
    box-shadow:0 18px 30px rgba(0,230,230,.4);
}

button:active{
    transform:scale(.98);
}

/* ===== Highlights spacing ===== */
.highlight-group input{
    margin-bottom:10px;
}

/* ===== Mobile ===== */
@media(max-width:768px){
    body{
        padding:14px;
    }

    .wrapper{
        padding:26px 20px;
        border-radius:18px;
    }

    h2{
        font-size:22px;
    }

    .form-grid{
        grid-template-columns:1fr;
        gap:18px;
    }

    button{
        font-size:15px;
        padding:14px;
    }
}

</style>

</head>
<body>
<div class="wrapper">
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
<input type="text" name="title" required minlength="5" maxlength="150" required>
</div>

<div class="full">
<label>Description *</label>
<textarea name="description" required minlength="20" required></textarea>
</div>

<div>
<label>Date *</label>
<input type="date" name="event_date" required min="<?= date('Y-m-d'); ?>" required>
</div>

<div>
<label>Time *</label>

<input type="time" name="event_time" required>
</div>

<div class="full">
<label>Location *</label>
<input type="text" name="location" required minlength="3" required>
</div>

<div>
<label>Category *</label>
<select name="category" required>
  <option value="">Select</option>
  <option value="Music">Music</option>
  <option value="Technology">Technology</option>
  <option value="Sports">Sports</option>
  <option value="Education">Education</option>
  <option value="Business">Business</option>
</select>
</div>

<div>
<label>Price (₹)</label>
<input type="number" name="price" value="0">
</div>

<div>
    <label>Total Guest Slots *</label>
    <input type="number" name="total_slots" min="1" required>
</div>

<div class="full">
<label>Main Image</label>
<input type="file" name="image" required>
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
