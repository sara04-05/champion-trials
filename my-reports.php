<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/issues.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$conn = getDBConnection();
$stmt = $conn->prepare("
    SELECT i.*, u.username, u.name, u.surname, 
    (SELECT COUNT(*) FROM issue_upvotes WHERE issue_id = i.id) as upvotes
    FROM issues i
    JOIN users u ON i.user_id = u.id
    WHERE i.user_id = ?
    ORDER BY i.reported_at DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$issues = [];
while ($row = $result->fetch_assoc()) {
    $issues[] = $row;
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reports - fixIT</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #000000 0%, #1a1a2e 50%, #000000 100%);
            position: relative;
            min-height: 100vh;
            color: #ffffff;
            margin: 0;
            overflow-x: hidden;
        }

        /* Soft neon radial glows only — no lines, no rotating shape */
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background:
                radial-gradient(circle at 20% 30%, rgba(0, 255, 0, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(0, 0, 255, 0.1) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(255, 0, 0, 0.08) 0%, transparent 40%);
            pointer-events: none;
            z-index: 0;
        }

        /* Hero Section */
        .page-hero {
            text-align: center;
            padding: 140px 20px 80px;
            margin-top: 70px;
            border-bottom: 4px solid #00ff00;
            background: linear-gradient(135deg, rgba(0,255,0,0.22), rgba(0,0,255,0.15), rgba(255,0,0,0.15));
            position: relative;
            overflow: hidden;
        }

        .page-hero h1 {
            font-size: 4.5rem;
            font-weight: 900;
            color: #ffffff;
            text-shadow:
                0 0 20px rgba(0,255,0,0.7),
                0 0 40px rgba(0,255,0,0.4),
                4px 4px 0 #000;
            margin: 0;
        }

        .page-hero h1 i {
            color: #00ff00;
            text-shadow: 0 0 25px #00ff00;
        }

        .reports-container {
            max-width: 1200px;
            margin: 0 auto 100px;
            padding: 40px 20px;
            position: relative;
            z-index: 1;
        }

        /* Clean glowing cards — no ::before rotating overlay */
        .report-card {
            background: rgba(0, 0, 0, 0.75);
            border: 3px solid #ffffff;
            border-radius: 25px;
            padding: 35px;
            margin-bottom: 30px;
            box-shadow:
                0 10px 30px rgba(0,0,0,0.6),
                0 0 25px rgba(0,255,0,0.2),
                inset 0 0 20px rgba(255,255,255,0.05);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .report-card:hover {
            transform: translateY(-10px) scale(1.02);
            border-color: #00ff00;
            box-shadow:
                0 20px 40px rgba(0,0,0,0.7),
                0 0 35px rgba(0,255,0,0.5);
        }

        .report-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 12px;
            text-shadow: 0 0 10px rgba(0,255,0,0.5);
        }

        .report-meta {
            color: rgba(255,255,255,0.85);
            font-size: 1rem;
            margin-bottom: 15px;
        }

        .report-meta i {
            color: #00ff00;
            margin-right: 8px;
        }

        .issue-status {
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .issue-status.pending      { background: rgba(255,165,0,0.3);   color: #ffaa00; border: 2px solid #ffaa00; }
        .issue-status.in_progress  { background: rgba(0,123,255,0.3);   color: #0099ff; border: 2px solid #0099ff; }
        .issue-status.fixed        { background: rgba(0,255,0,0.3);     color: #00ff00; border: 2px solid #00ff00; }
        .issue-status.rejected     { background: rgba(255,0,0,0.3);     color: #ff4444; border: 2px solid #ff4444; }

        .btn-primary {
            background: linear-gradient(135deg, #00ff00, #00cc00);
            border: none;
            color: #000;
            padding: 10px 24px;
            font-weight: 800;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,255,0,0.4);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,255,0,0.6);
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: rgba(0,0,0,0.6);
            border: 3px solid #ffffff;
            border-radius: 25px;
            margin: 50px auto;
            max-width: 600px;
        }

        .empty-state p {
            font-size: 1.4rem;
            color: rgba(255,255,255,0.8);
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .page-hero h1 { font-size: 2.8rem; }
            .report-card { padding: 25px; }
            .report-title { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

    <!-- Only soft radial glows now — no diagonal lines, no rotating element -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Hero Title -->
    <div class="page-hero">
        <h1>My Reports</h1>
    </div>

    <div class="reports-container">
        <?php if (count($issues) > 0): ?>
            <?php foreach ($issues as $issue): ?>
                <div class="report-card">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h3 class="report-title">
                                <?= htmlspecialchars($issue['title']) ?>
                            </h3>
                            <div class="report-meta">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($issue['city'] . ', ' . $issue['state']) ?>
                                &nbsp;&nbsp;|&nbsp;&nbsp;
                                <i class="fas fa-calendar"></i>
                                <?= date('M j, Y', strtotime($issue['reported_at'])) ?>
                                &nbsp;&nbsp;|&nbsp;&nbsp;
                                <i class="fas fa-tag"></i>
                                <?= ucfirst(str_replace('_', ' ', $issue['category'])) ?>
                            </div>
                        </div>
                        <span class="issue-status <?= $issue['status'] ?>">
                            <?= ucfirst(str_replace('_', ' ', $issue['status'])) ?>
                        </span>
                    </div>

                    <p style="color: rgba(255,255,255,0.9); line-height: 1.7; margin-bottom: 20px;">
                        <?= htmlspecialchars(substr($issue['description'], 0, 250)) ?>...
                    </p>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="issue-details.php?id=<?= $issue['id'] ?>" class="btn btn-primary">
                            View Details
                        </a>

                        <?php if ($issue['status'] !== 'fixed'): ?>
                            <span style="color: #00ff00; font-weight: 600;">
                                Estimated fix: <?= $issue['estimated_fix_days'] ?> days
                            </span>
                        <?php else: ?>
                            <span style="color: #00ff00; font-weight: 700;">
                                Issue Fixed!
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="empty-state">
                <p>You haven't reported any issues yet.</p>
                <a href="report.php" class="btn btn-primary" style="font-size: 1.3rem; padding: 14px 40px;">
                    Report Your First Issue
                </a>
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