<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

if (!isAdmin()) {
    redirect('index.php');
}

if (!isset($_GET['id'])) {
    redirect('manage_users.php');
}

$id = (int)$_GET['id'];

// Prevent deleting yourself
if ($id === $_SESSION['user_id']) {
    $_SESSION['error'] = "You cannot delete your own account.";
    redirect('manage_users.php');
}

$conn = getDBConnection();

$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    redirect('manage_users.php?deleted=1');
} else {
    $stmt->close();
    $conn->close();
    $_SESSION['error'] = "Failed to delete user.";
    redirect('manage_users.php');
}
