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

$record_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($record_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid record ID']);
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
