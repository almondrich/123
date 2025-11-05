<?php
/**
 * Authentication Functions
 * Login, Logout, Role Checking
 */

if (!defined('APP_ACCESS')) {
    die('Direct access not permitted');
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

/**
 * Require login - redirect if not authenticated
 */
function require_login() {
    if (!is_logged_in()) {
        set_flash('Please login to access this page', 'error');
        redirect('../public/login.php');
    }
}

/**
 * Check if user is admin
 */
function is_admin() {
    return is_logged_in() && $_SESSION['user_role'] === 'admin';
}

/**
 * Require admin role
 */
function require_admin() {
    require_login();
    if (!is_admin()) {
        set_flash('Access denied. Admin privileges required.', 'error');
        redirect('../public/index.php');
    }
}

/**
 * Login user
 */
function login_user($username, $password, $recaptcha_response = null) {
    global $pdo;

    // Rate limiting
    if (!check_rate_limit('login', 5, 300)) {
        return ['success' => false, 'message' => 'Too many login attempts. Please try again later.'];
    }

    // Verify reCAPTCHA if provided
    if ($recaptcha_response && !verify_recaptcha($recaptcha_response)) {
        return ['success' => false, 'message' => 'CAPTCHA verification failed. Please try again.'];
    }

    $sql = "SELECT id, username, password, role, status FROM users WHERE username = ? LIMIT 1";
    $stmt = db_query($sql, [$username]);

    if (!$stmt || $stmt->rowCount() === 0) {
        return ['success' => false, 'message' => 'Invalid username or password'];
    }

    $user = $stmt->fetch();

    if ($user['status'] !== 'active') {
        return ['success' => false, 'message' => 'Account is inactive. Contact administrator.'];
    }

    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Invalid username or password'];
    }

    // Regenerate session ID to prevent fixation
    session_regenerate_id(true);

    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['login_time'] = time();

    // Update last login
    $update_sql = "UPDATE users SET last_login = NOW() WHERE id = ?";
    db_query($update_sql, [$user['id']]);

    // Log activity
    log_activity('user_login', 'User logged in: ' . $username);

    return ['success' => true, 'message' => 'Login successful'];
}

/**
 * Logout user
 */
function logout_user() {
    if (is_logged_in()) {
        log_activity('user_logout', 'User logged out');
    }
    
    $_SESSION = [];
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
}

/**
 * Get authenticated user information
 */
function get_auth_user() {
    if (!is_logged_in()) {
        return [
            'id' => 0,
            'username' => 'Guest',
            'role' => 'guest',
            'email' => '',
            'full_name' => 'Guest User'
        ];
    }
    
    global $pdo;
    $sql = "SELECT id, username, role, email, full_name FROM users WHERE id = ? LIMIT 1";
    $stmt = db_query($sql, [$_SESSION['user_id']]);
    
    if ($stmt && $stmt->rowCount() > 0) {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Fallback if user not found in database
    return [
        'id' => $_SESSION['user_id'] ?? 0,
        'username' => $_SESSION['username'] ?? 'Unknown',
        'role' => $_SESSION['user_role'] ?? 'user',
        'email' => '',
        'full_name' => $_SESSION['username'] ?? 'Unknown User'
    ];
}
