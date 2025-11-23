<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/issues.php'; // Include issues.php to use awardPoints() function

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
    
    // Get blog post details to notify the author
    $stmt = $conn->prepare("SELECT user_id, title FROM blog_posts WHERE id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();
    
    if (!$post) {
        $conn->close();
        return ['success' => false, 'message' => 'Blog post not found'];
    }
    
    // Insert comment
    $stmt = $conn->prepare("INSERT INTO blog_comments (blog_post_id, user_id, comment_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $postId, $userId, $commentText);
    
    if ($stmt->execute()) {
        // Award points to commenter
        awardPoints($userId, POINTS_COMMENT, "Commented on blog post");
        
        // Check for badges after commenting
        checkAndAwardBadges($userId);
        
        // Send notification to blog post author (if not commenting on own post)
        if ($post['user_id'] != $userId) {
            $commenterName = getUserNameById($userId);
            $title = "New Comment on Your Post";
            $message = "{$commenterName} commented on your blog post \"{$post['title']}\" (Post #{$postId})";
            createNotification($post['user_id'], $title, $message, "blog_comment");
        }
        
        $stmt->close();
        $conn->close();
        return ['success' => true];
    }
    
    $stmt->close();
    $conn->close();
    return ['success' => false];
}
?>

