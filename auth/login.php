<?php
session_start();
require "../includes/db.php";
$error = "";

if(isset($_POST['login'])){

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if($email=="" || $password==""){
        $error = "All fields are required";
    } else {

        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result) == 0){
            $error = "Email not registered";
        } else {
            $user = mysqli_fetch_assoc($result);
            //with hash password
            // if(!password_verify($password, $user['password'])){
            //    $error = "Incorrect password";
            //   }

            //without hash password
            if($password !== $user['password']){
                $error = "Incorrect password";
            } else {
                $_SESSION['user_name'] = $user['name'] or die("data not retrive");
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];

                if($user['role'] === 'admin'){
                    header("Location: ../admin/admin_dashboard.php");
                } else {
                    header("Location: ../index.php");
                }
                exit;
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
  <title> Login </title>

  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="auth.css">
</head>
<body>
<form action="login.php" method="post">
  <div class="login-wrapper">

    <!-- LEFT SIDE -->
    <div class="login-left">
      <img src="../assets/images/logo.png" alt="logo">
      <h2>Welcome Back!</h2>
      <p>Manage and organize your events professionally.</p>
    </div>

    <!-- RIGHT SIDE -->
    <div class="login-right">
      <h2>Login</h2>

      <div class="input-group">
        <i class="fa fa-envelope"></i>
        <input type="email" name="email" placeholder="Email address" required>
      </div>

      <div class="input-group">
        <i class="fa fa-lock"></i>
        <input type="password" name="password" placeholder="Password" required>
      </div>

      <button type="submit" class="login-btn" name="login">Login</button>

      <?php if($error != ""): ?>
        <p style="color:red; text-align:center; margin-bottom:10px;">
          <?php echo $error; ?>
        </p>
      <?php endif; ?>

      <div class="extra-links">
        <p>Don't have an account?
          <a href="register.php">Register</a>
        </p>
      </div>
    </div>

  </div>
</form>
</body>

</body>
</html>
