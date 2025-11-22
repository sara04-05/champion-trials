<?php
// Application Configuration
session_start();

// Base URL
define('BASE_URL', 'http://localhost/GitHub/champion-trials/');

// Google Maps API Key - No longer needed (using OpenStreetMap/Leaflet)
// define('GOOGLE_MAPS_API_KEY', 'YOUR_GOOGLE_MAPS_API_KEY_HERE');

// File Upload Configuration
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('ISSUE_PHOTOS_DIR', UPLOAD_DIR . 'issues/');
define('BLOG_IMAGES_DIR', UPLOAD_DIR . 'blog/');

// Create upload directories if they don't exist
if (!file_exists(ISSUE_PHOTOS_DIR)) {
    mkdir(ISSUE_PHOTOS_DIR, 0777, true);
}
if (!file_exists(BLOG_IMAGES_DIR)) {
    mkdir(BLOG_IMAGES_DIR, 0777, true);
}

// Issue Categories
define('ISSUE_CATEGORIES', [
    'pothole' => ['color' => '#FF6B6B', 'name' => 'Pothole', 'fix_days' => 5],
    'broken_light' => ['color' => '#FFD93D', 'name' => 'Broken Streetlight', 'fix_days' => 2],
    'traffic' => ['color' => '#6BCF7F', 'name' => 'Traffic Issue', 'fix_days' => 0],
    'trash' => ['color' => '#4ECDC4', 'name' => 'Trash Overflow', 'fix_days' => 1],
    'environmental' => ['color' => '#95E1D3', 'name' => 'Environmental Hazard', 'fix_days' => 3],
    'safety' => ['color' => '#F38181', 'name' => 'Safety Issue', 'fix_days' => 2],
    'other' => ['color' => '#AA96DA', 'name' => 'Other', 'fix_days' => 4]
]);

// Points System
define('POINTS_REPORT_ISSUE', 10);
define('POINTS_COMMENT', 5);
define('POINTS_BLOG_POST', 15);
define('POINTS_UPVOTE', 2);

// Helper Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return $_SESSION['role'] ?? 'regular_user';
}

function isAdmin() {
    return isLoggedIn() && getUserRole() === 'admin';
}

function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}
?>

