<style>
    /* --- INLINED HEADER STYLES --- */
    .header-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ddd;
    }
    .user-profile-menu {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    #profileBtn {
        background-color: #fff;
        color: #333;
        font-size: 1em;
        border: 1px solid #ddd;
        cursor: pointer;
        border-radius: 50px;
        font-family: 'Mada', sans-serif;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        height: 42px;
        padding: 0 15px 0 5px;
    }
    .notification-button {
        background-color: transparent;
        border: 1px solid #ddd;
        color: #333;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        cursor: pointer;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }
    .notification-button .fa-bell { font-size: 1.1em; }
    .notification-badge { position: absolute; top: 0px; right: 0px; background-color: #e74c3c; color: white; font-size: 0.7rem; border-radius: 50%; padding: 3px 6px; display: flex; justify-content: center; align-items: center; min-width: 18px; height: 18px; font-weight: bold; }
    #profileBtn:hover { background-color: #f5f5f5; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .profile-dropdown { position: relative; display: inline-block; }

    /* Dropdown Animation */
    #profileDropdownContent, .notification-dropdown-content { 
        display: block; position: absolute; background-color: #ffffff; min-width: 180px; 
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.15); z-index: 2000; right: 0; 
        border: 1px solid #eee; border-radius: 8px; margin-top: 8px; overflow: hidden;
        opacity: 0; visibility: hidden; transform: translateY(10px);
        transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s;
    }
    #profileDropdownContent.show-dropdown, .notification-dropdown-content.show {
        opacity: 1; visibility: visible; transform: translateY(0);
    }
    .notification-dropdown-content { min-width: 320px; max-width: 350px; padding: 0; }
    #profileDropdownContent a { color: black; padding: 12px 16px; text-decoration: none; display: block; font-size: 1em; }
    #profileDropdownContent a:hover { background-color: #f1f1f1; }
    .notification-header { padding: 12px 16px; font-weight: bold; font-size: 1.1em; color: #333; border-bottom: 1px solid #eee; }
    .notification-body { max-height: 300px; overflow-y: auto; }
    .notification-item { display: flex; align-items: center; padding: 12px 16px; border-bottom: 1px solid #f0f0f0; transition: background-color 0.2s ease; text-decoration: none; color: inherit; }
    .no-notifications { text-align: center; color: #777; padding: 20px; font-size: 0.9em; }

    /* Mobile Header Controls */
    .mobile-header-controls { display: flex; align-items: center; gap: 10px; }
    .no-scroll { overflow: hidden; }
    .mobile-nav-toggle { display: none; background: none; border: none; cursor: pointer; z-index: 2001; padding: 10px; }
    .mobile-nav-toggle span { display: block; width: 25px; height: 3px; background-color: #333; margin: 5px 0; transition: transform 0.3s ease, opacity 0.3s ease; }
    .mobile-nav-toggle.active span:nth-child(1) { transform: translateY(8px) rotate(45deg); }
    .mobile-nav-toggle.active span:nth-child(2) { opacity: 0; }
    .mobile-nav-toggle.active span:nth-child(3) { transform: translateY(-8px) rotate(-45deg); }
    
    @media (max-width: 992px) {
        .header-content { padding: 0 15px; }
        .logo { margin-right: auto; }
        
        /* Slide-in Menu from Right */
        .main-nav { 
            position: fixed; top: 0; right: 0; transform: translateX(100%);
            width: 75%; max-width: 320px; height: 100vh; 
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
            z-index: 2000; padding-top: 100px; display: flex; 
            flex-direction: column; justify-content: flex-start; 
            transition: transform 0.5s cubic-bezier(0.23, 1, 0.32, 1);
        }
        .main-nav.nav-open { transform: translateX(0); box-shadow: -5px 0 15px rgba(0,0,0,0.1); }
        .main-nav ul { flex-direction: column; align-items: center; gap: 10px; width: 100%; padding: 15px 0; }
        .main-nav ul li { width: 100%; text-align: center; opacity: 0; transform: translateX(20px); animation: fadeInRight 0.5s ease forwards; }
        .main-nav.nav-open ul li:nth-child(1) { animation-delay: 0.2s; }
        .main-nav.nav-open ul li:nth-child(2) { animation-delay: 0.3s; }
        .main-nav.nav-open ul li:nth-child(3) { animation-delay: 0.4s; }
        .main-nav.nav-open ul li:nth-child(4) { animation-delay: 0.5s; }
        .main-nav.nav-open ul li:nth-child(5) { animation-delay: 0.6s; }
        .main-nav.nav-open ul li:nth-child(6) { animation-delay: 0.7s; }
        @keyframes fadeInRight { to { opacity: 1; transform: translateX(0); } }
        .main-nav ul li a { padding: 15px 0; width: 100%; display: block; font-size: 1.5em; font-weight: 600; }
        .mobile-nav-toggle { display: block; }
        
        .signin-button .desktop-text { display: none; }
        .signin-button { width: 45px; height: 45px; padding: 0; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .signin-button .mobile-icon { display: block !important; font-size: 1.5em; }
        
        #profileBtn .username-text, #profileBtn .fa-caret-down { display: none; }
        #profileBtn { padding: 0; width: 42px; justify-content: center; }
    }
</style>

<header class="main-header">
    <div class="header-content">
        <div class="logo">
            <div class="logo-main-line">
                <span>Tavern Publico</span>
            </div>
            <span class="est-year">EST ★ 2024</span>
        </div>

        <nav class="main-nav">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="menu.php">Menu</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="events.php">Events</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="about.php">About</a></li>
            </ul>
        </nav>

        <div class="header-right">
            <?php
            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                $avatar_path = isset($_SESSION['avatar']) && file_exists($_SESSION['avatar']) ? $_SESSION['avatar'] : 'images/default_avatar.png';

                echo '<div class="user-profile-menu">';
                echo '  <div class="profile-dropdown">';
                echo '    <button id="profileBtn" class="profile-button">';
                echo '      <img src="' . htmlspecialchars($avatar_path) . '" alt="My Avatar" class="header-avatar">';
                echo '      <span class="username-text">' . htmlspecialchars($_SESSION['username']) . '</span>';
                echo '      <i class="fas fa-caret-down" style="font-size: 0.8em; margin-left: 5px;"></i>';
                echo '    </button>';
                echo '    <div id="profileDropdownContent">';
                echo '      <a href="profile.php">My Profile</a>';
                echo '      <a href="logout.php">Logout</a>';
                echo '    </div>';
                echo '  </div>';
                echo '  <div class="notification-dropdown">';
                echo '      <button class="notification-button" id="notificationBtn">';
                echo '          <i class="fas fa-bell"></i>';
                echo '          <span class="notification-badge" id="notificationCount" style="display: none;">0</span>';
                echo '      </button>';
                echo '      <div class="notification-dropdown-content" id="notificationDropdownContent"></div>';
                echo '  </div>';
                echo '</div>';
            } else {
                echo '<a href="#" class="btn header-button signin-button" id="openModalBtn"><span 
                class="desktop-text">Sign In/Sign Up</span><i class="fas fa-user-circle mobile-icon" style="display: none;"></i></a>';
            }
            ?>
        </div>
        
        <button class="mobile-nav-toggle" aria-label="Open navigation menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Elements ---
    const profileButton = document.getElementById('profileBtn');
    const profileDropdown = document.getElementById('profileDropdownContent');
    const notificationButton = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdownContent');
    const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
    const mainNav = document.querySelector('.main-nav');
    const headerRight = document.querySelector('.header-right');

    // --- Active Nav Link Logic ---
    const navLinks = document.querySelectorAll('.main-nav a');
    const currentPageFile = window.location.pathname.split('/').pop();
    navLinks.forEach(link => {
        const linkFile = link.getAttribute('href').split('/').pop();
        if (linkFile === currentPageFile || (currentPageFile === '' && linkFile === 'index.php')) {
            link.classList.add('active-nav-link');
        }
    });

    // --- Mobile Nav Logic ---
    if (mobileNavToggle) {
        mobileNavToggle.addEventListener('click', function() {
            mainNav.classList.toggle('nav-open');
            this.classList.toggle('active');
            document.body.classList.toggle('no-scroll');
        });
    }

    // --- Dropdown Logic ---
    if (profileButton) {
        profileButton.addEventListener('click', function(event) {
            event.stopPropagation();
            if(notificationDropdown) notificationDropdown.classList.remove('show');
            profileDropdown.classList.toggle('show-dropdown');
        });
    }

    if (notificationButton) {
        notificationButton.addEventListener('click', function(event) {
            event.stopPropagation();
            if(profileDropdown) profileDropdown.classList.remove('show-dropdown');
            notificationDropdown.classList.toggle('show');
        });
    }

    window.addEventListener('click', function() {
        if (profileDropdown && profileDropdown.classList.contains('show-dropdown')) {
            profileDropdown.classList.remove('show-dropdown');
        }
        if (notificationDropdown && notificationDropdown.classList.contains('show')) {
            notificationDropdown.classList.remove('show');
        }
    });

    if(profileDropdown) profileDropdown.addEventListener('click', e => e.stopPropagation());
    if(notificationDropdown) notificationDropdown.addEventListener('click', e => e.stopPropagation());

    // --- Notification Fetching Logic ---
    async function fetchNotifications() {
        if (!notificationButton) return;
        try {
            const response = await fetch('get_notifications.php');
            const data = await response.json();
            const notificationCountBadge = document.getElementById('notificationCount');
            
            if (data.success && data.notifications.length > 0) {
                notificationCountBadge.textContent = data.notifications.length;
                notificationCountBadge.style.display = 'flex';
                // ... (rest of your notification display logic)
            } else {
                notificationCountBadge.style.display = 'none';
            }
        } catch (error) { console.error('Error fetching notifications:', error); }
    }
    
    // ... (rest of your notification click logic)

    if (notificationButton) {
        fetchNotifications();
        setInterval(fetchNotifications, 60000);
    }
});
</script>