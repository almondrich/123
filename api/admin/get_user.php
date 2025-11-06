<?php
/**
 * API: Get User Details
 */

define('APP_ACCESS', true);
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

// Set JSON header
header('Content-Type: application/json');

// Require admin authentication
require_login();
require_admin();

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get and validate input
$user_id = intval($_GET['id'] ?? 0);

if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

try {
    // Get user details
    $stmt = $pdo->prepare("
        SELECT id, username, full_name, email, role, status, created_at, last_login
        FROM users
        WHERE id = ?
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'user' => $user
    ]);

} catch (PDOException $e) {
    error_log("Error fetching user: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
