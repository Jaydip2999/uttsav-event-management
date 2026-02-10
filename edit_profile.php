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

<style>
:root{
  --primary:#00e6e6;
  --bg:#0f2027;
  --card:#112a33;
  --muted:#9fbfc7;
}
*{box-sizing:border-box;font-family:Poppins}
body{
  margin:0;
  background:radial-gradient(circle at top,var(--bg),#000);
  color:#fff;
}
.container{
  max-width:600px;
  margin:40px auto;
  padding:20px;
  animation:fade .8s ease;
}
@keyframes fade{
  from{opacity:0;transform:translateY(30px)}
  to{opacity:1}
}
.card{
  background:var(--card);
  border-radius:20px;
  padding:30px;
}
.card h2{
  margin-top:0;
}
label{
  font-size:13px;
  color:var(--muted);
}
input{
  width:100%;
  padding:12px 14px;
  margin:8px 0 18px;
  border-radius:12px;
  border:none;
  outline:none;
}
input[type=file]{
  background:#fff;
}
.btn{
  width:100%;
  padding:14px;
  border-radius:16px;
  border:none;
  background:linear-gradient(135deg,var(--primary),#00b3b3);
  font-weight:600;
  cursor:pointer;
  transition:.3s;
}
.btn:hover{
  transform:translateY(-2px);
  box-shadow:0 10px 25px rgba(0,230,230,.35);
}
.back{
  display:block;
  margin-top:18px;
  text-align:center;
  color:var(--primary);
  text-decoration:none;
}
</style>
</head>

<body>

<div class="container">
  <div class="card">
    <h2>Edit Profile</h2>

    <form method="post" enctype="multipart/form-data">

      <label>Full Name</label>
      <input type="text" name="full_name" value="<?= htmlspecialchars($org['full_name']) ?>" required>

      <label>Company Name</label>
      <input type="text" name="company_name" value="<?= htmlspecialchars($org['company_name']) ?>">

      <label>Mobile</label>
      <input type="text" name="mobile" value="<?= htmlspecialchars($org['mobile']) ?>">

      <label>Website</label>
      <input type="text" name="website" value="<?= htmlspecialchars($org['website']) ?>">

      <label>GST Number</label>
      <input type="text" name="gst_number" value="<?= htmlspecialchars($org['gst_number']) ?>">

      <label>Profile Picture</label>
      <input type="file" name="profile_pic">

      <button class="btn" name="update">Update Profile</button>
    </form>

    <a href="user_profile.php" class="back">← Back to Profile</a>
  </div>
</div>

</body>
</html>
