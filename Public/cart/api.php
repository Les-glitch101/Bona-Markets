<?php
// ============================================================
// CART API – GET / PUT / DELETE
// (POST/add is handled by cart/add.php to keep backwards compat)
// ============================================================
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once '../../config/database.php';
$user_id = (int) $_SESSION['user_id'];
$action  = $_GET['action'] ?? '';
$method  = $_SERVER['REQUEST_METHOD'];

// ── GET cart items ──
if ($method === 'GET' && $action === 'get') {
    $stmt = $pdo->prepare("
        SELECT c.id, c.quantity, c.product_id,
               p.name, p.price, p.image_url, p.stock,
               cat.name AS category
        FROM cart c
        JOIN products p   ON c.product_id   = p.id
        LEFT JOIN categories cat ON p.category_id = cat.id
        WHERE c.user_id = ?
        ORDER BY c.added_at DESC
    ");
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($items as &$i) {
        $i['price']    = (float) $i['price'];
        $i['quantity'] = (int)   $i['quantity'];
        $i['stock']    = (int)   $i['stock'];
        $i['id']       = (int)   $i['id'];
    }
    echo json_encode($items);
    exit;
}

// ── UPDATE quantity ──
if ($method === 'PUT' && $action === 'update') {
    $data         = json_decode(file_get_contents('php://input'), true);
    $cart_item_id = (int) ($data['cart_item_id'] ?? 0);
    $new_qty      = (int) ($data['quantity']      ?? 0);

    if ($new_qty <= 0) {
        $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?")->execute([$cart_item_id, $user_id]);
    } else {
        // Cap at available stock
        $stock = $pdo->prepare("SELECT p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.id = ? AND c.user_id = ?");
        $stock->execute([$cart_item_id, $user_id]);
        $row = $stock->fetch();
        $new_qty = $row ? min($new_qty, $row['stock']) : $new_qty;
        $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?")->execute([$new_qty, $cart_item_id, $user_id]);
    }

    // Return updated cart count
    $count = $pdo->prepare("SELECT SUM(quantity) AS total FROM cart WHERE user_id = ?");
    $count->execute([$user_id]);
    $total = (int) ($count->fetch()['total'] ?? 0);
    echo json_encode(['success' => true, 'cart_count' => $total]);
    exit;
}

// ── REMOVE item ──
if ($method === 'DELETE' && $action === 'remove') {
    $cart_item_id = (int) ($_GET['cart_item_id'] ?? 0);
    $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?")->execute([$cart_item_id, $user_id]);

    $count = $pdo->prepare("SELECT SUM(quantity) AS total FROM cart WHERE user_id = ?");
    $count->execute([$user_id]);
    $total = (int) ($count->fetch()['total'] ?? 0);
    echo json_encode(['success' => true, 'cart_count' => $total]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Unknown action']);