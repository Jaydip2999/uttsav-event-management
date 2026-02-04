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

    /* ================= ID PROOF ================= */
    if($error_msg == ""){
        if(!empty($_FILES['id_proof']['name'])){
            if(!is_dir('uploads/id_proofs')){
                mkdir('uploads/id_proofs',0777,true);
            }
            $id_path = 'uploads/id_proofs/'.time().'_'.$_FILES['id_proof']['name'];
            move_uploaded_file($_FILES['id_proof']['tmp_name'],$id_path);
        } else {
            $error_msg = "Please upload ID Proof.";
        }
    }

    /* ================= PROFILE PIC ================= */
    $profile_path = "";
    if($error_msg == "" && !empty($_FILES['profile_pic']['name'])){
        if(!is_dir('uploads/profile_pics')){
            mkdir('uploads/profile_pics',0777,true);
        }
        $profile_path = 'uploads/profile_pics/'.time().'_'.$_FILES['profile_pic']['name'];
        move_uploaded_file($_FILES['profile_pic']['tmp_name'],$profile_path);
    }

    /* ================= INSERT ================= */
    if($error_msg == ""){
        $sql = "INSERT INTO organizers
        (user_id, full_name, mobile, email, id_proof, address, profile_pic, company_name, gst_number, website, status)
        VALUES
        ($user_id,'$full_name','$mobile','$email','$id_path','$address','$profile_path','$company_name','$gst_number','$website','pending')";

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
  background: linear-gradient(135deg,#e6ecf3,#f8fafc);
  display:flex;
  justify-content:center;
  align-items:center;
  min-height:100vh;
}

.form-container{
    background: #fff;
    padding: 40px 35px;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 550px;
    transition: all 0.3s ease;
    margin:30px 0px;
}
.form-container:hover{
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}
.form-container h2{
    text-align:center;
    margin-bottom:25px;
    font-size:2rem;
    color:#333;
    position: relative;
}
.alert {
    padding: 10px 15px;
    margin-bottom: 15px;
    border-radius: 5px;
    font-weight: bold;
}
.alert.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.alert.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
.form-container h2::after{
    content:'';
    width:60px;
    height:3px;
    background:#4a90e2;
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
    color:#555;
}
.form-group input,
.form-group textarea{
    width: 100%;
    padding:12px 15px;
    border-radius:10px;
    border:1px solid #ccc;
    font-size:14px;
    outline:none;
    transition: 0.3s;
}
.form-group input:focus,
.form-group textarea:focus{
    border-color:#4a90e2;
    box-shadow: 0 0 8px rgba(74,144,226,0.2);
}
input[type="file"]{padding:6px;}
button{
    width:100%;
    padding:14px;
    background: linear-gradient(90deg,#4a90e2,#357abd);
    border:none;
    color:#fff;
    font-size:16px;
    font-weight:500;
    border-radius:12px;
    cursor:pointer;
    transition:0.3s;
}
button:hover{
    background: linear-gradient(90deg,#357abd,#2a5a9e);
}
i{
  margin-right:8px;
  color:#4a90e2;
}

/* ===== CHECKBOX GROUP ===== */
.checkbox-group {
    display: flex;
    align-items: center;
    gap: 15px; 
    font-size: 14px;
    margin-bottom: 25px; 
}

.checkbox-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #4a90e2;
    cursor: pointer;
}

.checkbox-group label {
    line-height: 1.4;
    cursor: pointer;
}

.checkbox-group a {
    color: #4a90e2;
    text-decoration: none;
    font-weight: 500;
}

.checkbox-group a:hover {
    text-decoration: underline;
}

a{
  text-decoration:none;
  color:#4a90e2;
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
            <label><i class="fa-solid fa-id-card"></i> ID Proof (PDF/JPG/PNG)</label>
            <input type="file" name="id_proof" accept=".jpg,.png,.pdf" required>
        </div>
        <div class="form-group">
            <label><i class="fa-solid fa-location-dot"></i> Address</label>
            <textarea name="address" rows="2" placeholder="Enter address"></textarea>
        </div>
        <div class="form-group">
            <label><i class="fa-solid fa-image"></i> Profile Picture</label>
            <input type="file" name="profile_pic" accept=".jpg,.png">
        </div>
        <div class="form-group">
            <label><i class="fa-solid fa-building"></i> Company / Group Name</label>
            <input type="text" name="company_name" placeholder="Enter company/group name">
        </div>
        <div class="form-group">
            <label><i class="fa-solid fa-file-invoice-dollar"></i> GST Number</label>
            <input type="text" name="gst_number" placeholder="Enter GST number">
        </div>
        <div class="form-group">
            <label><i class="fa-solid fa-globe"></i> Website / Social Link</label>
            <input type="url" name="website" placeholder="Enter website or social link">
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
