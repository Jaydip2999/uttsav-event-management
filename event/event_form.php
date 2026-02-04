<?php
session_start();
require_once "../includes/db.php";

// ✅ Login check
if(!isset($_SESSION['user_id'])){
    header("Location: auth/login.php");
    exit;
}

// Fetch organizer info
$user_id = $_SESSION['user_id'];
$org_query = "SELECT * FROM organizers WHERE user_id='$user_id'";
$org_result = mysqli_query($conn, $org_query);
$organizer = mysqli_fetch_assoc($org_result);

$success = '';
$error = '';

// ✅ Handle form submission
if(isset($_POST['submit'])){

    $title = mysqli_real_escape_string($conn,$_POST['title']);
    $short_description = mysqli_real_escape_string($conn,$_POST['short_description']);
    $description = mysqli_real_escape_string($conn,$_POST['description']);
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = mysqli_real_escape_string($conn,$_POST['location']);
    $price = $_POST['price'] ?? 0;
    $highlight1 = mysqli_real_escape_string($conn,$_POST['highlight1']);
    $highlight2 = mysqli_real_escape_string($conn,$_POST['highlight2']);
    $highlight3 = mysqli_real_escape_string($conn,$_POST['highlight3']);
    $highlight4 = mysqli_real_escape_string($conn,$_POST['highlight4']);

    // ✅ Handle main image upload
    $image = '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $upload_dir = "assets/images/events/";
        if(!is_dir($upload_dir)) mkdir($upload_dir,0777,true);
        $image = time().'_'.basename($_FILES['image']['name']);
        $target_file = $upload_dir.$image;
        if(move_uploaded_file($_FILES['image']['tmp_name'],$target_file)){
            $image = $target_file;
        } else {
            $error = "Failed to upload main image!";
        }
    }

    if(empty($error)){
        $sql = "INSERT INTO events 
        (organizer_id,title,short_description,description,image,event_date,event_time,location,price,status)
        VALUES
        ('{$organizer['id']}','$title','$short_description','$description','$image','$event_date','$event_time','$location','$price','pending')";

        if(mysqli_query($conn,$sql)){
            $success = "Event submitted successfully! Waiting for admin approval.";
        } else {
            $error = "Database Error: ".mysqli_error($conn);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Submit New Event</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

/* ===== Page ===== */
body{
    background:linear-gradient(135deg,#eef2ff,#f4f7fb);
    color:#333;
    padding:40px;
}

/* ===== Form Card ===== */
form{
    max-width:800px;
    margin:auto;
    background:#fff;
    padding:32px;
    border-radius:16px;
    box-shadow:0 10px 30px rgba(0,0,0,.1);
    animation:fadeUp .6s ease;
}

/* ===== Heading ===== */
h2{
    margin-bottom:20px;
    color:#6366f1;
    font-weight:600;
    animation:slideDown .6s ease;
    margin-left:24%
}

/* ===== Inputs ===== */
input,
textarea,
select{
    width:100%;
    padding:12px 14px;
    margin:8px 0;
    border-radius:10px;
    border:1px solid #d1d5db;
    background:#f9fafb;
    transition:.3s ease;
}

/* ===== Focus Effect ===== */
input:focus,
textarea:focus,
select:focus{
    outline:none;
    background:#fff;
    border-color:#6366f1;
    box-shadow:0 0 0 3px rgba(99,102,241,.2);
    transform:scale(1.01);
}

/* ===== Button ===== */
button{
    padding:15px 25px;
    background:linear-gradient(135deg,#6366f1,#4f46e5);
    color:#fff;
    border:none;
    border-radius:12px;
    cursor:pointer;
    margin-top:15px;
    font-size:15px;
    font-weight:500;
    transition:.3s ease;
}

button:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(99,102,241,.4);
}

/* ===== Success / Error ===== */
.success{
    background:#dcfce7;
    color:#166534;
    padding:10px 14px;
    border-radius:8px;
    margin-bottom:12px;
    animation:fadeUp .4s ease;
}

.error{
    background:#fee2e2;
    color:#991b1b;
    padding:10px 14px;
    border-radius:8px;
    margin-bottom:12px;
    animation:fadeUp .4s ease;
}

/* ===== Animations ===== */
@keyframes fadeUp{
    from{
        opacity:0;
        transform:translateY(20px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

@keyframes slideDown{
    from{
        opacity:0;
        transform:translateY(-15px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* ===== Mobile ===== */
@media(max-width:768px){
    body{
        padding:20px;
    }
    form{
        padding:25px 20px;
    }
}
</style>

</head>
<body>

<h2>Submit New Event</h2>

<?php if($success) echo "<div class='success'>$success</div>"; ?>
<?php if($error) echo "<div class='error'>$error</div>"; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Event Title *</label>
    <input type="text" name="title" required>

    <label>Short Description *</label>
    <input type="text" name="short_description" maxlength="255" required>

    <label>Event Description *</label>
    <textarea name="description" rows="5" required></textarea>

    <label>Event Date *</label>
    <input type="date" name="event_date" required>

    <label>Event Time *</label>
    <input type="time" name="event_time" required>

    <label>Location *</label>
    <input type="text" name="location" required>

    <label>Price (₹)</label>
    <input type="number" name="price">

    <label>Main Event Image</label>
    <input type="file" name="image" accept="image/*">
     <!-- Event Category -->
    <select name="category" required>
        <option value="">Select Category</option>
        <option value="Music">Music</option>
        <option value="Technology">Technology</option>
        <option value="Sports">Sports</option>
        <option value="Education">Education</option>
        <option value="Business">Business</option>
        <option value="Other">Other</option>
    </select>
    <label>Highlight 1</label>
    <input type="text" name="highlight1">
    <label>Highlight 2</label>
    <input type="text" name="highlight2">
    <label>Highlight 3</label>
    <input type="text" name="highlight3">
    <label>Highlight 4</label>
    <input type="text" name="highlight4">

    <button type="submit" name="submit">Submit Event</button>
</form>

</body>
</html>
