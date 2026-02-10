<?php
session_start();

$success_msg = "";
$error_msg   = "";

$conn = mysqli_connect("localhost","root","","event_management");
if(!$conn){
    die("DB Connection Failed");
}

// 🔥 USER MUST BE LOGGED IN
if(!isset($_SESSION['user_id'])){
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$email   = $_SESSION['email'];

if(isset($_POST['submit'])){

    $full_name    = mysqli_real_escape_string($conn,$_POST['full_name']);
    $mobile       = mysqli_real_escape_string($conn,$_POST['mobile']);
    $address      = mysqli_real_escape_string($conn,$_POST['address'] ?? '');
    $company_name = mysqli_real_escape_string($conn,$_POST['company_name'] ?? '');
    $gst_number   = mysqli_real_escape_string($conn,$_POST['gst_number'] ?? '');
    $website      = mysqli_real_escape_string($conn,$_POST['website'] ?? '');

    /* ✅ DUPLICATE CHECK (USER BASED) */
    $check = mysqli_query(
        $conn,
        "SELECT id FROM organizers WHERE user_id=$user_id LIMIT 1"
    );

    if(mysqli_num_rows($check) > 0){
        $error_msg = "You have already applied for organizer.";
    }

  $profile_path = "";
if(!empty($_FILES['profile_pic']['name'])){
    $uploadDir = __DIR__ . "/uploads/profile_pics/"; 

    if(!is_dir($uploadDir)) mkdir($uploadDir,0777,true);

    $profile_path = time().'_'.basename($_FILES['profile_pic']['name']);
    move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadDir.$profile_path);
}

// INSERT into DB
$sql = "INSERT INTO organizers (user_id, full_name, mobile, email, profile_pic, company_name, gst_number, website, address, status)
        VALUES ($user_id,'$full_name','$mobile','$email','$profile_path','$company_name','$gst_number','$website','$address','pending')";

    /* ================= INSERT ================= */
    if($error_msg == ""){
        $sql = "INSERT INTO organizers
        (user_id, full_name, mobile, email, profile_pic,company_name ,gst_number,website,address,status)
        VALUES
        ($user_id,'$full_name','$mobile','$email','$profile_path','$company_name','$gst_number','$website','$address','pending')";

        if(mysqli_query($conn,$sql)){
            $success_msg = "Registration submitted successfully. Admin approval pending.";
        } else {
            $error_msg = "Database error.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Organizer Registration</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
/* ===== GLOBAL RESET ===== */
*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:'Poppins', sans-serif;
}

body{
  background: #0f2027; /* Dark background */
  display:flex;
  justify-content:center;
  align-items:center;
  min-height:100vh;
}

.close-btn{
  position: relative;
  top:-15px;
  right:-98%;
  width:36px;
  height:36px;
  color:#fff;
}

.form-container{
    background: #1f2a33; /* Dark card */
    padding: 40px 35px;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.5);
    width: 100%;
    max-width: 550px;
    transition: all 0.3s ease;
    margin:30px 0px;
}

.form-container:hover{
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.7);
}

.form-container h2{
    text-align:center;
    margin-bottom:25px;
    font-size:2rem;
    color:#00e6e6; /* Cyan heading */
    position: relative;
}

.alert {
    padding: 10px 15px;
    margin-bottom: 15px;
    border-radius: 5px;
    font-weight: bold;
}

.alert.success {
    background-color: rgba(0,230,230,0.2);
    color: #00e6e6;
    border: 1px solid #00b3b3;
}

.alert.error {
    background-color: rgba(255,0,0,0.2);
    color: #ff4d4d;
    border: 1px solid #ff4d4d;
}

.form-container h2::after{
    content:'';
    width:60px;
    height:3px;
    background:#00e6e6;
    display:block;
    margin:8px auto 0;
    border-radius:2px;
}

.form-group{
  margin-bottom:18px;
}

.form-group label{
    display:block;
    margin-bottom:6px;
    font-weight:500;
    color:#cbd5e1; /* Muted text */
}

