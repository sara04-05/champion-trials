<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

if (!isAdmin()) {
    redirect('../index.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: manage_users.php');
    exit;
}

$id = (int)$_POST['id'];
$name = trim($_POST['name']);
$surname = trim($_POST['surname']);
$username = trim($_POST['username']);
$email = trim($_POST['email']);

// Basic validation
if (empty($name) || empty($surname) || empty($username) || empty($email)) {
    $_SESSION['error'] = "All fields are required.";
    header("Location: update_user.php?id=$id");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Invalid email format.";
    header("Location: update_user.php?id=$id");
    exit;
}

$conn = getDBConnection();

// Check if username or email already exists (excluding current user)
$sql = "SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $username, $email, $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['error'] = "Username or email already taken by another user.";
    header("Location: edit_user.php?id=$id");
    exit;
}

// Update the user
$update_sql = "UPDATE users SET name = ?, surname = ?, username = ?, email = ? WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("ssssi", $name, $surname, $username, $email, $id);

if ($update_stmt->execute()) {
    $_SESSION['success'] = "User updated successfully!";
} else {
    $_SESSION['error'] = "Failed to update user.";
}

$update_stmt->close();
$stmt->close();
$conn->close();

header("Location: edit_user.php?id=$id");
exit;