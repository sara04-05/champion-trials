<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Auto-categorize issue based on description
function categorizeIssue($description) {
    $description = strtolower($description);
    
    if (preg_match('/\b(pothole|hole|road|pavement|asphalt)\b/', $description)) {
        return 'pothole';
    } elseif (preg_match('/\b(light|lamp|streetlight|bulb|dark)\b/', $description)) {
        return 'broken_light';
    } elseif (preg_match('/\b(traffic|jam|congestion|accident|crash)\b/', $description)) {
        return 'traffic';
    } elseif (preg_match('/\b(trash|garbage|waste|dump|overflow)\b/', $description)) {
        return 'trash';
    } elseif (preg_match('/\b(environment|pollution|hazard|toxic|chemical)\b/', $description)) {
        return 'environmental';
    } elseif (preg_match('/\b(safety|danger|unsafe|hazard|risk)\b/', $description)) {
        return 'safety';
    }
    
    return 'other';
}

// Determine urgency level
function determineUrgency($description, $category) {
    $description = strtolower($description);
    
    // High urgency keywords
    if (preg_match('/\b(urgent|emergency|danger|critical|immediate|accident|fire)\b/', $description)) {
        return 'high';
    }
    
    // Category-based urgency
    if (in_array($category, ['safety', 'traffic', 'environmental'])) {
        return 'high';
    }
    
    if ($category === 'trash') {
        return 'medium';
    }
    
    return 'low';
}

