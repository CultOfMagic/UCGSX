<?php
// Database configuration
$host = 'localhost';
$dbname = 'ucgs_inventory';
$username = 'root';
$password = '';

try {
    // Initialize the PDO connection
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection errors
    die("Database connection failed: " . $e->getMessage());
}

?>
