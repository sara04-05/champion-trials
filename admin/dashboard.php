<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/issues.php';

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

// Most active users
$result = $conn->query("
    SELECT u.username, u.name, u.surname, COUNT(i.id) as issue_count
    FROM users u
    LEFT JOIN issues i ON u.id = i.user_id
    GROUP BY u.id
    ORDER BY issue_count DESC
    LIMIT 10
");
$most_active_users = $result->fetch_all(MYSQLI_ASSOC);

// Issues by category
$result = $conn->query("
    SELECT category, COUNT(*) as count
    FROM issues
    GROUP BY category
    ORDER BY count DESC
");
$issues_by_category = $result->fetch_all(MYSQLI_ASSOC);

// Get all issues for management
$filters = [
    'category' => $_GET['category'] ?? 'all',
    'status' => $_GET['status'] ?? 'all',
    'city' => $_GET['city'] ?? ''
];
$issues = getIssues($filters);

// Get all users for role assignment
$result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $result->fetch_all(MYSQLI_ASSOC);

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-container {
            max-width: 1400px;
            margin: 100px auto 50px;
            padding: 20px;
        }
        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            margin-bottom: 20px;
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
        .chart-container {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="dashboard-container">
        <h1 style="color: var(--text-light); margin-bottom: 30px;">Admin Dashboard</h1>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['total_issues']; ?></div>
                    <div class="stat-label">Total Issues</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['fixed_issues']; ?></div>
                    <div class="stat-label">Fixed Issues</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['pending_issues']; ?></div>
                    <div class="stat-label">Pending Issues</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['avg_resolution_days']; ?></div>
                    <div class="stat-label">Avg Resolution (days)</div>
                </div>
            </div>
        </div>
        
        <!-- Charts -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="chart-container">
                    <h3 style="color: var(--text-light); margin-bottom: 20px;">Issues by Category</h3>
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h3 style="color: var(--text-light); margin-bottom: 20px;">Issue Status</h3>
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="chart-container mb-4">
            <h3 style="color: var(--text-light); margin-bottom: 20px;">Filter Issues</h3>
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" style="color: var(--text-light);">Category</label>
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
                    <label class="form-label" style="color: var(--text-light);">Status</label>
                    <select name="status" class="form-control">
                        <option value="all" <?php echo ($filters['status'] === 'all') ? 'selected' : ''; ?>>All</option>
                        <option value="pending" <?php echo ($filters['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="in_progress" <?php echo ($filters['status'] === 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                        <option value="fixed" <?php echo ($filters['status'] === 'fixed') ? 'selected' : ''; ?>>Fixed</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="color: var(--text-light);">City</label>
                    <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($filters['city']); ?>" placeholder="Filter by city">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
        
        <!-- Issues Table -->
        <div class="chart-container">
            <h3 style="color: var(--text-light); margin-bottom: 20px;">Manage Issues</h3>
            <div class="table-responsive">
                <table class="table" style="color: var(--text-light);">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
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
        
        <!-- Most Active Users -->
        <div class="chart-container">
            <h3 style="color: var(--text-light); margin-bottom: 20px;">Most Active Users</h3>
            <table class="table" style="color: var(--text-light);">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Issues Reported</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($most_active_users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['name'] . ' ' . $user['surname']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo $user['issue_count']; ?></td>
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
    <script src="../assets/js/main.js"></script>
    <script>
        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($issues_by_category, 'category')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($issues_by_category, 'count')); ?>,
                    backgroundColor: ['#FF6B6B', '#FFD93D', '#6BCF7F', '#4ECDC4', '#95E1D3', '#F38181', '#AA96DA']
                }]
            }
        });
        
        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: ['Pending', 'In Progress', 'Fixed'],
                datasets: [{
                    label: 'Issues',
                    data: [
                        <?php echo $stats['pending_issues']; ?>,
                        <?php echo $stats['in_progress_issues']; ?>,
                        <?php echo $stats['fixed_issues']; ?>
                    ],
                    backgroundColor: ['#FFD93D', '#4ECDC4', '#6BCF7F']
                }]
            }
        });
        
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
                } else {
                    showAlert('Failed to update status', 'error');
                }
            } catch (error) {
                console.error('Error updating status:', error);
                showAlert('An error occurred', 'error');
            }
        }
    </script>
</body>
</html>

