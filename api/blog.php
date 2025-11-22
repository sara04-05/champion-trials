<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/blog.php';
require_once __DIR__ . '/../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        if ($action === 'all') {
            $limit = $_GET['limit'] ?? 20;
            $posts = getBlogPosts($limit);
            echo json_encode(['success' => true, 'posts' => $posts]);
        } elseif ($action === 'single' && isset($_GET['id'])) {
            $post = getBlogPostById($_GET['id']);
            if ($post) {
                echo json_encode(['success' => true, 'post' => $post]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Post not found']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        break;
        
    case 'POST':
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }
        
        if ($action === 'create') {
            // Handle both JSON and form-data
            if (isset($_POST['title'])) {
                // Form data (with file upload)
                $title = sanitize($_POST['title']);
                $content = sanitize($_POST['content']);
            } else {
                // JSON data
                $data = json_decode(file_get_contents('php://input'), true);
                $title = sanitize($data['title'] ?? '');
                $content = sanitize($data['content'] ?? '');
            }
            
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $uploadDir = BLOG_IMAGES_DIR;
                $fileName = time() . '_' . basename($_FILES['image']['name']);
                $targetPath = $uploadDir . $fileName;
                
                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $fileType = $_FILES['image']['type'];
                
                if (in_array($fileType, $allowedTypes)) {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                        $imagePath = 'uploads/blog/' . $fileName;
                    }
                }
            }
            
            $result = createBlogPost(
                $_SESSION['user_id'],
                $title,
                $content,
                $imagePath
            );
            
            echo json_encode($result);
        } elseif ($action === 'comment') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            $result = addBlogComment(
                $data['post_id'],
                $_SESSION['user_id'],
                sanitize($data['comment'])
            );
            
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>

