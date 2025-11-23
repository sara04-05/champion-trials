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

    <!-- Same fonts as About & Contact -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Manrope:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php include 'includes/theme-loader.php'; ?>

    <style>
        :root {
            --white: #ffffff;
            --offwhite: #fafafa;
            --lightgray: #f1f5f9;
            --midgray: #e2e8f0;
            --text: #0f172a;
            --text-light: #475569;
            --red: #ef4444;
            --blue: #3b82f6;
            --green: #10b981;
            --radius: 24px;
            --shadow: 0 10px 40px rgba(0,0,0,0.07);
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--offwhite);
            color: var(--text);
            line-height: 1.8;
        }

        h1, h2, h3, .section-title {
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        /* Same Hero as About & Contact */
        .blog-hero {
            background: var(--white);
            padding: 120px 20px 80px;
            text-align: center;
            margin-top: 70px;
            border-bottom: 5px solid var(--green);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .blog-hero h1 {
            font-size: 3.8rem;
            color: var(--text);
            margin-bottom: 20px;
        }

        .blog-hero h1 i { color: var(--green); margin-right: 12px; }

        .blog-hero p {
            font-size: 1.25rem;
            color: var(--text-light);
            max-width: 720px;
            margin: 0 auto;
        }

        .blog-container {
            max-width: 1280px;
            margin: 100px auto;
            padding: 0 20px;
        }

        .section-title {
            font-size: 2.8rem;
            text-align: center;
            margin-bottom: 20px;
        }

        .section-title::after {
            content: '';
            width: 90px;
            height: 5px;
            background: linear-gradient(90deg, var(--red), var(--blue));
            border-radius: 3px;
            display: block;
            margin: 20px auto 0;
        }

        .section-subtitle {
            text-align: center;
            color: var(--text-light);
            font-size: 1.15rem;
            max-width: 680px;
            margin: 0 auto 60px;
        }

        /* Blog Post Card - Same modern card style */
        .blog-post {
            background: var(--white);
            border-radius: var(--radius);
            padding: 50px;
            margin-bottom: 50px;
            box-shadow: var(--shadow);
            border: 1px solid var(--midgray);
            transition: var(--transition);
        }

        .blog-post:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }

        .blog-author-info {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 24px;
        }

        .blog-author-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--green), #0d8f63);
            color: white;
            font-weight: 800;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }

        .blog-author {
            font-weight: 600;
            color: var(--text);
        }

        .blog-date {
            color: var(--text-light);
            font-size: 0.95rem;
        }

        .professional-badge {
            background: linear-gradient(135deg, var(--blue), #2563eb);
            color: white;
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 10px;
        }

        .blog-title {
            font-size: 2.1rem;
            margin: 20px 0;
            color: var(--text);
        }

        .blog-image {
            width: 100%;
            max-height: 420px;
            object-fit: cover;
            border-radius: var(--radius);
            margin: 28px 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .blog-content {
            color: var(--text-light);
            font-size: 1.05rem;
            line-height: 1.9;
        }

        .blog-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 24px;
            border-top: 1px solid var(--midgray);
            margin-top: 30px;
        }

        .blog-stats {
            color: var(--text-light);
            font-size: 0.95rem;
        }

        .blog-stats i {
            color: var(--green);
            margin-right: 6px;
        }

        .btn-read-more {
            background: var(--green);
            color: white;
            padding: 12px 28px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
        }

        .btn-read-more:hover {
            transform: translateY(-3px);
            background: #0d8f63;
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.4);
        }

        .create-post-btn {
            background: var(--green);
            color: white;
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .create-post-btn:hover {
            transform: translateY(-4px);
            background: #0d8f63;
            box-shadow: 0 18px 40px rgba(16, 185, 129, 0.4);
        }

        .empty-state {
            text-align: center;
            padding: 100px 40px;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--midgray);
        }

        .empty-state i {
            font-size: 4.5rem;
            color: var(--text-light);
            margin-bottom: 20px;
        }

        /* Modal styling to match */
        .modal-overlay {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(10px);
        }

        .glassmorphism-modal {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        @media (max-width: 768px) {
            .blog-hero h1 { font-size: 2.8rem; }
            .blog-title { font-size: 1.8rem; }
            .blog-post { padding: 40px 30px; }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Same Hero Style -->
    <div class="blog-hero">
        <h1><i class="fas fa-blog"></i> Make Your City Better</h1>
        <p>Share insights, ideas, and expert knowledge to improve our community</p>
        <?php if ($isProfessional): ?>
            <p style="margin-top: 10px; color: var(--green); font-weight:600;">
                <i class="fas fa-star"></i> You're a verified professional — your posts will be highlighted
            </p>
        <?php endif; ?>
    </div>

    <div class="blog-container">

        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="section-title" style="font-size:2.4rem; margin:0;">Recent Posts</h2>
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
                        <div class="blog-author-info">
                            <div class="blog-author-avatar">
                                <?php echo strtoupper(substr($post['name'], 0, 1) . substr($post['surname'], 0, 1)); ?>
                            </div>
                            <div>
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

                        <h2 class="blog-title"><?php echo htmlspecialchars($post['title']); ?></h2>

                        <?php if ($post['image_path']): ?>
                            <img src="<?php echo BASE_URL . $post['image_path']; ?>" alt="Blog image" class="blog-image">
                        <?php endif; ?>

                        <div class="blog-content blog-content-preview">
                            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                        </div>

                        <div class="blog-footer">
                            <div class="blog-stats">
                                <span><i class="fas fa-comments"></i> <?php echo $post['comment_count']; ?> Comments</span>
                            </div>
                            <a href="blog-details.php?id=<?php echo $post['id']; ?>" class="btn-read-more">
                                Read More <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-blog"></i>
                    <h3>No posts yet</h3>
                    <p>Be the first to share your thoughts and help make our city better!</p>
                    <?php if (isLoggedIn()): ?>
                        <button class="create-post-btn mt-4" onclick="openCreatePostModal()">
                            <i class="fas fa-plus"></i> Create First Post
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal remains exactly as you had it (only minor visual polish via existing classes) -->
    <?php if (isLoggedIn()): ?>
    <div class="modal-overlay" id="createPostModal" style="display: none;">
        <div class="glassmorphism-modal" style="max-width: 700px;">
            <span class="close-modal" onclick="closeCreatePostModal()">&times;</span>
            <h2 class="modal-title">
                <i class="fas fa-edit"></i> Create New Post
            </h2>
            <?php if ($isProfessional): ?>
                <p style="text-align: center; color: var(--green); margin-bottom: 20px; font-weight:600;">
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
                </div>
                <div class="form-group">
                    <label><i class="fas fa-image"></i> Featured Image (Optional)</label>
                    <input type="file" id="postImage" class="form-control" accept="image/*" onchange="previewImage(this)">
                    <div id="imagePreview" style="margin-top: 15px; display: none;">
                        <img id="previewImg" src="" alt="Preview" style="max-width: 100%; border-radius: 16px; max-height: 200px; object-fit: cover;">
                    </div>
                </div>
                <button type="submit" class="btn-read-more" style="width:100%; padding:16px; font-size:1.1rem;">
                    <i class="fas fa-paper-plane"></i> Publish Post
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        // Your existing JS remains 100% unchanged
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
                const response = await fetch('api/blog.php?action=create', { method: 'POST', body: formData });
                const data = await response.json();
                if (data.success) {
                    showAlert('Post published successfully! You earned 15 points.', 'success');
                    setTimeout(() => location.reload(), 1500);
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