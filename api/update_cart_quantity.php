<?php
session_start();

if (isset($_POST['product_id']) && isset($_POST['quantity']) && isset($_POST['restaurant'])) {
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $restaurant = $_POST['restaurant'];

    if ($quantity < 1) {
        echo json_encode(['success' => false, 'message' => 'Quantity must be at least 1.']);
        exit;
    }

    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $productId && $item['restaurant'] == $restaurant) {
                $_SESSION['cart'][$key]['quantity'] = $quantity;
                echo json_encode(['success' => true]);
                exit;
            }
        }
        echo json_encode(['success' => false, 'message' => 'Product not found in cart.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
