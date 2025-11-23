<?php
// Unified Navbar Component
// This navbar works on ALL pages and includes login/signup modals

// Determine path prefix based on current directory
// If we're in admin/ subdirectory, we need ../ prefix
$currentDir = dirname($_SERVER['PHP_SELF']);
$isAdminDir = strpos($currentDir, '/admin') !== false || basename(dirname($_SERVER['SCRIPT_FILENAME'])) === 'admin';
$pathPrefix = $isAdminDir ? '../' : '';

// Helper function to get correct path
function getNavPath($path) {
    global $pathPrefix;
    return $pathPrefix . $path;
}

// Get user house logo if logged in
$houseLogo = null;
if (isLoggedIn() && isset($_SESSION['house_logo'])) {
    $houseLogo = $_SESSION['house_logo'];
} elseif (isLoggedIn()) {
    // Try to get from database
    require_once __DIR__ . '/houses.php';
    $userHouse = getUserHouse($_SESSION['user_id']);
    if ($userHouse && isset(HOUSES[$userHouse])) {
        $houseLogo = HOUSES[$userHouse]['logo'];
        $_SESSION['house_logo'] = $houseLogo;
        $_SESSION['house'] = $userHouse;
    }
}
?>
<nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
    <div class="container">
        <a class="navbar-brand" href="<?php echo getNavPath('index.php'); ?>">
            <?php if ($houseLogo): ?>
                <span style="font-size: 1.5rem; margin-right: 8px;"><?php echo $houseLogo; ?></span>
            <?php endif; ?>
            <i class="fas fa-tools"></i> fixIT
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto" id="navMenu">
                <?php if (!isLoggedIn()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo getNavPath('index.php'); ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo getNavPath('about.php'); ?>">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo getNavPath('contact.php'); ?>">Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo getNavPath('blog.php'); ?>">Make Your City Better</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="openLoginModal(); return false;">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="openSignupModal(); return false;">Sign Up</a></li>
                <?php elseif (isAdmin()): ?>
                    <!-- Admin Navbar -->
                    <li class="nav-item"><a class="nav-link" href="<?php echo getNavPath('index.php'); ?>">Home (Map)</a></li>
                    <?php if ($isAdminDir): ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard.php">Admin Dashboard</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="admin/dashboard.php">Admin Dashboard</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo getNavPath('manage_users.php'); ?>">Manage Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo getNavPath('blog.php'); ?>">Make Your City Better</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo getNavPath('notifications.php'); ?>">Notifications</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo getNavPath('profile.php'); ?>">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="logout(); return false;">Logout</a></li>
                <?php else: ?>
                    <!-- User Navbar -->
                    <li class="nav-item"><a class="nav-link" href="<?php echo getNavPath('index.php'); ?>">Home (Map)</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo getNavPath('report.php'); ?>">Report an Issue</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo getNavPath('blog.php'); ?>">Make Your City Better</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo getNavPath('my-reports.php'); ?>">My Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo getNavPath('notifications.php'); ?>">Notifications</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo getNavPath('profile.php'); ?>">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="logout(); return false;">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Login Modal - Available on ALL pages -->
<div class="modal-overlay" id="loginModal" style="display: none;">
    <div class="glassmorphism-modal">
        <span class="close-modal" onclick="closeLoginModal()">&times;</span>
        <h2 class="modal-title">Welcome Back</h2>
        <form id="loginForm" onsubmit="handleLogin(event)">
            <div class="form-group">
                <label><i class="fas fa-user"></i> Username or Email</label>
                <input type="text" id="loginUsername" class="form-control" placeholder="Enter your username or email" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="loginPassword" class="form-control" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-map-marker-alt"></i> Location (Optional)</label>
                <select id="loginState" class="form-control">
                    <option value="">Select State/Region</option>
                    <option value="Kosovo">Kosovo</option>
                    <option value="Albania">Albania</option>
                    <option value="North Macedonia">North Macedonia</option>
                    <option value="Serbia">Serbia</option>
                    <option value="Montenegro">Montenegro</option>
                </select>
                <select id="loginCity" class="form-control mt-2" disabled>
                    <option value="">Select City</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
            <p style="text-align: center; margin-top: 20px; color: var(--text-muted);">
                Don't have an account? <a href="#" onclick="closeLoginModal(); openSignupModal(); return false;" style="color: var(--primary); text-decoration: none;">Sign Up</a>
            </p>
        </form>
    </div>
</div>

<!-- Signup Modal - Available on ALL pages -->
<div class="modal-overlay" id="signupModal" style="display: none;">
    <div class="glassmorphism-modal" style="max-width: 550px;">
        <span class="close-modal" onclick="closeSignupModal()">&times;</span>
        <h2 class="modal-title">Join fixIT</h2>
        <form id="signupForm" onsubmit="handleSignup(event)">
            <div class="form-group">
                <label><i class="fas fa-map-marker-alt"></i> Location</label>
                <select id="signupState" class="form-control" required>
                    <option value="">Select State/Region</option>
                    <option value="Kosovo">Kosovo</option>
                    <option value="Albania">Albania</option>
                    <option value="North Macedonia">North Macedonia</option>
                    <option value="Serbia">Serbia</option>
                    <option value="Montenegro">Montenegro</option>
                </select>
                <select id="signupCity" class="form-control mt-2" required disabled>
                    <option value="">Select City</option>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> First Name</label>
                        <input type="text" id="signupName" class="form-control" placeholder="First name" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Last Name</label>
                        <input type="text" id="signupSurname" class="form-control" placeholder="Last name" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label><i class="fas fa-at"></i> Username</label>
                <input type="text" id="signupUsername" class="form-control" placeholder="Choose a username" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="email" id="signupEmail" class="form-control" placeholder="your.email@example.com" required>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="signupPassword" class="form-control" placeholder="Create a strong password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
            <p style="text-align: center; margin-top: 20px; color: var(--text-muted);">
                Already have an account? <a href="#" onclick="closeSignupModal(); openLoginModal(); return false;" style="color: var(--primary); text-decoration: none;">Login</a>
            </p>
        </form>
    </div>
</div>
