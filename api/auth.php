<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'POST':
        if ($action === 'register') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $result = registerUser(
                sanitize($data['name']),
                sanitize($data['surname']),
                sanitize($data['username']),
                sanitize($data['email']),
                $data['password'],
                sanitize($data['state']),
                sanitize($data['city'])
            );
            
            echo json_encode($result);
        } elseif ($action === 'login') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $result = loginUser(
                $data['username'], 
                $data['password'],
                $data['state'] ?? null,
                $data['city'] ?? null
            );
            echo json_encode($result);
        } elseif ($action === 'logout') {
            logoutUser();
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        break;
        
    case 'GET':
        if ($action === 'check') {
            echo json_encode([
                'logged_in' => isLoggedIn(),
                'user' => isLoggedIn() ? [
                    'id' => $_SESSION['user_id'],
                    'username' => $_SESSION['username'],
                    'name' => $_SESSION['name'],
                    'surname' => $_SESSION['surname'],
                    'role' => $_SESSION['role'],
                    'state' => $_SESSION['state'],
                    'city' => $_SESSION['city']
                ] : null
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>

