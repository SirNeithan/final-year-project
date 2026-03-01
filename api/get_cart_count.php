<?php
/**
 * Get Cart Count API
 * 
 * This file returns the number of items currently in the shopping cart.
 * Used to update the cart badge/counter in the navigation header.
 * 
 * Returns JSON response with the cart item count
 */

// Start session to access cart data
session_start();

// Check if cart exists in session
if (isset($_SESSION['cart'])) {
    // Count the number of items in the cart
    $cartCount = count($_SESSION['cart']);
    
    // Return success response with count
    echo json_encode(['success' => true, 'count' => $cartCount]);
} else {
    // Cart doesn't exist yet, return count of 0
    echo json_encode(['success' => true, 'count' => 0]);
}
?>