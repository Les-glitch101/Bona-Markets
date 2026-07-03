<?php
// ============================================================
// ORDER SUCCESS PAGE – Bona Markets
// ============================================================
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../../config/database.php';
$user_id  = (int) $_SESSION['user_id'];
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Fallback to session
if ($order_id === 0 && isset($_SESSION['last_order_id'])) {
    $order_id = (int) $_SESSION['last_order_id'];
}

$order = null;
$items = [];

if ($order_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch();

    if ($order) {
        $istmt = $pdo->prepare("
            SELECT oi.quantity, oi.price, p.name, p.image_url
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $istmt->execute([$order_id]);
        $items = $istmt->fetchAll();
    }
}

// Clear the session order tracker
unset($_SESSION['last_order_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Confirmed | Bona Markets</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; background: #ffffff; }
        @keyframes popIn {
            0%   { transform: scale(0);    opacity: 0; }
            70%  { transform: scale(1.12); }
            100% { transform: scale(1);    opacity: 1; }
        }
        @keyframes drawCheck {
            0%   { opacity: 0; transform: scale(0.5); }
            100% { opacity: 1; transform: scale(1);   }
        }
        .check-circle { animation: popIn .6s cubic-bezier(.175,.885,.32,1.275) forwards; }
        .check-mark   { animation: drawCheck .4s ease .35s forwards; opacity: 0; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">

    <div class="bg-white border border-gray-100 rounded-3xl shadow-xl p-10 max-w-lg w-full text-center">

        <!-- Logo -->
        <a href="../index.php" class="text-xl font-bold text-blue-600 block mb-6" style="font-family:'Syne',sans-serif">Bona Markets</a>

        <!-- Animated checkmark -->
        <div class="flex justify-center mb-5">
            <div class="check-circle w-24 h-24 rounded-full bg-green-500 flex items-center justify-center shadow-lg">
                <span class="check-mark text-white text-5xl font-bold leading-none">✓</span>
            </div>
        </div>

        <h1 class="text-2xl font-bold text-gray-800 mb-2" style="font-family:'Syne',sans-serif">Order Confirmed!</h1>

        <?php if ($order): ?>
            <p class="text-gray-500 text-sm mb-1">Thank you for your purchase.</p>
            <p class="text-gray-400 text-xs mb-6">Order <span class="font-bold text-gray-700">#<?= $order['id'] ?></span> · <?= date('d M Y, H:i', strtotime($order['created_at'])) ?></p>

            <!-- Order items summary -->
            <?php if (count($items)): ?>
            <div class="bg-gray-50 rounded-2xl p-4 mb-6 text-left">
                <?php
                $total = 0;
                foreach ($items as $item):
                    $sub = $item['price'] * $item['quantity'];
                    $total += $sub;
                ?>
                    <div class="flex justify-between items-center text-sm py-2 border-b border-gray-100 last:border-0">
                        <span class="text-gray-700"><?= htmlspecialchars($item['name']) ?> <span class="text-gray-400">×<?= $item['quantity'] ?></span></span>
                        <span class="font-semibold text-gray-800">R <?= number_format($sub, 2) ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="flex justify-between items-center font-bold text-gray-800 pt-3 mt-1">
                    <span>Total Paid</span>
                    <span>R <?= number_format($order['total'], 2) ?></span>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($order['address'])): ?>
            <div class="bg-blue-50 rounded-xl p-3 mb-6 text-left">
                <p class="text-xs font-semibold text-blue-700 mb-1">📦 Shipping to</p>
                <p class="text-sm text-blue-800"><?= htmlspecialchars($order['address']) ?></p>
            </div>
            <?php endif; ?>

        <?php else: ?>
            <p class="text-gray-500 text-sm mb-6">Your order has been placed. We'll notify you when it ships.</p>
        <?php endif; ?>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="../orders/index.php"
               class="bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition text-sm">
                View My Orders
            </a>
            <a href="../products/index.php"
               class="bg-gray-100 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 transition text-sm">
                Continue Shopping
            </a>
        </div>
    </div>

</body>
</html>