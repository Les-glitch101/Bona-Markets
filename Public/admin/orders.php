<?php
// ============================================================
// ADMIN – All Orders
// ============================================================

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';

// ─── GET ALL ORDERS ─────────────────────────────────────────────
$orders = $pdo->query("
    SELECT o.*, u.fullname as customer_name, u.email as customer_email,
           COUNT(oi.id) as item_count
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN order_items oi ON o.id = oi.order_id
    GROUP BY o.id
    ORDER BY o.created_at DESC
")->fetchAll();

$adminName = $_SESSION['fullname'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Orders | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .status-badge { padding: 0.2rem 0.7rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-paid { background: #dbeafe; color: #1e40af; }
        .status-shipped { background: #e0e7ff; color: #3730a3; }
        .status-delivered { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fdecea; color: #991b1b; }
    </style>
</head>
<body>

    <nav class="bg-white shadow-md sticky top-0 z-50 border-b border-gray-200">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="../index.php" class="text-2xl font-bold text-blue-600">Bona Markets</a>
            <div class="flex items-center space-x-4">
                <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-semibold">Admin</span>
                <span class="text-gray-700">👋 <?= htmlspecialchars($adminName) ?></span>
                <a href="../logout.php" class="text-red-500 hover:text-red-700 text-sm font-medium">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center gap-4 mb-6">
            <a href="dashboard.php" class="text-gray-500 hover:text-gray-700">← Back to Dashboard</a>
            <h1 class="text-2xl font-bold text-gray-800">All Orders</h1>
            <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full text-xs font-semibold"><?= count($orders) ?></span>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 text-left">Order ID</th>
                            <th class="px-6 py-3 text-left">Customer</th>
                            <th class="px-6 py-3 text-left">Items</th>
                            <th class="px-6 py-3 text-left">Date</th>
                            <th class="px-6 py-3 text-left">Total</th>
                            <th class="px-6 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (count($orders) > 0): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-800">#BM-<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-800"><?= htmlspecialchars($order['customer_name'] ?? 'Guest') ?></div>
                                        <div class="text-xs text-gray-400"><?= htmlspecialchars($order['customer_email'] ?? '') ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600"><?= $order['item_count'] ?> item(s)</td>
                                    <td class="px-6 py-4 text-gray-500 text-sm"><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                                    <td class="px-6 py-4 font-semibold text-gray-800">R <?= number_format($order['total'], 2) ?></td>
                                    <td class="px-6 py-4">
                                        <span class="status-badge status-<?= $order['status'] ?>">
                                            <?= ucfirst($order['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">No orders found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>