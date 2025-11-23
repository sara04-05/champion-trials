<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$conn = getDBConnection();

$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (isset($_GET['mark_read']) && $_GET['mark_read'] === 'all') {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
    redirect('notifications.php');
}

// Mark single notification as read
if (isset($_GET['mark_read']) && isset($_GET['id'])) {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $_GET['id'], $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

// Helper function to extract issue ID from notification message
function extractIssueId($message) {
    // Look for patterns like "issue #123" or "Issue ID: 123"
    if (preg_match('/issue[#\s]+(\d+)/i', $message, $matches)) {
        return $matches[1];
    }
    if (preg_match('/id[:\s]+(\d+)/i', $message, $matches)) {
        return $matches[1];
    }
    // Check if type field contains issue_id
    return null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - fixIT</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php include 'includes/theme-loader.php'; ?>
    <style>
        body {
            background-color: var(--bg-primary);
            min-height: 100vh;
            color: var(--text-primary);
        }
        
        .notifications-container {
            max-width: 1000px;
            margin: 100px auto 50px;
            padding: var(--spacing-lg);
        }
        
        .notification-item {
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-left: 4px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-md);
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-base);
            cursor: pointer;
        }
        
        .notification-item:hover {
            box-shadow: var(--shadow-md);
            transform: translateX(4px);
        }
        
        .notification-item.unread {
            border-left-color: var(--primary);
            background: #f0f9f0;
        }
        
        .notification-title {
            font-size: var(--font-size-lg);
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--spacing-sm);
        }
        
        .notification-message {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: var(--spacing-sm);
        }
        
        .notification-date {
            color: var(--text-muted);
            font-size: var(--font-size-sm);
        }
        
        .empty-state {
            text-align: center;
            padding: var(--spacing-xxl);
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="notifications-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 style="color: var(--text-primary);">Notifications</h1>
            <?php if (!empty($notifications)): ?>
                <a href="?mark_read=all" class="btn btn-secondary">
                    Mark All as Read
                </a>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $n): ?>
                <?php 
                $issueId = extractIssueId($n['message']);
                $linkUrl = $issueId ? "issue-details.php?id={$issueId}&from_notification=1" : null;
                ?>
                <div class="notification-item <?= !$n['is_read'] ? 'unread' : '' ?>" 
                     onclick="<?= $linkUrl ? "window.location.href='{$linkUrl}'" : '' ?>">
                    <div class="notification-title">
                        <?= htmlspecialchars($n['title']) ?>
                        <?php if (!$n['is_read']): ?>
                            <span class="badge bg-success ms-2">NEW</span>
                        <?php endif; ?>
                    </div>
                    <div class="notification-message">
                        <?= nl2br(htmlspecialchars($n['message'])) ?>
                    </div>
                    <div class="notification-date">
                        <i class="fas fa-clock"></i> <?= date('M j, Y \a\t g:i A', strtotime($n['created_at'])) ?>
                    </div>
                    <?php if ($linkUrl): ?>
                        <div style="margin-top: var(--spacing-sm);">
                            <a href="<?= $linkUrl ?>" class="btn btn-sm btn-primary">
                                View Issue <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-bell-slash fa-4x" style="color: var(--text-muted); margin-bottom: var(--spacing-lg);"></i>
                <p style="color: var(--text-secondary); font-size: var(--font-size-lg);">
                    No notifications yet.<br>
                    <small>You're all caught up!</small>
                </p>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    
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
