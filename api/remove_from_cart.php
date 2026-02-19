<?php
session_start();

if (isset($_POST['product_id']) && isset($_POST['restaurant'])) {
    $productId = intval($_POST['product_id']);
    $restaurant = $_POST['restaurant'];

    if (isset($_SESSION['cart'])) {
        // Find and remove the specific item
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $productId && $item['restaurant'] == $restaurant) {
                unset($_SESSION['cart'][$key]);
                // Re-index the array
                $_SESSION['cart'] = array_values($_SESSION['cart']);
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