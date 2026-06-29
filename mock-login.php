<?php
/**
 * mock-login.php – Temporary login for testing
 * PURPOSE: Simulate a logged-in user without building a full auth system.
 * Sets session variables to pretend the user is buyer with UserID = 1.
 **/

// Start a new session or resume the existing one
// Session allows us to store user ID across multiple pages
session_start();

// Set session variables to pretend this user is logged in as a buyer
$_SESSION['user_id'] = 1;      // UserID 1 exists in your Users table (inserted by SQL)
$_SESSION['role'] = 'BUYER';   // Role for future authorization (e.g., admin vs buyer)

// Redirect to the main product listing page after login
// The browser will automatically go to index.php
header('Location: index.php');

// Stop script execution – no further code should run after redirect
exit;
?>