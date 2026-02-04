<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
   <link rel="stylesheet" href="assets/style.css">

</head>
<body>
  <?php include "includes/header.php"; ?>
  <?php include "event/top_rate_card.php"; ?>
 
<section class="about-section" id="about">
  <div class="about-container">

    <div class="about-text">
      <h2 class="about-word">About <span>Our Event System</span></h2>
      <p>
        We provide a smart and digital way to manage, organize and execute
        events professionally. From planning to execution, everything is
        handled smoothly with our Event Management System.
      </p>

      <ul>
        <li><i class="fa-solid"></i> Easy Event Planning</li>
        <li><i class="fa-solid"></i> User Friendly Dashboard</li>
        <li><i class="fa-solid"></i> Secure & Reliable System</li>
      </ul>

      <button class="about-btn">Learn More</button>
    </div>

    <div class="about-image">
      <img src="assets\images\about_logo.jpeg" alt="About Event">
    </div>

  </div>
</section>

<section class="services-section" id="services">
  <div class="services-container">

    <h2 class="services-title">Our <span>Services</span></h2>
    <p class="services-subtitle">
      We offer complete digital solutions to plan, manage and execute events
      efficiently and professionally.
    </p>

    <div class="services-cards">

      <div class="service-card">
        <i class="fa-solid fa-calendar-days"></i>
        <h3>Event Planning</h3>
        <p>Plan events digitally with schedules, tasks and timelines.</p>
      </div>

      <div class="service-card">
        <i class="fa-solid fa-users"></i>
        <h3>Guest Management</h3>
        <p>Manage guest lists, invitations and confirmations easily.</p>
      </div>

      <div class="service-card">
        <i class="fa-solid fa-wallet"></i>
        <h3>Budget Management</h3>
        <p>Track expenses and manage event budgets efficiently.</p>
      </div>

      <div class="service-card">
        <i class="fa-solid fa-location-dot"></i>
        <h3>Venue Management</h3>
        <p>Manage venues, locations and event logistics.</p>
      </div>

      <div class="service-card">
        <i class="fa-solid fa-chart-line"></i>
        <h3>Analytics & Reports</h3>
        <p>Get detailed reports and insights for better decisions.</p>
      </div>

      <div class="service-card">
        <i class="fa-solid fa-headset"></i>
        <h3>Customer Support</h3>
        <p>24/7 support to help you at every stage of your event.</p>
      </div>
    </div>
  </div>
</section>


<?php include "includes/footer.php"; ?>

<!-- JS Flag for login -->
<script>
    window.USER_LOGGED_IN = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
</script>
<script src="assets/script.js"></script>
</body>
</html>
