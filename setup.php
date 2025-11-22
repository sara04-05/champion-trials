<?php
/**
 * fixIT - Setup Script
 * Run this file once to set up the database and initial configuration
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'city_care');

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>fixIT Setup</title>";
echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:50px auto;padding:20px;background:#0F0F23;color:#ECF0F1;}";
echo "h1{color:#00D4AA;} .success{color:#00D4AA;} .error{color:#E74C3C;} .warning{color:#FDCB6E;}</style></head><body>";
echo "<h1>fixIT - Database Setup</h1>";
echo "<p>Setting up database and initial configuration...</p>";

try {
    // First, connect without database to create it
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Create database if it doesn't exist
    echo "<p>Creating database...</p>";
    if ($conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME)) {
        echo "<p class='success'>✓ Database '" . DB_NAME . "' created successfully</p>";
    } else {
        echo "<p class='warning'>⚠ Database may already exist: " . $conn->error . "</p>";
    }
    
    // Select the database
    $conn->select_db(DB_NAME);
    
    // Read and execute schema
    $schema = file_get_contents(__DIR__ . '/database/schema.sql');
    
    // Remove CREATE DATABASE and USE statements as we've already handled them
    $schema = preg_replace('/CREATE DATABASE.*?;/i', '', $schema);
    $schema = preg_replace('/USE.*?;/i', '', $schema);
    
    // Split by semicolons and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && strlen($statement) > 5) {
            if ($conn->query($statement)) {
                $successCount++;
                if (preg_match('/CREATE TABLE/i', $statement)) {
                    $tableName = preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $statement, $matches) ? $matches[1] : 'table';
                    echo "<p class='success'>✓ Created table: " . $tableName . "</p>";
                } elseif (preg_match('/INSERT INTO/i', $statement)) {
                    echo "<p class='success'>✓ Inserted default data</p>";
                }
            } else {
                // Ignore errors for existing tables/duplicate entries
                if (strpos($conn->error, 'already exists') === false && 
                    strpos($conn->error, 'Duplicate entry') === false) {
                    $errorCount++;
                    echo "<p class='error'>⚠ Error: " . $conn->error . "</p>";
                    echo "<p class='warning'>Statement: " . substr($statement, 0, 100) . "...</p>";
                }
            }
        }
    }
    
    echo "<h2>Setup Complete!</h2>";
    echo "<p class='success'>✓ Successfully executed " . $successCount . " statements</p>";
    if ($errorCount > 0) {
        echo "<p class='warning'>⚠ " . $errorCount . " errors (may be expected if tables already exist)</p>";
    }
    
    echo "<h3>Default Admin Account:</h3>";
    echo "<ul>";
    echo "<li><strong>Username:</strong> admin</li>";
    echo "<li><strong>Password:</strong> admin123</li>";
    echo "<li><strong>Email:</strong> admin@citycare.com</li>";
    echo "</ul>";
    echo "<p class='error'><strong>⚠ Important: Change the admin password after first login!</strong></p>";
    echo "<p><a href='index.php' style='color:#00D4AA;text-decoration:none;font-weight:bold;'>→ Go to fixIT</a></p>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p class='error'><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Please check:</p>";
    echo "<ul>";
    echo "<li>MySQL is running in XAMPP</li>";
    echo "<li>Database credentials in config/database.php are correct</li>";
    echo "<li>You have permission to create databases</li>";
    echo "</ul>";
    echo "<p>You can also manually create the database by:</p>";
    echo "<ol>";
    echo "<li>Open phpMyAdmin (http://localhost/phpmyadmin)</li>";
    echo "<li>Create a new database named 'city_care'</li>";
    echo "<li>Import the file: database/schema.sql</li>";
    echo "</ol>";
}

echo "</body></html>";
?>
