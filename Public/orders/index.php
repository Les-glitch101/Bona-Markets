<?php
// ============================================================
// MY ORDERS PAGE – Bona Markets (WITH DELIVERY CONFIRMATION)
// ============================================================
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?redirect=orders/index.php');
    exit;
}

require_once '../../config/database.php';
$user_id      = (int) $_SESSION['user_id'];
$userFullName = $_SESSION['fullname'] ?? '';
$userEmail    = $_SESSION['email']    ?? '';
$userRole     = $_SESSION['role']     ?? 'buyer';

// ─── HANDLE DELIVERY CONFIRMATION ──────────────────────────────
$confirmMessage = '';
$confirmType = '';
$confirmedOrderId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delivery'])) {
    $order_id = intval($_POST['order_id'] ?? 0);
    
    // Verify order belongs to this user and is in 'shipped' status
    $check = $pdo->prepare("SELECT id, status FROM orders WHERE id = ? AND user_id = ?");
    $check->execute([$order_id, $user_id]);
    $order = $check->fetch();
    
    if ($order && $order['status'] === 'shipped') {
        // ✅ Update order status to 'delivered' in database
        $update = $pdo->prepare("UPDATE orders SET status = 'delivered', delivered_at = NOW() WHERE id = ?");
        if ($update->execute([$order_id])) {
            $confirmMessage = '✅ Order #' . $order_id . ' marked as delivered! Thank you for confirming.';
            $confirmType = 'success';
            $confirmedOrderId = $order_id;
        } else {
            $confirmMessage = '❌ Something went wrong. Please try again.';
            $confirmType = 'error';
        }
    } elseif ($order && $order['status'] === 'delivered') {
        $confirmMessage = '📦 This order has already been marked as delivered.';
        $confirmType = 'info';
    } else {
        $confirmMessage = '⚠️ This order cannot be confirmed as delivered.';
        $confirmType = 'error';
    }
    
    // Refresh page to show updated status
    header('Location: index.php?confirmed=' . $order_id . '&message=' . urlencode($confirmMessage) . '&type=' . $confirmType);
    exit;
}

// ─── CHECK FOR CONFIRMATION MESSAGE IN URL ──────────────────────
if (isset($_GET['confirmed'])) {
    $confirmedOrderId = intval($_GET['confirmed']);
    $confirmMessage = urldecode($_GET['message'] ?? '');
    $confirmType = $_GET['type'] ?? 'success';
}

