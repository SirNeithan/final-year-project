<?php
/**
 * Database Connection File (Dynamic: Local & Production)
 * * This script checks for cloud environment variables first. 
 * If they are not found, it defaults to your local development credentials.
 */

// 1. Define Local Environment Defaults
$local_host     = 'localhost';
$local_port     = '3306';       // Default MySQL port
$local_dbname   = 'smartdine';  // Your local DB name
$local_username = 'root';       // Default local username
$local_password = '';           // Default local password (empty)

// 2. Fetch Cloud Credentials OR use Local Defaults
// The getenv() function looks for the variables you set in your hosting platform.
// The "?:" (Elvis operator) says: "Use the environment variable if it exists, otherwise use the local variable."
$host     = getenv('DB_HOST') ?: $local_host;
$port     = getenv('DB_PORT') ?: $local_port;
$dbname   = getenv('DB_NAME') ?: $local_dbname;
$username = getenv('DB_USER') ?: $local_username;

// Passwords require a slightly different check because your local password might be an empty string
$env_pass = getenv('DB_PASS');
$password = ($env_pass !== false) ? $env_pass : $local_password;

try {
    // 3. Create the PDO connection
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    
    // Set error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Optional: If you run into SSL issues on the live server, you can add PDO::MYSQL_ATTR_SSL_CA here later.

} catch (PDOException $e) {
    // If connection fails, display error message and stop execution
    die("Database connection failed: " . $e->getMessage());
}
?>