.form-group input,
.form-group textarea{
    width: 100%;
    padding:12px 15px;
    border-radius:10px;
    border:1px solid #334454;
    background:#1a2b35; /* Dark input */
    color:#fff;
    font-size:14px;
    outline:none;
    transition: 0.3s;
}

.form-group input:focus,
.form-group textarea:focus{
    border-color:#00e6e6;
    box-shadow: 0 0 8px rgba(0,230,230,0.2);
}

input[type="file"]{
    padding:6px;
    background:#1a2b35;
    color:#fff;
}

button{
    width:100%;
    padding:14px;
    background: linear-gradient(90deg,#00e6e6,#00b3b3);
    border:none;
    color:#fff;
    font-size:16px;
    font-weight:500;
    border-radius:12px;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    background: linear-gradient(90deg,#00b3b3,#009999);
}

i{
  margin-right:8px;
  color:#00e6e6;
}

/* ===== CHECKBOX GROUP ===== */
.checkbox-group {
    display: flex;
    align-items: center;
    gap: 15px; 
    font-size: 14px;
    margin-bottom: 25px; 
    color:#cbd5e1;
}

.checkbox-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #00e6e6;
    cursor: pointer;
}

.checkbox-group label {
    line-height: 1.4;
    cursor: pointer;
}

.checkbox-group a {
    color: #00e6e6;
    text-decoration: none;
    font-weight: 500;
}

.checkbox-group a:hover {
    text-decoration: underline;
}

a{
  text-decoration:none;
  color:#00e6e6;
}
a:hover{
  text-decoration:underline;
}

@media(max-width:600px){
    .form-container{
      padding:30px 25px;
    }
    
    .checkbox-group {
        flex-direction: row;
        gap: 8px;
    }
    .form-container h2{
      font-size:20px;
    }
}


</style>
</head>

<body>

<div class="form-container">
    <a href="../index.php" class="close-btn">
  <i class="fa-solid fa-xmark"></i>
</a>

    <h2><i class="fa-solid fa-user-plus"></i> Organizer Registration</h2>
    <!-- SUCCESS / ERROR MESSAGE -->
    <?php if($success_msg != ""): ?>
        <div class="alert success"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if($error_msg != ""): ?>
        <div class="alert error"><?php echo $error_msg; ?></div>
    <?php endif; ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label><i class="fa-solid fa-user"></i> Full Name</label>
            <input type="text" name="full_name" placeholder="Enter full name" required>
        </div>
        <div class="form-group">
            <label><i class="fa-solid fa-phone"></i> Mobile Number</label>
            <input type="tel" name="mobile" placeholder="Enter mobile number" required>
        </div>
        <div class="form-group">
            <label><i class="fa-solid fa-envelope"></i> Email</label>
            <input type="email" name="email" placeholder="Enter email" required>
        </div>
         <div class="form-group">
            <label><i class="fa-solid fa-image"></i> Profile Picture</label>
            <input type="file" name="profile_pic" accept=".jpg,.png" required>
        </div>
          <div class="form-group">
            <label><i class="fa-solid fa-building"></i> Company / Group Name</label>
            <input type="text" name="company_name" placeholder="Enter company/group name" >
        </div>
         <div class="form-group">
            <label><i class="fa-solid fa-file-invoice-dollar"></i> GST Number</label>
            <input type="text" name="gst_number" placeholder="Enter GST number">
        </div>
        <div class="form-group">
            <label><i class="fa-solid fa-globe"></i> Website / Social Link</label>
            <input type="url" name="website" placeholder="Enter website or social link">
        </div>
        <div class="form-group">
            <label><i class="fa-solid fa-location-dot"></i> Address</label>
            <textarea name="address" rows="2" placeholder="Enter address" required></textarea>
        </div>
        <div class="form-group checkbox-group">
            <input type="checkbox" name="terms" required>
            <label>I agree to the <a href="#">Terms & Conditions</a></label>
        </div>
        <button type="submit" name="submit"><i class="fa-solid fa-paper-plane"></i> Submit</button>
    </form>
</div>

</body>
</html>
