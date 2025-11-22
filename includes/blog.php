<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Create blog post
function createBlogPost($userId, $title, $content, $imagePath = null) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("INSERT INTO blog_posts (user_id, title, content, image_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $userId, $title, $content, $imagePath);
    
    if ($stmt->execute()) {
        $postId = $conn->insert_id;
        
        // Award points
        awardPoints($userId, POINTS_BLOG_POST, "Posted blog: " . $title);
        
        $stmt->close();
        $conn->close();
        return ['success' => true, 'post_id' => $postId];
    } else {
        $stmt->close();
        $conn->close();
        return ['success' => false, 'message' => 'Failed to create blog post'];
    }
}

// Get all blog posts
function getBlogPosts($limit = 20) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("
        SELECT bp.*, u.username, u.name, u.surname, u.role,
        (SELECT COUNT(*) FROM blog_comments WHERE blog_post_id = bp.id) as comment_count
        FROM blog_posts bp
        JOIN users u ON bp.user_id = u.id
        ORDER BY bp.created_at DESC
        LIMIT ?
    ");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    return $posts;
}

// Get single blog post
function getBlogPostById($postId) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("
        SELECT bp.*, u.username, u.name, u.surname, u.role
        FROM blog_posts bp
        JOIN users u ON bp.user_id = u.id
        WHERE bp.id = ?
    ");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $post = $result->fetch_assoc();
    
    if ($post) {
        // Get comments
        $stmt2 = $conn->prepare("
            SELECT bc.*, u.username, u.name, u.surname
            FROM blog_comments bc
            JOIN users u ON bc.user_id = u.id
            WHERE bc.blog_post_id = ?
            ORDER BY bc.created_at ASC
        ");
        $stmt2->bind_param("i", $postId);
        $stmt2->execute();
        $comments = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        $post['comments'] = $comments;
        $stmt2->close();
    }
    
    $stmt->close();
    $conn->close();
    
    return $post;
}

// Add blog comment
function addBlogComment($postId, $userId, $commentText) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("INSERT INTO blog_comments (blog_post_id, user_id, comment_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $postId, $userId, $commentText);
    
    if ($stmt->execute()) {
        // Award points
        awardPoints($userId, POINTS_COMMENT, "Commented on blog post");
        
        $stmt->close();
        $conn->close();
        return ['success' => true];
    }
    
    $stmt->close();
    $conn->close();
    return ['success' => false];
}

// Helper function for awarding points (if not already included)
function awardPoints($userId, $points, $reason) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("UPDATE users SET points = points + ? WHERE id = ?");
    $stmt->bind_param("ii", $points, $userId);
    $stmt->execute();
    $stmt->close();
    
    $stmt = $conn->prepare("INSERT INTO points_history (user_id, points, reason) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $userId, $points, $reason);
    $stmt->execute();
    $stmt->close();
    
    $conn->close();
}
?>

