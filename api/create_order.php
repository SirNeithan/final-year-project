<?php
/**
 * Order Creation API
 * 
 * This file handles the creation of orders in the database after successful payment.
 * It retrieves cart items, creates an order record, adds order items, clears the cart,
 * and sends confirmation email to the customer.
 * 
 * Flow:
 * 1. Validate user is logged in
 * 2. Get cart items from session
 * 3. Create order in database
 * 4. Add order items
 * 5. Send confirmation email
 * 6. Clear cart
 */

// Start session to access user data and cart
session_start();

// Include database connection
include '../includes/connect.php';

// Include email functions
include '../includes/email_functions.php';

// Set response type to JSON for API communication
header('Content-Type: application/json');

// Get POST data sent from the checkout form
$data = json_decode(file_get_contents('php://input'), true);

// Validate that data was received
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

// Check if user is logged in (required to create an order)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Initialize variables for cart items and total
$cartItems = [];
$total = 0;

// Get cart items from session
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    // Extract product IDs from cart
    $cartIds = array_column($_SESSION['cart'], 'id');
    
    try {
        // Fetch product details from database
        $placeholders = implode(',', array_fill(0, count($cartIds), '?'));
        $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($cartIds);
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate total amount
        foreach ($cartItems as $item) {
            // Remove currency symbols and spaces from price
            $priceStr = str_replace(['UGX', ' ', '$'], '', $item['price']);
            $price = floatval($priceStr);
            $total += $price;
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error fetching cart items']);
        exit;
    }
}

// Ensure cart is not empty
if (empty($cartItems)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

try {
    // Start database transaction (ensures all operations succeed or all fail)
    $conn->beginTransaction();
    
    // Create order record in the orders table
    $stmt = $conn->prepare("
        INSERT INTO orders (user_id, total_amount, status, delivery_address, delivery_city, customer_name, customer_email, customer_phone, payment_method, transaction_id)
        VALUES (?, ?, 'pending', ?, ?, ?, ?, ?, 'credit_card', ?)
    ");
    
    // Execute order insertion with data from the form
    $stmt->execute([
        $_SESSION['user_id'],      // User who placed the order
        $total,                     // Total amount
        $data['address'],           // Delivery address
        $data['city'],              // Delivery city
        $data['name'],              // Customer name
        $data['email'],             // Customer email
        $data['phone'] ?? '',       // Customer phone number
        $data['transaction_id']     // Payment transaction ID
    ]);
    
    // Get the ID of the newly created order
    $orderId = $conn->lastInsertId();
    
    // Prepare statement to add order items
    $stmt = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, restaurant)
        VALUES (?, ?, ?, ?, 1, ?)
    ");
    
    // Add each cart item to the order_items table
    foreach ($cartItems as $item) {
        $stmt->execute([
            $orderId,                           // Link to the order
            $item['id'],                        // Product ID
            $item['name'],                      // Product name
            $item['price'],                     // Product price
            $item['restaurant'] ?? 'Unknown'    // Restaurant name
        ]);
    }
    
    // Commit transaction (save all changes to database)
    $conn->commit();
    
    // Send confirmation email to customer
    try {
        $emailSent = sendOrderConfirmationEmailHTML(
            $data['email'],
            $data['name'],
            $orderId,
            $data['transaction_id'],
            $total,
            $cartItems,
            $data['address'],
            $data['city'],
            $data['phone'] ?? ''
        );
        
        // Log email status
        if ($emailSent) {
            error_log("Order confirmation email sent successfully for order #$orderId");
        } else {
            error_log("Failed to send order confirmation email for order #$orderId");
        }
    } catch (Exception $e) {
        // Don't fail the order if email fails
        error_log("Email error for order #$orderId: " . $e->getMessage());
    }
    
    // Clear the cart after successful order creation
    $_SESSION['cart'] = [];
    
    // Return success response with order ID
    echo json_encode([
        'success' => true,
        'message' => 'Order created successfully',
        'order_id' => $orderId,
        'email_sent' => $emailSent ?? false
    ]);
    
} catch (Exception $e) {
    // If any error occurs, rollback all changes
    $conn->rollBack();
    
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Error creating order: ' . $e->getMessage()
    ]);
}