// Check for duplicate issues
function checkDuplicateIssue($latitude, $longitude, $radiusKm = 0.1) {
    $conn = getDBConnection();
    
    // Simple distance calculation (Haversine formula approximation)
    $stmt = $conn->prepare("
        SELECT id, title, category, status, 
        (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
        cos(radians(longitude) - radians(?)) + 
        sin(radians(?)) * sin(radians(latitude)))) AS distance
        FROM issues
        HAVING distance < ?
        ORDER BY distance
        LIMIT 5
    ");
    
    $stmt->bind_param("dddd", $latitude, $longitude, $latitude, $radiusKm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $duplicates = [];
    while ($row = $result->fetch_assoc()) {
        $duplicates[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    return $duplicates;
}

// Report new issue
function reportIssue($userId, $title, $description, $latitude, $longitude, $state, $city, $category = null, $urgency = null) {
    $conn = getDBConnection();
    
    // Auto-categorize if not provided
    if (!$category) {
        $category = categorizeIssue($description);
    }
    
    // Use provided urgency, or determine automatically if not provided
    if (!$urgency || !in_array($urgency, ['low', 'medium', 'high'])) {
        $urgency = determineUrgency($description, $category);
    }
    
    // Get estimated fix days
    $fixDays = ISSUE_CATEGORIES[$category]['fix_days'];
    
    // Insert issue
    $stmt = $conn->prepare("
        INSERT INTO issues (user_id, title, description, category, latitude, longitude, state, city, urgency_level, estimated_fix_days)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isssddsssi", $userId, $title, $description, $category, $latitude, $longitude, $state, $city, $urgency, $fixDays);
    
    if ($stmt->execute()) {
        $issueId = $conn->insert_id;
        
        // Award points
        awardPoints($userId, POINTS_REPORT_ISSUE, "Reported issue: " . $title);
        
        // Check for badges
        checkAndAwardBadges($userId);
        
        $stmt->close();
        $conn->close();
        return ['success' => true, 'issue_id' => $issueId];
    } else {
        $stmt->close();
        $conn->close();
        return ['success' => false, 'message' => 'Failed to report issue'];
    }
}

// Get all issues with filters
function getIssues($filters = []) {
    $conn = getDBConnection();
    
    $sql = "SELECT i.*, u.username, u.name, u.surname, 
            (SELECT COUNT(*) FROM issue_upvotes WHERE issue_id = i.id) as upvotes
            FROM issues i
            JOIN users u ON i.user_id = u.id
            WHERE 1=1";
    
    $params = [];
    $types = "";
    
    if (isset($filters['category']) && $filters['category'] !== 'all') {
        $sql .= " AND i.category = ?";
        $params[] = $filters['category'];
        $types .= "s";
    }
    
    if (isset($filters['status']) && $filters['status'] !== 'all') {
        $sql .= " AND i.status = ?";
        $params[] = $filters['status'];
        $types .= "s";
    }
    
    if (isset($filters['urgency']) && $filters['urgency'] !== 'all') {
        $sql .= " AND i.urgency_level = ?";
        $params[] = $filters['urgency'];
        $types .= "s";
    }
    
    if (isset($filters['city']) && !empty($filters['city'])) {
        $sql .= " AND i.city = ?";
        $params[] = $filters['city'];
        $types .= "s";
    }
    
    if (isset($filters['user_id']) && !empty($filters['user_id'])) {
        $sql .= " AND i.user_id = ?";
        $params[] = $filters['user_id'];
        $types .= "i";
    }
    
    if (isset($filters['date_from']) && !empty($filters['date_from'])) {
        $sql .= " AND DATE(i.reported_at) >= ?";
        $params[] = $filters['date_from'];
        $types .= "s";
    }
    
    $sql .= " ORDER BY i.reported_at DESC";
    
    if (isset($filters['limit'])) {
        $sql .= " LIMIT " . intval($filters['limit']);
    }
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $issues = [];
    while ($row = $result->fetch_assoc()) {
        $issues[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    return $issues;
}

// Get single issue details
function getIssueById($issueId) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("
        SELECT i.*, u.username, u.name, u.surname,
        (SELECT COUNT(*) FROM issue_upvotes WHERE issue_id = i.id) as upvotes,
        (SELECT COUNT(*) FROM issue_comments WHERE issue_id = i.id) as comment_count
        FROM issues i
        JOIN users u ON i.user_id = u.id
        WHERE i.id = ?
    ");
    $stmt->bind_param("i", $issueId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $issue = $result->fetch_assoc();
    
    // Get photos
    if ($issue) {
        $stmt2 = $conn->prepare("SELECT * FROM issue_photos WHERE issue_id = ? ORDER BY is_before DESC, uploaded_at ASC");
        $stmt2->bind_param("i", $issueId);
        $stmt2->execute();
        $photos = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        $issue['photos'] = $photos;
        
        // Get comments
        $stmt3 = $conn->prepare("
            SELECT ic.*, u.username, u.name, u.surname
            FROM issue_comments ic
            JOIN users u ON ic.user_id = u.id
            WHERE ic.issue_id = ?
            ORDER BY ic.created_at ASC
        ");
        $stmt3->bind_param("i", $issueId);
        $stmt3->execute();
        $comments = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);
        $issue['comments'] = $comments;
        
        // Get updates
        $stmt4 = $conn->prepare("
            SELECT iu.*, u.username, u.name, u.surname
            FROM issue_updates iu
            JOIN users u ON iu.user_id = u.id
            WHERE iu.issue_id = ?
            ORDER BY iu.created_at DESC
        ");
        $stmt4->bind_param("i", $issueId);
        $stmt4->execute();
        $updates = $stmt4->get_result()->fetch_all(MYSQLI_ASSOC);
        $issue['updates'] = $updates;
        
        $stmt2->close();
        $stmt3->close();
        $stmt4->close();
    }
    
    $stmt->close();
    $conn->close();
    
    return $issue;
}

// Create notification
function createNotification($userId, $title, $message, $type = null) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $userId, $title, $message, $type);
    
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    
    return $success;
}

// Update issue status
function updateIssueStatus($issueId, $status, $assignedWorkerId = null) {
    $conn = getDBConnection();
    
    // Get issue details
    $stmt = $conn->prepare("SELECT user_id, title FROM issues WHERE id = ?");
    $stmt->bind_param("i", $issueId);
    $stmt->execute();
    $result = $stmt->get_result();
    $issue = $result->fetch_assoc();
    $stmt->close();
    
    if (!$issue) {
        $conn->close();
        return false;
    }
    
    // Update status
    $stmt = $conn->prepare("UPDATE issues SET status = ?, assigned_worker_id = ? WHERE id = ?");
    $stmt->bind_param("sii", $status, $assignedWorkerId, $issueId);
    
    $success = $stmt->execute();
    $stmt->close();
    
    // Create notification for issue reporter
    if ($success) {
        $statusText = ucfirst(str_replace('_', ' ', $status));
        $title = "Issue Status Updated";
        $message = "Your issue \"{$issue['title']}\" (Issue #{$issueId}) status has been updated to: {$statusText}";
        createNotification($issue['user_id'], $title, $message, "issue_status");
    }
    
    $conn->close();
    
    return $success;
}

// Add issue comment
function addIssueComment($issueId, $userId, $commentText) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("INSERT INTO issue_comments (issue_id, user_id, comment_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $issueId, $userId, $commentText);
    
    if ($stmt->execute()) {
        // Award points
        awardPoints($userId, POINTS_COMMENT, "Commented on issue");
        
        $stmt->close();
        $conn->close();
        return ['success' => true];
    }
    
    $stmt->close();
    $conn->close();
    return ['success' => false];
}

// Upvote issue
function upvoteIssue($issueId, $userId) {
    $conn = getDBConnection();
    
    // Check if already upvoted
    $stmt = $conn->prepare("SELECT id FROM issue_upvotes WHERE issue_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $issueId, $userId);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        // Remove upvote
        $stmt->close();
        $stmt = $conn->prepare("DELETE FROM issue_upvotes WHERE issue_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $issueId, $userId);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        return ['success' => true, 'action' => 'removed'];
    } else {
        // Add upvote
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO issue_upvotes (issue_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $issueId, $userId);
        
        if ($stmt->execute()) {
            // Award points to issue reporter
            $issue = getIssueById($issueId);
            if ($issue && $issue['user_id'] != $userId) {
                awardPoints($issue['user_id'], POINTS_UPVOTE, "Issue upvoted");
            }
            
            // Award points to upvoter
            awardPoints($userId, POINTS_UPVOTE, "Upvoted issue");
            
            $stmt->close();
            $conn->close();
            return ['success' => true, 'action' => 'added'];
        }
    }
    
    $stmt->close();
    $conn->close();
    return ['success' => false];
}

// Award points to user
function awardPoints($userId, $points, $reason) {
    $conn = getDBConnection();
    
    // Update user points
    $stmt = $conn->prepare("UPDATE users SET points = points + ? WHERE id = ?");
    $stmt->bind_param("ii", $points, $userId);
    $stmt->execute();
    $stmt->close();
    
    // Add to points history
    $stmt = $conn->prepare("INSERT INTO points_history (user_id, points, reason) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $userId, $points, $reason);
    $stmt->execute();
    $stmt->close();
    
    $conn->close();
}

// Check and award badges
function checkAndAwardBadges($userId) {
    $conn = getDBConnection();
    
    // Get user stats
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_issues,
            SUM(CASE WHEN category IN ('pothole') THEN 1 ELSE 0 END) as road_issues,
            SUM(CASE WHEN category IN ('trash', 'environmental') THEN 1 ELSE 0 END) as env_issues
        FROM issues
        WHERE user_id = ?
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    // Check for Active Citizen badge (10+ issues)
    if ($stats['total_issues'] >= 10) {
        $badgeId = 1; // Active Citizen
        $stmt = $conn->prepare("INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $badgeId);
        $stmt->execute();
        $stmt->close();
    }
    
    // Check for Road Saver badge (5+ road issues)
    if ($stats['road_issues'] >= 5) {
        $badgeId = 2; // Road Saver
        $stmt = $conn->prepare("INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $badgeId);
        $stmt->execute();
        $stmt->close();
    }
    
    // Check for Green City Hero badge (5+ environmental issues)
    if ($stats['env_issues'] >= 5) {
        $badgeId = 3; // Green City Hero
        $stmt = $conn->prepare("INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $badgeId);
        $stmt->execute();
        $stmt->close();
    }
    
    $conn->close();
}
?>

