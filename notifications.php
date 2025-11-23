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
            position: relative;
            min-height: 100vh;
            margin: 0;
            color: #ffffff;
            overflow-x: hidden;
            font-family: 'Segoe UI', sans-serif;
        }

        /* Same soft neon radial glows as report.php — but NO text glow */
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background:
                radial-gradient(circle at 20% 30%, rgba(0, 255, 0, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(0, 0, 255, 0.10) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(255, 0, 0, 0.08) 0%, transparent 40%);
            pointer-events: none;
            z-index: -1;
        }

        .page-title {
            text-align: center;
            padding: 130px 20px 50px;
            margin-top: 70px;
        }

        .page-title h1 {
            font-size: 3.8rem;
            font-weight: 800;
            color: #ffffff;            /* Clean white — no neon */
            letter-spacing: 1px;
        }

        @media (max-width: 768px) {
            .page-title h1 { font-size: 2.8rem; }
        }
    </style>
</head>
<body>

    <?php include 'includes/navbar.php'; ?>

    <!-- Clean, non-glowing title -->
    <div class="page-title">
        <h1>Notifications</h1>
    </div>

    <div class="container mt-5 pt-4">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="glassmorphism-modal" style="position: relative; margin: 20px auto; padding: 40px;">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="modal-title" style="margin:0; font-size:2rem; color:#fff;">
                            Your Notifications
                        </h2>
                        <?php if (!empty($notifications)): ?>
                            <a href="?mark_read=all" class="btn btn-outline-light">
                                Mark All as Read
                            </a>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($notifications)): ?>
                        <?php foreach ($notifications as $n): ?>
                            <div class="bg-dark bg-opacity-25 rounded-3 p-4 mb-3 border-start <?= !$n['is_read'] ? 'border-success border-4' : 'border-secondary border-2' ?>">
                                <h5 class="mb-2" style="color:#ffffff; font-weight:600;">
                                    <?= htmlspecialchars($n['title']) ?>
                                    <?php if (!$n['is_read']): ?>
                                        <span class="badge bg-success ms-2 small">NEW</span>
                                    <?php endif; ?>
                                </h5>
                                <p class="mb-2" style="color:#e0e0e0; line-height:1.6;">
                                    <?= nl2br(htmlspecialchars($n['message'])) ?>
                                </p>
                                <small style="color:#aaaaaa;">
                                    <?= date('M j, Y \a\t g:i A', strtotime($n['created_at'])) ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-4x text-secondary mb-4"></i>
                            <p class="text-white-50 fs-3">No notifications yet.<br><small>You're all caught up!</small></p>
                        </div>
                    <?php endif; ?>

                </div>
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
</body>
</html>