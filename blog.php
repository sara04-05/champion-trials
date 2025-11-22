<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/blog.php';

// Allow viewing without login, but restrict posting/commenting
$posts = getBlogPosts();
$userRole = isLoggedIn() ? getUserRole() : 'guest';
$isProfessional = isLoggedIn() && in_array($userRole, ['engineer', 'doctor', 'safety_inspector', 'environmental_officer', 'construction_worker']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Your City Better - fixIT</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* === GLOBAL BACKGROUND (same as Contact/About) === */
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
        radial-gradient(circle at 20% 30%, rgba(0, 255, 0, 0.12) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(0, 0, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 50% 50%, rgba(255, 0, 0, 0.08) 0%, transparent 50%);
    pointer-events: none;
    z-index: 0;
}

/* === BLOG CONTAINER === */
.blog-container {
    max-width: 1100px;
    margin: 120px auto 70px;
    padding: 20px;
    position: relative;
    z-index: 1;
}

/* === BLOG HERO HEADER (matches Contact hero) === */
.blog-header-section {
    text-align: center;
    padding: 70px 20px;
    background: linear-gradient(135deg,
        rgba(0, 255, 0, 0.12),
        rgba(0, 0, 255, 0.12),
        rgba(255, 0, 0, 0.12)
    );
    border-radius: 25px;
    border: 3px solid #ffffff;
    position: relative;
    overflow: hidden;
    margin-bottom: 60px;
    box-shadow:
        0 0 25px rgba(0, 255, 0, 0.25),
        0 0 30px rgba(0, 0, 255, 0.2),
        inset 0 0 15px rgba(255, 255, 255, 0.1);
}

.blog-header-section::before {
    content: '';
    position: absolute;
    top: -40%;
    left: -40%;
    width: 180%;
    height: 180%;
    background:
        radial-gradient(circle, rgba(0, 255, 0, 0.12) 0%, transparent 60%),
        radial-gradient(circle, rgba(0, 0, 255, 0.12) 0%, transparent 60%);
    animation: rotate 20s linear infinite;
}

/* === Blog header text === */
.blog-header-section h1 {
    font-size: 3.5rem;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 15px;
    text-shadow:
        0 0 15px rgba(0, 255, 0, 0.5),
        0 0 20px rgba(0, 0, 255, 0.3);
}

.blog-header-section p {
    font-size: 1.3rem;
    color: #ffffff;
    opacity: 0.9;
}

.blog-header-section p i {
    color: #00ff00;
    text-shadow: 0 0 10px #00ff00;
}

/* === RECENT POSTS HEADER === */
.blog-container h2 {
    color: #ffffff !important;
    text-shadow: 0 0 10px rgba(0,255,0,0.5);
}

/* === BLOG POST CARD (matches Contact card / About cards) === */
.blog-post {
    background: rgba(0, 0, 0, 0.75);
    border: 3px solid #ffffff;
    border-radius: 25px;
    padding: 40px;
    margin-bottom: 40px;
    box-shadow:
        0 0 20px rgba(0, 255, 0, 0.2),
        0 0 25px rgba(0, 0, 255, 0.15),
        inset 0 0 10px rgba(255, 255, 255, 0.1);
    transition: 0.35s ease;
    position: relative;
    overflow: hidden;
}

.blog-post:hover {
    transform: translateY(-6px) scale(1.015);
    border-color: #00ff00;
    box-shadow:
        0 0 30px #00ff00,
        0 0 40px rgba(0, 0, 255, 0.35);
}

.blog-post::before {
    content: '';
    position: absolute;
    top: -60%;
    left: -60%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle,
        rgba(0,255,0,0.1),
        rgba(0,0,255,0.08),
        rgba(255,0,0,0.08),
        transparent 70%
    );
    animation: rotate 18s linear infinite;
    pointer-events: none;
}

/* === AUTHOR HEADER INSIDE CARD === */
.blog-author-info {
    display: flex;
    align-items: center;
    gap: 18px;
}

.blog-author-avatar {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    border: 2px solid #ffffff;
    background: rgba(255,255,255,0.1);
    color: #00ff00;
    font-weight: 700;
    font-size: 1.3rem;
    display: flex;
    align-items: center;
    justify-content: center;
    text-shadow: 0 0 10px #00ff00;
}

.blog-author {
    color: #ffffff;
    font-weight: 700;
    font-size: 1.1rem;
    text-shadow: 0 0 10px black;
}

.blog-date {
    color: #d0d0d0;
    font-size: 0.9rem;
}

