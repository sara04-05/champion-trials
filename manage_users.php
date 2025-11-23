<?php
require_once __DIR__ . '/config/config.php';  
require_once __DIR__ . '/includes/auth.php';  
require_once __DIR__ . '/config/database.php';

if (!isAdmin()) {
    redirect('../index.php');
}

$conn = getDBConnection();

$result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - fixIT</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* [Your existing beautiful styles remain unchanged] */
        body { background-color: #f9fafb; font-family: 'Arial', sans-serif; color: #333; margin-top: 70px; }
        .navbar { background-color: #4ECDC4; }
        .navbar .navbar-brand, .navbar .navbar-nav .nav-link { color: white; }
        .navbar .navbar-nav .nav-link:hover { color: #3ba89c; }
        .dashboard-container { max-width: 1200px; margin: 50px auto; padding: 30px; background-color: #fff; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1, h3 { color: #4ECDC4; font-weight: bold; margin-bottom: 25px; }
        .table thead { background-color: #4ECDC4; color: white; }
        .btn-primary { background-color: #4ECDC4; border-color: #4ECDC4; }
        .btn-primary:hover { background-color: #3ba89c; border-color: #3ba89c; }
        .btn-danger:hover { background-color: #c0392b; border-color: #c0392b; }
        .toast { position: fixed; top: 20px; right: 20px; z-index: 9999; width: 300px; opacity: 0; transition: opacity 0.5s ease; }
        .toast.show { opacity: 1; }
        .toast-success { background-color: #28a745; color: white; }
        .toast-error { background-color: #dc3545; color: white; }
    </style>
</head>
<body>

    <!-- Navbar (unchanged) -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-tools"></i> fixIT</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (!isLoggedIn()): ?>
                        <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="../about.php">About Us</a></li>
                        <li class="nav-item"><a class="nav-link" href="../contact.php">Contact Us</a></li>
                        <li class="nav-item"><a class="nav-link" href="../blog.php">Make Your City Better</a></li>
                        <li class="nav-item"><a class="nav-link" href="#" onclick="openLoginModal(); return false;">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="#" onclick="openSignupModal(); return false;">Sign Up</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="../index.php">Home (Map)</a></li>
                        <li class="nav-item"><a class="nav-link" href="../report.php">Report an Issue</a></li>
                        <li class="nav-item"><a class="nav-link" href="../blog.php">Make Your City Better</a></li>
                        <li class="nav-item"><a class="nav-link" href="../my-reports.php">My Reports</a></li>
                        <li class="nav-item"><a class="nav-link" href="../notifications.php">Notifications</a></li>
                        <li class="nav-item"><a class="nav-link" href="../profile.php">Profile</a></li>
                        <li class="nav-item"><a class="nav-link" href="#" onclick="logout(); return false;">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <h1>Manage Users</h1>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
    <?php foreach ($users as $user): ?>
        <tr id="user-<?php echo $user['id']; ?>">
            <td><?php echo $user['id']; ?></td>
            <td><?php echo htmlspecialchars($user['name'] . ' ' . $user['surname']); ?></td>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td>
                <!-- KËTU ËSHTË NDRYSHIMI I VETËM! -->
                <a href="edit.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary">
                    Edit
                </a>

                <button class="btn btn-sm btn-danger" onclick="confirmDeleteUser(<?php echo $user['id']; ?>)">
                    Delete
                </button>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
            </table>
        </div>
    </div>

    <!-- Toast -->
    <div id="toast" class="toast"></div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this user? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete User</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let userIdToDelete = null;

        function confirmDeleteUser(userId) {
            userIdToDelete = userId;
            new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
        }

        document.getElementById('confirmDeleteButton').addEventListener('click', function() {
            if (userIdToDelete) {
                // Redirect to your existing delete.php script
                window.location.href = `delete.php?id=${userIdToDelete}`;
            }
        });

        // Optional: Keep role update via AJAX if you want (or make it redirect too)
        async function updateUserRole(userId, role) {
            try {
                const response = await fetch('../api/users.php?action=update_role', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ user_id: userId, role: role })
                });
                const data = await response.json();
                if (data.success) {
                    showToast('Role updated successfully', 'success');
                } else {
                    showToast('Failed to update role', 'error');
                }
            } catch (err) {
                showToast('Error updating role', 'error');
            }
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast toast-${type} show`;
            setTimeout(() => toast.className = 'toast', 4000);
        }

        // Auto-hide success message after delete (optional enhancement)
        <?php if (isset($_GET['deleted'])): ?>
            showToast('User deleted successfully', 'success');
        <?php endif; ?>
    </script>
</body>
</html>