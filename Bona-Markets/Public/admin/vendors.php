<?php
// ============================================================
// ADMIN – Vendor Management
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

// ─── HANDLE APPROVAL/REJECTION ──────────────────────────────────
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_vendor'])) {
        $vendorId = intval($_POST['vendor_id']);
        $pdo->prepare("UPDATE vendor_profiles SET approved = 1 WHERE user_id = ?")->execute([$vendorId]);
        $message = 'Vendor approved successfully!';
    }
    if (isset($_POST['reject_vendor'])) {
        $vendorId = intval($_POST['vendor_id']);
        $pdo->prepare("DELETE FROM vendor_profiles WHERE user_id = ?")->execute([$vendorId]);
        $pdo->prepare("UPDATE users SET role = 'buyer' WHERE id = ?")->execute([$vendorId]);
        $message = 'Vendor rejected and removed.';
    }
    header('refresh:1');
}

// ─── GET ALL VENDORS ─────────────────────────────────────────────
// Pending vendors
$pendingVendors = $pdo->query("
    SELECT v.*, u.email, u.fullname as user_name
    FROM vendor_profiles v
    JOIN users u ON v.user_id = u.id
    WHERE v.approved = 0
    ORDER BY v.applied_at DESC
")->fetchAll();

// Approved vendors
$approvedVendors = $pdo->query("
    SELECT v.*, u.email, u.fullname as user_name
    FROM vendor_profiles v
    JOIN users u ON v.user_id = u.id
    WHERE v.approved = 1
    ORDER BY v.business_name ASC
")->fetchAll();

$adminName = $_SESSION['fullname'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vendors | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .btn-approve { background: #10b981; color: white; padding: 0.25rem 0.8rem; border-radius: 6px; border: none; cursor: pointer; font-size: 0.8rem; font-weight: 600; }
        .btn-approve:hover { background: #059669; }
        .btn-reject { background: #ef4444; color: white; padding: 0.25rem 0.8rem; border-radius: 6px; border: none; cursor: pointer; font-size: 0.8rem; font-weight: 600; }
        .btn-reject:hover { background: #dc2626; }
        .btn-view { background: #3b82f6; color: white; padding: 0.25rem 0.8rem; border-radius: 6px; border: none; cursor: pointer; font-size: 0.8rem; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-view:hover { background: #2563eb; }
        .pending-badge { background: #fef3c7; color: #92400e; padding: 0.2rem 0.7rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; }
        .approved-badge { background: #d1fae5; color: #065f46; padding: 0.2rem 0.7rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; }
        .nav-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 1rem; border-radius: 8px; color: #64748b; text-decoration: none; transition: all 0.2s; }
        .nav-link:hover { background: #f1f5f9; color: #0f172a; }
        .nav-link.active { background: #eff6ff; color: #2563eb; font-weight: 600; }
    </style>
</head>
<body>

    <!-- TOPBAR -->
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
            <h1 class="text-2xl font-bold text-gray-800">Vendor Management</h1>
        </div>

        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Pending Vendors -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200 bg-yellow-50 flex justify-between items-center">
                <h2 class="font-bold text-gray-800">⏳ Pending Applications</h2>
                <span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded-full text-xs font-semibold"><?= count($pendingVendors) ?></span>
            </div>
            <?php if (count($pendingVendors) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left">Business Name</th>
                                <th class="px-6 py-3 text-left">Owner</th>
                                <th class="px-6 py-3 text-left">Email</th>
                                <th class="px-6 py-3 text-left">Applied</th>
                                <th class="px-6 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($pendingVendors as $vendor): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-800"><?= htmlspecialchars($vendor['business_name']) ?></td>
                                    <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($vendor['user_name'] ?? 'Unknown') ?></td>
                                    <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($vendor['email']) ?></td>
                                    <td class="px-6 py-4 text-gray-500 text-sm"><?= date('d M Y', strtotime($vendor['applied_at'])) ?></td>
                                    <td class="px-6 py-4 flex gap-2">
                                        <form method="POST" action="">
                                            <input type="hidden" name="vendor_id" value="<?= $vendor['user_id'] ?>">
                                            <button type="submit" name="approve_vendor" class="btn-approve">✓ Approve</button>
                                        </form>
                                        <form method="POST" action="" onsubmit="return confirm('Reject <?= htmlspecialchars($vendor['business_name']) ?>?')">
                                            <input type="hidden" name="vendor_id" value="<?= $vendor['user_id'] ?>">
                                            <button type="submit" name="reject_vendor" class="btn-reject">✕ Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="px-6 py-8 text-center text-gray-500">🎉 No pending applications. All vendors are approved.</div>
            <?php endif; ?>
        </div>

        <!-- Approved Vendors -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="font-bold text-gray-800">✅ Approved Vendors</h2>
                <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded-full text-xs font-semibold"><?= count($approvedVendors) ?></span>
            </div>
            <?php if (count($approvedVendors) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left">Business Name</th>
                                <th class="px-6 py-3 text-left">Owner</th>
                                <th class="px-6 py-3 text-left">Email</th>
                                <th class="px-6 py-3 text-left">Products</th>
                                <th class="px-6 py-3 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($approvedVendors as $vendor): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-800"><?= htmlspecialchars($vendor['business_name']) ?></td>
                                    <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($vendor['user_name'] ?? 'Unknown') ?></td>
                                    <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($vendor['email']) ?></td>
                                    <td class="px-6 py-4 text-gray-500 text-sm">
                                        <?php
                                        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE vendor_id = ?");
                                        $stmt->execute([$vendor['user_id']]);
                                        echo $stmt->fetch()['count'];
                                        ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="../products/index.php" class="btn-view">View Store</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="px-6 py-8 text-center text-gray-500">No approved vendors yet.</div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>