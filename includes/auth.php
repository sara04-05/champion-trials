<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Registration
function registerUser($name, $surname, $username, $email, $password, $state, $city) {
    $conn = getDBConnection();
    
    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return ['success' => false, 'message' => 'Username or email already exists'];
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (name, surname, username, email, password, state, city) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $surname, $username, $email, $hashedPassword, $state, $city);
    
    if ($stmt->execute()) {
        $userId = $conn->insert_id;
        $stmt->close();
        $conn->close();
        return ['success' => true, 'user_id' => $userId];
    } else {
        $stmt->close();
        $conn->close();
        return ['success' => false, 'message' => 'Registration failed'];
    }
}

// Login
function loginUser($username, $password, $state = null, $city = null) {
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("SELECT id, username, email, password, role, role_approved, name, surname, state, city FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['role_approved'] = $user['role_approved'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['surname'] = $user['surname'];
            
            // Update location if provided, otherwise keep existing
            if ($state && $city) {
                $_SESSION['state'] = $state;
                $_SESSION['city'] = $city;
                // Update in database
                $updateStmt = $conn->prepare("UPDATE users SET state = ?, city = ? WHERE id = ?");
                $updateStmt->bind_param("ssi", $state, $city, $user['id']);
                $updateStmt->execute();
                $updateStmt->close();
            } else {
                $_SESSION['state'] = $user['state'];
                $_SESSION['city'] = $user['city'];
            }
            
            $stmt->close();
            $conn->close();
            return ['success' => true];
        }
    }
    
    $stmt->close();
    $conn->close();
    return ['success' => false, 'message' => 'Invalid username or password'];
}

// Logout
function logoutUser() {
    session_unset();
    session_destroy();
}

// Check if user can access resource based on role
function hasRole($requiredRoles) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $userRole = getUserRole();
    
    if (is_array($requiredRoles)) {
        return in_array($userRole, $requiredRoles);
    }
    
    return $userRole === $requiredRoles;
}
?>

