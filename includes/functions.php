<?php
/**
 * Helper Functions
 * Sanitization, Validation, Flash Messages, CSRF Protection
 */

if (!defined('APP_ACCESS')) {
    die('Direct access not permitted');
}

/**
 * Sanitize input data
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Execute safe database query with prepared statements
 */
function db_query($sql, $params = []) {
    global $pdo;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Database Query Error: " . $e->getMessage());
        error_log("SQL: " . $sql);
        return false;
    }
}

/**
 * Set flash message
 */
function set_flash($message, $type = 'info') {
    $_SESSION['flash_message'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Display and clear flash message
 */
function show_flash() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        $alertClass = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ];
        
        $class = $alertClass[$flash['type']] ?? 'alert-info';
        
        echo '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8');
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
        
        unset($_SESSION['flash_message']);
    }
}

/**
 * Generate CSRF token
 */
function generate_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verify_token($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirect helper
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Redirect back
 */
function redirect_back() {
    $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    redirect($referer);
}

/**
 * Validate date format
 */
function validate_date($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Validate time format
 */
function validate_time($time) {
    return preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $time);
}

/**
 * Validate datetime format
 */
function validate_datetime($datetime) {
    return validate_date($datetime, 'Y-m-d\TH:i');
}

/**
 * Handle file upload
 */
function handle_upload($file, $allowed_types = ['jpg', 'jpeg', 'png', 'pdf']) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Invalid file upload'];
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error occurred'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File too large (max 5MB)'];
    }
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $destination = UPLOAD_DIR . $filename;
    
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'message' => 'Failed to move uploaded file'];
}

/**
 * Get client IP address
 */
function get_client_ip() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }
    
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

/**
 * Simple rate limiting (per session)
 */
function check_rate_limit($action, $max_attempts = 5, $time_window = 300) {
    $key = 'rate_limit_' . $action;

    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'start_time' => time()];
    }

    $rate_data = $_SESSION[$key];

    // Reset if time window expired
    if (time() - $rate_data['start_time'] > $time_window) {
        $_SESSION[$key] = ['count' => 1, 'start_time' => time()];
        return true;
    }

    // Check if limit exceeded
    if ($rate_data['count'] >= $max_attempts) {
        return false;
    }

    // Increment counter
    $_SESSION[$key]['count']++;
    return true;
}

/**
 * Check daily form submission limit per user
 */
function check_daily_form_limit($user_id, $max_forms = 50) {
    global $pdo;

    $today = date('Y-m-d');
    $sql = "SELECT COUNT(*) as form_count FROM prehospital_forms
            WHERE created_by = ? AND DATE(created_at) = ?";
    $stmt = db_query($sql, [$user_id, $today]);

    if ($stmt) {
        $result = $stmt->fetch();
        return $result['form_count'] < $max_forms;
    }

    return false; // Error checking, deny submission
}

/**
 * Log activity
 */
function log_activity($action, $details = '') {
    global $pdo;
    
    $user_id = $_SESSION['user_id'] ?? null;
    $ip_address = get_client_ip();
    
    $sql = "INSERT INTO activity_logs (user_id, action, details, ip_address, created_at) 
            VALUES (?, ?, ?, ?, NOW())";
    
    db_query($sql, [$user_id, $action, $details, $ip_address]);
}

/**
 * Escape output
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Verify reCAPTCHA response
 */
function verify_recaptcha($response) {
    $secret_key = RECAPTCHA_SECRET_KEY;

    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $secret_key,
        'response' => $response,
        'remoteip' => get_client_ip()
    ];

    // Use cURL for better reliability
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($result === false || $http_code !== 200) {
        error_log("reCAPTCHA verification failed: HTTP $http_code, cURL error: $curl_error");
        return false;
    }

    $result = json_decode($result, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("reCAPTCHA JSON decode error: " . json_last_error_msg());
        return false;
    }

    if (!isset($result['success'])) {
        error_log("reCAPTCHA response missing 'success' field");
        return false;
    }

    if (!$result['success']) {
        error_log("reCAPTCHA verification failed: " . json_encode($result));
    }

    return $result['success'];
}

/**
 * JSON response helper
 */
function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}
