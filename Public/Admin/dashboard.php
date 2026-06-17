<?php
$pageTitle = 'Admin Dashboard – Bona Markets';
require_once '../../includes/header.php';

// fakedata
$stats = [
    'pending_vendors' => 3,
    'total_vendors'   => 12,
    'total_products'  => 48,
    'total_orders'    => 27,
];
?>

<div class="container mx-auto px-4 py-8">

    // Page heading
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
        <p class="text-gray-500 text-sm mt-1">Welcome back. Here's what's happening on the platform.</p>
    </div>

    // Stat cards
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">

        // Pending vendors (highlighted because it needs attention)
        <a href="/admin/vendors.php" class="block bg-amber-50 border border-amber-300 rounded-xl p-5 hover:shadow-md transition">
            <p class="text-3xl font-bold text-amber-600"><?= $stats['pending_vendors'] ?></p>
            <p class="text-sm text-amber-700 mt-1 font-medium">Pending Applications</p>
            <span class="inline-block mt-2 text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Needs attention</span>
        </a>

        // Total vendors
        <a href="/admin/vendors.php" class="block bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition">
            <p class="text-3xl font-bold text-blue-600"><?= $stats['total_vendors'] ?></p>
            <p class="text-sm text-gray-500 mt-1">Total Vendors</p>
        </a>

        // Total products
        <a href="/admin/products.php" class="block bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition">
            <p class="text-3xl font-bold text-blue-600"><?= $stats['total_products'] ?></p>
            <p class="text-sm text-gray-500 mt-1">Total Products</p>
        </a>

        // Total orders
        <a href="/admin/orders.php" class="block bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition">
            <p class="text-3xl font-bold text-blue-600"><?= $stats['total_orders'] ?></p>
            <p class="text-sm text-gray-500 mt-1">Total Orders</p>
        </a>

    </div>

    // Quick links
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <a href="/admin/vendors.php" class="block bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition">
            <h2 class="font-semibold text-gray-800 mb-1">Vendor Applications</h2>
            <p class="text-sm text-gray-500">Review, approve or reject vendors applying to sell on the platform.</p>
            <span class="inline-block mt-3 text-sm text-blue-600 font-medium">Go to vendors →</span>
        </a>

        <a href="/admin/orders.php" class="block bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition">
            <h2 class="font-semibold text-gray-800 mb-1">Order Management</h2>
            <p class="text-sm text-gray-500">View all orders placed across the platform with buyer and status info.</p>
            <span class="inline-block mt-3 text-sm text-blue-600 font-medium">Go to orders →</span>
        </a>

        <a href="/admin/products.php" class="block bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition">
            <h2 class="font-semibold text-gray-800 mb-1">All Products</h2>
            <p class="text-sm text-gray-500">Browse every product listed on Bona Markets across all vendors.</p>
            <span class="inline-block mt-3 text-sm text-blue-600 font-medium">Go to products →</span>
        </a>

    </div>

</div>

<?php require_once '../../includes/footer.php'; ?>
