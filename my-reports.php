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
        .reports-container {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 20px;
        }
        .report-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        .report-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        .report-title {
            color: var(--text-light);
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        .report-meta {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="reports-container">
        <h1 style="color: var(--text-light); margin-bottom: 30px;">My Reports</h1>
        
        <?php if (count($issues) > 0): ?>
            <?php foreach ($issues as $issue): ?>
                <div class="report-card">
                    <div class="report-header">
                        <div>
                            <h3 class="report-title"><?php echo htmlspecialchars($issue['title']); ?></h3>
                            <div class="report-meta">
                                <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($issue['city'] . ', ' . $issue['state']); ?></span>
                                <span class="ms-3"><i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($issue['reported_at'])); ?></span>
                                <span class="ms-3"><i class="fas fa-tag"></i> <?php echo ucfirst(str_replace('_', ' ', $issue['category'])); ?></span>
                            </div>
                        </div>
                        <div>
                            <span class="issue-status <?php echo $issue['status']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $issue['status'])); ?>
                            </span>
                        </div>
                    </div>
                    <p style="color: rgba(255, 255, 255, 0.9); margin-bottom: 15px;">
                        <?php echo htmlspecialchars(substr($issue['description'], 0, 200)); ?>...
                    </p>
                    <div style="display: flex; gap: 10px;">
                        <a href="issue-details.php?id=<?php echo $issue['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                        <?php if ($issue['status'] !== 'fixed'): ?>
                            <span style="color: rgba(255, 255, 255, 0.7); align-self: center;">
                                Estimated fix: <?php echo $issue['estimated_fix_days']; ?> days
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="profile-card" style="text-align: center; padding: 50px;">
                <p style="color: rgba(255, 255, 255, 0.7); font-size: 1.2rem;">You haven't reported any issues yet.</p>
                <a href="report.php" class="btn btn-primary mt-3">Report Your First Issue</a>
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

