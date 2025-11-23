<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

if (!isAdmin()) {
    header("Location: ../index.php");
    exit();
}

// Mos e mbyll $conn këtu! Do ta mbyllim në fund

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = (int)$_GET['id'];
} else {
    header("Location: ../manage_users.php");
    exit();
}

$conn = getDBConnection();

// Merr përdoruesin
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "User not found.";
    header("Location: ../manage_users.php");
    exit();
}
$user = $result->fetch_assoc();
$stmt->close();

// Procesimi i POST (update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $email   = trim($_POST['email']);
    $role    = $_POST['role'];

    // Validime
    if (empty($name) || empty($surname) || empty($email)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif (!in_array($role, ['user', 'admin'])) {
        $error = "Invalid role.";
    } else {
        // Kontrollo nëse emaili ekziston te dikush tjetër
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check->bind_param("si", $email, $userId);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $error = "This email is already used by another account.";
        }
        $check->close();

        if (!isset($error)) {
            $update = $conn->prepare("UPDATE users SET name = ?, surname = ?, email = ?, role = ? WHERE id = ?");
            $update->bind_param("ssssi", $name, $surname, $email, $role, $userId);

            if ($update->execute()) {
                $_SESSION['success'] = "User updated successfully!";
                header("Location: edit_user.php?id=$userId"); // rifresko faqen
                exit();
            } else {
                $error = "Database error. Try again.";
            }
            $update->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - fixIT</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f9fafb; margin-top: 70px; }
        .container { max-width: 800px; background:#fff; padding:30px; border-radius:10px; box-shadow:0 0 15px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #4ECDC4; border: none; }
        .btn-primary:hover { background-color: #3ba89c; }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center mb-4" style="color:#4ECDC4;">Edit User</h1>

    <!-- Mesazhet e suksesit/gabimit -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="" method="POST">  <!-- KËTU ISHTE GABIMI! -->
        <input type="hidden" name="id" value="<?= $userId ?>">

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Surname</label>
            <input type="text" name="surname" class="form-control" value="<?= htmlspecialchars($user['surname']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-control" required>
                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="../manage_users.php" class="btn btn-secondary me-md-2">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
    </form>
</div>

<?php $conn->close(); // e mbyllim vetëm në fund ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>