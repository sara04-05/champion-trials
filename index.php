<?php
require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>fixIT - Select your destination — for a better city.</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <?php include 'includes/theme-loader.php'; ?>
    <style>
        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }
    </style>
    
</head>
<body>
    <!-- Navigation Bar -->
 <!-- UNIFIED NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-tools"></i> fixIT
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto" id="navMenu">
                <?php if (!isLoggedIn()): ?>
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="blog.php">Make Your City Better</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="openLoginModal(); return false;">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="openSignupModal(); return false;">Sign Up</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="index.php">Home (Map)</a></li>
                    <li class="nav-item"><a class="nav-link" href="report.php">Report an Issue</a></li>
                    <li class="nav-item"><a class="nav-link" href="blog.php">Make Your City Better</a></li>
                    <li class="nav-item"><a class="nav-link" href="my-reports.php">My Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="notifications.php">Notifications</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="logout(); return false;">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>


    <!-- Full-Screen Map -->
    <div id="map" class="fullscreen-map"></div>
    
    <!-- Map Controls (shown when logged in) -->
    <?php if (isLoggedIn()): ?>
    <div class="map-controls">
        <h3>Filters</h3>
        <div class="filter-group">
            <label>Category</label>
            <select id="categoryFilter" class="form-control">
                <option value="all">All Categories</option>
                <option value="pothole">Pothole</option>
                <option value="broken_light">Broken Light</option>
                <option value="traffic">Traffic</option>
                <option value="trash">Trash</option>
                <option value="environmental">Environmental</option>
                <option value="safety">Safety</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Status</label>
            <select id="statusFilter" class="form-control">
                <option value="all">All Status</option>
                <option value="pending">Pending</option>
                <option value="in_progress">In Progress</option>
                <option value="fixed">Fixed</option>
            </select>
        </div>
        <div class="filter-group">
            <label>Urgency</label>
            <select id="urgencyFilter" class="form-control">
                <option value="all">All Urgency</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
            </select>
        </div>
        <button class="btn btn-primary btn-block mt-3" onclick="enableReportMode()">
            <i class="fas fa-plus"></i> Report Issue
        </button>
    </div>
    <?php endif; ?>
    
    <!-- Accessibility Controls -->
    <div class="accessibility-controls">
        <div class="font-size-controls">
            <button id="fontSizeDecrease" aria-label="Decrease Font Size">A-</button>
            <button id="fontSizeReset" aria-label="Reset Font Size">A</button>
            <button id="fontSizeIncrease" aria-label="Increase Font Size">A+</button>
        </div>
    </div>


    <!-- Login Modal -->
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
                    Don't have an account? <a href="#" onclick="closeLoginModal(); openSignupModal(); return false;" style="color: var(--primary-green); text-decoration: none;">Sign Up</a>
                </p>
            </form>
        </div>
    </div>

    <!-- Signup Modal -->
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
                    Already have an account? <a href="#" onclick="closeSignupModal(); openLoginModal(); return false;" style="color: var(--primary-green); text-decoration: none;">Login</a>
                </p>
            </form>
        </div>
    </div>

     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/map.js"></script>

<?php if (!isLoggedIn()): ?>
<div style="
    width: 100%;
    text-align: center;
    padding: var(--spacing-lg);
    margin-top: 20px;
    color: var(--text-primary);
    font-size: var(--font-size-lg);
    background: var(--color-white);
    border-top: 2px solid var(--primary);
    box-shadow: var(--shadow-sm);
">
    Want a better experience with fixIT?  
    <a href="#" onclick="openSignupModal(); return false;" 
       style="color: var(--primary); font-weight: bold; text-decoration: none;">
        Sign up now!
    </a>
</div>
<?php endif; ?>

</body>
</html>


