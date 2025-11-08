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

// Security: Prevent path traversal attacks
// Remove any directory traversal attempts and sanitize filename
$filePath = basename($filePath); // Remove any path components
$filePath = preg_replace('/[^a-zA-Z0-9._-]/', '', $filePath); // Only allow safe characters

// Construct full path from upload directory only (prevent path traversal)
$uploadDir = realpath(UPLOAD_DIR);
if (!$uploadDir || !is_dir($uploadDir)) {
    http_response_code(500);
    error_log("Upload directory not found: " . UPLOAD_DIR);
    die('Upload directory not found');
}

// Build secure path - only within upload directory
$realPath = $uploadDir . DIRECTORY_SEPARATOR . $filePath;

// Verify file exists and is a regular file (not directory)
if (!file_exists($realPath) || !is_file($realPath)) {
    http_response_code(404);
    die('File not found');
}

// Double-check path is within upload directory (prevent symlink attacks)
$realPathResolved = realpath($realPath);
if (!$realPathResolved || strpos($realPathResolved, $uploadDir) !== 0) {
    http_response_code(403);
    error_log("Path traversal attempt detected: " . ($_GET['file'] ?? ''));
    die('Access denied');
}

// Use resolved path for final operations
$realPath = $realPathResolved;

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
