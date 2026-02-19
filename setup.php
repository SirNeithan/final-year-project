<?php

require 'includes/connect.php';

// Drop existing users table and recreate it
$conn->query("DROP TABLE IF EXISTS users");

$conn->query("CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Insert users with proper bcrypt hashes
$demoPassword = password_hash('password', PASSWORD_BCRYPT);
$adminPassword = password_hash('admin123', PASSWORD_BCRYPT);

$conn->query("INSERT INTO users (username, password, email, role) VALUES ('demo1', '$demoPassword', 'demo1@smartdine.com', 'user')");
$conn->query("INSERT INTO users (username, password, email, role) VALUES ('demo2', '$demoPassword', 'demo2@smartdine.com', 'user')");
$conn->query("INSERT INTO users (username, password, email, role) VALUES ('demo3', '$demoPassword', 'demo3@smartdine.com', 'user')");
$conn->query("INSERT INTO users (username, password, email, role) VALUES ('demo4', '$demoPassword', 'demo4@smartdine.com', 'user')");
$conn->query("INSERT INTO users (username, password, email, role) VALUES ('admin', '$adminPassword', 'admin@smartdine.com', 'admin')");

echo "Setup complete! Users created with proper bcrypt hashes.";
?>

