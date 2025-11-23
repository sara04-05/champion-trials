<?php
require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - fixIT</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* --- GLOBAL BACKGROUND (same as About page) --- */
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

    /* --- HERO SECTION (same style as About hero) --- */
    .contact-hero {
        background: linear-gradient(135deg, rgba(0, 255, 0, 0.15) 0%, rgba(0, 0, 255, 0.15) 50%, rgba(255, 0, 0, 0.15) 100%);
        padding: 120px 20px 100px;
        text-align: center;
        margin-top: 70px;
        border-bottom: 3px solid #00ff00;
        position: relative;
        z-index: 1;
    }

    .contact-hero h1 {
        font-size: 4.2rem;
        font-weight: 800;
        color: #ffffff;
        text-shadow:
            0 0 20px rgba(0, 255, 0, 0.5),
            0 0 40px rgba(0, 0, 255, 0.3),
            3px 3px 0px #000;
    }

    .contact-hero p {
        font-size: 1.35rem;
        color: #ffffff;
        max-width: 700px;
        margin: 0 auto;
        line-height: 1.9;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
    }

    /* --- CONTACT WRAPPER (same spacing as About sections) --- */
    .contact-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 60px 20px;
        position: relative;
        z-index: 2;
    }

    /* --- CONTACT CARD (same style as About "about-card") --- */
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

    .contact-title {
        font-size: 3rem;
        font-weight: 800;
        color: #ffffff;
        text-align: center;
        margin-bottom: 30px;
        text-shadow:
            2px 2px 0px #000000,
            0 0 15px rgba(0, 255, 0, 0.4);
    }

    /* --- Contact items styled like Feature Cards --- */
    .contact-item {
        margin-bottom: 25px;
        padding: 20px;
        background: rgba(0, 0, 0, 0.5);
        border: 2px solid #ffffff;
        border-radius: 15px;
        display: flex;
        align-items: center;
        color: #ffffff;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
        transition: 0.4s ease;
    }

    .contact-item:hover {
        transform: translateY(-8px);
        box-shadow: 0 0 20px #00ff00;
        border-color: #00ff00;
    }

    .contact-item i {
        font-size: 1.8rem;
        color: #00ff00;
        margin-right: 15px;
        text-shadow: 0 0 10px rgba(0, 255, 0, 0.8);
    }

    .section-subtitle {
        text-align: center;
        color: #ffffff;
        font-size: 1.2rem;
        margin-bottom: 50px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
    }

    /* === MODAL STYLES === */
/* --- MODAL OVERLAY (background overlay for both Login and Signup modals) --- */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);  /* Semi-transparent dark background */
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

/* --- GLASSMORPHISM MODAL (the actual modal content) --- */
.glassmorphism-modal {
    background: rgba(255, 255, 255, 0.1);  /* Transparent white background */
    padding: 30px;
    border-radius: 15px;
    backdrop-filter: blur(15px);  /* Glassmorphism effect with blur */
    width: 100%;
    max-width: 550px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);  /* Soft shadow for modal */
    color: #ffffff;
}

/* --- CLOSE BUTTON (close modal icon) --- */
.close-modal {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 2rem;
    cursor: pointer;
    color: #ffffff;
}

/* --- MODAL TITLE (for both Login and Signup modals) --- */
.modal-title {
    font-size: 2.5rem;
    color: #ffffff;
    margin-bottom: 20px;
    text-align: center;
}

/* --- FORM GROUP (for the form inputs inside modals) --- */
.form-group {
    margin-bottom: 15px;
}

.form-group label {
    color: #ffffff;
    font-size: 1.1rem;
    margin-bottom: 5px;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 12px;
    font-size: 1rem;
    border: 1px solid #fff;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
}

.form-group input::placeholder,
.form-group select {
    color: #bbbbbb;
}

/* --- SUBMIT BUTTON (Login and Signup buttons) --- */
button[type="submit"] {
    width: 100%;
    padding: 15px;
    background: #00ff00;
    border: none;
    border-radius: 10px;
    color: #ffffff;
    font-size: 1.2rem;
    cursor: pointer;
    transition: background 0.3s;
}

button[type="submit"]:hover {
    background: #00cc00;  /* Darker green when hovering */
}

/* --- LINK STYLE (Sign Up / Login switch links) --- */
p a {
    color: #00ff00;
    text-decoration: none;
    font-weight: bold;
}

p a:hover {
    text-decoration: underline;
}

/* --- RESPONSIVE DESIGN --- */
@media (max-width: 768px) {
    .glassmorphism-modal {
        max-width: 90%;  /* Smaller modal width on mobile devices */
    }
    .modal-title {
        font-size: 2rem;  /* Smaller title on mobile devices */
    }
    .form-group input,
    .form-group select {
        padding: 10px;  /* Adjust input padding on mobile */
    }
}

</style>
</head>

<body>

<!-- === UNIFIED NAVBAR === -->
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

<!-- HERO -->
<div class="contact-hero">
    <h1><i class="fas fa-envelope-open-text"></i> Contact Us</h1>
    <p>We're here to assist you with support, inquiries, or feedback. Let's improve our communities together! 💬</p>
</div>

<div class="contact-container">
    <div class="contact-card">
        <h1 class="contact-title">Get in Touch</h1>
        <p class="section-subtitle">Reach us through any of the channels below</p>

        <div class="contact-item">
            <i class="fas fa-envelope"></i>
            <strong>Email:</strong>&nbsp; support@citycare.com
        </div>

        <div class="contact-item">
            <i class="fas fa-phone"></i>
            <strong>Phone:</strong>&nbsp; +1 (555) 123-4567
        </div>

        <div class="contact-item">
            <i class="fas fa-map-marker-alt"></i>
            <strong>Address:</strong>&nbsp; 123 City Care Street, Urban City, UC 12345
        </div>

        <div class="contact-item">
            <i class="fas fa-clock"></i>
            <strong>Business Hours:</strong>&nbsp; Monday - Friday, 9 AM – 5 PM
        </div>

        <h2 class="section-title" style="margin-top: 50px;">Report a Bug 🐞</h2>
        <p class="section-subtitle">Found an issue? Help us improve by describing the problem in detail!</p>
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

<!-- Accessibility Controls -->
<div class="accessibility-controls">
    <div class="font-size-controls">
        <button id="fontSizeDecrease" aria-label="Decrease Font Size">A-</button>
        <button id="fontSizeReset" aria-label="Reset Font Size">A</button>
        <button id="fontSizeIncrease" aria-label="Increase Font Size">A+</button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>

<script>
    // Open and Close Modals
    function openLoginModal() {
        document.getElementById('loginModal').style.display = 'flex';
    }

    function closeLoginModal() {
        document.getElementById('loginModal').style.display = 'none';
    }

    function openSignupModal() {
        document.getElementById('signupModal').style.display = 'flex';
    }

    function closeSignupModal() {
        document.getElementById('signupModal').style.display = 'none';
    }
</script>

</body>
</html>
