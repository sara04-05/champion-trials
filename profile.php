<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/houses.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$conn = getDBConnection();
$userHouse = getUserHouse($_SESSION['user_id']);
$houseImage = $userHouse && !empty(HOUSES[$userHouse]['image']) ? HOUSES[$userHouse]['image'] : null;
$houseLogo = $userHouse ? (HOUSES[$userHouse]['logo'] ?? null) : null;

// [All your existing PHP queries remain 100% unchanged]
$stmt = $conn->prepare("SELECT b.id, b.name, b.description, b.icon FROM badges b INNER JOIN user_badges ub ON b.id = ub.badge_id WHERE ub.user_id = ? ORDER BY ub.earned_at DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$badgesResult = $stmt->get_result();
$badges = $badgesResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(DISTINCT i.id) as total_issues, COUNT(DISTINCT ic.id) as total_comments, COUNT(DISTINCT bp.id) as total_posts, SUM(CASE WHEN i.status = 'fixed' THEN 1 ELSE 0 END) as fixed_issues FROM users u LEFT JOIN issues i ON u.id = i.user_id LEFT JOIN issue_comments ic ON u.id = ic.user_id LEFT JOIN blog_posts bp ON u.id = bp.user_id WHERE u.id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM points_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$pointsHistory = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $conn->prepare("SELECT points FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
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
        body { font-family: 'Inter', sans-serif; background: var(--offwhite); color: var(--text); line-height: 1.8; }
        h1,h2,h3 { font-family: 'Manrope', sans-serif; font-weight: 800; letter-spacing: -0.5px; }

        .profile-hero {
            background: var(--white); padding: 120px 20px 80px; text-align: center; margin-top: 70px;
            border-bottom: 5px solid var(--green); box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .profile-hero h1 { font-size: 3.8rem; color: var(--text); }
        .profile-hero h1 i { color: var(--green); margin-right: 12px; }

        .profile-container { max-width: 1100px; margin: 100px auto; padding: 0 20px; }
        .profile-card { background: var(--white); border-radius: var(--radius); padding: 50px; margin-bottom: 50px; box-shadow: var(--shadow); border: 1px solid var(--midgray); }

        .profile-avatar { width: 160px; height: 160px; border-radius: 50%; overflow: hidden; margin: 0 auto 30px; border: 8px solid var(--green); box-shadow: 0 15px 40px rgba(16,185,129,0.3); transition: var(--transition); }
        .profile-avatar:hover { transform: scale(1.08); box-shadow: 0 20px 50px rgba(16,185,129,0.4); }
        .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .profile-avatar-fallback { background: linear-gradient(135deg,var(--green),#0d8f63); color: white; font-size: 4.5rem; font-weight: 800; display: flex; align-items: center; justify-content: center; }

        .profile-name { font-size: 2.6rem; text-align: center; margin-bottom: 8px; }
        .profile-role { font-size: 1.3rem; color: var(--green); font-weight: 600; text-align: center; margin-bottom: 16px; }
        .profile-points { text-align: center; font-size: 1.6rem; font-weight: 700; color: var(--green); margin: 20px 0 40px; }

        /* Pretty Stats Table */
        .stats-table { width: 100%; border-collapse: separate; border-spacing: 0 16px; margin: 20px 0; }
        .stats-table td { padding: 20px 30px; background: var(--lightgray); border-radius: var(--radius); text-align: center; font-size: 1.1rem; }
        .stats-table .value { font-size: 2.4rem; font-weight: 800; color: var(--green); display: block; margin-bottom: 6px; }
        .stats-table .label { color: var(--text-light); font-weight: 500; }

        /* 2×2 House Grid */
        .houses-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 32px;
            margin-top: 30px;
        }
        @media (max-width: 768px) { .houses-grid { grid-template-columns: 1fr; } }

        .house-card { background: var(--white); border-radius: var(--radius); padding: 36px 28px; text-align: center; border: 3px solid var(--midgray); transition: var(--transition); cursor: pointer; }
        .house-card:hover { transform: translateY(-12px); box-shadow: 0 20px 50px rgba(0,0,0,0.12); }
        .house-card[selected], .house-card[data-house="<?php echo $userHouse; ?>"] { border-color: var(--green) !important; box-shadow: 0 15px 40px rgba(16,185,129,0.25); }
        .house-logo img { max-height: 140px; border-radius: 18px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); transition: var(--transition); }
        .house-logo img:hover { transform: scale(1.1); }
        .house-colors { display: flex; justify-content: center; gap: 14px; margin: 18px 0; }
        .house-color { width: 38px; height: 38px; border-radius: 50%; border: 4px solid white; box-shadow: 0 4px 12px rgba(0,0,0,0.2); }

        /* Pretty Points Table */
        .points-table { width: 100%; border-collapse: collapse; margin-top: 20px; background: var(--white); border-radius: var(--radius); overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.06); }
        .points-table thead { background: linear-gradient(135deg,#0f172a,#1e293b); color: white; }
        .points-table th { padding: 18px; font-weight: 600; }
        .points-table tbody tr { transition: var(--transition); }
        .points-table tbody tr:hover { background: #f0fdf4; transform: translateY(-2px); }
        .points-table td { padding: 20px; border-bottom: 1px solid var(--midgray); }
        .points-positive { color: var(--green); font-weight: 800; font-size: 1.2rem; }

        .badge-item { background: var(--lightgray); padding: 24px; border-radius: 20px; text-align: center; min-width: 180px; transition: var(--transition); }
        .badge-item:hover { transform: translateY(-8px); box-shadow: 0 12px 30px rgba(0,0,0,0.1); }
        .badge-icon { font-size: 3.2rem; margin-bottom: 12px; }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>


    <div class="profile-container">

        <!-- Header + Stats Table -->
        <div class="profile-card">
            <div class="profile-avatar">
                <?php if ($houseImage): ?>
                    <img src="<?php echo htmlspecialchars($houseImage); ?>" alt="House Crest">
                <?php else: ?>
                    <div class="profile-avatar-fallback">
                        <?php echo $houseLogo ?: strtoupper(substr($_SESSION['name'],0,1).substr($_SESSION['surname'],0,1)); ?>
                    </div>
                <?php endif; ?>
            </div>

            <h1 class="profile-name"><?php echo htmlspecialchars($_SESSION['name'] . ' ' . $_SESSION['surname']); ?></h1>
            <div class="profile-role"><?php echo ucfirst(str_replace('_',' ', $_SESSION['role'])); ?></div>
            <div class="profile-points">⭐ <?php echo number_format($userPoints); ?> Points</div>

            <!-- Beautiful Stats Table -->
            <table class="stats-table">
                <tr>
                    <td><span class="value"><?php echo $stats['total_issues']; ?></span><span class="label">Issues Reported</span></td>
                    <td><span class="value"><?php echo $stats['fixed_issues']; ?></span><span class="label">Issues Fixed</span></td>
                    <td><span class="value"><?php echo $stats['total_comments']; ?></span><span class="label">Comments Made</span></td>
                    <td><span class="value"><?php echo $stats['total_posts']; ?></span><span class="label">Blog Posts</span></td>
                </tr>
            </table>
        </div>

        <!-- Houses: 2×2 Grid -->
        <div class="profile-card">
            <h2 style="text-align:center; margin-bottom:30px;">Choose Your House</h2>

            <?php if ($userHouse): ?>
                <div style="background:#f0fdf4; padding:30px; border-radius:20px; text-align:center; border:3px solid var(--green); margin-bottom:40px;">
                    <h3 style="margin:0 0 16px; font-size:1.8rem;">Current House: <strong><?php echo HOUSES[$userHouse]['name']; ?></strong></h3>
                    <?php if ($houseImage): ?>
                        <img src="<?php echo htmlspecialchars($houseImage); ?>" alt="Current" style="max-height:110px; border-radius:16px; box-shadow:0 8px 25px rgba(0,0,0,0.15);">
                    <?php else: ?>
                        <div style="font-size:5rem; margin:16px 0;"><?php echo $houseLogo; ?></div>
                    <?php endif; ?>
                    <p style="margin-top:16px; color:var(--text-light);"><?php echo HOUSES[$userHouse]['description']; ?></p>
                </div>
            <?php endif; ?>

            <div class="houses-grid">
                <?php foreach (HOUSES as $houseKey => $houseInfo): ?>
                    <div class="house-card <?php echo ($userHouse === $houseKey) ? 'selected' : ''; ?>" 
                         data-house="<?php echo $houseKey; ?>" onclick="selectHouse('<?php echo $houseKey; ?>')">
                        <div class="house-logo">
                            <?php if (!empty($houseInfo['image'])): ?>
                                <img src="<?php echo htmlspecialchars($houseInfo['image']); ?>" alt="<?php echo $houseInfo['name']; ?>">
                            <?php else: ?>
                                <div style="font-size:6rem;"><?php echo $houseInfo['logo']; ?></div>
                            <?php endif; ?>
                        </div>
                        <h3 style="margin:20px 0 10px;"><?php echo $houseInfo['name']; ?></h3>
                        <p style="font-size:0.95rem; color:var(--text-light); margin:12px 0;"><?php echo $houseInfo['description']; ?></p>
                        <div class="house-colors">
                            <?php foreach ($houseInfo['colors'] as $color): ?>
                                <div class="house-color" style="background:<?php echo $color; ?>"></div>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($userHouse === $houseKey): ?>
                            <div style="color:var(--green); font-weight:800; margin-top:16px; font-size:1.2rem;">Selected</div>
                        <?php else: ?>
                            <button class="btn btn-primary mt-3" style="border-radius:50px; padding:10px 28px;">Select House</button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Badges -->
        <div class="profile-card">
            <h2 style="text-align:center; margin-bottom:30px;">My Badges</h2>
            <?php if ($badges): ?>
                <div style="display:flex; flex-wrap:wrap; gap:24px; justify-content:center;">
                    <?php foreach ($badges as $badge): ?>
                        <div class="badge-item">
                            <div class="badge-icon"><?php echo htmlspecialchars($badge['icon']); ?></div>
                            <div class="badge-name"><?php echo htmlspecialchars($badge['name']); ?></div>
                            <div class="badge-desc"><?php echo htmlspecialchars($badge['description']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="text-align:center; color:var(--text-light); font-size:1.1rem;">No badges yet — keep contributing!</p>
            <?php endif; ?>
        </div>

        <!-- Points History — Now Super Pretty -->
        <div class="profile-card">
            <h2 style="text-align:center; margin-bottom:30px;">Recent Points Activity</h2>
            <?php if ($pointsHistory): ?>
                <table class="points-table">
                    <thead>
                        <tr>
                            <th>Points</th>
                            <th>Reason</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pointsHistory as $h): ?>
                            <tr>
                                <td class="points-positive">+<?php echo $h['points']; ?></td>
                                <td><?php echo htmlspecialchars($h['reason']); ?></td>
                                <td><?php echo date('M j, Y \a\t g:i A', strtotime($h['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align:center; color:var(--text-light);">No points activity yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        async function selectHouse(house) {
            const res = await fetch('api/houses.php?action=select', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ house })
            });
            const data = await res.json();
            if (data.success) {
                showAlert('House selected! Reloading...', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert(data.message || 'Failed', 'error');
            }
        }
    </script>
</body>
</html>