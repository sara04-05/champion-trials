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

// Total issues
$result = $conn->query("SELECT COUNT(*) as total FROM issues");
$stats['total_issues'] = $result->fetch_assoc()['total'];

// Fixed issues
$result = $conn->query("SELECT COUNT(*) as total FROM issues WHERE status = 'fixed'");
$stats['fixed_issues'] = $result->fetch_assoc()['total'];

// Pending issues
$result = $conn->query("SELECT COUNT(*) as total FROM issues WHERE status = 'pending'");
$stats['pending_issues'] = $result->fetch_assoc()['total'];

// In progress issues
$result = $conn->query("SELECT COUNT(*) as total FROM issues WHERE status = 'in_progress'");
$stats['in_progress_issues'] = $result->fetch_assoc()['total'];

// Average resolution time
$result = $conn->query("
    SELECT AVG(DATEDIFF(updated_at, reported_at)) as avg_days 
    FROM issues 
    WHERE status = 'fixed'
");
$stats['avg_resolution_days'] = round($result->fetch_assoc()['avg_days'] ?? 0, 1);

// Get all issues for management
$filters = [
    'category' => $_GET['category'] ?? 'all',
    'status' => $_GET['status'] ?? 'all',
    'city' => $_GET['city'] ?? ''
];
$issues = getIssues($filters);

// Get recent blog posts
$blogPosts = getBlogPosts(5);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - fixIT</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <?php include '../includes/theme-loader.php'; ?>
    <style>
        body {
            background-color: var(--bg-primary);
            min-height: 100vh;
            color: var(--text-primary);
        }
        
        .dashboard-container {
            max-width: 1400px;
            margin: 100px auto 50px;
            padding: var(--spacing-lg);
        }
        
        .stat-card {
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            text-align: center;
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-base);
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }
        
        .stat-value {
            font-size: var(--font-size-xxl);
            font-weight: 700;
            color: var(--primary);
            margin-bottom: var(--spacing-sm);
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }
        
        .section-card {
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            margin-bottom: var(--spacing-lg);
            box-shadow: var(--shadow-sm);
        }
        
        .section-card h3 {
            color: var(--text-primary);
            margin-bottom: var(--spacing-lg);
            font-size: var(--font-size-xl);
        }
        
        #adminMap {
            width: 100%;
            height: 500px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
        }
        
        .issue-row {
            padding: var(--spacing-md);
            border-bottom: 1px solid var(--border-color);
            transition: all var(--transition-fast);
        }
        
        .issue-row:hover {
            background-color: var(--bg-secondary);
        }
        
        .issue-row:last-child {
            border-bottom: none;
        }
        
        .blog-post-item {
            padding: var(--spacing-md);
            border-bottom: 1px solid var(--border-color);
        }
        
        .blog-post-item:last-child {
            border-bottom: none;
        }
        
        .blog-post-title {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: var(--spacing-xs);
        }
        
        .blog-post-meta {
            color: var(--text-muted);
            font-size: var(--font-size-sm);
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="dashboard-container">
        <h1 style="color: var(--text-primary); margin-bottom: var(--spacing-xl);">Admin Dashboard</h1>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['total_issues']; ?></div>
                    <div class="stat-label">Total Issues</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['fixed_issues']; ?></div>
                    <div class="stat-label">Fixed Issues</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['pending_issues']; ?></div>
                    <div class="stat-label">Pending Issues</div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['avg_resolution_days']; ?></div>
                    <div class="stat-label">Avg Resolution (days)</div>
                </div>
            </div>
        </div>
        
        <!-- Map Section -->
        <div class="section-card">
            <h3><i class="fas fa-map-marked-alt"></i> All Issues Map</h3>
            <div id="adminMap"></div>
        </div>
        
        <!-- Filters -->
        <div class="section-card">
            <h3>Filter Issues</h3>
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="all" <?php echo ($filters['category'] === 'all') ? 'selected' : ''; ?>>All</option>
                        <?php foreach (ISSUE_CATEGORIES as $key => $cat): ?>
                            <option value="<?php echo $key; ?>" <?php echo ($filters['category'] === $key) ? 'selected' : ''; ?>>
                                <?php echo $cat['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="all" <?php echo ($filters['status'] === 'all') ? 'selected' : ''; ?>>All</option>
                        <option value="pending" <?php echo ($filters['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="in_progress" <?php echo ($filters['status'] === 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                        <option value="fixed" <?php echo ($filters['status'] === 'fixed') ? 'selected' : ''; ?>>Fixed</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($filters['city']); ?>" placeholder="Filter by city">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
        
        <!-- Issues Management -->
        <div class="section-card">
            <h3>Manage Issues</h3>
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
                                <td><?php echo $issue['id']; ?></td>
                                <td><?php echo htmlspecialchars($issue['title']); ?></td>
                                <td><?php echo ucfirst(str_replace('_', ' ', $issue['category'])); ?></td>
                                <td>
                                    <select class="form-control form-control-sm" onchange="updateIssueStatus(<?php echo $issue['id']; ?>, this.value)">
                                        <option value="pending" <?php echo ($issue['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="in_progress" <?php echo ($issue['status'] === 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                        <option value="fixed" <?php echo ($issue['status'] === 'fixed') ? 'selected' : ''; ?>>Fixed</option>
                                    </select>
                                </td>
                                <td>
                                    <span class="badge <?php 
                                        echo $issue['urgency_level'] === 'high' ? 'bg-danger' : 
                                            ($issue['urgency_level'] === 'medium' ? 'bg-warning' : 'bg-info'); 
                                    ?>">
                                        <?php echo ucfirst($issue['urgency_level']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($issue['city']); ?></td>
                                <td><?php echo htmlspecialchars($issue['username']); ?></td>
                                <td>
                                    <a href="../issue-details.php?id=<?php echo $issue['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Blog Section -->
        <div class="section-card">
            <h3><i class="fas fa-blog"></i> Make Your City Better - Recent Posts</h3>
            <?php if (count($blogPosts) > 0): ?>
                <?php foreach ($blogPosts as $post): ?>
                    <div class="blog-post-item">
                        <div class="blog-post-title">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </div>
                        <div class="blog-post-meta">
                            By <?php echo htmlspecialchars($post['name'] . ' ' . $post['surname']); ?> 
                            on <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="mt-3">
                    <a href="../blog.php" class="btn btn-secondary">View All Posts</a>
                </div>
            <?php else: ?>
                <p style="color: var(--text-muted);">No blog posts yet.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="../assets/js/main.js"></script>
    <script>
        // Initialize map
        const adminMap = L.map('adminMap').setView([42.6026, 20.9030], 6); // Kosovo center
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(adminMap);
        
        // Load all issues
        async function loadAllIssues() {
            try {
                const response = await fetch('../api/issues.php?action=all&limit=1000');
                const data = await response.json();
                
                if (data.success && data.issues) {
                    data.issues.forEach(issue => {
                        const marker = L.marker([issue.latitude, issue.longitude]).addTo(adminMap);
                        const statusColor = issue.status === 'fixed' ? 'green' : 
                                          issue.status === 'in_progress' ? 'blue' : 'orange';
                        marker.bindPopup(`
                            <strong>${issue.title}</strong><br>
                            Status: <span style="color: ${statusColor}">${issue.status}</span><br>
                            Category: ${issue.category}<br>
                            Urgency: ${issue.urgency_level}<br>
                            <a href="../issue-details.php?id=${issue.id}" class="btn btn-sm btn-primary mt-2">View Details</a>
                        `);
                    });
                }
            } catch (error) {
                console.error('Error loading issues:', error);
            }
        }
        
        loadAllIssues();
        
        // Update issue status
        async function updateIssueStatus(issueId, status) {
            try {
                const response = await fetch('../api/issues.php?action=update_status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ issue_id: issueId, status: status })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert('Issue status updated successfully', 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showAlert('Failed to update status', 'error');
                }
            } catch (error) {
                console.error('Error updating status:', error);
                showAlert('An error occurred', 'error');
            }
        }
    </script>
    
    <!-- Accessibility Controls -->
    <div class="accessibility-controls">
        <div class="font-size-controls">
            <button id="fontSizeDecrease" aria-label="Decrease Font Size">A-</button>
            <button id="fontSizeReset" aria-label="Reset Font Size">A</button>
            <button id="fontSizeIncrease" aria-label="Increase Font Size">A+</button>
        </div>
    </div>
</body>
</html>
