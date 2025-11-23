<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

if (!isAdmin()) {
    redirect('../index.php');
}

if (!isset($_GET['id'])) {
    header('Location: manage_users.php');  // me _
    exit;
}

$id = (int)$_GET['id'];
$conn = getDBConnection();

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();

if (!$user_data) {
    header('Location: manage_users.php');  // me _
    exit;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - fixIT Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; padding-top: 80px; }
        .card { max-width: 600px; margin: 0 auto; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .card-header { background: #4ECDC4; color: white; font-weight: bold; }
        .btn-save { background: #4ECDC4; border: none; }
        .btn-save:hover { background: #3ba89c; }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header text-center">
            <h4>Edit User ID: <?php echo $user_data['id']; ?></h4>
        </div>
        <div class="card-body">
            <a href="manage_users.php" class="btn btn-secondary btn-sm mb-3">
                ← Back to Users List
            </a>

            <form action="update_user.php" method="post">
                <input type="hidden" name="id" value="<?php echo $user_data['id']; ?>">

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Surname</label>
                    <input type="text" name="surname" class="form-control" value="<?php echo htmlspecialchars($user_data['surname']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                </div>

                <button type="submit" class="btn btn-save text-white w-100 py-2">Update User</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>