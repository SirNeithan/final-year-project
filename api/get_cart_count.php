<?php
ob_start();
session_start();
header('Content-Type: application/json');
header('Cache-Control: no-cache');
ob_clean();

$count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $count += ($item['quantity'] ?? 1);
    }
}

echo json_encode(['success' => true, 'count' => $count]);
