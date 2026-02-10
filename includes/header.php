    
    <header class="navbar">

    <a href="\php\Event-management-system\index.php"><div class="logo">
         <img src="/php/Event-management-system/assets/images/logo.png" alt="logo">

        </div></a>
        
    
        <nav class="nav-links" id="navLinks">
            <a href="\php\Event-management-system\index.php">Home</a>
            <a href="\php\Event-management-system\index.php#about">About</a>
            <a href="\php\Event-management-system\event\event_cards.php">Events</a>
            <a href="\php\Event-management-system\index.php#services">Services</a>
            <a href="\php\Event-management-system\index.php#contact">Contact</a>
        </nav>

        <div class="nav-icons">
            <i class="fa-regular fa-user" id="profileIcon"></i>
            <i class="fa-solid fa-bars" id="menuBtn"></i>
        </div>
    </header>

    <div class="profile-popup" id="profilePopup">
    <?php if(!empty($_SESSION['user_id'])): ?>

        <?php if($_SESSION['role'] === 'admin'): ?>
            <a href="\php\Event-management-system\admin/admin_dashboard.php">Admin Dashboard</a>

        <?php else : ?>
            <a href="/php/event-management-system/user_profile.php">
                <i class="fa-regular fa-user"></i>
                <?php echo $_SESSION['user_name'];?>
            </a>

            <?php if($_SESSION['role'] !== 'organizer'): ?>
                <a href="\php\Event-management-system\organizer/organizer_form.php" class="organizer_form_btn">
                    <i class="fa-solid fa-user-plus"></i> Become Organizer
                </a>
            <?php else: ?>
                <a href="\php\Event-management-system\event/event_form.php" class="organizer_form_btn">
                    <i class="fa-solid fa-calendar-plus"></i> Add Event
                </a>
            <?php endif; ?>

        <?php endif; ?>
    
        <a href="\php\Event-management-system\logout.php" class="logout-btn">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
        </a>

    <?php else: ?>

        <a href="/php/Event-management-system/auth/login.php">Log In</a>

    <?php endif; ?>

    </div>

    