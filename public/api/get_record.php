<?php
/**
 * API: Get Record Details
 * Returns record data for modal display
 */

define('APP_ACCESS', true);
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

header('Content-Type: application/json');

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// Require authentication
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Validate record ID format and range
$record_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($record_id <= 0 || $record_id > 2147483647) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid record ID']);
    exit;
}

// Verify ID format (prevent injection attempts)
if (isset($_GET['id']) && !preg_match('/^\d+$/', (string)$_GET['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid record ID format']);
    exit;
}

try {
    
    // Get record with creator information
    $sql = "SELECT pf.*, u.full_name as created_by_name, u.username as created_by_username
            FROM prehospital_forms pf
            LEFT JOIN users u ON pf.created_by = u.id
            WHERE pf.id = ?";

    $stmt = db_query($sql, [$record_id]);
    $record = $stmt->fetch();

    if (!$record) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Record not found']);
        exit;
    }
    
    // AUTHORIZATION CHECK: Verify user has permission to view this record
    // Users can only view their own records unless they are admins
    $current_user = get_auth_user();
    if ($record['created_by'] != $current_user['id'] && !is_admin()) {
        log_security_event('unauthorized_access_attempt', "Attempted to view record ID: $record_id without permission", 'warning');
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access denied. You do not have permission to view this record.']);
        exit;
    }

    // Return record data
    echo json_encode([
        'success' => true,
        'record' => $record
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching record: ' . $e->getMessage()
    ]);
}
