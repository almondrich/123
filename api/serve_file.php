<?php
/**
 * Secure File Serving Script
 * Serves uploaded files with authentication and security checks
 */

define('APP_ACCESS', true);
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Require authentication
require_login();

// Get file path from query parameter
$filePath = $_GET['file'] ?? '';

if (empty($filePath)) {
    http_response_code(400);
    die('Invalid file path');
}

// Security: Ensure file path is within uploads_private directory
$realPath = realpath($filePath);
$uploadDir = realpath(UPLOAD_DIR);

if (!$realPath || !$uploadDir || strpos($realPath, $uploadDir) !== 0) {
    http_response_code(403);
    die('Access denied');
}

// Check if file exists
if (!file_exists($realPath)) {
    http_response_code(404);
    die('File not found');
}

// Get file info
$fileSize = filesize($realPath);
$mimeType = mime_content_type($realPath);

// Set headers for secure serving
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . $fileSize);
header('Content-Disposition: inline; filename="' . basename($realPath) . '"');
header('Cache-Control: private, max-age=3600');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

// Log file access
log_security_event('file_access', 'Accessed file: ' . basename($realPath), 'info');

// Output file content
readfile($realPath);
exit();
