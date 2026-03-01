<?php
/**
 * Database Connection File
 * 
 * This file establishes a connection to the MySQL database using PDO (PHP Data Objects).
 * PDO provides a secure way to interact with databases and prevents SQL injection attacks.
 * 
 * This file should be included at the top of any PHP file that needs database access.
 * 
 * Usage: include 'includes/connect.php';
 */

// Database configuration
$host = 'localhost';        // Database server (usually 'localhost' for local development)
$dbname = 'smartdine';      // Name of the database
$username = 'root';         // Database username (default is 'root' for XAMPP/WAMP)
$password = '';             // Database password (empty by default for local development)

try {
    // Create a new PDO connection
    // PDO is more secure than mysqli and supports prepared statements
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set error mode to exception
    // This makes PDO throw exceptions for errors instead of silent failures
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Connection successful - $conn can now be used for database queries
} catch (PDOException $e) {
    // If connection fails, display error message and stop execution
    die("Database connection failed: " . $e->getMessage());
}
?>