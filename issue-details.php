<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$conn = getDBConnection();

// Fetch notifications
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

// Mark all as read
if (isset($_GET['mark_read']) && $_GET['mark_read'] === 'all') {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
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
        body {
            background: linear-gradient(135deg, #000000 0%, #1a1a2e 50%, #000000 100%);
            min-height: 100vh;
            margin: 0;
            color: #ffffff;
            font-family: 'Segoe UI', sans-serif;
        }

        .page-title {
            text-align: center;
            padding: 120px 20px 60px;
            margin-top: 70px;
        }

        .page-title h1 {
            font-size: 3.8rem;
            font-weight: 900;
            color: #00ff00;
            text-shadow: 0 0 20px rgba(0,255,0,0.6);
        }

        @media (max-width: 768px) {
            .page-title h1 { font-size: 2.6rem; }
        }

        .container-main {
            max-width: 1000px;
            margin: 0 auto 100px;
            padding: 20px;
        }

        .glass-card {
            background: rgba(10, 10, 30, 0.85);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 35px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .notif-item {
            background: rgba(255,255,255,0.06);
            border-radius: 16px;
            padding: 22px;
            margin-bottom: 18px;
            position: relative;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .notif-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.4);
        }

        .notif-item.unread {
            border-left: 5px solid #00ff00;
            background: rgba(0, 255, 0, 0.08);
            box-shadow: 0 0 20px rgba(0,255,0,0.15);
        }

        .notif-title {
            font-size: 1.35rem;
            font-weight: 700;
            color: #00ff00;
            margin-bottom: 10px;
            text-shadow: 0 0 10px rgba(0,255,0,0.4);
        }

        .notif-message {
            color: #e0e0e0;
            line-height: 1.7;
            font-size: 1.05rem;
            margin-bottom: 12px;
        }

        .notif-date {
            color: rgba(255,255,255,0.6);
            font-size: 0.95rem;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: rgba(10,10,30,0.7);
            border-radius: 20px;
            border: 2px dashed rgba(0,255,0,0.3);
        }

        .empty-state p {
            font-size: 1.5rem;
            color: rgba(255,255,255,0.7);
        }
    </style>
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <!-- Page Title -->
    <div class="page-title">
        <h1>Notifications</h1>
    </div>

    <div class="container-main">
        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2 style="margin:0; color:#00ff00; font-size:2.2rem;">Your Notifications</h2>
                <?php if (!empty($notifications)): ?>
                    <a href="?mark_read=all" class="btn btn-outline-light px-4 py-2" style="font-weight:600;">
                        Mark All as Read
                    </a>
                <?php endif; ?>
            </div>

            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $n): ?>
                    <div class="notif-item <?= !$n['is_read'] ? 'unread' : '' ?>">
                        <div class="notif-title">
                            <?= htmlspecialchars($n['title']) ?>
                        </div>
                        <div class="notif-message">
                            <?= nl2br(htmlspecialchars($n['message'])) ?>
                        </div>
                        <div class="notif-date">
                            <?= date('M j, Y \a\t g:i A', strtotime($n['created_at'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>No notifications yet.<br>You're all caught up!</p>
                </div>
            <?php endif; ?>
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
</body>
</html>