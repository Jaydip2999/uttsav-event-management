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
    $form_email   = mysqli_real_escape_string($conn,$_POST['email']);
    $address      = mysqli_real_escape_string($conn,$_POST['address'] ?? '');
    $company_name = mysqli_real_escape_string($conn,$_POST['company_name'] ?? '');
    $gst_number   = mysqli_real_escape_string($conn,$_POST['gst_number'] ?? '');
    $website      = mysqli_real_escape_string($conn,$_POST['website'] ?? '');

    /* ✅ MOBILE VALIDATION (10 DIGITS ONLY) */
    if(!preg_match('/^[0-9]{10}$/', $mobile)){
        $error_msg = "Mobile number must be exactly 10 digits.";
    }

    else{

        /* CHECK EXISTING ORGANIZER RECORD */
        $check = mysqli_query(
            $conn,
            "SELECT id,status FROM organizers WHERE user_id=$user_id LIMIT 1"
        );

        if(mysqli_num_rows($check) > 0){

            $existing = mysqli_fetch_assoc($check);

            if($existing['status'] == 'pending'){
                $error_msg = "Your previous request is still pending.";
            }
            elseif($existing['status'] == 'approved'){
                $error_msg = "You are already an approved organizer.";
            }
            elseif($existing['status'] == 'rejected'){

                // 🔥 UPDATE INSTEAD OF INSERT
                $sql = "UPDATE organizers SET
                        full_name='$full_name',
                        mobile='$mobile',
                        email='$form_email',
                        company_name='$company_name',
                        gst_number='$gst_number',
                        website='$website',
                        address='$address',
                        status='pending'
                        WHERE user_id=$user_id";

                if(mysqli_query($conn,$sql)){
                    $success_msg = "Re-application submitted successfully. Admin approval pending.";
                } else {
                    $error_msg = "Database error.";
                }

            }

        } else {

            /* ================= PROFILE UPLOAD ================= */
            $profile_path = "";
            if(!empty($_FILES['profile_pic']['name'])){
                $uploadDir = __DIR__ . "/uploads/profile_pics/";
                if(!is_dir($uploadDir)) mkdir($uploadDir,0777,true);

                $profile_path = time().'_'.basename($_FILES['profile_pic']['name']);
                move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadDir.$profile_path);
            }

            /* ================= INSERT ================= */
            $sql = "INSERT INTO organizers
            (user_id, full_name, mobile, email, profile_pic, company_name, gst_number, website, address, status)
            VALUES
            ($user_id,'$full_name','$mobile','$form_email','$profile_path','$company_name','$gst_number','$website','$address','pending')";

            if(mysqli_query($conn,$sql)){
                $success_msg = "Registration submitted successfully. Admin approval pending.";
            } else {
                $error_msg = "Database error.";
            }
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
<link rel="stylesheet" href="../assets/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<bod class="organizer-page">

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
            <label>Profile Picture</label>
             <div class="custom-file">
             <input type="file" name="profile_pic" id="profile_pic" accept="image/*" required>
             <label for="profile_pic" class="file-label">
            <i class="fa fa-upload"></i> <span>Select profile image</span>
             </label>
        </div>
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

<script>
document.getElementById("profile_pic").addEventListener("change", function(){
  const fileName = this.files[0]?.name || "Select profile image";
  this.nextElementSibling.querySelector("span").innerText = fileName;
});
</script>

</body>
</html>
