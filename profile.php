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
$houseImage = $userHouse && !empty(HOUSES[$userHouse]['image']) ? HOUSES[$userHouse]['image'] : null;
$houseLogo = $userHouse ? (HOUSES[$userHouse]['logo'] ?? null) : null;

// Get user badges
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
$stmt = $conn->prepare("SELECT * FROM points_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
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
        
        /* NEW: Beautiful Avatar with House Image */
        .profile-avatar {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 20px;
            border: 6px solid var(--primary);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }
        
        .profile-avatar:hover {
            transform: scale(1.08);
            box-shadow: 0 15px 40px rgba(0,0,0,0.4);
        }
        
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-avatar-fallback {
            width: 100%;
            height: 100%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: white;
            font-weight: bold;
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
        
        .stats-grid, .houses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin: var(--spacing-lg) 0;
        }
        
        .house-card {
            padding: 30px 20px;
            background: white;
            border: 3px solid var(--border-color);
            border-radius: var(--radius-lg);
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .house-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
        }
        
        .house-card[selected], .house-card[data-house="<?php echo $userHouse; ?>"] {
            border-color: var(--primary) !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transform: translateY(-5px);
        }
        
        .house-logo img {
            max-height: 130px;
            max-width: 100%;
            object-fit: contain;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
            transition: transform 0.4s ease;
        }
        
        .house-logo img:hover {
            transform: scale(1.12);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php if ($houseImage): ?>
                        <img src="<?php echo htmlspecialchars($houseImage); ?>" alt="House Crest">
                    <?php else: ?>
                        <div class="profile-avatar-fallback">
                            <?php echo $houseLogo ?: strtoupper(substr($_SESSION['name'], 0, 1) . substr($_SESSION['surname'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <h1 class="profile-name"><?php echo htmlspecialchars($_SESSION['name'] . ' ' . $_SESSION['surname']); ?></h1>
                <div class="profile-role"><?php echo ucfirst(str_replace('_', ' ', $_SESSION['role'])); ?></div>
                <div class="profile-points">
                    Star <?php echo $userPoints; ?> Points
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
                Pick Your House
            </h2>

            <?php if ($userHouse): ?>
                <div style="padding: 25px; background: var(--bg-secondary); border-radius: var(--radius-lg); margin-bottom: 30px; text-align: center; border: 2px solid var(--primary);">
                    <h3 style="color: var(--text-primary); margin: 0 0 15px 0;">
                        Current House: <strong><?php echo HOUSES[$userHouse]['name']; ?></strong>
                    </h3>
                    <?php if ($houseImage): ?>
                        <img src="<?php echo htmlspecialchars($houseImage); ?>" alt="<?php echo HOUSES[$userHouse]['name']; ?>" style="max-height: 100px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                    <?php else: ?>
                        <div style="font-size: 4rem; margin: 10px 0;"><?php echo $houseLogo; ?></div>
                    <?php endif; ?>
                    <p style="color: var(--text-secondary); margin-top: 15px;"><?php echo HOUSES[$userHouse]['description']; ?></p>
                </div>
            <?php endif; ?>

            <div class="houses-grid">
                <?php foreach (HOUSES as $houseKey => $houseInfo): ?>
                    <div class="house-card <?php echo ($userHouse === $houseKey) ? 'selected' : ''; ?>" 
                         data-house="<?php echo $houseKey; ?>"
                         onclick="selectHouse('<?php echo $houseKey; ?>')">
                        <div class="house-logo">
                            <?php if (!empty($houseInfo['image'])): ?>
                                <img src="<?php echo htmlspecialchars($houseInfo['image']); ?>" 
                                     alt="<?php echo $houseInfo['name']; ?> Crest">
                            <?php else: ?>
                                <div style="font-size: 5rem;"><?php echo $houseInfo['logo']; ?></div>
                            <?php endif; ?>
                        </div>
                        <h3 style="margin: 15px 0 8px; font-weight: 700; color: var(--text-primary);">
                            <?php echo $houseInfo['name']; ?>
                        </h3>
                        <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 15px;">
                            <?php echo $houseInfo['description']; ?>
                        </p>
                        <div style="display: flex; gap: 8px; justify-content: center; margin: 15px 0;">
                            <?php foreach ($houseInfo['colors'] as $color): ?>
                                <div style="width: 32px; height: 32px; background: <?php echo $color; ?>; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.2);"></div>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($userHouse === $houseKey): ?>
                            <div style="color: var(--primary); font-weight: 700; font-size: 1.1rem;">
                                Selected
                            </div>
                        <?php else: ?>
                            <button class="btn btn-primary btn-sm">Select House</button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Badges & Points History (unchanged) -->
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
                <p style="color: var(--text-secondary);">No badges earned yet. Keep contributing!</p>
            <?php endif; ?>
        </div>
        
        <div class="profile-card">
            <h2 style="color: var(--text-primary); margin-bottom: 20px; font-weight: 700;">Recent Points History</h2>
            <?php if (count($pointsHistory) > 0): ?>
                <table class="points-table">
                    <thead>
                        <tr><th>Points</th><th>Reason</th><th>Date</th></tr>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        async function selectHouse(house) {
            try {
                const response = await fetch('api/houses.php?action=select', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ house: house })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert('House selected successfully! Theme updated.', 'success');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showAlert(data.message || 'Failed to select house', 'error');
                }
            } catch (error) {
                showAlert('Error. Try again.', 'error');
            }
        }
    </script>
</body>
</html>