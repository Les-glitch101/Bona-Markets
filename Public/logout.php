<?php
/**
 * Bona Markets - Logout Page
 * Destroys user session and redirects to homepage
 * 
 * Week 1: Basic logout functionality
 * Week 2+: Will work with database sessions
 */

// Start session to destroy it
session_start();

// Clear all session variables
$_SESSION = array();

// If using session cookies, delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session completely
session_destroy();

// Redirect to homepage with logout success message
header('Location: index.php?logout=success');
exit;
?>