// Fetch all orders with their items
$ostmt = $pdo->prepare("
    SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC
");
$ostmt->execute([$user_id]);
$orders = $ostmt->fetchAll();

// Attach items to each order
foreach ($orders as &$order) {
    $istmt = $pdo->prepare("
        SELECT oi.quantity, oi.price, p.name, p.image_url
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $istmt->execute([$order['id']]);
    $order['items'] = $istmt->fetchAll();
}
unset($order);

$statusColours = [
    'pending'   => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
    'paid'      => 'bg-green-50 text-green-700 border border-green-200',
    'shipped'   => 'bg-blue-50 text-blue-700 border border-blue-200',
    'delivered' => 'bg-emerald-100 text-emerald-800 border border-emerald-300',
    'cancelled' => 'bg-red-50 text-red-700 border border-red-200',
];
$statusIcons = [
    'pending'   => '🕐',
    'paid'      => '✅',
    'shipped'   => '🚚',
    'delivered' => '📦',
    'cancelled' => '❌',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Orders | Bona Markets</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .order-card { transition: box-shadow .15s, transform .15s; }
        .order-card:hover { box-shadow: 0 8px 32px rgba(0,0,0,.08); }
        details > summary { list-style: none; cursor: pointer; }
        details > summary::-webkit-details-marker { display: none; }
        details[open] .chevron { transform: rotate(180deg); }
        .chevron { transition: transform .2s; }
        
        /* Toast notification */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 12px;
            color: white;
            font-weight: 500;
            z-index: 9999;
            animation: slideIn 0.4s ease, fadeOut 0.4s ease 4s forwards;
            max-width: 420px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
        }
        .toast-success { background: #10b981; }
        .toast-error { background: #ef4444; }
        .toast-info { background: #3b82f6; }
        
        @keyframes slideIn {
            from { transform: translateX(100px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes fadeOut {
            to { opacity: 0; transform: translateY(-10px); }
        }
        
        /* Confirm button */
        .confirm-btn {
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #10b981, #059669);
        }
        .confirm-btn:hover {
            transform: scale(1.03);
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.5);
        }
        .confirm-btn:active {
            transform: scale(0.97);
        }
        
        /* Confetti burst for delivery confirmation */
        @keyframes confettiFall {
            0% { transform: translateY(0) rotate(0deg) scale(1); opacity: 1; }
            100% { transform: translateY(200px) rotate(720deg) scale(0); opacity: 0; }
        }
        .confetti-piece {
            position: fixed;
            width: 12px;
            height: 12px;
            border-radius: 2px;
            animation: confettiFall 2s ease-out forwards;
            pointer-events: none;
            z-index: 10000;
        }
        
        /* Delivery confirmation section */
        .delivery-confirm-section {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            border: 2px solid #86efac;
            border-radius: 12px;
            padding: 16px 20px;
            margin-top: 16px;
        }
        .delivery-confirm-section .emoji-big {
            font-size: 2rem;
            display: block;
            margin-bottom: 4px;
        }
        
        /* Status badge animation for delivered */
        .badge-delivered {
            animation: pulse-green 2s ease-in-out infinite;
        }
        @keyframes pulse-green {
            0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            50% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        }
        
        /* Responsive table */
        @media (max-width: 640px) {
            .order-table td, .order-table th {
                padding: 8px 10px;
                font-size: 0.8rem;
            }
            .order-table .product-cell {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }
            .order-table .product-cell img {
                width: 32px;
                height: 32px;
            }
        }
    </style>
</head>
<body class="min-h-screen">

    <!-- ── TOAST CONTAINER ── -->
    <div id="toast-container"></div>

    <!-- ── CONFETTI CONTAINER ── -->
    <div id="confetti-container"></div>

    <!-- ── NAVBAR ── -->
    <nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="../index.php" class="text-2xl font-bold text-blue-600" style="font-family:'Syne',sans-serif">Bona Markets</a>
            <div class="hidden md:flex items-center space-x-6 text-sm font-medium">
                <a href="../products/index.php" class="text-gray-600 hover:text-blue-600">Shop</a>
                <a href="../cart/index.php" class="text-gray-600 hover:text-blue-600">Cart 🛒</a>
                <a href="index.php" class="text-blue-600 font-semibold">My Orders</a>
                <?php if ($userRole === 'vendor'): ?>
                    <a href="../vendor/dashboard.php" class="text-gray-600 hover:text-blue-600">Dashboard</a>
                <?php endif; ?>
                <span class="text-gray-400">👋 <?= htmlspecialchars($userFullName ?: $userEmail) ?></span>
                <a href="../logout.php" class="text-red-500 hover:text-red-700">Logout</a>
            </div>
        </div>
    </nav>

    <!-- ── MAIN ── -->
    <div class="container mx-auto px-4 py-8 max-w-4xl">

        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800" style="font-family:'Syne',sans-serif">My Orders</h1>
                <p class="text-sm text-gray-400 mt-1"><?= count($orders) ?> order<?= count($orders) !== 1 ? 's' : '' ?> placed</p>
            </div>
            <a href="../products/index.php"
               class="bg-blue-600 text-white px-5 py-2 rounded-xl text-sm font-semibold hover:bg-blue-700 transition">
                + Continue Shopping
            </a>
        </div>

        <?php if (count($orders) === 0): ?>
            <!-- Empty state -->
            <div class="bg-white border border-gray-100 rounded-3xl shadow-sm p-16 text-center">
                <div class="text-5xl mb-4">📋</div>
                <h2 class="text-xl font-bold text-gray-800 mb-2">No orders yet</h2>
                <p class="text-gray-500 text-sm mb-6">When you place your first order it will appear here.</p>
                <a href="../products/index.php" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition text-sm">
                    Browse Products
                </a>
            </div>

        <?php else: ?>

            <div class="space-y-4">
                <?php foreach ($orders as $order):
                    $statusKey    = strtolower($order['status'] ?? 'pending');
                    $badgeCls     = $statusColours[$statusKey] ?? 'bg-gray-50 text-gray-600 border border-gray-200';
                    $statusIcon   = $statusIcons[$statusKey]   ?? '📦';
                    $orderDate    = date('d M Y, H:i', strtotime($order['created_at']));
                    $itemCount    = count($order['items']);
                    $isShipped    = ($statusKey === 'shipped');
                    $isDelivered  = ($statusKey === 'delivered');
                ?>
                <details class="order-card bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden" 
                         <?= isset($confirmedOrderId) && $confirmedOrderId == $order['id'] ? 'open' : '' ?>>
                    <summary class="p-5 flex flex-wrap items-center gap-4">

                        <!-- Order ID + Date -->
                        <div class="flex-1 min-w-48">
                            <p class="font-bold text-gray-800 text-sm">Order #<?= $order['id'] ?></p>
                            <p class="text-xs text-gray-400 mt-0.5"><?= $orderDate ?></p>
                        </div>

                        <!-- Item count -->
                        <p class="text-xs text-gray-500"><?= $itemCount ?> item<?= $itemCount !== 1 ? 's' : '' ?></p>

                        <!-- Total -->
                        <p class="font-bold text-gray-800 text-sm min-w-20 text-right">
                            R <?= number_format($order['total'], 2) ?>
                        </p>

                        <!-- Status badge -->
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold <?= $badgeCls ?> <?= $isDelivered ? 'badge-delivered' : '' ?>">
                            <?= $statusIcon ?> <?= ucfirst($statusKey) ?>
                        </span>

                        <!-- Chevron -->
                        <svg class="chevron w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>

                    <!-- Expanded detail -->
                    <div class="border-t border-gray-100 p-5">

                        <!-- Delivery address -->
                        <?php if (!empty($order['address'])): ?>
                        <div class="flex items-start gap-2 bg-blue-50 rounded-xl p-3 mb-4 text-sm">
                            <span class="text-blue-500 mt-0.5">📍</span>
                            <div>
                                <p class="font-semibold text-blue-700 text-xs mb-0.5">Shipping Address</p>
                                <p class="text-blue-800"><?= htmlspecialchars($order['address']) ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Items table -->
                        <div class="rounded-xl overflow-hidden border border-gray-100">
                            <table class="w-full text-sm order-table">
                                <thead>
                                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                                        <th class="text-left px-4 py-3 font-semibold">Product</th>
                                        <th class="text-center px-4 py-3 font-semibold">Qty</th>
                                        <th class="text-right px-4 py-3 font-semibold">Unit Price</th>
                                        <th class="text-right px-4 py-3 font-semibold">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <?php foreach ($order['items'] as $item):
                                        $sub = $item['price'] * $item['quantity'];
                                    ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3 product-cell">
                                                <?php if ($item['image_url']): ?>
                                                    <img src="/Bona-Markets/Public/<?= htmlspecialchars($item['image_url']) ?>"
                                                         alt="<?= htmlspecialchars($item['name']) ?>"
                                                         class="w-10 h-10 rounded-lg object-cover bg-gray-100 flex-shrink-0" />
                                                <?php else: ?>
                                                    <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-xl flex-shrink-0">🛍️</div>
                                                <?php endif; ?>
                                                <span class="font-medium text-gray-800"><?= htmlspecialchars($item['name']) ?></span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-600"><?= $item['quantity'] ?></td>
                                        <td class="px-4 py-3 text-right text-gray-600">R <?= number_format($item['price'], 2) ?></td>
                                        <td class="px-4 py-3 text-right font-semibold text-gray-800">R <?= number_format($sub, 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50">
                                        <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-700 text-sm">Order Total</td>
                                        <td class="px-4 py-3 text-right font-bold text-gray-800">R <?= number_format($order['total'], 2) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- ─── DELIVERY CONFIRMATION SECTION ─── -->
                        <?php if ($isShipped): ?>
                        <div class="delivery-confirm-section">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    <span class="emoji-big">📩</span>
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm">Hey! Has your order arrived yet?</p>
                                        <p class="text-sm text-gray-600">Please confirm once you've received your items.</p>
                                    </div>
                                </div>
                                <form method="POST" action="" onsubmit="return confirmDelivery(<?= $order['id'] ?>)" class="flex-shrink-0">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>" />
                                    <button type="submit" name="confirm_delivery" value="1"
                                            class="confirm-btn text-white px-6 py-3 rounded-xl font-semibold text-sm flex items-center gap-2">
                                        ✅ Yes, it has arrived!
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($isDelivered): ?>
                        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mt-4 text-center">
                            <p class="text-emerald-700 font-medium text-sm">
                                🎉 Order confirmed as delivered on <?= date('d M Y', strtotime($order['delivered_at'] ?? $order['created_at'])) ?>
                            </p>
                            <p class="text-emerald-600 text-xs mt-1">Thank you for shopping with Bona Markets!</p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($order['payment_id'])): ?>
                        <p class="text-xs text-gray-300 mt-3 text-right">Payment ref: <?= htmlspecialchars($order['payment_id']) ?></p>
                        <?php endif; ?>
                    </div>
                </details>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </div>

    <!-- ── FOOTER ── -->
    <footer class="border-t border-gray-100 mt-16 py-6 text-center text-xs text-gray-400">
        &copy; <?= date('Y') ?> Bona Markets. All rights reserved.
    </footer>

    <!-- ── JAVASCRIPT ── -->
    <script>
        // ─── CONFIRM DELIVERY ──────────────────────────────────────────
        function confirmDelivery(orderId) {
            return confirm('⚠️ Confirm that you have received Order #' + orderId + '?\n\nThis will mark the order as delivered.');
        }

        // ─── SHOW TOAST NOTIFICATION ──────────────────────────────────
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.textContent = message;
            container.appendChild(toast);
            setTimeout(() => {
                if (toast.parentNode) toast.remove();
            }, 4500);
        }

        // ─── CONFETTI BURST ────────────────────────────────────────────
        function showConfetti() {
            const colors = ['#10b981', '#34d399', '#6ee7b7', '#fcd34d', '#f472b6', '#60a5fa', '#a78bfa'];
            const container = document.getElementById('confetti-container');
            
            for (let i = 0; i < 50; i++) {
                const piece = document.createElement('div');
                piece.className = 'confetti-piece';
                piece.style.left = Math.random() * 100 + 'vw';
                piece.style.top = -10 + 'px';
                piece.style.background = colors[Math.floor(Math.random() * colors.length)];
                piece.style.width = (Math.random() * 8 + 4) + 'px';
                piece.style.height = (Math.random() * 8 + 4) + 'px';
                piece.style.animationDelay = (Math.random() * 1.5) + 's';
                piece.style.animationDuration = (Math.random() * 1.5 + 1) + 's';
                piece.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
                container.appendChild(piece);
                
                // Clean up confetti after animation
                setTimeout(() => {
                    if (piece.parentNode) piece.remove();
                }, 3000);
            }
        }

        // ─── CHECK FOR CONFIRMATION MESSAGE ──────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($confirmMessage && $confirmedOrderId): ?>
                setTimeout(() => {
                    showToast('<?= addslashes($confirmMessage) ?>', '<?= $confirmType ?>');
                    <?php if ($confirmType === 'success'): ?>
                        showConfetti();
                        // Highlight the confirmed order
                        const orderDetails = document.querySelector('details[open]');
                        if (orderDetails) {
                            orderDetails.style.transition = 'all 0.5s ease';
                            orderDetails.style.boxShadow = '0 0 0 4px rgba(16, 185, 129, 0.4), 0 8px 32px rgba(0,0,0,0.08)';
                            setTimeout(() => {
                                orderDetails.style.boxShadow = '';
                            }, 3000);
                        }
                    <?php endif; ?>
                }, 300);
            <?php endif; ?>
        });

        console.log('✅ My Orders page loaded with delivery confirmation!');
    </script>

</body>
</html>