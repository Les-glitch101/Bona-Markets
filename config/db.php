<?php
/**
 * config/db.php – Database connection
 * ============================================================
 * PURPOSE: Establish a PDO connection to the MySQL database.
 * This file is included by all API endpoints and pages that need database access.
 * 
 * CONFIGURATION:
 * - Host: localhost (XAMPP default)
 * - Database: BonaMarketsDB
 * - Username: root (XAMPP default)
 * - Password: (empty for XAMPP)
 * 
 * ERROR HANDLING:
 * - If connection fails, the script stops and shows an error message.
 * - PDO exceptions are enabled to catch SQL errors easily.
 * ============================================================
 */

// Database connection parameters
$host = 'localhost';        // MySQL server address (local machine)
$dbname = 'BonaMarketsDB';  // Name of your database
$username = 'root';         // XAMPP default MySQL user
$password = '';             // XAMPP default password (empty string)

try {
    // Create a new PDO instance (PHP Data Objects)
    // DSN format: driver:host=...;dbname=...;charset=...
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set error mode to exception – any SQL error will throw a PDOException
    // This makes debugging easier because you can catch and display errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array – results come back with column names as keys
    // Example: ['ProductID' => 1, 'Name' => 'Laptop', ...]
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // If connection fails (wrong password, database doesn't exist), show error and stop
    die("Database connection failed: " . $e->getMessage());
}
?>