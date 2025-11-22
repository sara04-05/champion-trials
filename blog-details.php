<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/blog.php';

// Allow viewing without login, but restrict commenting

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
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .blog-details-container {
            max-width: 900px;
            margin: 100px auto 50px;
            padding: 20px;
        }
        
        .blog-post-detail {
            background: rgba(26, 26, 46, 0.95);
            backdrop-filter: blur(30px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 50px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-lg);
        }
        
        .blog-header {
            margin-bottom: 30px;
            padding-bottom: 25px;
            border-bottom: 1px solid var(--glass-border);
        }
        
        .blog-author-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .blog-author-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .blog-author {
            color: var(--text-light);
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 5px;
        }
        
        .blog-date {
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        
        .blog-title {
            color: var(--text-light);
            font-size: 2.5rem;
            font-weight: 700;
            margin: 30px 0;
            line-height: 1.2;
        }
        
        .blog-content {
            color: rgba(255, 255, 255, 0.95);
            line-height: 1.9;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        
        .blog-image {
            width: 100%;
            border-radius: 15px;
            margin: 30px 0;
            max-height: 500px;
            object-fit: cover;
            box-shadow: var(--shadow-md);
        }
        
        .comment-section {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid var(--glass-border);
        }
        
        .comment-form {
            background: rgba(255, 255, 255, 0.03);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .comment {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            border: 1px solid var(--glass-border);
            transition: all 0.3s ease;
        }
        
        .comment:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(5px);
        }
        
        .comment-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        
        .comment-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.9rem;
        }
        
        .comment-author {
            font-weight: 600;
            color: var(--primary-green);
            margin-bottom: 0;
        }
        
        .comment-text {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 10px;
            line-height: 1.7;
        }
        
        .comment-date {
            color: var(--text-muted);
            font-size: 0.85rem;
        }
        
        .professional-badge {
            background: var(--gradient-secondary);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-green);
            text-decoration: none;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            transform: translateX(-5px);
            color: var(--secondary-green);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="blog-details-container">
        <a href="blog.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Blog
        </a>
        
        <div class="blog-post-detail">
            <div class="blog-header">
                <div class="blog-author-info">
                    <div class="blog-author-avatar">
                        <?php echo strtoupper(substr($post['name'], 0, 1) . substr($post['surname'], 0, 1)); ?>
                    </div>
                    <div>
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
            
            <h1 class="blog-title"><?php echo htmlspecialchars($post['title']); ?></h1>
            
            <?php if ($post['image_path']): ?>
                <img src="<?php echo BASE_URL . $post['image_path']; ?>" alt="Blog image" class="blog-image">
            <?php endif; ?>
            
            <div class="blog-content">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>
        </div>
        
        <!-- Comments Section -->
        <div class="blog-post-detail comment-section">
            <h2 style="color: var(--text-light); margin-bottom: 25px; font-size: 1.75rem;">
                <i class="fas fa-comments"></i> Comments (<?php echo count($post['comments'] ?? []); ?>)
            </h2>
            
            <?php if (isLoggedIn()): ?>
            <div class="comment-form">
                <form id="commentForm" onsubmit="handleAddComment(event, <?php echo $post['id']; ?>)">
                    <div class="form-group">
                        <textarea id="commentText" class="form-control" rows="4" placeholder="Share your thoughts..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Post Comment
                    </button>
                </form>
            </div>
            <?php else: ?>
            <div class="comment-form" style="text-align: center; padding: 30px;">
                <p style="color: var(--text-muted); margin-bottom: 15px;">
                    <i class="fas fa-lock"></i> Please <a href="index.php" style="color: var(--primary-green);">login</a> to comment on posts
                </p>
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
                                    <div class="comment-author"><?php echo htmlspecialchars($comment['name'] . ' ' . $comment['surname']); ?></div>
                                    <div class="comment-date">
                                        <i class="fas fa-clock"></i> <?php echo date('M j, Y g:i A', strtotime($comment['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="comment-text"><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                        <i class="fas fa-comments" style="font-size: 3rem; opacity: 0.3; margin-bottom: 15px;"></i>
                        <p>No comments yet. Be the first to share your thoughts!</p>
                    </div>
                <?php endif; ?>
            </div>
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
        async function handleAddComment(event, postId) {
            event.preventDefault();
            
            const commentText = document.getElementById('commentText').value.trim();
            
            if (!commentText) {
                showAlert('Please enter a comment', 'error');
                return;
            }
            
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="loading"></span> Posting...';
            submitBtn.disabled = true;
            
            try {
                const response = await fetch('api/blog.php?action=comment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ post_id: postId, comment: commentText })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert('Comment added successfully!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showAlert('Failed to add comment', 'error');
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            } catch (error) {
                showAlert('An error occurred', 'error');
                console.error('Error adding comment:', error);
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }
    </script>
</body>
</html>
