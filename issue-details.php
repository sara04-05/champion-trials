<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/issues.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$issueId = $_GET['id'] ?? null;
if (!$issueId) {
    redirect('index.php');
}

$issue = getIssueById($issueId);
if (!$issue) {
    redirect('index.php');
}

// Check if user has upvoted
$conn = getDBConnection();
$stmt = $conn->prepare("SELECT id FROM issue_upvotes WHERE issue_id = ? AND user_id = ?");
$stmt->bind_param("ii", $issueId, $_SESSION['user_id']);
$stmt->execute();
$hasUpvoted = $stmt->get_result()->num_rows > 0;
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($issue['title']); ?> - fixIT</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .issue-details-container {
            max-width: 1000px;
            margin: 100px auto 50px;
            padding: 20px;
        }
        .issue-detail-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }
        .issue-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 20px;
        }
        .issue-title {
            color: var(--text-light);
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .issue-meta {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 20px;
        }
        .issue-description {
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.8;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        .issue-photos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .issue-photo {
            width: 100%;
            border-radius: 10px;
            cursor: pointer;
        }
        .comment-section {
            margin-top: 30px;
        }
        .comment {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .comment-author {
            font-weight: 600;
            color: var(--primary-green);
            margin-bottom: 5px;
        }
        .comment-text {
            color: rgba(255, 255, 255, 0.9);
        }
        .comment-date {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="issue-details-container">
        <div class="issue-detail-card">
            <div class="issue-header">
                <div>
                    <h1 class="issue-title"><?php echo htmlspecialchars($issue['title']); ?></h1>
                    <div class="issue-meta">
                        <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($issue['city'] . ', ' . $issue['state']); ?></span>
                        <span class="ms-3"><i class="fas fa-user"></i> <?php echo htmlspecialchars($issue['username']); ?></span>
                        <span class="ms-3"><i class="fas fa-calendar"></i> <?php echo date('F j, Y g:i A', strtotime($issue['reported_at'])); ?></span>
                    </div>
                </div>
                <div>
                    <span class="issue-status <?php echo $issue['status']; ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $issue['status'])); ?>
                    </span>
                </div>
            </div>
            
            <div class="issue-description">
                <?php echo nl2br(htmlspecialchars($issue['description'])); ?>
            </div>
            
            <div class="d-flex gap-3 mb-3">
                <button class="btn btn-primary" onclick="toggleUpvote(<?php echo $issue['id']; ?>)">
                    <i class="fas fa-thumbs-up"></i> 
                    <span id="upvoteCount"><?php echo $issue['upvotes'] ?? 0; ?></span> Upvotes
                </button>
                <span class="badge bg-info" style="align-self: center;">
                    Category: <?php echo ucfirst(str_replace('_', ' ', $issue['category'])); ?>
                </span>
                <span class="badge bg-warning" style="align-self: center;">
                    Urgency: <?php echo ucfirst($issue['urgency_level']); ?>
                </span>
                <?php if ($issue['estimated_fix_days']): ?>
                    <span class="badge bg-success" style="align-self: center;">
                        Est. Fix: <?php echo $issue['estimated_fix_days']; ?> days
                    </span>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($issue['photos'])): ?>
                <div class="issue-photos">
                    <?php foreach ($issue['photos'] as $photo): ?>
                        <img src="<?php echo BASE_URL . $photo['photo_path']; ?>" 
                             alt="Issue photo" 
                             class="issue-photo"
                             onclick="window.open(this.src, '_blank')">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Updates Section -->
        <?php if (!empty($issue['updates'])): ?>
            <div class="issue-detail-card">
                <h2 style="color: var(--text-light); margin-bottom: 20px;">Updates</h2>
                <?php foreach ($issue['updates'] as $update): ?>
                    <div class="comment">
                        <div class="comment-author"><?php echo htmlspecialchars($update['name'] . ' ' . $update['surname']); ?></div>
                        <div class="comment-text"><?php echo nl2br(htmlspecialchars($update['update_text'])); ?></div>
                        <div class="comment-date"><?php echo date('M j, Y g:i A', strtotime($update['created_at'])); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Comments Section -->
        <div class="issue-detail-card comment-section">
            <h2 style="color: var(--text-light); margin-bottom: 20px;">Comments (<?php echo count($issue['comments'] ?? []); ?>)</h2>
            
            <form id="commentForm" onsubmit="handleAddComment(event, <?php echo $issue['id']; ?>)">
                <div class="form-group">
                    <textarea id="commentText" class="form-control" rows="3" placeholder="Add a comment..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Post Comment</button>
            </form>
            
            <div id="commentsList" class="mt-4">
                <?php if (!empty($issue['comments'])): ?>
                    <?php foreach ($issue['comments'] as $comment): ?>
                        <div class="comment">
                            <div class="comment-author"><?php echo htmlspecialchars($comment['name'] . ' ' . $comment['surname']); ?></div>
                            <div class="comment-text"><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></div>
                            <div class="comment-date"><?php echo date('M j, Y g:i A', strtotime($comment['created_at'])); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: rgba(255, 255, 255, 0.7);">No comments yet. Be the first to comment!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        async function toggleUpvote(issueId) {
            try {
                const response = await fetch('api/issues.php?action=upvote', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ issue_id: issueId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Reload page to update upvote count
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error upvoting:', error);
            }
        }
        
        async function handleAddComment(event, issueId) {
            event.preventDefault();
            
            const commentText = document.getElementById('commentText').value;
            
            try {
                const response = await fetch('api/issues.php?action=comment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ issue_id: issueId, comment: commentText })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert('Comment added successfully!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showAlert('Failed to add comment', 'error');
                }
            } catch (error) {
                showAlert('An error occurred', 'error');
                console.error('Error adding comment:', error);
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>

