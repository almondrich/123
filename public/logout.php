<?php
/**
 * Logout Handler
 */

define('APP_ACCESS', true);
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Perform logout
logout_user();

// Set flash message
set_flash('You have been logged out successfully', 'success');

// Redirect to login
redirect('login.php');
