<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/issues.php';
require_once __DIR__ . '/../includes/blog.php';

if (!isAdmin()) {
    redirect('../index.php');
}

$conn = getDBConnection();

// Get statistics
$stats = [];
$result = $conn->query("SELECT COUNT(*) as total FROM issues");
$stats['total_issues'] = $result->fetch_assoc()['total'];
$result = $conn->query("SELECT COUNT(*) as total FROM issues WHERE status = 'fixed'");
$stats['fixed_issues'] = $result->fetch_assoc()['total'];
$result = $conn->query("SELECT COUNT(*) as total FROM issues WHERE status = 'pending'");
$stats['pending_issues'] = $result->fetch_assoc()['total'];
$result = $conn->query("SELECT COUNT(*) as total FROM issues WHERE status = 'in_progress'");
$stats['in_progress_issues'] = $result->fetch_assoc()['total'];
$result = $conn->query("SELECT AVG(DATEDIFF(updated_at, reported_at)) as avg_days FROM issues WHERE status = 'fixed'");
$stats['avg_resolution_days'] = round($result->fetch_assoc()['avg_days'] ?? 0, 1);

$filters = [
    'category' => $_GET['category'] ?? 'all',
    'status' => $_GET['status'] ?? 'all',
    'city' => $_GET['city'] ?? ''
];
$issues = getIssues($filters);
$blogPosts = getBlogPosts(5);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - fixIT</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Manrope:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <?php include '../includes/theme-loader.php'; ?>

    <style>
        :root {
            --white: #ffffff;
            --offwhite: #fafafa;
            --lightgray: #f1f5f9;
            --midgray: #e2e8f0;
            --text: #0f172a;
            --text-light: #475569;
            --red: #ef4444;
            --blue: #3b82f6;
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

        h1, h2, h3 { font-family: 'Manrope', sans-serif; font-weight: 800; letter-spacing: -0.5px; }

        /* Same Hero */
        .admin-hero {
            background: var(--white);
            padding: 120px 20px 80px;
            text-align: center;
            margin-top: 70px;
            border-bottom: 5px solid var(--green);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .admin-hero h1 {
            font-size: 3.8rem;
            color: var(--text);
        }

        .admin-hero h1 i { color: var(--green); margin-right: 12px; }

        .dashboard-container {
            max-width: 1400px;
            margin: 100px auto;
            padding: 0 20px;
        }

        .page-title {
            font-size: 2.8rem;
            margin-bottom: 50px;
            text-align: center;
        }

        /* Stat Cards */
        .stat-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 40px 30px;
            text-align: center;
            box-shadow: var(--shadow);
            border: 1px solid var(--midgray);
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }

        .stat-value {
            font-size: 3.2rem;
            font-weight: 800;
            color: var(--green);
            margin-bottom: 12px;
        }

        .stat-label {
            font-size: 1.1rem;
            color: var(--text-light);
            font-weight: 500;
        }

        /* Section Cards */
        .section-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 50px;
            margin-bottom: 50px;
            box-shadow: var(--shadow);
            border: 1px solid var(--midgray);
        }

        .section-card h3 {
            font-size: 2rem;
            margin-bottom: 30px;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        #adminMap {
            height: 520px;
            border-radius: var(--radius);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-top: 20px;
        }

        /* Filters */
        .filter-form .form-control,
        .filter-form .form-select {
            border-radius: 16px;
            padding: 14px 18px;
            font-size: 1rem;
        }

        .filter-form .btn {
            background: var(--green);
            color: white;
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 600;
        }

        /* Issues Table */
        .table {
            margin: 0;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0,0,0,0.06);
        }

        .table thead {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: white;
        }

        .table thead th {
            padding: 18px;
            font-weight: 600;
            border: none;
        }

        .table tbody tr:hover {
            background: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        }

        .table td {
            padding: 20px;
            vertical-align: middle;
        }

        .table .form-select {
            border-radius: 50px;
            padding: 8px 16px;
        }

        .badge {
            padding: 6px 14px;
            border-radius: 50px;
            font-weight: 600;
        }

        /* Blog Section */
        .blog-post-item {
            padding: 24px 0;
            border-bottom: 1px solid var(--midgray);
        }

        .blog-post-item:last-child { border-bottom: none; }

        .blog-post-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
        }

        .blog-post-meta {
            color: var(--text-light);
            font-size: 0.95rem;
        }

        @media (max-width: 768px) {
            .admin-hero h1 { font-size: 2.8rem; }
            .stat-value { font-size: 2.4rem; }
            .section-card { padding: 30px 20px; }
            .page-title { font-size: 2.2rem; }
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <!-- Hero -->
    <div class="admin-hero">
        <h1>Admin Dashboard</h1>
    </div>

    <div class="dashboard-container">

        <h2 class="page-title">Platform Overview</h2>

        <!-- Statistics -->
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($stats['total_issues']); ?></div>
                    <div class="stat-label">Total Issues Reported</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($stats['fixed_issues']); ?></div>
                    <div class="stat-label">Issues Fixed</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($stats['pending_issues']); ?></div>
                    <div class="stat-label">Pending Review</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['avg_resolution_days']; ?> days</div>
                    <div class="stat-label">Avg. Resolution Time</div>
                </div>
            </div>
        </div>

        <!-- Map -->
        <div class="section-card">
            <h3>All Reported Issues on Map</h3>
            <div id="adminMap"></div>
        </div>

        <!-- Filters -->
        <div class="section-card">
            <h3>Filter & Search Issues</h3>
            <form method="GET" class="row g-3 filter-form">
                <div class="col-md-4">
                    <select name="category" class="form-select">
                        <option value="all" <?php echo ($filters['category'] === 'all') ? 'selected' : ''; ?>>All Categories</option>
                        <?php foreach (ISSUE_CATEGORIES as $key => $cat): ?>
                            <option value="<?php echo $key; ?>" <?php echo ($filters['category'] === $key) ? 'selected' : ''; ?>>
                                <?php echo $cat['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="all" <?php echo ($filters['status'] === 'all') ? 'selected' : ''; ?>>All Status</option>
                        <option value="pending" <?php echo ($filters['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="in_progress" <?php echo ($filters['status'] === 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                        <option value="fixed" <?php echo ($filters['status'] === 'fixed') ? 'selected' : ''; ?>>Fixed</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="city" class="form-control" placeholder="City name..." value="<?php echo htmlspecialchars($filters['city']); ?>">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn">Apply Filters</button>
                </div>
            </form>
        </div>

        <!-- Issues Table -->
        <div class="section-card">
            <h3>Manage Reported Issues</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Urgency</th>
                            <th>City</th>
                            <th>Reported By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($issues as $issue): ?>
                            <tr>
                                <td><strong>#<?php echo $issue['id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($issue['title']); ?></td>
                                <td><?php echo ucfirst(str_replace('_', ' ', $issue['category'])); ?></td>
                                <td>
                                    <select class="form-select form-select-sm" onchange="updateIssueStatus(<?php echo $issue['id']; ?>, this.value)">
                                        <option value="pending" <?php echo ($issue['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="in_progress" <?php echo ($issue['status'] === 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                        <option value="fixed" <?php echo ($issue['status'] === 'fixed') ? 'selected' : ''; ?>>Fixed</option>
                                    </select>
                                </td>
                                <td>
                                    <span class="badge <?php 
                                        echo $issue['urgency_level'] === 'high' ? 'bg-danger' : 
                                            ($issue['urgency_level'] === 'medium' ? 'bg-warning text-dark' : 'bg-success'); 
                                    ?>">
                                        <?php echo ucfirst($issue['urgency_level']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($issue['city']); ?></td>
                                <td><?php echo htmlspecialchars($issue['username']); ?></td>
                                <td>
                                    <a href="../issue-details.php?id=<?php echo $issue['id']; ?>" class="btn btn-primary btn-sm">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Blog Posts -->
        <div class="section-card">
            <h3>Recent Community Posts</h3>
            <?php if (count($blogPosts) > 0): ?>
                <?php foreach ($blogPosts as $post): ?>
                    <div class="blog-post-item">
                        <div class="blog-post-title">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </div>
                        <div class="blog-post-meta">
                            By <?php echo htmlspecialchars($post['name'] . ' ' . $post['surname']); ?> 
                            • <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="text-end mt-4">
                    <a href="../blog.php" class="btn btn-outline-primary">View All Posts</a>
                </div>
            <?php else: ?>
                <p class="text-muted">No community posts yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="../assets/js/main.js"></script>
    <script>
        const adminMap = L.map('adminMap').setView([42.6026, 20.9030], 8);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(adminMap);

        async function loadAllIssues() {
            try {
                const response = await fetch('../api/issues.php?action=all&limit=1000');
                const data = await response.json();

                if (data.success && data.issues) {
                    data.issues.forEach(issue => {
                        const color = issue.status === 'fixed' ? '#10b981' : 
                                    issue.status === 'in_progress' ? '#3b82f6' : '#f59e0b';

                        const marker = L.circleMarker([issue.latitude, issue.longitude], {
                            color: color,
                            radius: 8,
                            weight: 3,
                            fillOpacity: 0.9
                        }).addTo(adminMap);

                        marker.bindPopup(`
                            <strong>${issue.title}</strong><br>
                            <small>Status: <strong style="color:${color}">${issue.status.replace('_', ' ')}</strong></small><br>
                            Category: ${issue.category.replace('_', ' ')} | Urgency: ${issue.urgency_level}<br>
                            <a href="../issue-details.php?id=${issue.id}" class="btn btn-sm btn-primary mt-2 d-block">View Details</a>
                        `);
                    });
                }
            } catch (error) {
                console.error('Error loading issues:', error);
            }
        }

        loadAllIssues();

        async function updateIssueStatus(issueId, status) {
            try {
                const response = await fetch('../api/issues.php?action=update_status', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ issue_id: issueId, status: status })
                });
                const data = await response.json();
                if (data.success) {
                    showAlert('Status updated successfully', 'success');
                    setTimeout(() => location.reload(), 1200);
                } else {
                    showAlert('Update failed', 'error');
                }
            } catch (error) {
                showAlert('Network error', 'error');
            }
        }
    </script>
</body>
</html>