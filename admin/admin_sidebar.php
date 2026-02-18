 <div class="side-header">
    <span>ADMIN PANEL</span>
    <button class="menu-btn" onclick="toggleMenu()">
      <i class="fa-solid fa-bars"></i>
    </button>
  </div>
<div class="sidebar" id="sidebar">
  <span class="side-header">ADMIN PANEL</span>
<nav class="side-links">

  <a href="admin_dashboard.php">
    <i class="fa-solid fa-house"></i> Dashboard
  </a>
  
 <a href="admin_withdraw.php">
  <i class="fa-solid fa-wallet"></i> Admin Earnings
</a>

  <a href="organizers_list.php">
    <i class="fa-solid fa-user-tie"></i> Organizers
  </a>

  <a href="withdraw.php">
    <i class="fa-solid fa-wallet"></i> Withdraw Requests
  </a>

  <a href="event_pending_list.php">
    <i class="fa-solid fa-calendar-days"></i>Manage  Events
  </a>

  <a href="manage_bookings.php">
    <i class="fa-solid fa-money-check-dollar"></i> Event Payment Request
  </a>

  <a href="users.php">
    <i class="fa-solid fa-user-group"></i> Users
  </a>

  <a href="audit_logs.php">
    <i class="fa-solid fa-clipboard-list"></i> Logs
  </a>

  <a href="logout.php" class="logout">
    <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
  </a>

</nav>

</div>

<div class="overlay" id="overlay" onclick="toggleMenu()"></div>

<script>
function toggleMenu(){
  document.getElementById("sidebar").classList.toggle("active");
  document.getElementById("overlay").classList.toggle("active");
}
</script>
