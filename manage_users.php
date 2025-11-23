<?php
require_once __DIR__ . '/config/config.php';  // Correct path to config.php
require_once __DIR__ . '/includes/auth.php';  // Correct path to auth.php
require_once __DIR__ . '/config/database.php';

if (!isAdmin()) {
    redirect('../index.php');
}

$conn = getDBConnection();

// Get all users for management
$result = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $result->fetch_all(MYSQLI_ASSOC);

// Get user roles for selection
$roles = ['user', 'admin'];

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
        /* Your custom styles here */
    </style>
</head>
<body>
    <?php include __DIR__ . '/includes/navbar.php';  // Correct path to navbar.php ?>

    <div class="dashboard-container">
        <h1 style="color: #333; margin-bottom: 30px;">Manage Users</h1>

        <!-- Users Table -->
        <div class="chart-container">
            <h3 style="color: #333; margin-bottom: 20px;">All Users</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
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
                                    <select class="form-control form-control-sm" onchange="updateUserRole(<?php echo $user['id']; ?>, this.value)">
                                        <option value="user" <?php echo ($user['role'] === 'user') ? 'selected' : ''; ?>>User</option>
                                        <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                </td>
                                <td>
                                    <a href="../user-details.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                    <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update user role
        async function updateUserRole(userId, role) {
            try {
                const response = await fetch('../api/users.php?action=update_role', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ user_id: userId, role: role })
                });

                const data = await response.json();

                if (data.success) {
                    showAlert('User role updated successfully', 'success');
                    // Update the role in the table without reloading
                    document.querySelector(`#user-${userId} .form-control-sm`).value = role;
                } else {
                    showAlert('Failed to update role', 'error');
                }
            } catch (error) {
                console.error('Error updating user role:', error);
                showAlert('An error occurred', 'error');
            }
        }

        // Delete user
        async function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                try {
                    const response = await fetch('../api/users.php?action=delete_user', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ user_id: userId })
                    });

                    const data = await response.json();

                    if (data.success) {
                        showAlert('User deleted successfully', 'success');
                        // Remove the row from the table without reloading
                        document.querySelector(`#user-${userId}`).remove();
                    } else {
                        showAlert('Failed to delete user', 'error');
                    }
                } catch (error) {
                    console.error('Error deleting user:', error);
                    showAlert('An error occurred', 'error');
                }
            }
        }

        // Show alert message
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} mt-3`;
            alertDiv.innerText = message;
            document.body.appendChild(alertDiv);

            setTimeout(() => alertDiv.remove(), 5000);
        }
    </script>
</body>
</html>
