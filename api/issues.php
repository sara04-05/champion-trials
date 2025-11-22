<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/issues.php';
require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        if ($action === 'all') {
            $filters = [
                'category' => $_GET['category'] ?? 'all',
                'status' => $_GET['status'] ?? 'all',
                'urgency' => $_GET['urgency'] ?? 'all',
                'city' => $_GET['city'] ?? '',
                'date_from' => $_GET['date_from'] ?? '',
                'limit' => $_GET['limit'] ?? 100
            ];
            $issues = getIssues($filters);
            echo json_encode(['success' => true, 'issues' => $issues]);
        } elseif ($action === 'single' && isset($_GET['id'])) {
            $issue = getIssueById($_GET['id']);
            if ($issue) {
                echo json_encode(['success' => true, 'issue' => $issue]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Issue not found']);
            }
        } elseif ($action === 'duplicates') {
            if (isset($_GET['lat']) && isset($_GET['lng'])) {
                $duplicates = checkDuplicateIssue($_GET['lat'], $_GET['lng']);
                echo json_encode(['success' => true, 'duplicates' => $duplicates]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Missing coordinates']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        break;
        
    case 'POST':
        if ($action === 'report') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $result = reportIssue(
                $_SESSION['user_id'],
                sanitize($data['title']),
                sanitize($data['description']),
                $data['latitude'],
                $data['longitude'],
                sanitize($data['state']),
                sanitize($data['city']),
                $data['category'] ?? null
            );
            
            echo json_encode($result);
        } elseif ($action === 'comment') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $result = addIssueComment(
                $data['issue_id'],
                $_SESSION['user_id'],
                sanitize($data['comment'])
            );
            
            echo json_encode($result);
        } elseif ($action === 'upvote') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $result = upvoteIssue($data['issue_id'], $_SESSION['user_id']);
            echo json_encode($result);
        } elseif ($action === 'update_status' && isAdmin()) {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $result = updateIssueStatus(
                $data['issue_id'],
                $data['status'],
                $data['assigned_worker_id'] ?? null
            );
            
            echo json_encode(['success' => $result]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>

