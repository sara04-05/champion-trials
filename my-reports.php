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
    <?php include 'includes/theme-loader.php'; ?>
    <style>
        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }
        
        .page-hero {
            text-align: center;
            padding: 120px 20px 60px;
            margin-top: 70px;
            background: var(--color-white);
            border-bottom: 3px solid var(--primary);
        }
        
        .page-hero h1 {
            font-size: 3rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }
        
        .page-hero h1 i {
            color: var(--primary);
        }
        
        .reports-container {
            max-width: 1200px;
            margin: 0 auto 100px;
            padding: 40px 20px;
        }
        
        .report-card {
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-base);
        }
        
        .report-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }
        
        .report-title {
            font-size: 1.6rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 12px;
        }
        
        .report-meta {
            color: var(--text-secondary);
            font-size: 0.95rem;
            margin-bottom: 15px;
        }
        
        .report-meta i {
            color: var(--primary);
            margin-right: 8px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            margin: 50px auto;
            max-width: 600px;
        }
        
        .empty-state p {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 30px;
        }
        
        @media (max-width: 768px) {
            .page-hero h1 {
                font-size: 2.2rem;
            }
            
            .report-card {
                padding: 20px;
            }
            
            .report-title {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Hero Title -->
    <div class="page-hero">
        <h1><i class="fas fa-clipboard-list"></i> My Reports</h1>
    </div>
    
    <div class="reports-container">
        <?php if (count($issues) > 0): ?>
            <?php foreach ($issues as $issue): ?>
                <div class="report-card">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div style="flex: 1;">
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
                    
                    <p style="color: var(--text-secondary); line-height: 1.7; margin-bottom: 20px;">
                        <?= htmlspecialchars(substr($issue['description'], 0, 250)) ?>...
                    </p>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="issue-details.php?id=<?= $issue['id'] ?>" class="btn btn-primary">
                            View Details
                        </a>
                        
                        <?php if ($issue['status'] !== 'fixed'): ?>
                            <span style="color: var(--primary); font-weight: 600;">
                                Estimated fix: <?= $issue['estimated_fix_days'] ?> days
                            </span>
                        <?php else: ?>
                            <span style="color: var(--primary); font-weight: 600;">
                                <i class="fas fa-check-circle"></i> Issue Fixed!
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox" style="font-size: 4rem; color: var(--text-muted); margin-bottom: 20px;"></i>
                <p>You haven't reported any issues yet.</p>
                <a href="report.php" class="btn btn-primary">
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
