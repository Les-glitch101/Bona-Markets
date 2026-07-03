<?php
// ============================================================
// DATABASE CONNECTION (MySQL for XAMPP)
// ============================================================

// Database configuration
$host = 'sql304.infinityfree.com';
$dbname = 'if0_42220494_bonamarkets';
$username = 'if0_42220494';          // Default XAMPP MySQL username
$password = 'qj31LsIwGnk0';              // Default XAMPP MySQL password (empty)

// Data Source Name
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";

try {
    // Create PDO instance
    $pdo = new PDO($dsn, $username, $password);
    
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Optional: Test connection
    // echo "✅ Connected to MySQL successfully!";
    
} catch(PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage());
}

// Helper function to get the database connection
function getDB() {
    global $pdo;
    return $pdo;
}
?>