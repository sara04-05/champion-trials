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
        /* Global Styles */
        body {
            background-color: #f9fafb;
            font-family: 'Arial', sans-serif;
            color: #333;
            margin-top: 70px;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1, h3 {
            color: #4ECDC4;
            font-weight: bold;
            margin-bottom: 25px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table th, .table td {
            vertical-align: middle;
            padding: 15px;
        }

        .table thead {
            background-color: #4ECDC4;
            color: white;
        }

        .table tbody tr {
            border-bottom: 1px solid #ddd;
            transition: background-color 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .btn-primary {
            background-color: #4ECDC4;
            border-color: #4ECDC4;
        }

        .btn-primary:hover {
            background-color: #3ba89c;
            border-color: #3ba89c;
        }

        .btn-danger {
            background-color: #e74c3c;
            border-color: #e74c3c;
        }

        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }

        .form-control-sm {
            width: 120px;
            display: inline-block;
            margin: 0;
        }

        /* Toast Notification */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            width: 300px;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .toast.show {
            opacity: 1;
        }

        .toast-success {
            background-color: #28a745;
            color: white;
        }

        .toast-error {
            background-color: #dc3545;
            color: white;
        }

        /* Card Styling */
        .user-card {
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
        }

        .user-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 15px;
            }

            .table th, .table td {
                padding: 10px;
            }

            .form-control-sm {
                width: 100px;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/includes/navbar.php';  // Correct path to navbar.php ?>

    <div class="dashboard-container">
        <h1>Manage Users</h1>

        <!-- Users Table -->
        <div class="chart-container">
            <h3>All Users</h3>
            <div class="table-responsive">
                <table class="table table-striped">
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
                            <tr id="user-<?php echo $user['id']; ?>" class="user-card">
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
                                
                                    <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>)">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast"></div>

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
                    showToast('User role updated successfully', 'success');
                    // Update the role in the table without reloading
                    document.querySelector(`#user-${userId} .form-control-sm`).value = role;
                } else {
                    showToast('Failed to update role', 'error');
                }
            } catch (error) {
                console.error('Error updating user role:', error);
                showToast('An error occurred', 'error');
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
                        showToast('User deleted successfully', 'success');
                        // Remove the row from the table without reloading
                        document.querySelector(`#user-${userId}`).remove();
                    } else {
                        showToast('Failed to delete user', 'error');
                    }
                } catch (error) {
                    console.error('Error deleting user:', error);
                    showToast('An error occurred', 'error');
                }
            }
        }

        // Show toast notification
        function showToast(message, type) {
            const toast = document.getElementById('toast');
            toast.className = `toast toast-${type} show`;
            toast.innerText = message;

            setTimeout(() => {
                toast.classList.remove('show');
            }, 5000);
        }
    </script>
</body>
</html>
