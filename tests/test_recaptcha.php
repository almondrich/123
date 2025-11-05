<?php
/**
 * reCAPTCHA Test Script
 */

define('APP_ACCESS', true);
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

echo "<h2>reCAPTCHA Integration Test</h2>";

// Test 1: Check if keys are defined
echo "<h3>Test 1: Configuration Check</h3>";
if (defined('RECAPTCHA_SITE_KEY') && defined('RECAPTCHA_SECRET_KEY')) {
    echo "✓ reCAPTCHA keys are defined<br>";
    echo "Site Key: " . substr(RECAPTCHA_SITE_KEY, 0, 20) . "...<br>";
    echo "Secret Key: " . substr(RECAPTCHA_SECRET_KEY, 0, 20) . "...<br><br>";
} else {
    echo "✗ reCAPTCHA keys are not defined<br><br>";
}

// Test 2: Test verify_recaptcha function with empty response
echo "<h3>Test 2: Empty Response Test</h3>";
$result = verify_recaptcha('');
if (!$result) {
    echo "✓ Empty response correctly rejected<br><br>";
} else {
    echo "✗ Empty response incorrectly accepted<br><br>";
}

// Test 3: Test verify_recaptcha function with invalid response
echo "<h3>Test 3: Invalid Response Test</h3>";
$result = verify_recaptcha('invalid_response');
if (!$result) {
    echo "✓ Invalid response correctly rejected<br><br>";
} else {
    echo "✗ Invalid response incorrectly accepted<br><br>";
}

// Test 4: Check if login.php includes reCAPTCHA widget
echo "<h3>Test 4: Login Form Check</h3>";
$login_content = file_get_contents(__DIR__ . '/../public/login.php');
if (strpos($login_content, 'g-recaptcha') !== false) {
    echo "✓ reCAPTCHA widget found in login.php<br>";
} else {
    echo "✗ reCAPTCHA widget NOT found in login.php<br>";
}

if (strpos($login_content, RECAPTCHA_SITE_KEY) !== false) {
    echo "✓ Site key correctly embedded in login.php<br>";
} else {
    echo "✗ Site key NOT found in login.php<br>";
}

if (strpos($login_content, 'recaptcha/api.js') !== false) {
    echo "✓ reCAPTCHA script included in login.php<br><br>";
} else {
    echo "✗ reCAPTCHA script NOT found in login.php<br><br>";
}

// Test 5: Check server-side verification in auth.php
echo "<h3>Test 5: Server-Side Verification Check</h3>";
$auth_content = file_get_contents(__DIR__ . '/../includes/auth.php');
if (strpos($auth_content, 'verify_recaptcha') !== false) {
    echo "✓ Server-side verification called in auth.php<br>";
} else {
    echo "✗ Server-side verification NOT found in auth.php<br>";
}

if (strpos($auth_content, 'g-recaptcha-response') !== false) {
    echo "✓ reCAPTCHA response field checked in auth.php<br><br>";
} else {
    echo "✗ reCAPTCHA response field NOT checked in auth.php<br><br>";
}

echo "<hr>";
echo "<h3>Summary:</h3>";
echo "The reCAPTCHA integration appears to be fully implemented with the provided keys.<br>";
echo "To manually test:<br>";
echo "1. Visit: <a href='http://localhost:8000/login.php'>http://localhost:8000/login.php</a><br>";
echo "2. Try logging in without completing the CAPTCHA - should show error<br>";
echo "3. Complete the CAPTCHA and try logging in - should work if credentials are correct<br>";
?>
