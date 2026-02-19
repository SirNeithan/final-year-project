<?php
session_start();

if (isset($_SESSION['cart'])) {
    $cartCount = count($_SESSION['cart']);
    echo json_encode(['success' => true, 'count' => $cartCount]);
} else {
    echo json_encode(['success' => true, 'count' => 0]);
}
?>