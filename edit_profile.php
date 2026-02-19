<?php
session_start();

/* ===== DB CONNECTION ===== */
$conn = new mysqli("localhost","root","","event_management");
if($conn->connect_error) die("Database connection failed");

/* ===== AUTH CHECK ===== */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer'){
  header("Location: organizer/organizer_form.php");
  exit;
}

$user_id = $_SESSION['user_id'];

/* ===== FETCH ORGANIZER ===== */
$stmt = $conn->prepare("SELECT * FROM organizers WHERE user_id=?");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$org = $stmt->get_result()->fetch_assoc();
if(!$org) die("Organizer not found");

if(isset($_POST['update'])){
  $full_name = $_POST['full_name'];
  $company   = $_POST['company_name'];
  $mobile    = $_POST['mobile'];
  $website   = $_POST['website'];
  $gst       = $_POST['gst_number'];

  $profile_pic = $org['profile_pic']; // old image name

  if(!empty($_FILES['profile_pic']['name'])){
    // ✅ Correct upload folder relative to this file
    $uploadDir = __DIR__ . "../organizer/uploads/profile_pics/";  

    if(!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    // delete old image
    if(!empty($org['profile_pic']) && file_exists($uploadDir.$org['profile_pic'])){
        unlink($uploadDir.$org['profile_pic']);
    }

    // only image NAME
    $profile_pic = time().'_'.basename($_FILES['profile_pic']['name']);

    move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadDir.$profile_pic);
  }

  // ✅ Update DB with only file name
  $up = $conn->prepare("
    UPDATE organizers SET 
      full_name=?, company_name=?, mobile=?, website=?, gst_number=?, profile_pic=?
    WHERE user_id=?
  ");
  $up->bind_param("ssssssi", $full_name,$company,$mobile,$website,$gst,$profile_pic,$user_id);
  $up->execute();

  header("Location: user_profile.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Organizer Profile</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/style.css">
<link rel="icon" type="image/png" href="/php/event-management-system/assets/images/logo.png">
</head>
<body class="op-edit-page">

<div class="op-edit-container">
  <div class="op-edit-card">
    <h2 class="op-edit-title">Edit Profile</h2>

    <form method="post" enctype="multipart/form-data" class="op-edit-form">

      <div class="op-field">
        <label>Full Name</label>
        <input type="text" name="full_name"
          value="<?= htmlspecialchars($org['full_name']) ?>" required>
      </div>

      <div class="op-field">
        <label>Company Name</label>
        <input type="text" name="company_name"
          value="<?= htmlspecialchars($org['company_name']) ?>">
      </div>

      <div class="op-field">
        <label>Mobile</label>
        <input type="text" name="mobile"
          value="<?= htmlspecialchars($org['mobile']) ?>">
      </div>

      <div class="op-field">
        <label>Website</label>
        <input type="text" name="website"
          value="<?= htmlspecialchars($org['website']) ?>">
      </div>

      <div class="op-field">
        <label>GST Number</label>
        <input type="text" name="gst_number"
          value="<?= htmlspecialchars($org['gst_number']) ?>">
      </div>

      <!-- FILE UPLOAD -->
      <div class="op-field">
        <label>Profile Picture</label>
        <div class="op-file">
          <input type="file" name="profile_pic" id="opProfilePic" accept="image/*">
          <label for="opProfilePic" class="op-file-label">
            <i class="fa fa-upload"></i>
            <span>Select profile image</span>
          </label>
        </div>
      </div>

      <button class="op-edit-btn" name="update">Update Profile</button>
    </form>

    <a href="user_profile.php" class="op-edit-back">← Back to Profile</a>
  </div>
</div>

</body>

</html>
