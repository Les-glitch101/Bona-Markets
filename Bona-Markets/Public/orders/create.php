<?php
// ============================================================
// ORDERS / create.php  –  POST endpoint to place an order
// Called by checkout/index.php via fetch()
// ============================================================
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorised']);
    exit;
}

require_once '../../config/database.php';
$user_id = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data           = json_decode(file_get_contents('php://input'), true);
$address        = trim($data['address']        ?? '');
$payment_id     = trim($data['payment_id']     ?? '');
$selected_items = $data['selected_items']      ?? [];

if (empty($address)) {
    http_response_code(400);
    echo json_encode(['error' => 'Delivery address is required.']);
    exit;
}
if (empty($selected_items)) {
    http_response_code(400);
    echo json_encode(['error' => 'No items selected for checkout.']);
    exit;
}

// Calculate total and validate stock
$total = 0;
$validatedItems = [];

try {
    $pdo->beginTransaction();

    foreach ($selected_items as $item) {
        $product_id = (int) $item['product_id'];
        $quantity   = (int) ($item['quantity'] ?? 1);

        // Lock row and check stock
        $pstmt = $pdo->prepare("SELECT id, name, price, stock FROM products WHERE id = ? AND status = 'active' FOR UPDATE");
        $pstmt->execute([$product_id]);
        $product = $pstmt->fetch();

        if (!$product) {
            $pdo->rollBack();
            echo json_encode(['error' => "Product not found or no longer available."]);
            exit;
        }
        if ($product['stock'] < $quantity) {
            $pdo->rollBack();
            echo json_encode(['error' => "\"" . $product['name'] . "\" only has " . $product['stock'] . " units left."]);
            exit;
        }

        $price  = (float) $product['price'];
        $total += $price * $quantity;
        $validatedItems[] = [
            'product_id' => $product_id,
            'quantity'   => $quantity,
            'price'      => $price,
        ];
    }

    // Insert order — uses address + payment_id columns (add via migration below if missing)
    $ostmt = $pdo->prepare("
        INSERT INTO orders (user_id, total, status, address, payment_id, created_at)
        VALUES (?, ?, 'paid', ?, ?, NOW())
    ");
    $ostmt->execute([$user_id, $total, $address, $payment_id]);
    $order_id = (int) $pdo->lastInsertId();

    // Insert order items and decrement stock
    $iistmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $ustmt  = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
    $dstmt  = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");

    foreach ($validatedItems as $v) {
        $iistmt->execute([$order_id, $v['product_id'], $v['quantity'], $v['price']]);
        $ustmt->execute([$v['quantity'], $v['product_id']]);
        $dstmt->execute([$user_id, $v['product_id']]);
    }

    $pdo->commit();

    $_SESSION['last_order_id'] = $order_id;
    echo json_encode(['success' => true, 'order_id' => $order_id]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['error' => 'Order failed: ' . $e->getMessage()]);
}