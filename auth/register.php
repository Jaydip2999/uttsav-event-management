<?php
session_start();
require "../includes/db.php";

$error = "";
if(isset($_POST['register'])){
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    //for hash verify 
    // $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    //without hash verify
    $password = trim($_POST['password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');

    // VALIDATION
    if($name=="" || $email=="" || $password=="" || $confirm==""){
        $error = "All fields are required";
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Invalid email format";
    }
    elseif(strlen($password) < 6){
        $error = "Password must be at least 6 characters";
    }
    elseif($password !== $confirm){
        $error = "Passwords do not match";
    }
    else{
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
        if(mysqli_num_rows($check) > 0){
            $error = "Email already registered!";
        } else {
            $sql = "INSERT INTO users(name,email,password)
                    VALUES('$name','$email','$password')";
            $_SESSION['user_name'] = $name;
            if(mysqli_query($conn, $sql)){
                header("Location: login.php");
                exit;
            } else {
                $error = "Something went wrong";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Regiser-page</title>
 
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="auth.css">
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <form action="register.php" method="post">
  <div class="auth-wrapper">
  <!-- LEFT SIDE  -->
  <div class="auth-left">
      <img src="../assets/images/logo.png" alt="logo">
    <h2>Create Account</h2>
    <p>Join us to manage and organize events professionally.</p>
  </div>

  <!-- RIGHT SIDE -->
  <div class="auth-right">
    <h2>Register</h2>

    <form action="register.php" method="post">
      <div class="input-group">
        <i class="fa fa-user"></i>
        <input type="text" name="name" placeholder="Full Name" required>
      </div>

      <div class="input-group">
        <i class="fa fa-envelope"></i>
        <input type="email" name="email" placeholder="Email Address" required>
      </div>

      <div class="input-group">
        <i class="fa fa-lock"></i>
        <input type="password" name="password" placeholder="Password" required>
      </div>

      <div class="input-group">
        <i class="fa fa-lock"></i>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      </div>

      <button type="submit" class="btn-primary" name="register">Create Account</button>
      <?php if($error != ""): ?>
    <p style="color:red; text-align:center; margin-bottom:10px;">
        <?php echo $error; ?>
    </p>
    <?php endif; ?>


    <div class="extra-links">
      <p>Already have an account?
        <a href="login.php">Login</a>
      </p>
    </div>
  </div>
</div>
</form>
  <script src="main.js"></script>
</body>
</html>
