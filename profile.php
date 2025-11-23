<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/houses.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$conn = getDBConnection();

// Get user house
$userHouse = getUserHouse($_SESSION['user_id']);
if ($userHouse && isset(HOUSES[$userHouse])) {
    $houseData = HOUSES[$userHouse];
    $houseLogo = $houseData['logo'];
} else {
    $houseLogo = null;
}

// Get user badges - Fixed query
$stmt = $conn->prepare("
    SELECT b.id, b.name, b.description, b.icon 
    FROM badges b
    INNER JOIN user_badges ub ON b.id = ub.badge_id
    WHERE ub.user_id = ?
    ORDER BY ub.earned_at DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$badgesResult = $stmt->get_result();
$badges = [];
while ($row = $badgesResult->fetch_assoc()) {
    $badges[] = $row;
}
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

// Get user points
$stmt = $conn->prepare("SELECT points FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();
$userPoints = $user['points'] ?? 0;
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
    <?php if ($userHouse): ?>
        <style><?php echo getHouseThemeCSS($userHouse); ?></style>
    <?php endif; ?>
    <style>
        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }
        
        .profile-container {
            max-width: 1000px;
            margin: 100px auto 50px;
            padding: var(--spacing-lg);
        }
        
        .profile-card {
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            margin-bottom: var(--spacing-lg);
            box-shadow: var(--shadow-sm);
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: var(--spacing-xl);
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--color-white);
            margin: 0 auto 20px;
        }
        
        .profile-name {
            color: var(--text-primary);
            font-size: 2rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .profile-role {
            color: var(--primary);
            font-size: 1.2rem;
            text-transform: capitalize;
            font-weight: 600;
        }
        
        .profile-points {
            color: var(--primary);
            font-size: 1.3rem;
            margin-top: 15px;
            font-weight: 600;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: var(--spacing-lg);
        }
        
        .stat-card {
            background: var(--bg-secondary);
            padding: 25px;
            border-radius: var(--radius-md);
            text-align: center;
            border: 1px solid var(--border-color);
        }
        
        .stat-value {
            font-size: 2.5rem;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .badge-item {
            display: inline-block;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            padding: 20px;
            border-radius: var(--radius-md);
            margin: 10px;
            text-align: center;
            transition: all var(--transition-base);
        }
        
        .badge-item:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-sm);
        }
        
        .badge-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .badge-name {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .badge-desc {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }
        
        .points-table {
            width: 100%;
        }
        
        .points-table th {
            background: var(--bg-secondary);
            padding: var(--spacing-md);
            text-align: left;
            font-weight: 600;
            color: var(--text-primary);
            border-bottom: 2px solid var(--border-color);
        }
        
        .points-table td {
            padding: var(--spacing-md);
            border-bottom: 1px solid var(--border-color);
            color: var(--text-secondary);
        }
        
        .points-table tr:hover {
            background: var(--bg-secondary);
        }
        
        .points-positive {
            color: var(--primary);
            font-weight: 600;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar" style="font-size: 4rem;">
                    <?php if ($houseLogo): ?>
                        <?php echo $houseLogo; ?>
                    <?php else: ?>
                        <?php echo strtoupper(substr($_SESSION['name'], 0, 1) . substr($_SESSION['surname'], 0, 1)); ?>
                    <?php endif; ?>
                </div>
                <h1 class="profile-name"><?php echo htmlspecialchars($_SESSION['name'] . ' ' . $_SESSION['surname']); ?></h1>
                <div class="profile-role"><?php echo ucfirst(str_replace('_', ' ', $_SESSION['role'])); ?></div>
                <div class="profile-points">
                    <i class="fas fa-star"></i> <?php echo $userPoints; ?> Points
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
        
        <!-- Pick Your House Section -->
        <div class="profile-card">
            <h2 style="color: var(--text-primary); margin-bottom: 20px; font-weight: 700;">
                <i class="fas fa-home"></i> Pick Your House
            </h2>
            <?php if ($userHouse): ?>
                <div style="padding: 20px; background: var(--bg-secondary); border-radius: var(--radius-md); margin-bottom: 20px;">
                    <h3 style="color: var(--text-primary); margin-bottom: 10px;">
                        Current House: <?php echo HOUSES[$userHouse]['name']; ?> <?php echo HOUSES[$userHouse]['logo']; ?>
                    </h3>
                    <p style="color: var(--text-secondary);"><?php echo HOUSES[$userHouse]['description']; ?></p>
                </div>
            <?php endif; ?>
            <div class="houses-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <?php foreach (HOUSES as $houseKey => $houseInfo): ?>
                    <div class="house-card" 
                         data-house="<?php echo $houseKey; ?>"
                         style="padding: 25px; background: var(--color-white); border: 2px solid var(--border-color); border-radius: var(--radius-lg); text-align: center; cursor: pointer; transition: all var(--transition-base); <?php echo ($userHouse === $houseKey) ? 'border-color: var(--primary); box-shadow: var(--shadow-md);' : ''; ?>">
                        <div class="house-logo" style="font-size: 4rem; margin-bottom: 15px;">
                            <?php echo $houseInfo['logo']; ?>
                        </div>
                        <h3 style="color: var(--text-primary); margin-bottom: 10px; font-weight: 700;">
                            <?php echo $houseInfo['name']; ?>
                        </h3>
                        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 15px;">
                            <?php echo $houseInfo['description']; ?>
                        </p>
                        <div style="display: flex; gap: 8px; justify-content: center; margin-top: 15px;">
                            <?php foreach ($houseInfo['colors'] as $color): ?>
                                <div style="width: 30px; height: 30px; background: <?php echo $color; ?>; border-radius: 50%; border: 2px solid var(--border-color);"></div>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($userHouse === $houseKey): ?>
                            <div style="margin-top: 15px; color: var(--primary); font-weight: 600;">
                                <i class="fas fa-check-circle"></i> Selected
                            </div>
                        <?php else: ?>
                            <button class="btn btn-primary btn-sm mt-3" onclick="selectHouse('<?php echo $houseKey; ?>')">
                                Select House
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="profile-card">
            <h2 style="color: var(--text-primary); margin-bottom: 20px; font-weight: 700;">My Badges</h2>
            <?php if (count($badges) > 0): ?>
                <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                    <?php foreach ($badges as $badge): ?>
                        <div class="badge-item">
                            <div class="badge-icon"><?php echo htmlspecialchars($badge['icon']); ?></div>
                            <div class="badge-name"><?php echo htmlspecialchars($badge['name']); ?></div>
                            <div class="badge-desc"><?php echo htmlspecialchars($badge['description']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: var(--text-secondary);">No badges earned yet. Keep contributing to earn badges!</p>
            <?php endif; ?>
        </div>
        
        <div class="profile-card">
            <h2 style="color: var(--text-primary); margin-bottom: 20px; font-weight: 700;">Recent Points History</h2>
            <?php if (count($pointsHistory) > 0): ?>
                <table class="points-table">
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
                                <td class="points-positive">+<?php echo $history['points']; ?></td>
                                <td><?php echo htmlspecialchars($history['reason']); ?></td>
                                <td><?php echo date('M j, Y g:i A', strtotime($history['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--text-secondary);">No points history yet.</p>
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
    <script>
        async function selectHouse(house) {
            try {
                const response = await fetch('api/houses.php?action=select', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ house: house })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert('House selected successfully! Theme updated.', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showAlert(data.message || 'Failed to select house', 'error');
                }
            } catch (error) {
                showAlert('An error occurred. Please try again.', 'error');
                console.error('Select house error:', error);
            }
        }
    </script>
</body>
</html>
