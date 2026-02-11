<?php
session_start();

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['product_id']) && isset($_POST['restaurant'])) {
    $productId = intval($_POST['product_id']);
    $restaurant = $_POST['restaurant'];

    // Check if the product is already in the cart
    $alreadyInCart = false;
    foreach ($_SESSION['cart'] as $item) {
        if ($item['id'] == $productId && $item['restaurant'] == $restaurant) {
            $alreadyInCart = true;
            break;
        }
    }

    if (!$alreadyInCart) {
        $_SESSION['cart'][] = ['id' => $productId, 'restaurant' => $restaurant]; // Add the product to the cart
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Product already in cart.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>