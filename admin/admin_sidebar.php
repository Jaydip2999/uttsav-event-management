 <div class="side-header">
    <span>ADMIN PANEL</span>
    <button class="menu-btn" onclick="toggleMenu()">
      <i class="fa-solid fa-bars"></i>
    </button>
  </div>
<div class="sidebar" id="sidebar">
  <span class="side-header">ADMIN PANEL</span>
  <nav class="side-links">
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="organizers_list.php">Organizers</a>
    <a href="event_pending_list.php">Events</a>
    <a href="users.php">Users</a>
    <a href="audit_logs.php">Logs</a>
    <a href="logout.php" class="logout">Logout</a>
  </nav>

</div>

<div class="overlay" id="overlay" onclick="toggleMenu()"></div>

<script>
function toggleMenu(){
  document.getElementById("sidebar").classList.toggle("active");
  document.getElementById("overlay").classList.toggle("active");
}
</script>
