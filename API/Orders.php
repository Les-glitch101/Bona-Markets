<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) $_SESSION['user_id'] = 1;
$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->prepare("SELECT * FROM Orders WHERE UserID = ? ORDER BY Created_At DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();
    foreach ($orders as &$order) {
        $itemStmt = $pdo->prepare("
            SELECT oi.Quantity, oi.Price, p.Name, p.Image
            FROM order_items oi
            JOIN Products p ON oi.ProductID = p.ProductID
            WHERE oi.OrderID = ?
        ");
        $itemStmt->execute([$order['ID']]);
        $order['items'] = $itemStmt->fetchAll();
    }
    echo json_encode($orders);
}
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $address = $data['address'];
    $payment_id = $data['payment_id'];
    $selectedItems = isset($data['selected_items']) ? $data['selected_items'] : [];

    if (empty($selectedItems)) {
        http_response_code(400);
        echo json_encode(['error' => 'No items selected for checkout']);
        exit;
    }

    $cartStmt = $pdo->prepare("SELECT ID FROM Carts WHERE UserID = ?");
    $cartStmt->execute([$user_id]);
    $cart = $cartStmt->fetch();
    if (!$cart) {
        http_response_code(400);
        echo json_encode(['error' => 'No cart found']);
        exit;
    }
    $cart_id = $cart['ID'];

    $total = 0;
    foreach ($selectedItems as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    $pdo->beginTransaction();
    try {
        $orderStmt = $pdo->prepare("INSERT INTO Orders (UserID, Total, Status, Address, PaymentID) VALUES (?, ?, 'PAID', ?, ?)");
        $orderStmt->execute([$user_id, $total, $address, $payment_id]);
        $order_id = $pdo->lastInsertId();

        $itemInsert = $pdo->prepare("INSERT INTO order_items (OrderID, ProductID, Quantity, Price) VALUES (?, ?, ?, ?)");
        foreach ($selectedItems as $item) {
            $prodStmt = $pdo->prepare("SELECT ProductID FROM Products WHERE Name = ?");
            $prodStmt->execute([$item['name']]);
            $product = $prodStmt->fetch();
            if ($product) {
                $itemInsert->execute([$order_id, $product['ProductID'], $item['quantity'], $item['price']]);
                $pdo->prepare("DELETE FROM CartItems WHERE CartID = ? AND ProductID = ?")->execute([$cart_id, $product['ProductID']]);
            }
        }

        $pdo->commit();
        $_SESSION['last_order_id'] = $order_id;
        echo json_encode(['success' => true, 'order_id' => $order_id]);
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['error' => 'Order creation failed: ' . $e->getMessage()]);
    }
}
?>