<?php
/**
 * API: Change User Password (Admin)
 */

define('APP_ACCESS', true);
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

// Require admin authentication
require_login();
require_admin();

// Rate limiting - prevent abuse
if (!check_rate_limit('admin_change_password', 10, 300)) {
    set_flash('error', 'Too many password change attempts. Please wait 5 minutes.');
    header('Location: ../../public/admin/users.php');
    exit;
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('error', 'Invalid request method');
    header('Location: ../../public/admin/users.php');
    exit;
}

// Verify CSRF token
if (!verify_token($_POST['csrf_token'] ?? '')) {
    set_flash('error', 'Invalid security token');
    header('Location: ../../public/admin/users.php');
    exit;
}

// Get and validate input
$user_id = intval($_POST['user_id'] ?? 0);
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validation
$errors = [];

if ($user_id <= 0) {
    $errors[] = 'Invalid user ID';
}

if (empty($new_password)) {
    $errors[] = 'New password is required';
} else {
    $password_validation = validate_password_strength($new_password);
    if (!$password_validation['valid']) {
        $errors = array_merge($errors, $password_validation['errors']);
    }
}

if ($new_password !== $confirm_password) {
    $errors[] = 'Passwords do not match';
}

// Verify user exists
if (empty($errors)) {
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $errors[] = 'User not found';
    }
}

// If there are errors, return
if (!empty($errors)) {
    set_flash('error', implode('<br>', $errors));
    header('Location: ../../public/admin/users.php');
    exit;
}

try {
    // Hash new password
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$password_hash, $user_id]);

    set_flash('success', "Password for '{$user['username']}' updated successfully!");
    header('Location: ../../public/admin/users.php');
    exit;

} catch (PDOException $e) {
    error_log("Error changing password: " . $e->getMessage());
    set_flash('error', 'Database error occurred while changing password');
    header('Location: ../../public/admin/users.php');
    exit;
}
