<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/blog.php';

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
    <?php include 'includes/theme-loader.php'; ?>
    <style>
        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }
        
        .blog-container {
            max-width: 1100px;
            margin: 120px auto 70px;
            padding: 20px;
        }
        
        .blog-header-section {
            text-align: center;
            padding: 60px 20px;
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            margin-bottom: 60px;
            box-shadow: var(--shadow-sm);
        }
        
        .blog-header-section h1 {
            font-size: 3rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 15px;
        }
        
        .blog-header-section h1 i {
            color: var(--primary);
        }
        
        .blog-header-section p {
            font-size: 1.2rem;
            color: var(--text-secondary);
        }
        
        .blog-header-section p i {
            color: var(--primary);
        }
        
        .blog-post {
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-base);
        }
        
        .blog-post:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }
        
        .blog-author-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .blog-author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid var(--border-color);
            background: var(--primary);
            color: var(--color-white);
            font-weight: 700;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .blog-author {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 1rem;
        }
        
        .blog-date {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .professional-badge {
            background: var(--primary);
            padding: 4px 10px;
            border-radius: var(--radius-sm);
            font-size: 0.75rem;
            color: var(--color-white);
            font-weight: 600;
            margin-left: 10px;
        }
        
        .blog-title {
            color: var(--text-primary);
            font-size: 1.8rem;
            font-weight: 700;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        
        .blog-image {
            width: 100%;
            border-radius: var(--radius-md);
            margin-bottom: 25px;
            box-shadow: var(--shadow-sm);
        }
        
        .blog-content {
            color: var(--text-secondary);
            font-size: 1rem;
            line-height: 1.8;
        }
        
        .blog-footer {
            border-top: 1px solid var(--border-color);
            padding-top: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }
        
        .blog-stats span {
            color: var(--text-muted);
        }
        
        .blog-stats i {
            color: var(--primary);
        }
        
        .create-post-btn {
            background: var(--primary);
            color: var(--color-white);
            border: none;
            padding: 12px 24px;
            border-radius: var(--radius-md);
            font-weight: 600;
            transition: all var(--transition-base);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .create-post-btn:hover {
            background: #45a049;
            color: var(--color-white);
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
        }
        
        .empty-state i {
            font-size: 4rem;
            color: var(--text-muted);
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            color: var(--text-primary);
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: var(--text-secondary);
        }
        
        @media (max-width: 768px) {
            .blog-header-section h1 {
                font-size: 2rem;
            }
            
            .blog-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="blog-container">
        <div class="blog-header-section">
            <h1><i class="fas fa-blog"></i> Make Your City Better</h1>
            <p>Share insights, ideas, and expert knowledge to improve our community</p>
            <?php if ($isProfessional): ?>
                <p style="margin-top: 10px; color: var(--primary);">
                    <i class="fas fa-star"></i> You're a verified professional - your posts will be highlighted
                </p>
            <?php endif; ?>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 style="color: var(--text-primary); font-size: 1.5rem;">Recent Posts</h2>
            <?php if (isLoggedIn()): ?>
                <button class="create-post-btn" onclick="openCreatePostModal()">
                    <i class="fas fa-plus"></i> Create New Post
                </button>
            <?php else: ?>
                <a href="#" class="create-post-btn" onclick="openSignupModal(); return false;">
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
                                            <span class="professional-badge">
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
                    <h3>No posts yet</h3>
                    <p>Be the first to share your thoughts and help make our city better!</p>
                    <?php if (isLoggedIn()): ?>
                        <button class="create-post-btn mt-3" onclick="openCreatePostModal()">
                            <i class="fas fa-plus"></i> Create First Post
                        </button>
                    <?php endif; ?>
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
                <p style="text-align: center; color: var(--primary); margin-bottom: 20px;">
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
    
    <!-- Accessibility Controls -->
    <div class="accessibility-controls">
        <div class="font-size-controls">
            <button id="fontSizeDecrease" aria-label="Decrease Font Size">A-</button>
            <button id="fontSizeReset" aria-label="Reset Font Size">A</button>
            <button id="fontSizeIncrease" aria-label="Increase Font Size">A+</button>
        </div>
    </div>
</body>
</html>