/* === PROFESSIONAL BADGE === */
.professional-badge {
    background: linear-gradient(135deg, #00ff00, #00cc66);
    padding: 4px 10px;
    border-radius: 15px;
    font-size: 0.7rem;
    color: #000;
    font-weight: bold;
    box-shadow: 0 0 12px #00ff00;
}

/* === BLOG TITLE === */
.blog-title {
    color: #ffffff;
    font-size: 2rem;
    font-weight: 800;
    margin-top: 20px;
    margin-bottom: 20px;
    text-shadow:
        0 0 10px rgba(0,255,0,0.4),
        0 0 10px rgba(0,0,255,0.4);
}

/* === BLOG IMAGE === */
.blog-image {
    width: 100%;
    border-radius: 20px;
    margin-bottom: 25px;
    box-shadow:
        0 0 20px rgba(255,255,255,0.1),
        0 0 30px rgba(0,255,0,0.2);
}

/* === BLOG PREVIEW TEXT === */
.blog-content {
    color: #eeeeee;
    font-size: 1.05rem;
    line-height: 1.8;
}

.blog-content-preview::after {
    background: linear-gradient(to bottom, transparent, rgba(0,0,0,0.8));
}

/* === BLOG FOOTER === */
.blog-footer {
    border-top: 2px solid #ffffff;
    padding-top: 15px;
    display: flex;
    justify-content: space-between;
}

.blog-stats span {
    color: #cccccc;
    text-shadow: 0 0 10px black;
}

.blog-stats i {
    color: #00ff00;
}

/* === BUTTONS (match Contact page / neon) === */
.create-post-btn,
.btn-primary {
    background: linear-gradient(135deg, #00ff00, #00bb55);
    border: 2px solid #ffffff;
    padding: 12px 26px;
    border-radius: 14px;
    color: #000 !important;
    font-weight: 700;
    transition: 0.3s ease;
    box-shadow: 0 0 15px #00ff00;
}

.create-post-btn:hover,
.btn-primary:hover {
    transform: translateY(-4px) scale(1.05);
    box-shadow:
        0 0 25px #00ff00,
        0 0 35px rgba(0,0,255,0.3);
    border-color: #00ff00;
}

.btn-primary i {
    color: #000 !important;
}

/* === EMPTY STATE === */
.empty-state i {
    color: #ffffff;
    text-shadow: 0 0 15px rgba(255,255,255,0.5);
}

.empty-state h3 {
    color: #ffffff;
}

/* === ROTATION ANIMATION === */
@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* === RESPONSIVE === */
@media (max-width: 768px) {
    .blog-header-section h1 {
        font-size: 2.4rem;
    }
    .blog-title {
        font-size: 1.6rem;
    }
}

    </style>
</head>
<body>
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
                    <li class="nav-item"><a class="nav-link" href="" onclick="openSignupModal(); return false;">Sign Up</a></li>
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
    
    <div class="blog-container">
        <div class="blog-header-section">
            <h1><i class="fas fa-blog"></i> Make Your City Better</h1>
            <p>Share insights, ideas, and expert knowledge to improve our community</p>
            <?php if ($isProfessional): ?>
                <p style="margin-top: 10px; color: var(--primary-green);">
                    <i class="fas fa-star"></i> You're a verified professional - your posts will be highlighted
                </p>
            <?php endif; ?>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 style="color: var(--text-light); font-size: 1.5rem;">Recent Posts</h2>
            <?php if (isLoggedIn()): ?>
                <button class="create-post-btn" onclick="openCreatePostModal()">
                    <i class="fas fa-plus"></i> Create New Post
                </button>
            <?php else: ?>
                <a href="index.php" class="create-post-btn" style="text-decoration: none;">
                    <i class="fas fa-sign-in-alt"></i> Login to Post
                </a>
            <?php endif; ?>
        </div>
        
        <div id="blogPosts">
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="blog-post">
                        <div class="blog-header">
                            <div class="blog-author-info">
                                <div class="blog-author-avatar">
                                    <?php echo strtoupper(substr($post['name'], 0, 1) . substr($post['surname'], 0, 1)); ?>
                                </div>
                                <div class="blog-author-details">
                                    <div class="blog-author">
                                        <?php echo htmlspecialchars($post['name'] . ' ' . $post['surname']); ?>
                                        <?php if ($post['role'] !== 'regular_user'): ?>
                                            <span class="professional-badge ms-2">
                                                <i class="fas fa-certificate"></i> <?php echo ucfirst(str_replace('_', ' ', $post['role'])); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="blog-date">
                                        <i class="fas fa-clock"></i> <?php echo date('F j, Y \a\t g:i A', strtotime($post['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <h2 class="blog-title"><?php echo htmlspecialchars($post['title']); ?></h2>
                        
                        <?php if ($post['image_path']): ?>
                            <img src="<?php echo BASE_URL . $post['image_path']; ?>" alt="Blog image" class="blog-image">
                        <?php endif; ?>
                        
                        <div class="blog-content blog-content-preview">
                            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                        </div>
                        
                        <div class="blog-footer">
                            <div class="blog-stats">
                                <span>
                                    <i class="fas fa-comments"></i> 
                                    <?php echo $post['comment_count']; ?> Comments
                                </span>
                            </div>
                            <a href="blog-details.php?id=<?php echo $post['id']; ?>" class="btn btn-primary">
                                Read More <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="blog-post empty-state">
                    <i class="fas fa-blog"></i>
                    <h3 style="color: var(--text-light); margin-bottom: 10px;">No posts yet</h3>
                    <p>Be the first to share your thoughts and help make our city better!</p>
                    <button class="create-post-btn mt-3" onclick="openCreatePostModal()">
                        <i class="fas fa-plus"></i> Create First Post
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Create Post Modal (only shown when logged in) -->
    <?php if (isLoggedIn()): ?>
    <div class="modal-overlay" id="createPostModal" style="display: none;">
        <div class="glassmorphism-modal" style="max-width: 700px;">
            <span class="close-modal" onclick="closeCreatePostModal()">&times;</span>
            <h2 class="modal-title">
                <i class="fas fa-edit"></i> Create New Post
            </h2>
            <?php if ($isProfessional): ?>
                <p style="text-align: center; color: var(--primary-green); margin-bottom: 20px;">
                    <i class="fas fa-star"></i> Your professional insights will be highlighted
                </p>
            <?php endif; ?>
            <form id="createPostForm" onsubmit="handleCreatePost(event)" enctype="multipart/form-data">
                <div class="form-group">
                    <label><i class="fas fa-heading"></i> Post Title</label>
                    <input type="text" id="postTitle" class="form-control" placeholder="Enter a compelling title..." required maxlength="200">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Content</label>
                    <textarea id="postContent" class="form-control" rows="10" placeholder="Share your thoughts, insights, or expert knowledge..." required></textarea>
                    <small style="color: var(--text-muted); margin-top: 5px; display: block;">
                        <?php if ($isProfessional): ?>
                            <i class="fas fa-lightbulb"></i> Tip: Share professional insights, solutions, or best practices
                        <?php else: ?>
                            <i class="fas fa-lightbulb"></i> Share your ideas, experiences, or suggestions for city improvement
                        <?php endif; ?>
                    </small>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-image"></i> Featured Image (Optional)</label>
                    <input type="file" id="postImage" class="form-control" accept="image/*" onchange="previewImage(this)">
                    <small style="color: var(--text-muted); margin-top: 5px; display: block;">
                        Recommended: 1200x630px. Max size: 5MB
                    </small>
                    <div id="imagePreview" style="margin-top: 15px; display: none;">
                        <img id="previewImg" src="" alt="Preview" style="max-width: 100%; border-radius: 10px; max-height: 200px; object-fit: cover;">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-paper-plane"></i> Publish Post
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        function openCreatePostModal() {
            document.getElementById('createPostModal').style.display = 'flex';
        }
        
        function closeCreatePostModal() {
            document.getElementById('createPostModal').style.display = 'none';
            document.getElementById('createPostForm').reset();
            document.getElementById('imagePreview').style.display = 'none';
        }
        
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
        
        async function handleCreatePost(event) {
            event.preventDefault();
            
            const formData = new FormData();
            formData.append('title', document.getElementById('postTitle').value);
            formData.append('content', document.getElementById('postContent').value);
            
            const imageFile = document.getElementById('postImage').files[0];
            if (imageFile) {
                // Validate file size (5MB)
                if (imageFile.size > 5 * 1024 * 1024) {
                    showAlert('Image size must be less than 5MB', 'error');
                    return;
                }
                formData.append('image', imageFile);
            }
            
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="loading"></span> Publishing...';
            submitBtn.disabled = true;
            
            try {
                const response = await fetch('api/blog.php?action=create', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert('Post published successfully! You earned 15 points.', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert(data.message || 'Failed to publish post', 'error');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            } catch (error) {
                showAlert('An error occurred. Please try again.', 'error');
                console.error('Create post error:', error);
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }
    </script>
</body>
</html>
