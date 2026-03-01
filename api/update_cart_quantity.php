<?php
/**
 * Update Cart Quantity API
 * 
 * This file handles updating the quantity of a product in the shopping cart.
 * 
 * Expected POST parameters:
 * - product_id: The ID of the product to update
 * - quantity: The new quantity (must be at least 1)
 * - restaurant: The name of the restaurant
 * 
 * Returns JSON response indicating success or failure
 */

// Start session to access cart data
session_start();

// Check if all required parameters were sent via POST
if (isset($_POST['product_id']) && isset($_POST['quantity']) && isset($_POST['restaurant'])) {
    // Get product ID and convert to integer for security
    $productId = intval($_POST['product_id']);
    
    // Get new quantity and convert to integer
    $quantity = intval($_POST['quantity']);
    
    // Get restaurant name
    $restaurant = $_POST['restaurant'];

    // Validate quantity (must be at least 1)
    if ($quantity < 1) {
        echo json_encode(['success' => false, 'message' => 'Quantity must be at least 1.']);
        exit;
    }

    // Check if cart exists in session
    if (isset($_SESSION['cart'])) {
        // Loop through cart to find the specific item
        foreach ($_SESSION['cart'] as $key => $item) {
            // Match both product ID and restaurant
            if ($item['id'] == $productId && $item['restaurant'] == $restaurant) {
                // Update the quantity
                $_SESSION['cart'][$key]['quantity'] = $quantity;
                
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
