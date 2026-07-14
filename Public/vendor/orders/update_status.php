<?php
// ============================================================
// VENDOR / orders/update_status.php
// Lets a logged-in vendor mark an order as shipped, but only if
// that order actually contains at least one of their products.
// ============================================================
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'vendor' && $_SESSION['role'] !== 'admin')) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorised.']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$vendor_id = (int) $_SESSION['user_id'];
$data      = json_decode(file_get_contents('php://input'), true);
$order_id  = (int) ($data['order_id'] ?? 0);
$newStatus = $data['status'] ?? 'shipped';

$allowedStatuses = ['pending', 'paid', 'shipped', 'delivered'];
if (!in_array($newStatus, $allowedStatuses, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status.']);
    exit;
}

if ($order_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid order.']);
    exit;
}

// Confirm this order contains at least one product belonging to this vendor
$check = $pdo->prepare("
    SELECT COUNT(*) AS cnt
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ? AND p.vendor_id = ?
");
$check->execute([$order_id, $vendor_id]);
$owns = (int) $check->fetch()['cnt'] > 0;

if (!$owns) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'You do not have permission to update this order.']);
    exit;
}

$pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$newStatus, $order_id]);

echo json_encode(['success' => true, 'message' => 'Order updated.', 'status' => $newStatus]);
