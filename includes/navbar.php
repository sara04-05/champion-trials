<nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-tools"></i> fixIT
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (!isLoggedIn()): ?>
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="blog.php">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="openLoginModal(); return false;">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="openSignupModal(); return false;">Sign Up</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="index.php">Home (Map)</a></li>
                    <li class="nav-item"><a class="nav-link" href="report.php">Report an Issue</a></li>
                    <li class="nav-item"><a class="nav-link" href="blog.php">Make Your City Better</a></li>
                    <li class="nav-item"><a class="nav-link" href="my-reports.php">My Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="notifications.php">Notifications</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                    <?php if (isAdmin()): ?>
                        <li class="nav-item"><a class="nav-link" href="admin/dashboard.php">Admin</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="logout(); return false;">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

