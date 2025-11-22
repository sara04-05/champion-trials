<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$conn = getDBConnection();

// Get user badges
$stmt = $conn->prepare("
    SELECT b.* FROM badges b
    JOIN user_badges ub ON b.id = ub.badge_id
    WHERE ub.user_id = ?
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$badges = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get user stats
$stmt = $conn->prepare("
    SELECT 
        COUNT(DISTINCT i.id) as total_issues,
        COUNT(DISTINCT ic.id) as total_comments,
        COUNT(DISTINCT bp.id) as total_posts,
        SUM(CASE WHEN i.status = 'fixed' THEN 1 ELSE 0 END) as fixed_issues
    FROM users u
    LEFT JOIN issues i ON u.id = i.user_id
    LEFT JOIN issue_comments ic ON u.id = ic.user_id
    LEFT JOIN blog_posts bp ON u.id = bp.user_id
    WHERE u.id = ?
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get points history
$stmt = $conn->prepare("
    SELECT * FROM points_history
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 10
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$pointsHistory = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - fixIT</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .profile-container {
            max-width: 1000px;
            margin: 100px auto 50px;
            padding: 20px;
        }
        .profile-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--primary-green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            margin: 0 auto 20px;
        }
        .profile-name {
            color: var(--text-light);
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .profile-role {
            color: var(--primary-green);
            font-size: 1.2rem;
            text-transform: capitalize;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .stat-value {
            font-size: 2.5rem;
            color: var(--primary-green);
            font-weight: bold;
        }
        .stat-label {
            color: rgba(255, 255, 255, 0.7);
            margin-top: 10px;
        }
        .badge-item {
            display: inline-block;
            background: rgba(255, 255, 255, 0.1);
            padding: 15px 20px;
            border-radius: 10px;
            margin: 10px;
            text-align: center;
        }
        .badge-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .badge-name {
            color: var(--text-light);
            font-weight: 600;
        }
        .badge-desc {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($_SESSION['name'], 0, 1) . substr($_SESSION['surname'], 0, 1)); ?>
                </div>
                <h1 class="profile-name"><?php echo htmlspecialchars($_SESSION['name'] . ' ' . $_SESSION['surname']); ?></h1>
                <div class="profile-role"><?php echo ucfirst(str_replace('_', ' ', $_SESSION['role'])); ?></div>
                <div style="color: var(--primary-green); font-size: 1.5rem; margin-top: 10px;">
                    <i class="fas fa-star"></i> <?php 
                    $conn = getDBConnection();
                    $stmt = $conn->prepare("SELECT points FROM users WHERE id = ?");
                    $stmt->bind_param("i", $_SESSION['user_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();
                    echo $user['points'] ?? 0;
                    $stmt->close();
                    $conn->close();
                    ?> Points
                </div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['total_issues']; ?></div>
                    <div class="stat-label">Issues Reported</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['fixed_issues']; ?></div>
                    <div class="stat-label">Issues Fixed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['total_comments']; ?></div>
                    <div class="stat-label">Comments</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['total_posts']; ?></div>
                    <div class="stat-label">Blog Posts</div>
                </div>
            </div>
        </div>
        
        <div class="profile-card">
            <h2 style="color: var(--text-light); margin-bottom: 20px;">My Badges</h2>
            <?php if (count($badges) > 0): ?>
                <?php foreach ($badges as $badge): ?>
                    <div class="badge-item">
                        <div class="badge-icon"><?php echo htmlspecialchars($badge['icon']); ?></div>
                        <div class="badge-name"><?php echo htmlspecialchars($badge['name']); ?></div>
                        <div class="badge-desc"><?php echo htmlspecialchars($badge['description']); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: rgba(255, 255, 255, 0.7);">No badges earned yet. Keep contributing to earn badges!</p>
            <?php endif; ?>
        </div>
        
        <div class="profile-card">
            <h2 style="color: var(--text-light); margin-bottom: 20px;">Recent Points History</h2>
            <table class="table" style="color: var(--text-light);">
                <thead>
                    <tr>
                        <th>Points</th>
                        <th>Reason</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pointsHistory as $history): ?>
                        <tr>
                            <td style="color: var(--primary-green); font-weight: bold;">+<?php echo $history['points']; ?></td>
                            <td><?php echo htmlspecialchars($history['reason']); ?></td>
                            <td><?php echo date('M j, Y g:i A', strtotime($history['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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

