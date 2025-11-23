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
$issues = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reports - fixIT</title>

    <!-- Same beautiful fonts as profile & report pages -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Manrope:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php include 'includes/theme-loader.php'; ?>

    <style>
        :root {
            --white: #ffffff; --offwhite: #fafafa; --lightgray: #f1f5f9; --midgray: #e2e8f0;
            --text: #0f172a; --text-light: #475569; --green: #10b981;
            --radius: 24px; --shadow: 0 10px 40px rgba(0,0,0,0.07); --transition: all 0.4s cubic-bezier(0.4,0,0.2,1);
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--offwhite);
            color: var(--text);
            line-height: 1.8;
            min-height: 100vh;
        }
        h1, h2, h3 { font-family: 'Manrope', sans-serif; font-weight: 800; letter-spacing: -0.5px; }

        /* Hero Header */
        .page-hero {
            background: var(--white);
            padding: 130px 20px 80px;
            text-align: center;
            border-bottom: 5px solid var(--green);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .page-hero h1 {
            font-size: 3.8rem;
            color: var(--text);
        }
        .page-hero h1 i {
            color: var(--green);
            margin-right: 12px;
        }
        .page-hero p {
            font-size: 1.3rem;
            color: var(--text-light);
            margin-top: 16px;
        }

        /* Reports Container */
        .reports-container {
            max-width: 1100px;
            margin: 80px auto;
            padding: 0 20px;
        }

        /* Report Card */
        .report-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: var(--shadow);
            border: 1px solid var(--midgray);
            transition: var(--transition);
        }
        .report-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }

        .report-title {
            font-size: 1.9rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 12px;
        }

        .report-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            color: var(--text-light);
            font-size: 0.98rem;
            margin-bottom: 20px;
        }
        .report-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .report-meta i {
            color: var(--green);
        }

        .report-description {
            color: var(--text-light);
            line-height: 1.8;
            margin-bottom: 24px;
            font-size: 1.05rem;
        }

        .issue-status {
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .issue-status.pending { background: #fef3c7; color: #92400e; }
        .issue-status.in_progress { background: #dbeafe; color: #1e40af; }
        .issue-status.fixed { background: #d1fae5; color: #065f46; }

        .btn-primary {
            background: var(--green);
            border: none;
            border-radius: 50px;
            padding: 12px 28px;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-primary:hover {
            background: #0d8f63;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(16,185,129,0.3);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 40px;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--midgray);
            max-width: 700px;
            margin: 60px auto;
        }
        .empty-state i {
            font-size: 5rem;
            color: var(--text-light);
            margin-bottom: 24px;
        }
        .empty-state p {
            font-size: 1.4rem;
            color: var(--text-light);
            margin-bottom: 32px;
        }
    </style>
</head>
<body>

    <?php include 'includes/navbar.php'; ?>


    <div class="reports-container">
        <?php if (count($issues) > 0): ?>
            <?php foreach ($issues as $issue): ?>
                <div class="report-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h3 class="report-title">
                                <?= htmlspecialchars($issue['title']) ?>
                            </h3>
                            <div class="report-meta">
                                <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($issue['city'] . ', ' . $issue['state']) ?></span>
                                <span><i class="fas fa-calendar-alt"></i> <?= date('M j, Y \a\t g:i A', strtotime($issue['reported_at'])) ?></span>
                                <span><i class="fas fa-tag"></i> <?= ucfirst(str_replace('_', ' ', $issue['category'] ?? 'uncategorized')) ?></span>
                                <span><i class="fas fa-thumbs-up"></i> <?= $issue['upvotes'] ?? 0 ?> Upvotes</span>
                            </div>
                        </div>
                        <span class="issue-status <?= $issue['status'] ?? 'pending' ?>">
                            <?= ucfirst(str_replace('_', ' ', $issue['status'] ?? 'pending')) ?>
                        </span>
                    </div>

                    <p class="report-description">
                        <?= htmlspecialchars(substr($issue['description'], 0, 280)) ?>
                        <?= strlen($issue['description']) > 280 ? '...' : '' ?>
                    </p>

                    <div class="d-flex justify-content-between align-items-center">
                        <a href="issue-details.php?id=<?= $issue['id'] ?>" class="btn btn-primary">
                            View Full Details
                        </a>

                        <?php if ($issue['status'] === 'fixed'): ?>
                            <span style="color: var(--green); font-weight: 700; font-size: 1.1rem;">
                                Issue Fixed! Thank you!
                            </span>
                        <?php elseif ($issue['estimated_fix_days']): ?>
                            <span style="color: var(--text-light); font-weight: 600;">
                                Estimated fix in ~<?= $issue['estimated_fix_days'] ?> days
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>You haven't reported any issues yet.</p>
                <p style="font-size: 1.1rem; color: var(--text-light); margin: 20px 0;">
                    Be the change you want to see in your city!
                </p>
                <a href="report.php" class="btn btn-primary btn-lg" style="padding: 14px 36px; font-size: 1.1rem;">
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