<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$conn = getDBConnection();

// Get notifications
$stmt = $conn->prepare("
    SELECT * FROM notifications
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 50
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Mark as read
if (isset($_GET['mark_read']) && $_GET['mark_read'] == 'all') {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = TRUE WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
    redirect('notifications.php');
}

$conn->close();
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
    <style>
        .notifications-container {
            max-width: 800px;
            margin: 100px auto 50px;
            padding: 20px;
        }
        .notification-item {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }
        .notification-item:hover {
            transform: translateX(5px);
        }
        .notification-item.unread {
            border-left: 4px solid var(--primary-green);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="notifications-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 style="color: var(--text-light);">Notifications</h1>
            <a href="?mark_read=all" class="btn btn-sm btn-secondary">Mark All as Read</a>
        </div>
        
        <?php if (count($notifications) > 0): ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-item <?php echo $notification['is_read'] ? '' : 'unread'; ?>">
                    <h4 style="color: var(--text-light); margin-bottom: 10px;"><?php echo htmlspecialchars($notification['title']); ?></h4>
                    <p style="color: rgba(255, 255, 255, 0.9);"><?php echo htmlspecialchars($notification['message']); ?></p>
                    <small style="color: rgba(255, 255, 255, 0.6);">
                        <?php echo date('M j, Y g:i A', strtotime($notification['created_at'])); ?>
                    </small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="profile-card" style="text-align: center; padding: 50px;">
                <p style="color: rgba(255, 255, 255, 0.7); font-size: 1.2rem;">No notifications yet.</p>
            </div>
        <?php endif; ?>
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
</body>
</html>

