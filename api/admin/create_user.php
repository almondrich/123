<?php
/**
 * API: Create New User
 */

define('APP_ACCESS', true);
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

// Require admin authentication
require_login();
require_admin();

// Rate limiting - prevent abuse
if (!check_rate_limit('admin_create_user', 10, 300)) {
    set_flash('error', 'Too many user creation attempts. Please wait 5 minutes.');
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
$full_name = trim($_POST['full_name'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'user';
$status = $_POST['status'] ?? 'active';

// Validation
$errors = [];

if (empty($full_name)) {
    $errors[] = 'Full name is required';
}

if (empty($username)) {
    $errors[] = 'Username is required';
} elseif (strlen($username) < 3) {
    $errors[] = 'Username must be at least 3 characters';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

if (empty($password)) {
    $errors[] = 'Password is required';
} else {
    $password_validation = validate_password_strength($password);
    if (!$password_validation['valid']) {
        $errors = array_merge($errors, $password_validation['errors']);
    }
}

if (!in_array($role, ['user', 'admin'])) {
    $errors[] = 'Invalid role';
}

if (!in_array($status, ['active', 'inactive'])) {
    $errors[] = 'Invalid status';
}

// Check if username already exists
if (empty($errors)) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        $errors[] = 'Username already exists';
    }
}

// Check if email already exists
if (empty($errors)) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = 'Email already exists';
    }
}

// If there are errors, return
if (!empty($errors)) {
    set_flash('error', implode('<br>', $errors));
    header('Location: ../../public/admin/users.php');
    exit;
}

try {
    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $pdo->prepare("
        INSERT INTO users (username, password, full_name, email, role, status)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $username,
        $password_hash,
        $full_name,
        $email,
        $role,
        $status
    ]);

    // Log admin action
    log_security_event('admin_user_created', "Created user: $username (Role: $role, Status: $status)", 'info');

    set_flash('success', "User '$username' created successfully!");
    header('Location: ../../public/admin/users.php');
    exit;

} catch (PDOException $e) {
    error_log("Error creating user: " . $e->getMessage());
    set_flash('error', 'Database error occurred while creating user');
    header('Location: ../../public/admin/users.php');
    exit;
}
