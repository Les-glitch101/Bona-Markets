<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) $_SESSION['user_id'] = 1;
$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

function getOrCreateCart($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT ID FROM Carts WHERE UserID = ?");
    $stmt->execute([$user_id]);
    $cart = $stmt->fetch();
    if ($cart) return $cart['ID'];
    $pdo->prepare("INSERT INTO Carts (UserID) VALUES (?)")->execute([$user_id]);
    return $pdo->lastInsertId();
}
$cart_id = getOrCreateCart($pdo, $user_id);

if ($method === 'GET') {
    $stmt = $pdo->prepare("
        SELECT ci.ID AS cart_item_id, ci.Quantity, p.ProductID, p.Name, p.Price, p.Image
        FROM CartItems ci
        JOIN Products p ON ci.ProductID = p.ProductID
        WHERE ci.CartID = ?
    ");
    $stmt->execute([$cart_id]);
    $items = $stmt->fetchAll();
    $cart_items = [];
    foreach ($items as $item) {
        $cart_items[] = [
            'cart_item_id' => $item['cart_item_id'],
            'product_id'   => $item['ProductID'],
            'name'         => $item['Name'],
            'price'        => (float)$item['Price'],
            'quantity'     => (int)$item['Quantity'],
            'image'        => $item['Image'] ?: 'assets/placeholder.jpg'
        ];
    }
    echo json_encode($cart_items);
}
elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $product_id = $data['product_id'];
    $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;
    $check = $pdo->prepare("SELECT ID, Quantity FROM CartItems WHERE CartID = ? AND ProductID = ?");
    $check->execute([$cart_id, $product_id]);
    $existing = $check->fetch();
    if ($existing) {
        $new_qty = $existing['Quantity'] + $quantity;
        $pdo->prepare("UPDATE CartItems SET Quantity = ? WHERE ID = ?")->execute([$new_qty, $existing['ID']]);
    } else {
        $pdo->prepare("INSERT INTO CartItems (CartID, ProductID, Quantity) VALUES (?, ?, ?)")->execute([$cart_id, $product_id, $quantity]);
    }
    echo json_encode(['success' => true]);
}
elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $cart_item_id = $data['cart_item_id'];
    $new_qty = (int)$data['quantity'];
    if ($new_qty <= 0) {
        $pdo->prepare("DELETE FROM CartItems WHERE ID = ? AND CartID = ?")->execute([$cart_item_id, $cart_id]);
    } else {
        $pdo->prepare("UPDATE CartItems SET Quantity = ? WHERE ID = ? AND CartID = ?")->execute([$new_qty, $cart_item_id, $cart_id]);
    }
    echo json_encode(['success' => true]);
}
elseif ($method === 'DELETE') {
    $cart_item_id = isset($_GET['cart_item_id']) ? (int)$_GET['cart_item_id'] : 0;
    $pdo->prepare("DELETE FROM CartItems WHERE ID = ? AND CartID = ?")->execute([$cart_item_id, $cart_id]);
    echo json_encode(['success' => true]);
}
?>