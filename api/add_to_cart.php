<?php
/**
 * Add to Cart API
 * 
 * This file handles adding products to the shopping cart.
 * The cart is stored in the PHP session for the current user.
 * 
 * Expected POST parameters:
 * - product_id: The ID of the product to add
 * - restaurant: The name of the restaurant (optional, defaults to 'Smart Dine')
 * 
 * Returns JSON response indicating success or failure
 */

// Start session to access cart data
session_start();

// Initialize cart array if it doesn't exist in the session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if product_id was sent via POST
if (isset($_POST['product_id'])) {
    // Get product ID and convert to integer for security
    $productId = intval($_POST['product_id']);
    
    // Get restaurant name, default to 'Smart Dine' if not provided
    $restaurant = isset($_POST['restaurant']) ? $_POST['restaurant'] : 'Smart Dine';

    // Check if the product is already in the cart
    // We check both product ID and restaurant to allow same product from different restaurants
    $alreadyInCart = false;
    foreach ($_SESSION['cart'] as $item) {
        if ($item['id'] == $productId && $item['restaurant'] == $restaurant) {
            $alreadyInCart = true;
            break;
        }
    }

    // Only add if not already in cart
    if (!$alreadyInCart) {
        // Add product to cart with initial quantity of 1
        $_SESSION['cart'][] = [
            'id' => $productId, 
            'restaurant' => $restaurant,
            'quantity' => 1
        ];
        // Return success response
        echo json_encode(['success' => true]);
    } else {
        // Product already exists in cart
        echo json_encode(['success' => false, 'message' => 'Product already in cart.']);
    }
} else {
    // Invalid request - product_id not provided
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>