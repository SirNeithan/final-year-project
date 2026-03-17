<?php
ob_start();
session_start();
include '../includes/connect.php';
header('Content-Type: application/json');
ob_clean();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

// Build cart items from session + JSON file
$cartItems = [];
$subtotal  = 0;

if (!empty($_SESSION['cart'])) {
    $allProducts = json_decode(file_get_contents('../data/products.json'), true);
    foreach ($allProducts as $i => $p) {
        if (!isset($p['id'])) $allProducts[$i]['id'] = $i + 1;
    }
    foreach ($_SESSION['cart'] as $cartItem) {
        foreach ($allProducts as $p) {
            if ($p['id'] == $cartItem['id'] && $p['restaurant'] == $cartItem['restaurant']) {
                $p['quantity'] = $cartItem['quantity'] ?? 1;
                $cartItems[]   = $p;
                break;
            }
        }
    }
    foreach ($cartItems as $item) {
        $price     = floatval(str_replace(['UGX','KSh',' ',','], '', $item['price']));
        $subtotal += $price * ($item['quantity'] ?? 1);
    }
}

if (empty($cartItems)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty.']);
    exit;
}

$deliveryFee = intval($data['delivery_fee'] ?? 0);
$total       = $subtotal + $deliveryFee;

try {
    $conn->beginTransaction();

    // Insert order
    $stmt = $conn->prepare("
        INSERT INTO orders
            (user_id, total_amount, status, delivery_address, delivery_city,
             customer_name, customer_email, customer_phone, payment_method)
        VALUES (?, ?, 'pending', ?, ?, ?, ?, ?, 'pay_on_delivery')
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        $total,
        $data['address']  ?? '',
        $data['city']     ?? '',
        $data['name']     ?? '',
        $data['email']    ?? '',
        $data['phone']    ?? '',
    ]);
    $orderId = $conn->lastInsertId();

    // Insert order items
    $stmt = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, restaurant)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    foreach ($cartItems as $item) {
        $stmt->execute([
            $orderId,
            $item['id'],
            $item['name'],
            $item['price'],
            $item['quantity'] ?? 1,
            $item['restaurant'] ?? '',
        ]);
    }

    $conn->commit();

    // Clear cart
    $_SESSION['cart'] = [];

    echo json_encode(['success' => true, 'order_id' => $orderId]);

} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
