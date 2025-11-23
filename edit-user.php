<?php
require_once __DIR__ . '/config/config.php';  
require_once __DIR__ . '/includes/auth.php';  
require_once __DIR__ . '/config/database.php';

if (!isAdmin()) {
    redirect('../index.php');
}

$conn = getDBConnection();

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Fetch the user from the database
    $result = $conn->query("SELECT * FROM users WHERE id = $userId LIMIT 1");
    $user = $result->fetch_assoc();
} else {
    // If no user ID is provided, redirect
    redirect('../manage_users.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update user details if the form is submitted
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $conn->query("UPDATE users SET name = '$name', surname = '$surname', email = '$email', role = '$role' WHERE id = $userId");

    // Redirect back to the manage users page after update
    redirect('../manage_users.php');
}

$conn->close();
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
        /* Custom Styles for Edit User Page */
        body {
            background-color: #f9fafb;
            font-family: 'Arial', sans-serif;
            color: #333;
            margin-top: 70px;
        }

        .container {
            max-width: 800px;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #4ECDC4;
            font-weight: bold;
            margin-bottom: 25px;
        }

        .form-control {
            border-radius: 8px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #4ECDC4;
            box-shadow: 0 0 5px rgba(78, 205, 196, 0.5);
        }

        .form-label {
            font-weight: bold;
        }

        .btn-primary {
            background-color: #4ECDC4;
            border-color: #4ECDC4;
        }

        .btn-primary:hover {
            background-color: #3ba89c;
            border-color: #3ba89c;
        }

        .btn {
            border-radius: 5px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Edit User</h1>

        <form action="edit-user.php?id=<?php echo $user['id']; ?>" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="surname" class="form-label">Surname</label>
                <input type="text" class="form-control" id="surname" name="surname" value="<?php echo htmlspecialchars($user['surname']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-control" id="role" name="role">
                    <option value="user" <?php echo ($user['role'] === 'user') ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
