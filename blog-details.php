<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/blog.php';

$postId = $_GET['id'] ?? null;
if (!$postId) {
    redirect('blog.php');
}

$post = getBlogPostById($postId);
if (!$post) {
    redirect('blog.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - fixIT</title>

    <!-- Same fonts as all other pages -->
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

        /* Hero-style header (same as blog list) */
        .blog-details-hero {
            background: var(--white);
            padding: 120px 20px 80px;
            text-align: center;
            margin-top: 70px;
            border-bottom: 5px solid var(--green);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .blog-details-hero h1 {
            font-size: 3.8rem;
            color: var(--text);
            margin-bottom: 20px;
        }

        .blog-details-hero h1 i { color: var(--green); margin-right: 12px; }

        .blog-details-container {
            max-width: 900px;
            margin: 100px auto;
            padding: 0 20px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--green);
            font-weight: 600;
            text-decoration: none;
            margin-bottom: 30px;
            font-size: 1.1rem;
            transition: var(--transition);
        }

        .back-link:hover {
            color: #0d8f63;
            transform: translateX(-4px);
        }

        /* Main post card */
        .blog-post-detail {
            background: var(--white);
            border-radius: var(--radius);
            padding: 60px 50px;
            margin-bottom: 60px;
            box-shadow: var(--shadow);
            border: 1px solid var(--midgray);
            transition: var(--transition);
        }

        .blog-post-detail:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }

        .blog-author-info {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .blog-author-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--green), #0d8f63);
            color: white;
            font-weight: 800;
            font-size: 1.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        }

        .blog-author {
            font-weight: 600;
            color: var(--text);
            font-size: 1.2rem;
        }

        .blog-date {
            color: var(--text-light);
            font-size: 0.98rem;
        }

        .professional-badge {
            background: linear-gradient(135deg, var(--blue), #2563eb);
            color: white;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 600;
            margin-left: 12px;
        }

        .blog-title {
            font-size: 3rem;
            margin: 30px 0 35px;
            color: var(--text);
            line-height: 1.2;
        }

        .blog-image {
            width: 100%;
            max-height: 520px;
            object-fit: cover;
            border-radius: var(--radius);
            margin: 40px 0;
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        }

        .blog-content {
            font-size: 1.15rem;
            color: var(--text-light);
            line-height: 1.9;
            margin-bottom: 40px;
        }

        /* Comments Section */
        .comment-section {
            background: var(--white);
            border-radius: var(--radius);
            padding: 50px;
            box-shadow: var(--shadow);
            border: 1px solid var(--midgray);
        }

        .comment-form {
            margin-bottom: 40px;
        }

        .comment-form textarea {
            border-radius: 16px;
            padding: 16px;
            font-size: 1.05rem;
        }

        .comment {
            background: var(--lightgray);
            padding: 24px;
            border-radius: 18px;
            margin-bottom: 20px;
            border-left: 4px solid var(--green);
            transition: var(--transition);
        }

        .comment:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        .comment-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 12px;
        }

        .comment-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--green), #0d8f63);
            color: white;
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .comment-author {
            font-weight: 600;
            color: var(--text);
        }

        .comment-date {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .comment-text {
            margin-top: 10px;
            color: var(--text);
            line-height: 1.7;
        }

        .no-comments {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-light);
        }

        .no-comments i {
            font-size: 3.5rem;
            opacity: 0.3;
            margin-bottom: 16px;
        }

        .btn-post-comment {
            background: var(--green);
            color: white;
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
            transition: var(--transition);
        }

        .btn-post-comment:hover {
            background: #0d8f63;
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.4);
        }

        @media (max-width: 768px) {
            .blog-details-hero h1 { font-size: 2.8rem; }
            .blog-title { font-size: 2.4rem; }
            .blog-post-detail, .comment-section { padding: 40px 30px; }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Same hero style as blog list -->
    <div class="blog-details-hero">
        <h1><i class="fas fa-blog"></i> Blog Post</h1>
    </div>

    <div class="blog-details-container">

        <a href="blog.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Blog
        </a>

        <!-- Main Post -->
        <div class="blog-post-detail">
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

            <h1 class="blog-title"><?php echo htmlspecialchars($post['title']); ?></h1>

            <?php if ($post['image_path']): ?>
                <img src="<?php echo BASE_URL . $post['image_path']; ?>" alt="Featured image" class="blog-image">
            <?php endif; ?>

            <div class="blog-content">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>
        </div>

        <!-- Comments -->
        <div class="comment-section">
            <h2 style="font-size:2.4rem; margin-bottom:30px;">
                Comments (<?php echo count($post['comments'] ?? []); ?>)
            </h2>

            <?php if (isLoggedIn()): ?>
            <div class="comment-form">
                <form id="commentForm" onsubmit="handleAddComment(event, <?php echo $post['id']; ?>)">
                    <div class="form-group mb-3">
                        <textarea id="commentText" class="form-control" rows="4" placeholder="Share your thoughts on this post..." required></textarea>
                    </div>
                    <button type="submit" class="btn-post-comment">
                        Post Comment
                    </button>
                </form>
            </div>
            <?php else: ?>
            <div style="text-align:center; padding:40px; background:#f8f9fa; border-radius:18px; color:var(--text-light);">
                <i class="fas fa-lock" style="font-size:2.5rem; opacity:0.4; margin-bottom:16px;"></i>
                <p>Please <a href="#" onclick="openLoginModal(); return false;" style="color:var(--green); font-weight:600;">log in</a> to leave a comment</p>
            </div>
            <?php endif; ?>

            <div id="commentsList">
                <?php if (!empty($post['comments'])): ?>
                    <?php foreach ($post['comments'] as $comment): ?>
                        <div class="comment">
                            <div class="comment-header">
                                <div class="comment-avatar">
                                    <?php echo strtoupper(substr($comment['name'], 0, 1) . substr($comment['surname'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div class="comment-author">
                                        <?php echo htmlspecialchars($comment['name'] . ' ' . $comment['surname']); ?>
                                    </div>
                                    <div class="comment-date">
                                        <?php echo date('M j, Y \a\t g:i A', strtotime($comment['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="comment-text">
                                <?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-comments">
                        <i class="fas fa-comments"></i>
                        <p>No comments yet. Be the first to share your thoughts!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        async function handleAddComment(event, postId) {
            event.preventDefault();
            const commentText = document.getElementById('commentText').value.trim();
            if (!commentText) {
                showAlert('Please enter a comment', 'error');
                return;
            }
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Posting...';
            submitBtn.disabled = true;

            try {
                const response = await fetch('api/blog.php?action=comment', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ post_id: postId, comment: commentText })
                });
                const data = await response.json();
                if (data.success) {
                    showAlert('Comment added successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('Failed to add comment', 'error');
                }
            } catch (error) {
                showAlert('An error occurred', 'error');
                console.error('Error:', error);
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }
    </script>
</body>
</html>