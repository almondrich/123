<?php
/**
 * Delete Pre-Hospital Care Record
 */

define('APP_ACCESS', true);
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Security headers
header('Content-Type: application/json');
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

// Require authentication
require_login();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request method'], 405);
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // CSRF Protection: Verify token
    $csrf_token = $input['csrf_token'] ?? '';
    if (!verify_token($csrf_token)) {
        json_response(['success' => false, 'message' => 'Invalid security token'], 403);
    }
    
    $record_id = isset($input['id']) ? (int)$input['id'] : 0;
    
    // Validate record ID format and range
    if ($record_id <= 0 || $record_id > 2147483647) {
        throw new Exception('Invalid record ID');
    }
    
    // Verify ID format (prevent injection attempts)
    if (isset($input['id']) && !preg_match('/^\d+$/', (string)$input['id'])) {
        throw new Exception('Invalid record ID format');
    }
    
    // Check if record exists
    $check_sql = "SELECT id, form_number, created_by FROM prehospital_forms WHERE id = ?";
    $check_stmt = db_query($check_sql, [$record_id]);
    $record = $check_stmt->fetch();
    
    if (!$record) {
        throw new Exception('Record not found');
    }
    
    // AUTHORIZATION CHECK: Verify user has permission to delete this record
    // Users can only delete their own records unless they are admins
    $current_user = get_auth_user();
    if ($record['created_by'] != $current_user['id'] && !is_admin()) {
        log_security_event('unauthorized_access_attempt', "Attempted to delete record ID: $record_id without permission", 'warning');
        throw new Exception('Access denied. You do not have permission to delete this record.');
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Delete injuries first (foreign key constraint)
    $delete_injuries_sql = "DELETE FROM injuries WHERE form_id = ?";
    db_query($delete_injuries_sql, [$record_id]);
    
    // Delete main record
    $delete_sql = "DELETE FROM prehospital_forms WHERE id = ?";
    $delete_stmt = db_query($delete_sql, [$record_id]);
    
    if (!$delete_stmt) {
        throw new Exception('Failed to delete record');
    }
    
    // Commit transaction
    $pdo->commit();
    
    // Log activity
    log_activity('form_deleted', "Deleted form: {$record['form_number']}");
    
    json_response([
        'success' => true,
        'message' => 'Record deleted successfully'
    ], 200);
    
} catch (Exception $e) {
    // Rollback on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Delete Record Error: " . $e->getMessage());
    
    json_response([
        'success' => false,
        'message' => $e->getMessage()
    ], 400);
}
