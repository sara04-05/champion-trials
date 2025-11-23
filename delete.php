<<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'config/database.php';

if (!isAdmin()) {
    header('Location: ../index.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: manage-users.php');
    exit;
}

$id = (int)$_GET['id'];

$conn = getDBConnection();

$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $conn->close();
    header('Location: manage-users.php?deleted=1');
} else {
    $conn->close();
    header('Location: manage-users.php');
}
exit;
?>