<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Get user's house
function getUserHouse($userId) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT house FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    
    return $user['house'] ?? null;
}

// Set user's house
function setUserHouse($userId, $house) {
    if (!array_key_exists($house, HOUSES)) {
        return ['success' => false, 'message' => 'Invalid house'];
    }
    
    $conn = getDBConnection();
    $houseData = HOUSES[$house];
    $houseLogo = $houseData['logo'];
    
    $stmt = $conn->prepare("UPDATE users SET house = ?, house_logo = ? WHERE id = ?");
    $stmt->bind_param("ssi", $house, $houseLogo, $userId);
    
    if ($stmt->execute()) {
        // Update session
        $_SESSION['house'] = $house;
        $_SESSION['house_logo'] = $houseLogo;
        
        $stmt->close();
        $conn->close();
        return ['success' => true];
    }
    
    $stmt->close();
    $conn->close();
    return ['success' => false, 'message' => 'Failed to update house'];
}

// Get house theme CSS
function getHouseThemeCSS($house) {
    if (!$house || !array_key_exists($house, HOUSES)) {
        return '';
    }
    
    $houseData = HOUSES[$house];
    $primary = $houseData['colors']['primary'];
    $secondary = $houseData['colors']['secondary'];
    $accent = $houseData['colors']['accent'];
    
    return "
    :root {
        --house-primary: {$primary};
        --house-secondary: {$secondary};
        --house-accent: {$accent};
    }
    
    .house-theme {
        --primary: var(--house-primary);
        --color-green: var(--house-primary);
        --color-blue: var(--house-primary);
        --color-red: var(--house-primary);
        --color-purple: var(--house-primary);
    }
    
    .navbar, .btn-primary, .btn-primary:hover,
    .stat-value, .badge-icon, .feature-icon,
    .issue-status.fixed, .notification-item.unread {
        background-color: var(--house-primary) !important;
        border-color: var(--house-primary) !important;
    }
    
    a, .nav-link:hover, .text-primary {
        color: var(--house-primary) !important;
    }
    
    .border-primary {
        border-color: var(--house-primary) !important;
    }
    ";
}
?>

