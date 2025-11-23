<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/houses.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($method === 'POST' && $action === 'select') {
    $data = json_decode(file_get_contents('php://input'), true);
    $house = sanitize($data['house'] ?? '');
    
    if (empty($house)) {
        echo json_encode(['success' => false, 'message' => 'House is required']);
        exit;
    }
    
    $result = setUserHouse($_SESSION['user_id'], $house);
    echo json_encode($result);
} elseif ($method === 'GET' && $action === 'current') {
    $house = getUserHouse($_SESSION['user_id']);
    echo json_encode(['success' => true, 'house' => $house]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>

