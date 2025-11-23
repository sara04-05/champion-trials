<?php
require_once __DIR__ . '/config/config.php';  
require_once __DIR__ . '/includes/auth.php';  
require_once __DIR__ . '/config/database.php';

if (!isAdmin()) {
    redirect('index.php');
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
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: var(--bg-primary);
            min-height: 100vh;
            color: var(--text-primary);
        }
        
        .users-container {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: var(--spacing-lg);
        }
        
        .section-card {
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            box-shadow: var(--shadow-sm);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="users-container">
        <div class="section-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 style="color: var(--text-primary); margin: 0;">Manage Users</h1>
                <a href="add-user.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New User
                </a>
            </div>
            
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success">
                    User deleted successfully!
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['updated'])): ?>
                <div class="alert alert-success">
                    User updated successfully!
                </div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>State</th>
                            <th>City</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['name'] . ' ' . $user['surname']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge <?php echo $user['role'] === 'admin' ? 'bg-danger' : 'bg-info'; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($user['state'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($user['city'] ?? '-'); ?></td>
                                <td>
                                    <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDeleteUser(<?php echo $user['id']; ?>)">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay" id="deleteUserModal" style="display: none;">
        <div class="glassmorphism-modal">
            <span class="close-modal" onclick="closeDeleteModal()">&times;</span>
            <h2 class="modal-title">Confirm Deletion</h2>
            <p style="color: var(--text-secondary); margin-bottom: var(--spacing-lg);">
                Are you sure you want to delete this user? This action cannot be undone.
            </p>
            <div class="d-flex gap-2">
                <button class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <button class="btn btn-danger" id="confirmDeleteButton">Delete User</button>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        let userIdToDelete = null;
        
        function confirmDeleteUser(userId) {
            userIdToDelete = userId;
            document.getElementById('deleteUserModal').style.display = 'flex';
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteUserModal').style.display = 'none';
            userIdToDelete = null;
        }
        
        document.getElementById('confirmDeleteButton').addEventListener('click', function() {
            if (userIdToDelete) {
                window.location.href = `delete.php?id=${userIdToDelete}`;
            }
        });
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
