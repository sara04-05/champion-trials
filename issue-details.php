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

// Mark notification as read if linked from notification
if (isset($_GET['from_notification'])) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND link_url LIKE ?");
    $linkUrl = "issue-details.php?id=" . $issueId;
    $stmt->bind_param("is", $_SESSION['user_id'], $linkUrl);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Details - fixIT</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <?php include 'includes/theme-loader.php'; ?>
    <style>
        body {
            background-color: var(--bg-primary);
            min-height: 100vh;
            color: var(--text-primary);
        }
        
        .issue-details-container {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: var(--spacing-lg);
        }
        
        .issue-header {
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            margin-bottom: var(--spacing-lg);
            box-shadow: var(--shadow-md);
        }
        
        .issue-title {
            font-size: var(--font-size-xl);
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: var(--spacing-md);
        }
        
        .issue-meta {
            display: flex;
            flex-wrap: wrap;
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-md);
            color: var(--text-secondary);
        }
        
        .issue-meta-item {
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
        }
        
        .issue-meta-item i {
            color: var(--primary);
        }
        
        .issue-description {
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            margin-bottom: var(--spacing-lg);
            box-shadow: var(--shadow-md);
        }
        
        .issue-description h3 {
            margin-bottom: var(--spacing-md);
            color: var(--text-primary);
        }
        
        .issue-description p {
            color: var(--text-secondary);
            line-height: 1.8;
        }
        
        .issue-map {
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
            box-shadow: var(--shadow-md);
        }
        
        .issue-map h3 {
            margin-bottom: var(--spacing-md);
            color: var(--text-primary);
        }
        
        #detailMap {
            width: 100%;
            height: 400px;
            border-radius: var(--radius-md);
        }
        
        .issue-actions {
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            margin-bottom: var(--spacing-lg);
            box-shadow: var(--shadow-md);
        }
        
        .issue-comments {
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            box-shadow: var(--shadow-md);
        }
        
        .comment-item {
            padding: var(--spacing-md);
            border-bottom: 1px solid var(--border-color);
            margin-bottom: var(--spacing-md);
        }
        
        .comment-item:last-child {
            border-bottom: none;
        }
        
        .comment-author {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--spacing-xs);
        }
        
        .comment-text {
            color: var(--text-secondary);
            margin-bottom: var(--spacing-xs);
        }
        
        .comment-date {
            color: var(--text-muted);
            font-size: var(--font-size-sm);
        }
        
        @media (max-width: 768px) {
            .issue-details-container {
                padding: var(--spacing-md);
            }
            
            .issue-meta {
                flex-direction: column;
                gap: var(--spacing-sm);
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="issue-details-container">
        <div class="issue-header">
            <h1 class="issue-title"><?php echo htmlspecialchars($issue['title']); ?></h1>
            
            <div class="issue-meta">
                <div class="issue-meta-item">
                    <i class="fas fa-user"></i>
                    <span><?php echo htmlspecialchars($issue['name'] . ' ' . $issue['surname']); ?></span>
                </div>
                <div class="issue-meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?php echo htmlspecialchars($issue['city'] . ', ' . $issue['state']); ?></span>
                </div>
                <div class="issue-meta-item">
                    <i class="fas fa-tag"></i>
                    <span><?php echo ucfirst(str_replace('_', ' ', $issue['category'])); ?></span>
                </div>
                <div class="issue-meta-item">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Urgency: <?php echo ucfirst($issue['urgency_level']); ?></span>
                </div>
                <div class="issue-meta-item">
                    <i class="fas fa-calendar"></i>
                    <span><?php echo date('M j, Y g:i A', strtotime($issue['reported_at'])); ?></span>
                </div>
            </div>
            
            <div style="margin-top: var(--spacing-md);">
                <span class="issue-status <?php echo $issue['status']; ?>">
                    <?php echo ucfirst(str_replace('_', ' ', $issue['status'])); ?>
                </span>
            </div>
        </div>
        
        <div class="issue-description">
            <h3>Description</h3>
            <p><?php echo nl2br(htmlspecialchars($issue['description'])); ?></p>
        </div>
        
        <?php if (!empty($issue['photos'])): ?>
        <div class="issue-description">
            <h3>Photos</h3>
            <div class="row">
                <?php foreach ($issue['photos'] as $photo): ?>
                <div class="col-md-4 mb-3">
                    <img src="<?php echo BASE_URL . $photo['photo_path']; ?>" alt="Issue photo" class="img-fluid" style="border-radius: var(--radius-md);">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="issue-map">
            <h3>Location</h3>
            <div id="detailMap"></div>
        </div>
        
        <div class="issue-actions">
            <h3>Actions</h3>
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-primary" onclick="upvoteIssue(<?php echo $issue['id']; ?>)">
                    <i class="fas fa-thumbs-up"></i> Upvote (<?php echo $issue['upvotes']; ?>)
                </button>
                <?php if (isAdmin()): ?>
                <select class="form-control" style="max-width: 200px;" onchange="updateIssueStatus(<?php echo $issue['id']; ?>, this.value)">
                    <option value="pending" <?php echo ($issue['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="in_progress" <?php echo ($issue['status'] === 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                    <option value="fixed" <?php echo ($issue['status'] === 'fixed') ? 'selected' : ''; ?>>Fixed</option>
                </select>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="issue-comments">
            <h3>Comments (<?php echo count($issue['comments'] ?? []); ?>)</h3>
            
            <form id="commentForm" onsubmit="addComment(event)" class="mb-4">
                <div class="form-group">
                    <textarea id="commentText" class="form-control" rows="3" placeholder="Add a comment..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Post Comment</button>
            </form>
            
            <div id="commentsList">
                <?php if (!empty($issue['comments'])): ?>
                    <?php foreach ($issue['comments'] as $comment): ?>
                    <div class="comment-item">
                        <div class="comment-author">
                            <?php echo htmlspecialchars($comment['name'] . ' ' . $comment['surname']); ?>
                        </div>
                        <div class="comment-text">
                            <?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?>
                        </div>
                        <div class="comment-date">
                            <?php echo date('M j, Y g:i A', strtotime($comment['created_at'])); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: var(--text-muted);">No comments yet. Be the first to comment!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        // Initialize map
        const map = L.map('detailMap').setView([<?php echo $issue['latitude']; ?>, <?php echo $issue['longitude']; ?>], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Add marker
        L.marker([<?php echo $issue['latitude']; ?>, <?php echo $issue['longitude']; ?>])
            .addTo(map)
            .bindPopup('<?php echo htmlspecialchars($issue['title'], ENT_QUOTES); ?>')
            .openPopup();
        
        // Upvote issue
        async function upvoteIssue(issueId) {
            try {
                const response = await fetch('api/issues.php?action=upvote', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ issue_id: issueId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert('Issue upvoted!', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showAlert('Failed to upvote', 'error');
                }
            } catch (error) {
                showAlert('An error occurred', 'error');
            }
        }
        
        // Add comment
        async function addComment(event) {
            event.preventDefault();
            
            const commentText = document.getElementById('commentText').value;
            
            try {
                const response = await fetch('api/issues.php?action=comment', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        issue_id: <?php echo $issue['id']; ?>,
                        comment: commentText
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert('Comment added!', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showAlert('Failed to add comment', 'error');
                }
            } catch (error) {
                showAlert('An error occurred', 'error');
            }
        }
        
        // Update issue status (admin only)
        async function updateIssueStatus(issueId, status) {
            try {
                const response = await fetch('api/issues.php?action=update_status', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ issue_id: issueId, status: status })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert('Issue status updated!', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showAlert('Failed to update status', 'error');
                }
            } catch (error) {
                showAlert('An error occurred', 'error');
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
