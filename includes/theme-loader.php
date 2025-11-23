<?php
// Global Theme Loader - Include this in all pages
if (!isset($userHouse)) {
    if (isLoggedIn()) {
        require_once __DIR__ . '/houses.php';
        $userHouse = getUserHouse($_SESSION['user_id']);
        
        // Update session if house exists in DB but not in session
        if ($userHouse && !isset($_SESSION['house'])) {
            $houseData = HOUSES[$userHouse];
            $_SESSION['house'] = $userHouse;
            $_SESSION['house_logo'] = $houseData['logo'];
        }
    } else {
        $userHouse = null;
    }
}

// Output theme CSS if user has a house
if ($userHouse && isset(HOUSES[$userHouse])) {
    $houseData = HOUSES[$userHouse];
    $primary = $houseData['colors']['primary'];
    $secondary = $houseData['colors']['secondary'];
    $accent = $houseData['colors']['accent'];
    ?>
    <style>
    :root {
        --house-primary: <?php echo $primary; ?>;
        --house-secondary: <?php echo $secondary; ?>;
        --house-accent: <?php echo $accent; ?>;
    }
    
    /* Apply house theme colors */
    .navbar, .navbar-brand, .btn-primary, 
    .stat-value, .badge-icon, .feature-icon,
    .issue-status.fixed, .notification-item.unread,
    .profile-avatar, .house-logo {
        background-color: var(--house-primary) !important;
        border-color: var(--house-primary) !important;
    }
    
    .btn-primary:hover {
        background-color: <?php echo $primary; ?> !important;
        opacity: 0.9;
    }
    
    a, .nav-link:hover, .text-primary, 
    .stat-value, .badge-name, h1, h2, h3,
    .issue-title, .report-title {
        color: var(--house-primary) !important;
    }
    
    .border-primary, .feature-card:hover,
    .about-card:hover, .report-card:hover {
        border-color: var(--house-primary) !important;
    }
    
    .btn-primary {
        background: var(--house-primary) !important;
        color: var(--house-accent) !important;
    }
    
    .btn-primary:hover {
        background: var(--house-primary) !important;
        opacity: 0.85;
    }
    </style>
    <?php
}
?>

