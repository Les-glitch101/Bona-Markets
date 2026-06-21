<?php
/*
    DATABASE SETUP TEMPLATE

    Copy this file and rename it to:
    database.php

    Then update the values below to match your computer.

    For XAMPP defaults:
    - host = 127.0.0.1
    - dbname = bonamarkets
    - username = root
    - password = empty string ''
*/

$host = '127.0.0.1';
$dbname = 'bonamarkets';
$username = 'root';
$password = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>