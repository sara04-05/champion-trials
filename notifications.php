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

if (isset($_GET['mark_read']) && isset($_GET['id'])) {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $_GET['id'], $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

function extractIssueId($message) {
    if (preg_match('/issue[#\s]+(\d+)/i', $message, $matches)) {
        return $matches[1];
    }
    if (preg_match('/id[:\s]+(\d+)/i', $message, $matches)) {
        return $matches[1];
    }
    return null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - fixIT</title>

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

        h1, h2, .section-title {
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .notifications-hero {
            background: var(--white);
            padding: 120px 20px 80px;
            text-align: center;
            margin-top: 70px;
            border-bottom: 5px solid var(--green);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .notifications-hero h1 {
            font-size: 3.8rem;
            color: var(--text);
            margin-bottom: 20px;
        }

        .notifications-hero h1 i { color: var(--green); margin-right: 12px; }

        .notifications-container {
            max-width: 1000px;
            margin: 100px auto;
            padding: 0 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 50px;
        }

        .page-header h1 {
            font-size: 2.8rem;
            margin: 0;
        }

        .btn-mark-all {
            background: var(--green);
            color: white;
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
            transition: var(--transition);
        }

        .btn-mark-all:hover {
            background: #0d8f63;
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.4);
        }

        .notification-item {
            background: var(--white);
            border-radius: var(--radius);
            padding: 32px 40px;
            margin-bottom: 28px;
            box-shadow: var(--shadow);
            border: 1px solid var(--midgray);
            border-left: 5px solid var(--midgray);
            transition: var(--transition);
            cursor: pointer;
        }

        .notification-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }

        .notification-item.unread {
            border-left-color: var(--green);
            background: #f0fdf4;
            position: relative;
        }

        .notification-item.unread::after {
            content: "NEW";
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--green);
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 50px;
        }

        .notification-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 10px;
        }

        .notification-message {
            font-size: 1.05rem;
            color: var(--text-light);
            line-height: 1.7;
            margin-bottom: 14px;
        }

        .notification-date {
            color: var(--text-light);
            font-size: 0.95rem;
        }

        .notification-date i {
            color: var(--green);
            margin-right: 6px;
        }

        .btn-view-issue {
            background: var(--green);
            color: white;
            padding: 10px 24px;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-top: 16px;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
            transition: var(--transition);
        }

        .btn-view-issue:hover {
            background: #0d8f63;
            transform: translateY(-2px);
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
            font-size: 5rem;
            color: #cbd5e1;
            margin-bottom: 24px;
        }

        .empty-state h3 {
            font-size: 1.8rem;
            color: var(--text-light);
            margin-bottom: 12px;
        }

        .empty-state p {
            font-size: 1.15rem;
            color: var(--text-light);
        }

        @media (max-width: 768px) {
            .notifications-hero h1 { font-size: 2.8rem; }
            .page-header { flex-direction: column; gap: 20px; text-align: center; }
            .notification-item { padding: 28px; }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>



    <div class="notifications-container">

        <div class="page-header">
            <h1>Your Notifications</h1>
            <?php if (!empty($notifications)): ?>
                <a href="?mark_read=all" class="btn-mark-all">
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
                    </div>
                    <div class="notification-message">
                        <?= nl2br(htmlspecialchars($n['message'])) ?>
                    </div>
                    <div class="notification-date">
                        <i class="fas fa-clock"></i> <?= date('M j, Y \a\t g:i A', strtotime($n['created_at'])) ?>
                    </div>
                    <?php if ($linkUrl): ?>
                        <a href="<?= $linkUrl ?>" class="btn-view-issue" onclick="event.stopPropagation();">
                            View Issue
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <h3>All caught up!</h3>
                <p>You have no notifications at the moment.<br>We'll let you know when something important happens.</p>
            </div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>