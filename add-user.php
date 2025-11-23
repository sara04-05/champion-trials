<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php'; // For registerUser function

if (!isAdmin()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $state = trim($_POST['state']);
    $city = trim($_POST['city']);
    $role = $_POST['role'] ?? 'regular_user';
    
    // Validation
    if (empty($name) || empty($surname) || empty($username) || empty($email) || empty($password) || empty($state) || empty($city)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        $result = registerUser($name, $surname, $username, $email, $password, $state, $city);
        
        if ($result['success']) {
            // Update role if admin selected different role
            if ($role !== 'regular_user') {
                $conn = getDBConnection();
                $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
                $stmt->bind_param("si", $role, $result['user_id']);
                $stmt->execute();
                $stmt->close();
                $conn->close();
            }
            
            $success = "User created successfully!";
            // Clear form
            $_POST = [];
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - fixIT</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: var(--bg-primary);
            min-height: 100vh;
            color: var(--text-primary);
        }
        
        .form-container {
            max-width: 800px;
            margin: 100px auto 50px;
            padding: var(--spacing-xl);
            background: var(--color-white);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="form-container">
        <h1 style="color: var(--text-primary); margin-bottom: var(--spacing-lg);">Add New User</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="surname" class="form-control" value="<?php echo htmlspecialchars($_POST['surname'] ?? ''); ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            
            <div class="form-group">
                <label>State/Region</label>
                <select name="state" class="form-control" required>
                    <option value="">Select State/Region</option>
                    <option value="Kosovo" <?php echo (isset($_POST['state']) && $_POST['state'] === 'Kosovo') ? 'selected' : ''; ?>>Kosovo</option>
                    <option value="Albania" <?php echo (isset($_POST['state']) && $_POST['state'] === 'Albania') ? 'selected' : ''; ?>>Albania</option>
                    <option value="North Macedonia" <?php echo (isset($_POST['state']) && $_POST['state'] === 'North Macedonia') ? 'selected' : ''; ?>>North Macedonia</option>
                    <option value="Serbia" <?php echo (isset($_POST['state']) && $_POST['state'] === 'Serbia') ? 'selected' : ''; ?>>Serbia</option>
                    <option value="Montenegro" <?php echo (isset($_POST['state']) && $_POST['state'] === 'Montenegro') ? 'selected' : ''; ?>>Montenegro</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>City</label>
                <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Role</label>
                <select name="role" class="form-control">
                    <option value="regular_user" <?php echo (isset($_POST['role']) && $_POST['role'] === 'regular_user') ? 'selected' : ''; ?>>Regular User</option>
                    <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="engineer" <?php echo (isset($_POST['role']) && $_POST['role'] === 'engineer') ? 'selected' : ''; ?>>Engineer</option>
                    <option value="safety_inspector" <?php echo (isset($_POST['role']) && $_POST['role'] === 'safety_inspector') ? 'selected' : ''; ?>>Safety Inspector</option>
                    <option value="environmental_officer" <?php echo (isset($_POST['role']) && $_POST['role'] === 'environmental_officer') ? 'selected' : ''; ?>>Environmental Officer</option>
                </select>
            </div>
            
            <div class="d-flex gap-2">
                <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create User</button>
            </div>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    
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

