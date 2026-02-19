<?php
include "includes/db.php";

// Form submit check
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO contact_messages (name, email, subject, message)
            VALUES ('$name', '$email', '$subject', '$message')";

    if($conn->query($sql)){
        echo "<script>
                alert('Message sent successfully!');
                window.location='index.php';
              </script>";
    }else{
        echo "Error: " . $conn->error;
    }
}
?>
