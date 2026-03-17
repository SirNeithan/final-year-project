<?php

$host = getenv('DB_HOST') ?: 'localhost'; 
$dbname = getenv('DB_NAME') ?: 'smartdine'; 
$username = getenv('DB_USER') ?: 'root'; 
$password = getenv('DB_PASSWORD') ?: ''; 
$port = getenv('DB_PORT') ?: 3306; 

try {
    // Aiven REQUIRES SSL. These options enable it.
    $options = [
        PDO::MYSQL_ATTR_SSL_CA => null, 
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false, 
    ];

    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password, $options);
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>