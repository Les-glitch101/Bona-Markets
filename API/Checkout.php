<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) $_SESSION['user_id'] = 1;
$user_id = $_SESSION['user_id'];

// Get cart
$cartStmt = $pdo->prepare("SELECT ID FROM Carts WHERE UserID = ?");
$cartStmt->execute([$user_id]);
$cart = $cartStmt->fetch();
if (!$cart) {
    http_response_code(400);
    echo json_encode(['error' => 'No cart found']);
    exit;
}
$cart_id = $cart['ID'];

// Get cart items
$itemsStmt = $pdo->prepare("
    SELECT ci.Quantity, p.Price, p.Name, p.ProductID
    FROM CartItems ci
    JOIN Products p ON ci.ProductID = p.ProductID
    WHERE ci.CartID = ?
");
$itemsStmt->execute([$cart_id]);
$items = $itemsStmt->fetchAll();

if (empty($items)) {
    http_response_code(400);
    echo json_encode(['error' => 'Cart is empty']);
    exit;
}

$total = 0;
$item_names = [];
foreach ($items as $item) {
    $total += $item['Price'] * $item['Quantity'];
    $item_names[] = $item['Name'] . ' x ' . $item['Quantity'];
}
$item_name = implode(', ', $item_names);

// Generate unique order ID
$order_id = 'ORD' . time() . rand(100, 999);

// Store in session
$_SESSION['payfast_order_id'] = $order_id;
$_SESSION['payfast_total'] = $total;
$_SESSION['payfast_items'] = $items;
$_SESSION['payfast_address'] = $_POST['address'] ?? 'No address provided';

// ============================================================
// PAYFAST CONFIGURATION
// ============================================================
// Replace with your own Merchant ID and Key from PayFast
$payfast_merchant_id = '10000100';
$merchant_key = '46f0cd694581a';
$payfast_url = 'https://sandbox.payfast.co.za/eng/process';

$return_url = 'http://localhost/BonaMarkets/payfast_return.php';
$cancel_url = 'http://localhost/BonaMarkets/cart.php';
$notify_url = 'http://localhost/BonaMarkets/payfast_itn.php';

// Build the form data
$payfast_data = array(
    'merchant_id' => $payfast_merchant_id,
    'merchant_key' => $merchant_key,
    'return_url' => $return_url,
    'cancel_url' => $cancel_url,
    'notify_url' => $notify_url,
    'm_payment_id' => $order_id,
    'amount' => number_format($total, 2, '.', ''),
    'item_name' => substr($item_name, 0, 100),
    'item_description' => 'Order ' . $order_id,
    'email_confirmation' => '0'
);

// Generate signature
$signature = '';
foreach ($payfast_data as $key => $val) {
    if ($key !== 'signature') {
        $signature .= $key . '=' . urlencode(trim($val)) . '&';
    }
}
$signature = rtrim($signature, '&');
$signature = md5($signature);
$payfast_data['signature'] = $signature;

// Store in session
$_SESSION['payfast_data'] = $payfast_data;
$_SESSION['payfast_url'] = $payfast_url;

echo json_encode([
    'success' => true,
    'redirect_url' => 'payfast_redirect.php'
]);
?>