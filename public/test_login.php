<?php
/**
 * Login Test - Debug Script
 */

define('APP_ACCESS', true);
require_once '../includes/config.php';
require_once '../includes/functions.php';

echo "<h2>Database Connection Test</h2>";

// Test 1: Check if database connection works
if (isset($pdo)) {
    echo "✓ Database connected successfully<br>";
    echo "Database: " . DB_NAME . "<br><br>";
} else {
    echo "✗ Database connection failed<br><br>";
}

// Test 2: Check if users table exists and has data
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<h3>Users Table Check:</h3>";
    echo "✓ Users table exists<br>";
    echo "Total users: " . $result['count'] . "<br><br>";
} catch (Exception $e) {
    echo "✗ Users table error: " . $e->getMessage() . "<br><br>";
}

// Test 3: Check admin user
try {
    $stmt = $pdo->query("SELECT id, username, email, role, status FROM users WHERE username = 'admin'");
    $user = $stmt->fetch();
    
    echo "<h3>Admin User Check:</h3>";
    if ($user) {
        echo "✓ Admin user found<br>";
        echo "ID: " . $user['id'] . "<br>";
        echo "Username: " . $user['username'] . "<br>";
        echo "Email: " . $user['email'] . "<br>";
        echo "Role: " . $user['role'] . "<br>";
        echo "Status: " . $user['status'] . "<br><br>";
    } else {
        echo "✗ Admin user NOT found<br><br>";
    }
} catch (Exception $e) {
    echo "✗ Error checking admin user: " . $e->getMessage() . "<br><br>";
}

// Test 4: Test password verification
try {
    $stmt = $pdo->query("SELECT password FROM users WHERE username = 'admin'");
    $user = $stmt->fetch();
    
    echo "<h3>Password Test:</h3>";
    if ($user) {
        $stored_hash = $user['password'];
        $test_password = 'admin123';
        
        echo "Stored hash: " . substr($stored_hash, 0, 30) . "...<br>";
        echo "Testing password: " . $test_password . "<br>";
        
        if (password_verify($test_password, $stored_hash)) {
            echo "✓ Password verification SUCCESSFUL<br>";
        } else {
            echo "✗ Password verification FAILED<br>";
            echo "<br><strong>Generating new hash for 'admin123':</strong><br>";
            $new_hash = password_hash('admin123', PASSWORD_DEFAULT);
            echo "New hash: " . $new_hash . "<br>";
            echo "<br><strong>SQL to fix:</strong><br>";
            echo "<code>UPDATE users SET password = '" . $new_hash . "' WHERE username = 'admin';</code><br>";
        }
    }
} catch (Exception $e) {
    echo "✗ Error testing password: " . $e->getMessage() . "<br>";
}

echo "<br><hr>";
echo "<h3>Next Steps:</h3>";
echo "1. If password verification failed, run the UPDATE SQL command above in phpMyAdmin<br>";
echo "2. Then try logging in again at: <a href='login.php'>login.php</a><br>";
?>
