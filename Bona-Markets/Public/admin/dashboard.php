<?php
// ============================================================
// ADMIN DASHBOARD – Bona Markets
// ============================================================

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// ─── CHECK ADMIN LOGIN ─────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header('Location: /index.php');
    exit;
}

// ─── DATABASE CONNECTION ──────────────────────────────────────
require_once __DIR__ . '/../../config/database.php';

// ─── GET STATISTICS ────────────────────────────────────────────
// Total vendors (approved)
$stmt = $pdo->query("SELECT COUNT(*) as count FROM vendor_profiles WHERE approved = 1");
$totalVendors = $stmt->fetch()['count'];

// Pending vendors
$stmt = $pdo->query("SELECT COUNT(*) as count FROM vendor_profiles WHERE approved = 0");
$pendingVendors = $stmt->fetch()['count'];

// Total products
$stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
$totalProducts = $stmt->fetch()['count'];

// Total orders
$stmt = $pdo->query("SELECT COUNT(*) as count FROM orders");
$totalOrders = $stmt->fetch()['count'];

// Total revenue (from delivered orders)
$stmt = $pdo->query("SELECT COALESCE(SUM(total), 0) as total FROM orders WHERE status = 'delivered'");
$totalRevenue = $stmt->fetch()['total'];

// Recent orders (last 5)
$recentOrders = $pdo->query("
    SELECT o.*, u.fullname as customer_name 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 5
")->fetchAll();

// Pending vendor applications (last 5)
$pendingVendorsList = $pdo->query("
    SELECT v.*, u.email, u.fullname as user_name
    FROM vendor_profiles v
    JOIN users u ON v.user_id = u.id
    WHERE v.approved = 0
    ORDER BY v.applied_at DESC
    LIMIT 5
")->fetchAll();

// ─── HANDLE VENDOR APPROVAL ────────────────────────────────────
$approvalMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_vendor'])) {
        $vendorId = intval($_POST['vendor_id']);
        $stmt = $pdo->prepare("UPDATE vendor_profiles SET approved = 1 WHERE user_id = ?");
        $stmt->execute([$vendorId]);
        $approvalMessage = 'Vendor approved successfully!';
        // Refresh page to update stats
        header('refresh:1');
    }
    
    if (isset($_POST['reject_vendor'])) {
        $vendorId = intval($_POST['vendor_id']);
        $stmt = $pdo->prepare("DELETE FROM vendor_profiles WHERE user_id = ?");
        $stmt->execute([$vendorId]);
        // Also revert user role to buyer
        $pdo->prepare("UPDATE users SET role = 'buyer' WHERE id = ?")->execute([$vendorId]);
        $approvalMessage = 'Vendor rejected and removed.';
        header('refresh:1');
    }
}

// ─── GET GREETING ──────────────────────────────────────────────
$hour = date('H');
if ($hour < 12) {
    $greeting = 'morning';
} elseif ($hour < 18) {
    $greeting = 'afternoon';
} else {
    $greeting = 'evening';
}

