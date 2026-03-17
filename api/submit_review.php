<?php
session_start();
require '../includes/connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$productId  = intval($data['product_id'] ?? 0);
$restaurant = trim($data['restaurant'] ?? '');
$rating     = intval($data['rating'] ?? 0);
$comment    = trim($data['comment'] ?? '');

if ($rating < 1 || $rating > 5 || !$restaurant) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

try {
    $stmt = $conn->prepare("
        INSERT INTO reviews (user_id, product_id, restaurant, rating, comment)
        VALUES (?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE rating=VALUES(rating), comment=VALUES(comment), created_at=NOW()
    ");
    $stmt->execute([$_SESSION['user_id'], $productId, $restaurant, $rating, $comment]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
