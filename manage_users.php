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
    <title>Manage Users - fixIT Admin</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Manrope:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php include 'includes/theme-loader.php'; ?>

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

        h1, h2, .section-title {
            font-family: 'Manrope', sans-serif;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

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
            margin-bottom: 20px;
        }

        .admin-hero h1 i { color: var(--green); margin-right: 12px; }

        .admin-container {
            max-width: 1280px;
            margin: 100px auto;
            padding: 0 20px;
        }

        .admin-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 50px;
            box-shadow: var(--shadow);
            border: 1px solid var(--midgray);
            margin-bottom: 40px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-size: 2.8rem;
            margin: 0;
        }

        .btn-add-user {
            background: var(--green);
            color: white;
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-add-user:hover {
            background: #0d8f63;
            transform: translateY(-4px);
            box-shadow: 0 18px 40px rgba(16, 185, 129, 0.4);
        }

        /* Beautiful Table */
        .table {
            background: var(--white);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0,0,0,0.06);
            margin: 0;
        }

        .table thead {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: white;
        }

        .table thead th {
            padding: 18px 16px;
            font-weight: 600;
            letter-spacing: 0.5px;
            border: none;
        }

        .table tbody tr {
            transition: var(--transition);
        }

        .table tbody tr:hover {
            background: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        }

        .table td {
            padding: 20px 16px;
            vertical-align: middle;
            border-top: 1px solid var(--midgray);
        }

        .badge-admin { background: var(--red); }
        .badge-user { background: var(--blue); }
        .badge-professional { background: linear-gradient(135deg, #8b5cf6, #6366f1); }

        .btn-edit, .btn-delete {
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .btn-edit {
            background: var(--blue);
            color: white;
        }

        .btn-delete {
            background: var(--red);
            color: white;
        }

        .btn-edit:hover { background: #2563eb; }
        .btn-delete:hover { background: #dc2626; }

        /* Alerts */
        .alert {
            border-radius: 16px;
            padding: 16px 24px;
            font-weight: 500;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        /* Modal */
        .modal-overlay {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(12px);
        }

        .glassmorphism-modal {
            background: var(--white);
            border-radius: var(--radius);
            padding: 40px;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }

        .modal-title {
            font-size: 1.8rem;
            margin-bottom: 16px;
            color: var(--red);
        }

        #confirmDeleteButton {
            background: var(--red);
            padding: 12px 28px;
            border-radius: 50px;
        }

        @media (max-width: 768px) {
            .admin-hero h1 { font-size: 2.8rem; }
            .page-header { flex-direction: column; gap: 20px; text-align: center; }
            .admin-card { padding: 30px 20px; }
            .table { font-size: 0.9rem; }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Same Hero -->
    <div class="admin-hero">
        <h1><i class="fas fa-users-cog"></i> Admin Panel</h1>
    </div>

    <div class="admin-container">
        <div class="admin-card">

            <div class="page-header">
                <h1>Manage Users</h1>
                <a href="add-user.php" class="btn-add-user">
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
                                <td><strong>#<?php echo $user['id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($user['name'] . ' ' . $user['surname']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge <?php 
                                        echo $user['role'] === 'admin' ? 'badge-admin' : 
                                            ($user['role'] === 'regular_user' ? 'badge-user' : 'badge-professional');
                                    ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($user['state'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($user['city'] ?? '-'); ?></td>
                                <td>
                                    <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="btn btn-edit btn-sm">
                                        Edit
                                    </a>
                                    <button class="btn btn-delete btn-sm" onclick="confirmDeleteUser(<?php echo $user['id']; ?>)">
                                        Delete
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
            <p style="color:#475569; font-size:1.1rem;">
                Are you sure you want to delete this user?<br>
                <strong>This action cannot be undone.</strong>
            </p>
            <div class="d-flex gap-3 mt-4 justify-content-end">
                <button class="btn btn-secondary px-4 py-2" onclick="closeDeleteModal()">Cancel</button>
                <button class="btn btn-danger px-4 py-2" id="confirmDeleteButton">Delete User</button>
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
</body>
</html>