$adminName = $_SESSION['fullname'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <title>Admin Dashboard | Bona Markets</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #f8fafc;
        }
        .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }
        .pending-badge {
            background: #fef3c7;
            color: #92400e;
            padding: 0.2rem 0.7rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .approved-badge {
            background: #d1fae5;
            color: #065f46;
            padding: 0.2rem 0.7rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .btn-approve {
            background: #10b981;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-approve:hover {
            background: #059669;
        }
        .btn-reject {
            background: #ef4444;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-reject:hover {
            background: #dc2626;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            color: #64748b;
            text-decoration: none;
            transition: all 0.2s;
        }
        .nav-link:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .nav-link.active {
            background: #eff6ff;
            color: #2563eb;
            font-weight: 600;
        }
        .nav-link svg {
            flex-shrink: 0;
        }
    </style>
</head>
<body>

    <!-- ============================================================ -->
    <!-- TOPBAR -->
    <!-- ============================================================ -->
    <nav class="bg-white shadow-md sticky top-0 z-50 border-b border-gray-200">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <a href="../index.php" class="text-2xl font-bold text-blue-600 tracking-tight">
                    Bona Markets
                </a>

                <!-- Right: Admin Badge + User -->
                <div class="flex items-center space-x-4">
                    <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-semibold">Admin</span>
                    <span class="text-gray-700">👋 <?= htmlspecialchars($adminName) ?></span>
                    <a href="../logout.php" class="text-red-500 hover:text-red-700 text-sm font-medium">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ============================================================ -->
    <!-- MAIN CONTENT -->
    <!-- ============================================================ -->
    <div class="container mx-auto px-4 py-8">

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Good <?= $greeting ?>, <?= htmlspecialchars($adminName) ?> 👋</h1>
            <p class="text-gray-500 text-sm mt-1">Here's what's happening on the platform today.</p>
        </div>

        <?php if ($approvalMessage): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                <?= htmlspecialchars($approvalMessage) ?>
            </div>
        <?php endif; ?>

        <!-- ============================================================ -->
        <!-- STATS CARDS -->
        <!-- ============================================================ -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-8">

            <!-- Pending Vendors (Highlighted) -->
            <a href="#pending-vendors" class="stat-card bg-white rounded-xl shadow-sm p-5 border-l-4 border-yellow-500 hover:shadow-md transition">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Pending Vendors</p>
                <p class="text-2xl md:text-3xl font-bold text-yellow-600 mt-1"><?= $pendingVendors ?></p>
                <p class="text-xs text-yellow-600 mt-1">Needs attention</p>
            </a>

            <!-- Total Vendors -->
            <div class="stat-card bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500 hover:shadow-md transition">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Vendors</p>
                <p class="text-2xl md:text-3xl font-bold text-blue-600 mt-1"><?= $totalVendors ?></p>
                <p class="text-xs text-gray-400 mt-1">Approved vendors</p>
            </div>

            <!-- Total Products -->
            <div class="stat-card bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500 hover:shadow-md transition">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Products</p>
                <p class="text-2xl md:text-3xl font-bold text-green-600 mt-1"><?= $totalProducts ?></p>
                <p class="text-xs text-gray-400 mt-1">Across all vendors</p>
            </div>

            <!-- Total Revenue -->
            <div class="stat-card bg-white rounded-xl shadow-sm p-5 border-l-4 border-purple-500 hover:shadow-md transition">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Revenue</p>
                <p class="text-2xl md:text-3xl font-bold text-purple-600 mt-1">R <?= number_format($totalRevenue, 2) ?></p>
                <p class="text-xs text-gray-400 mt-1">From delivered orders</p>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- TWO-COLUMN: Recent Orders + Pending Vendors -->
        <!-- ============================================================ -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- LEFT: Recent Orders -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="font-bold text-gray-800">Recent Orders</h2>
                    <a href="orders.php" class="text-blue-600 text-sm hover:underline">View all →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left">Order ID</th>
                                <th class="px-6 py-3 text-left">Customer</th>
                                <th class="px-6 py-3 text-left">Amount</th>
                                <th class="px-6 py-3 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (count($recentOrders) > 0): ?>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3 font-medium text-gray-800">#BM-<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                        <td class="px-6 py-3 text-gray-600"><?= htmlspecialchars($order['customer_name'] ?? 'Guest') ?></td>
                                        <td class="px-6 py-3 font-semibold text-gray-800">R <?= number_format($order['total'], 2) ?></td>
                                        <td class="px-6 py-3">
                                            <?php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'paid' => 'bg-blue-100 text-blue-800',
                                                'shipped' => 'bg-indigo-100 text-indigo-800',
                                                'delivered' => 'bg-green-100 text-green-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                            ];
                                            $color = $statusColors[$order['status']] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium <?= $color ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">No orders yet</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- RIGHT: Pending Vendors -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden" id="pending-vendors">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="font-bold text-gray-800">Pending Vendors</h2>
                    <span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded-full text-xs font-semibold"><?= $pendingVendors ?></span>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php if (count($pendingVendorsList) > 0): ?>
                        <?php foreach ($pendingVendorsList as $vendor): ?>
                            <div class="px-6 py-4 hover:bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($vendor['business_name']) ?></p>
                                        <p class="text-sm text-gray-500"><?= htmlspecialchars($vendor['user_name'] ?? 'Unknown') ?></p>
                                        <p class="text-sm text-gray-400"><?= htmlspecialchars($vendor['email']) ?></p>
                                        <p class="text-xs text-gray-400 mt-1">Applied: <?= date('d M Y', strtotime($vendor['applied_at'])) ?></p>
                                    </div>
                                    <div class="flex gap-2">
                                        <form method="POST" action="">
                                            <input type="hidden" name="vendor_id" value="<?= $vendor['user_id'] ?>">
                                            <button type="submit" name="approve_vendor" class="btn-approve">✓ Approve</button>
                                        </form>
                                        <form method="POST" action="" onsubmit="return confirm('Reject <?= htmlspecialchars($vendor['business_name']) ?>? This cannot be undone.')">
                                            <input type="hidden" name="vendor_id" value="<?= $vendor['user_id'] ?>">
                                            <button type="submit" name="reject_vendor" class="btn-reject">✕ Reject</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if ($pendingVendors > 5): ?>
                            <div class="px-6 py-3 text-center text-sm text-gray-500">
                                + <?= $pendingVendors - 5 ?> more pending vendors
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="px-6 py-8 text-center text-gray-500">
                            <div class="text-3xl mb-2">🎉</div>
                            <p>No pending vendor applications.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- QUICK LINKS -->
        <!-- ============================================================ -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            <a href="vendors.php" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition text-center">
                <div class="text-3xl mb-2">👥</div>
                <h3 class="font-semibold text-gray-800">Vendor Management</h3>
                <p class="text-sm text-gray-500 mt-1">View all vendors, approve or reject</p>
                <span class="inline-block mt-3 text-blue-600 text-sm font-medium">Go to vendors →</span>
            </a>

            <a href="products.php" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition text-center">
                <div class="text-3xl mb-2">📦</div>
                <h3 class="font-semibold text-gray-800">All Products</h3>
                <p class="text-sm text-gray-500 mt-1">Browse every product on the platform</p>
                <span class="inline-block mt-3 text-blue-600 text-sm font-medium">Go to products →</span>
            </a>

            <a href="orders.php" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition text-center">
                <div class="text-3xl mb-2">📋</div>
                <h3 class="font-semibold text-gray-800">Order Management</h3>
                <p class="text-sm text-gray-500 mt-1">View all orders across the platform</p>
                <span class="inline-block mt-3 text-blue-600 text-sm font-medium">Go to orders →</span>
            </a>
        </div>

    </div>

    <!-- ============================================================ -->
    <!-- FOOTER -->
    <!-- ============================================================ -->
    <footer class="bg-gray-800 text-white mt-8 py-6">
        <div class="container mx-auto px-4 text-center text-sm text-gray-400">
            <p>&copy; <?= date('Y') ?> Bona Markets. All rights reserved.</p>
            <p class="text-xs mt-1">Admin Dashboard v1.0</p>
        </div>
    </footer>

</body>
</html>