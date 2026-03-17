<?php

$host =getenv('DB_HOST') ?: 'localhost'; // Database host
$dbname = getenv('DB_NAME') ?:'smartdine'; // Database name
$username =getenv('DB_USER') ?: 'root'; // Database username
$password = getenv('DB_PASSWORD') ?: ''; // Database password
$port = getenv('DB_PORT') ?: 3306; // Database port


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