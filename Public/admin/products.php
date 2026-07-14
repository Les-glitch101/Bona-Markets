<?php
// ============================================================
// ADMIN – All Products
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

// ─── HANDLE PRODUCT DELETE ─────────────────────────────────────
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $productId = intval($_POST['product_id']);
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$productId]);
    $message = 'Product deleted successfully.';
    header('refresh:1');
}

// ─── GET ALL PRODUCTS ────────────────────────────────────────────
$products = $pdo->query("
    SELECT p.*, c.name as category_name, vp.business_name as vendor_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN vendor_profiles vp ON p.vendor_id = vp.user_id
    ORDER BY p.created_at DESC
")->fetchAll();

$adminName = $_SESSION['fullname'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Products | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .btn-delete { background: #ef4444; color: white; padding: 0.2rem 0.6rem; border-radius: 6px; border: none; cursor: pointer; font-size: 0.75rem; font-weight: 600; }
        .btn-delete:hover { background: #dc2626; }
        .product-thumb { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; background: #f3f4f6; }
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
            <h1 class="text-2xl font-bold text-gray-800">All Products</h1>
            <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full text-xs font-semibold"><?= count($products) ?></span>
        </div>

        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 text-left">Image</th>
                            <th class="px-6 py-3 text-left">Product</th>
                            <th class="px-6 py-3 text-left">Vendor</th>
                            <th class="px-6 py-3 text-left">Category</th>
                            <th class="px-6 py-3 text-left">Price</th>
                            <th class="px-6 py-3 text-left">Stock</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (count($products) > 0): ?>
                            <?php foreach ($products as $product): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <?php if ($product['image_url']): ?>
                                            <img src="/Bona-Markets/Public/<?= htmlspecialchars($product['image_url']) ?>" class="product-thumb" alt="<?= htmlspecialchars($product['name']) ?>">
                                        <?php else: ?>
                                            <div class="product-thumb flex items-center justify-center text-gray-300 text-xl">🛍️</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-800"><?= htmlspecialchars($product['name']) ?></td>
                                    <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($product['vendor_name'] ?? 'Unknown') ?></td>
                                    <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?></td>
                                    <td class="px-6 py-4 font-semibold text-gray-800">R <?= number_format($product['price'], 2) ?></td>
                                    <td class="px-6 py-4 text-gray-600"><?= $product['stock'] ?></td>
                                    <td class="px-6 py-4">
                                        <?php if ($product['stock'] == 0): ?>
                                            <span class="bg-red-100 text-red-800 px-2 py-0.5 rounded-full text-xs font-medium">Out of Stock</span>
                                        <?php elseif ($product['stock'] <= 3): ?>
                                            <span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded-full text-xs font-medium">Low Stock</span>
                                        <?php else: ?>
                                            <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded-full text-xs font-medium">Active</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <form method="POST" action="" onsubmit="return confirm('Delete <?= htmlspecialchars($product['name']) ?>?')">
                                            <input type="hidden" name="delete_product" value="1">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                            <button type="submit" class="btn-delete">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-500">No products found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>