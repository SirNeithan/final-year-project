<?php
/**
 * Remove from Cart API
 * 
 * This file handles removing products from the shopping cart.
 * 
 * Expected POST parameters:
 * - product_id: The ID of the product to remove
 * - restaurant: The name of the restaurant
 * 
 * Returns JSON response indicating success or failure
 */

// Start session to access cart data
session_start();

// Check if required parameters were sent via POST
if (isset($_POST['product_id']) && isset($_POST['restaurant'])) {
    // Get product ID and convert to integer for security
    $productId = intval($_POST['product_id']);
    
    // Get restaurant name
    $restaurant = $_POST['restaurant'];

    // Check if cart exists in session
    if (isset($_SESSION['cart'])) {
        // Loop through cart to find and remove the specific item
        foreach ($_SESSION['cart'] as $key => $item) {
            // Match both product ID and restaurant
            if ($item['id'] == $productId && $item['restaurant'] == $restaurant) {
                // Remove the item from cart
                unset($_SESSION['cart'][$key]);
                
                // Re-index the array to maintain sequential keys
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                
                // Return success response
                echo json_encode(['success' => true]);
                exit;
            }
        }
        
        // Product not found in cart
        echo json_encode(['success' => false, 'message' => 'Product not found in cart.']);
    } else {
        // Cart is empty
        echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
    }
} else {
    // Invalid request - missing required parameters
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>