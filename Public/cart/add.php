<?php
// ============================================================
// CART / add.php  –  Add or increment a product in the cart
// Accepts POST (AJAX or form). Returns JSON.
// ============================================================
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please log in to add items to your cart.']);
    exit;
}

require_once '../../config/database.php';

$user_id    = (int) $_SESSION['user_id'];
$product_id = (int) ($_POST['product_id'] ?? 0);
$quantity   = max(1, (int) ($_POST['quantity'] ?? 1));

if ($product_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    exit;
}

// Verify product exists, is active and has stock
$stmt = $pdo->prepare("SELECT id, name, stock FROM products WHERE id = ? AND status = 'active'");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not available.']);
    exit;
}

if ($product['stock'] < 1) {
    echo json_encode(['success' => false, 'message' => 'Sorry, this product is out of stock.']);
    exit;
}

// Upsert: if already in cart, increment quantity; otherwise insert
$existing = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
$existing->execute([$user_id, $product_id]);
$row = $existing->fetch();

if ($row) {
    $newQty = $row['quantity'] + $quantity;
    if ($newQty > $product['stock']) {
        $newQty = $product['stock'];
    }
    $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?")->execute([$newQty, $row['id']]);
} else {
    $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)")
        ->execute([$user_id, $product_id, $quantity]);
}

// Return updated cart count for badge
$count = $pdo->prepare("SELECT SUM(quantity) AS total FROM cart WHERE user_id = ?");
$count->execute([$user_id]);
$cartTotal = (int) ($count->fetch()['total'] ?? 0);

echo json_encode([
    'success'    => true,
    'message'    => '"' . $product['name'] . '" added to your cart!',
    'cart_count' => $cartTotal,
]);