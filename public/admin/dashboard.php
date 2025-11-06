<?php
/**
 * Admin Dashboard
 * Redirects to user management for now
 */

define('APP_ACCESS', true);
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

// Require admin authentication
require_login();
require_admin();

// Redirect to user management page
header('Location: users.php');
exit;
