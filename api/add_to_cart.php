<?php
// Prevent any output before JSON (whitespace, BOM, etc.)
ob_start();

session_start();

// Always return JSON
header('Content-Type: application/json');
header('Cache-Control: no-cache');

// Discard any accidental output so far
ob_clean();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'No product_id provided.']);
    exit;
}

$productId  = intval($_POST['product_id']);
$restaurant = isset($_POST['restaurant']) ? trim($_POST['restaurant']) : 'Smart Dine';

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product_id.']);
    exit;
}

// Check if already in cart
foreach ($_SESSION['cart'] as &$item) {
    if ($item['id'] == $productId && $item['restaurant'] === $restaurant) {
        // Already exists — increment quantity instead of rejecting
        $item['quantity'] = ($item['quantity'] ?? 1) + 1;
        echo json_encode(['success' => true, 'message' => 'Quantity updated.']);
        exit;
    }
}
unset($item);

// Add new item
$_SESSION['cart'][] = [
    'id'         => $productId,
    'restaurant' => $restaurant,
    'quantity'   => 1,
];

echo json_encode(['success' => true]);
