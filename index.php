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
    <style>
        /* === GLOBAL BACKGROUND (exact same as About page) === */
body {
    background: linear-gradient(135deg, #000000 0%, #1a1a2e 50%, #000000 100%);
    position: relative;
}

body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background:
        radial-gradient(circle at 20% 30%, rgba(0, 255, 0, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(0, 0, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 50% 50%, rgba(255, 0, 0, 0.08) 0%, transparent 50%);
    pointer-events: none;
    z-index: 0;
}

/* === HERO SECTION (cloned from About hero) === */
.contact-hero {
    background: linear-gradient(135deg, rgba(0, 255, 0, 0.15) 0%, rgba(0, 0, 255, 0.15) 50%, rgba(255, 0, 0, 0.15) 100%);
    padding: 120px 20px 100px;
    text-align: center;
    margin-top: 70px;
    border-bottom: 3px solid #00ff00;
    position: relative;
    z-index: 1;
}

.contact-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background:
        repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255, 255, 255, 0.03) 10px, rgba(255, 255, 255, 0.03) 20px);
    pointer-events: none;
}

.contact-hero h1 {
    font-size: 4.5rem;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 20px;
    text-shadow:
        0 0 20px rgba(0, 255, 0, 0.5),
        0 0 40px rgba(0, 0, 255, 0.3),
        3px 3px 0px #000000;
}

.contact-hero p {
    font-size: 1.4rem;
    color: #ffffff;
    max-width: 700px;
    margin: 0 auto;
    line-height: 1.9;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
}

/* === CONTACT CONTAINER (same spacing) === */
.contact-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 60px 20px;
    position: relative;
    z-index: 1;
}

/* === CONTACT CARD (IDENTICAL to About page cards) === */
.contact-card {
    background: rgba(0, 0, 0, 0.7);
    border: 3px solid #ffffff;
    border-radius: 25px;
    padding: 50px;
    margin-bottom: 40px;
    box-shadow:
        0 10px 30px rgba(0, 0, 0, 0.5),
        0 0 20px rgba(0, 255, 0, 0.2),
        inset 0 0 20px rgba(255, 255, 255, 0.05);
    position: relative;
    overflow: hidden;
}

.contact-card::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background:
        radial-gradient(circle, rgba(0, 255, 0, 0.1) 0%, transparent 70%),
        radial-gradient(circle, rgba(0, 0, 255, 0.1) 0%, transparent 70%),
        radial-gradient(circle, rgba(255, 0, 0, 0.1) 0%, transparent 70%);
    animation: rotate 20s linear infinite;
    pointer-events: none;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* === CONTACT TITLE (same style as About section-title) === */
.contact-title {
    font-size: 2.8rem;
    font-weight: 800;
    color: #ffffff;
    text-align: center;
    text-shadow:
        2px 2px 0px #000000,
        0 0 15px rgba(0, 255, 0, 0.4);
    margin-bottom: 25px;
}

.section-subtitle {
    text-align: center;
    color: #ffffff;
    font-size: 1.2rem;
    margin-bottom: 40px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
}

/* === CONTACT ITEMS (same as Feature Cards style) === */
.contact-item {
    margin-bottom: 25px;
    padding: 25px;
    background: rgba(0, 0, 0, 0.55);
    border: 2px solid #ffffff;
    border-radius: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #ffffff;
    transition: all 0.4s ease;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
}

.contact-item i {
    font-size: 2rem;
    color: #00ff00;
    text-shadow: 0 0 15px rgba(0, 255, 0, 0.8);
}

.contact-item:hover {
    transform: translateY(-8px) scale(1.03);
    border-color: #00ff00;
    box-shadow:
        0 15px 35px rgba(0, 0, 0, 0.6),
        0 0 25px #00ff00;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .contact-hero h1 {
        font-size: 2.8rem;
    }
    .contact-hero p {
        font-size: 1.1rem;
    }
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
    padding: 25px 15px;
    margin-top: 20px;
    color: #ffffff;
    font-size: 1.25rem;
    text-shadow: 1px 1px 4px rgba(0,0,0,0.8);
    background: rgba(0, 0, 0, 0.6);
    border-top: 2px solid #00ff00;
    backdrop-filter: blur(6px);
">
    Want a better experience with fixIT?  
    <a href="#" onclick="openSignupModal(); return false;" 
       style="color: #00ff00; font-weight: bold; text-decoration: none;">
        Sign up now!
    </a>
</div>
<?php endif; ?>

</body>
</html>


