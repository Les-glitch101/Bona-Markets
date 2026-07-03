<?php
// ============================================================
// INDEX PAGE – Bona Markets Homepage
// ============================================================

session_start();
require_once '../config/database.php';

// Get user info from session
$isLoggedIn   = isset($_SESSION['user_id']);
$userFullName = $isLoggedIn ? $_SESSION['fullname'] : '';
$userEmail    = $isLoggedIn ? $_SESSION['email']    : '';
$userRole     = $isLoggedIn ? $_SESSION['role']     : '';

// ─── FETCH FEATURED PRODUCTS FROM DB ───────────────────────
$featuredProducts = [];
try {
    $stmt = $pdo->query("
        SELECT p.id, p.name, p.price, p.image_url, p.stock,
               c.name  AS category_name,
               vp.business_name AS vendor_name
        FROM products p
        LEFT JOIN categories c        ON p.category_id = c.id
        LEFT JOIN vendor_profiles vp  ON p.vendor_id   = vp.user_id
        WHERE p.status = 'active' AND p.stock > 0
        ORDER BY p.created_at DESC
        LIMIT 4
    ");
    $featuredProducts = $stmt->fetchAll();
} catch (PDOException $e) {
    // Silently fail — empty array means empty state shown
}

// ─── FETCH CATEGORIES WITH REAL PRODUCT COUNTS ─────────────
$categoryData = [];
try {
    $stmt = $pdo->query("
        SELECT c.id, c.name,
               COUNT(p.id) AS product_count
        FROM categories c
        LEFT JOIN products p ON p.category_id = c.id AND p.status = 'active' AND p.stock > 0
        GROUP BY c.id, c.name
        ORDER BY product_count DESC
        LIMIT 8
    ");
    $categoryData = $stmt->fetchAll();
} catch (PDOException $e) {
    // Silently fail
}

// Category emoji map
$categoryEmojis = [
    'electronics'    => '📱',
    'clothing'       => '👕',
    'fashion'        => '👗',
    'home'           => '🏠',
    'garden'         => '🌿',
    'accessories'    => '⌚',
    'food'           => '🍎',
    'beauty'         => '💄',
    'health'         => '💊',
    'sports'         => '⚽',
    'toys'           => '🧸',
    'books'          => '📚',
    'art'            => '🎨',
    'jewellery'      => '💍',
    'jewelry'        => '💍',
    'crafts'         => '🧶',
    'African'        => '🌍',
];
function getCategoryEmoji(string $name): string {
    global $categoryEmojis;
    $lower = strtolower($name);
    foreach ($categoryEmojis as $key => $emoji) {
        if (str_contains($lower, strtolower($key))) return $emoji;
    }
    return '🛍️';
}


// Check for logout success message
$logoutMessage = '';
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $logoutMessage = '
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>You have been successfully logged out.</span>
            </div>
        </div>
    ';
}

// Check for login success message
$loginMessage = '';
if (isset($_GET['login']) && $_GET['login'] === 'success') {
    $loginMessage = '
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Welcome back' . (!empty($userFullName) ? ', <strong>' . htmlspecialchars($userFullName) . '</strong>' : '') . '! 🎉</span>
            </div>
        </div>
    ';
}

// Check for registration success message
$registerMessage = '';
if (isset($_GET['registered']) && $_GET['registered'] === 'success') {
    $registerMessage = '
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Account created successfully! Please log in.</span>
            </div>
        </div>
    ';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <title>Bona Markets | Multi-Vendor Marketplace</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        .product-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }
        /* Modal overlay for login prompt */
        .login-prompt-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .login-prompt-overlay.active {
            display: flex;
        }
        .login-prompt {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            max-width: 420px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: popIn 0.3s ease;
        }
        @keyframes popIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .login-prompt .icon {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }
        .login-prompt h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1a1208;
            margin-bottom: 0.5rem;
        }
        .login-prompt p {
            color: #7a6e5f;
            font-size: 0.9rem;
            margin-bottom: 1.2rem;
        }
        .login-prompt .btn-group {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .login-prompt .btn-group a {
            padding: 0.6rem 1.8rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }
        .login-prompt .btn-login {
            background: #3B82F6;
            color: white;
        }
        .login-prompt .btn-login:hover {
            background: #2563EB;
        }
        .login-prompt .btn-signup {
            background: #f3f4f6;
            color: #1a1208;
        }
        .login-prompt .btn-signup:hover {
            background: #e5e7eb;
        }
        .login-prompt .btn-close {
            margin-top: 0.75rem;
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            font-size: 0.85rem;
            text-decoration: underline;
        }
        .login-prompt .btn-close:hover {
            color: #6b7280;
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- ============================================================ -->
    <!-- LOGIN PROMPT MODAL -->
    <!-- ============================================================ -->
    <div class="login-prompt-overlay" id="loginPrompt">
        <div class="login-prompt">
            <div class="icon">🔐</div>
            <h3>Login or Sign Up</h3>
            <p>Please login or create an account to use this feature.</p>
            <div class="btn-group">
                <a href="login.php" class="btn-login">Login</a>
                <a href="register.php" class="btn-signup">Sign Up</a>
            </div>
            <button class="btn-close" onclick="closeLoginPrompt()">Cancel</button>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- NAVBAR -->
    <!-- ============================================================ -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <a href="index.php" class="text-2xl md:text-3xl font-bold text-blue-600 tracking-tight">
                    Bona Markets
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-gray-600 hover:text-blue-600 font-medium">Shop</a>
                    <a href="products/index.php" class="text-gray-600 hover:text-blue-600 font-medium">Categories</a>
                    <a href="about.php" class="text-gray-600 hover:text-blue-600 font-medium">About</a>
                    <a href="contact.php" class="text-gray-600 hover:text-blue-600 font-medium">Contact</a>
                </div>

                <!-- Desktop Auth Buttons (Dynamic) -->
                <div class="hidden md:flex items-center space-x-4">
                    <?php if ($isLoggedIn): ?>
                        <?php if ($userRole === 'vendor'): ?>
                            <a href="vendor/dashboard.php" class="text-gray-600 hover:text-blue-600 font-medium">Vendor Dashboard</a>
                        <?php endif; ?>
                        <?php if ($userRole === 'admin'): ?>
                            <a href="admin/dashboard.php" class="text-gray-600 hover:text-blue-600 font-medium">Admin Panel</a>
                        <?php endif; ?>
                        <a href="cart/index.php" class="text-gray-600 hover:text-blue-600 font-medium">Cart 🛒</a>
                        <a href="orders/index.php" class="text-gray-600 hover:text-blue-600 font-medium">Orders</a>
                        <span class="text-gray-600">👋 <?= htmlspecialchars($userFullName ?: $userEmail) ?></span>
                        <a href="logout.php" class="text-red-500 hover:text-red-700 font-medium">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-600 hover:text-blue-600 font-medium">Login</a>
                        <a href="register.php" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition">
                            Sign Up
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobileMenuBtn" class="md:hidden text-gray-600 focus:outline-none">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Dropdown Menu -->
            <div id="mobileMenu" class="hidden md:hidden mt-4 pb-3 border-t pt-4 flex flex-col space-y-3">
                <a href="index.php" class="text-gray-600 hover:text-blue-600 py-1">Shop</a>
                <a href="products/index.php" class="text-gray-600 hover:text-blue-600 py-1">Categories</a>
                <a href="about.php" class="text-gray-600 hover:text-blue-600 py-1">About</a>
                <a href="contact.php" class="text-gray-600 hover:text-blue-600 py-1">Contact</a>
                <div class="flex flex-col space-y-2 pt-2">
                    <?php if ($isLoggedIn): ?>
                        <?php if ($userRole === 'vendor'): ?>
                            <a href="vendor/dashboard.php" class="text-gray-600 py-1">Vendor Dashboard</a>
                        <?php endif; ?>
                        <?php if ($userRole === 'admin'): ?>
                            <a href="admin/dashboard.php" class="text-gray-600 py-1">Admin Panel</a>
                        <?php endif; ?>
                        <a href="cart/index.php" class="text-gray-600 py-1">Cart 🛒</a>
                        <a href="orders/index.php" class="text-gray-600 py-1">Orders</a>
                        <span class="text-gray-600 py-1">👋 <?= htmlspecialchars($userFullName ?: $userEmail) ?></span>
                        <a href="logout.php" class="text-red-500 py-1">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-600 hover:text-blue-600 py-1">Login</a>
                        <a href="register.php" class="bg-blue-600 text-white text-center px-4 py-2 rounded-lg">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- ============================================================ -->
    <!-- MAIN CONTENT -->
    <!-- ============================================================ -->
    <main>

        <!-- ─── HERO BANNER ────────────────────────────────────────── -->
        <?php if (!$isLoggedIn): ?>
            <!-- SHOW FOR NOT LOGGED IN -->
            <section class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
                <div class="container mx-auto px-4 py-12 md:py-16 text-center">
                    <h1 class="text-3xl md:text-5xl font-bold mb-4">
                        Welcome to Bona Markets
                    </h1>
                    <p class="text-lg md:text-xl opacity-90 max-w-2xl mx-auto">
                        Discover unique products from trusted vendors around the world
                    </p>
                    <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="register.php" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                            Start Shopping
                        </a>
                        <a href="register.php" class="border border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">
                            Become a Vendor
                        </a>
                    </div>
                </div>
            </section>
        <?php else: ?>
            <!-- SHOW FOR LOGGED IN (Personalized Dashboard Welcome) -->
            <section class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
                <div class="container mx-auto px-4 py-12 md:py-16 text-center">
                    <div class="max-w-3xl mx-auto">
                        <div class="text-5xl mb-4">👋</div>
                        <h1 class="text-3xl md:text-5xl font-bold mb-4">
                            Welcome back, <?= htmlspecialchars($userFullName ?: $userEmail) ?>!
                        </h1>
                        <p class="text-lg md:text-xl opacity-90 max-w-2xl mx-auto">
                            <?php if ($userRole === 'vendor'): ?>
                                Your store is ready. Here's what's happening today.
                            <?php elseif ($userRole === 'admin'): ?>
                                Welcome to the admin dashboard. Manage your platform here.
                            <?php else: ?>
                                Ready to discover something new? Browse our latest products.
                            <?php endif; ?>
                        </p>
                        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                            <?php if ($userRole === 'vendor'): ?>
                                <a href="vendor/dashboard.php" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                                    Go to Dashboard →
                                </a>
                                <a href="vendor/dashboard.php" class="border border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">
                                    Add Product
                                </a>
                            <?php elseif ($userRole === 'admin'): ?>
                                <a href="admin/dashboard.php" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                                    Go to Admin Panel →
                                </a>
                            <?php elseif ($userRole === 'buyer'): ?>
                                <a href="products/index.php" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                                    Browse Products
                                </a>
                                <a href="apply.php" class="border border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">
                                    Become a Vendor
                                </a>
                            <?php else: ?>
                                <a href="products/index.php" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition" onclick="showLoginPrompt(event)">
                                    Browse Products
                                </a>
                                <a href="apply.php" class="border border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">
                                    Become a Vendor
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Search & Filter Bar -->
        <div class="container mx-auto px-4 -mt-6">
            <div class="bg-white rounded-lg shadow-md p-4">
                <?php if ($isLoggedIn): ?>
                <form method="GET" action="products/index.php" class="flex flex-col md:flex-row gap-3">
                    <input type="text" name="search" placeholder="Search products..."
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg bg-white">
                        <option value="">All Categories</option>
                        <?php foreach ($categoryData as $cat): ?>
                            <?php if ($cat['product_count'] > 0): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <select name="sort" class="px-4 py-2 border border-gray-300 rounded-lg bg-white">
                        <option value="">Newest First</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                    </select>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Search
                    </button>
                </form>
                <?php else: ?>
                <div class="flex flex-col md:flex-row gap-3">
                    <input type="text" placeholder="Search products..."
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           onclick="showLoginPrompt(event)" readonly>
                    <select class="px-4 py-2 border border-gray-300 rounded-lg bg-white" onclick="showLoginPrompt(event)">
                        <option value="">All Categories</option>
                        <?php foreach ($categoryData as $cat): ?>
                            <?php if ($cat['product_count'] > 0): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <select class="px-4 py-2 border border-gray-300 rounded-lg bg-white" onclick="showLoginPrompt(event)">
                        <option>Newest First</option>
                        <option>Price: Low to High</option>
                        <option>Price: High to Low</option>
                    </select>
                    <button type="button" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition"
                            onclick="showLoginPrompt(event)">
                        Search
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Welcome Message for Logged-in Users (Shown below search bar) -->
        <?php if ($isLoggedIn): ?>
            <div class="container mx-auto px-4 py-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-blue-700">
                        👋 Welcome back, <strong><?= htmlspecialchars($userFullName ?: $userEmail) ?></strong>!
                        <?php if ($userRole === 'vendor'): ?>
                            <a href="vendor/dashboard.php" class="text-blue-600 hover:underline ml-2">Go to your vendor dashboard →</a>
                        <?php elseif ($userRole === 'buyer'): ?>
                            <a href="products/index.php" class="text-blue-600 hover:underline ml-2">Start shopping →</a>
                        
                            <?php elseif ($userRole === 'admin'): ?>
                            <a href="admin/dashboard.php" class="text-blue-600 hover:underline ml-2">Go to admin panel →</a>
                        <?php else: ?>
                            <a href="products/index.php" class="text-blue-600 hover:underline ml-2" onclick="showLoginPrompt(event)">Start shopping →</a>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Featured Products Section -->
        <div class="container mx-auto px-4 py-12">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Featured Products</h2>
                <a href="products/index.php" class="text-blue-600 hover:underline">View All →</a>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

                <?php if (count($featuredProducts) > 0): ?>
                    <?php foreach ($featuredProducts as $p): ?>
                        <div class="product-card bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">

                            <!-- Image -->
                            <?php if (!empty($p['image_url'])): ?>
                                <img src="/Bona-Markets/Public/<?= htmlspecialchars($p['image_url']) ?>"
                                     alt="<?= htmlspecialchars($p['name']) ?>"
                                     class="w-full h-48 object-cover" />
                            <?php else: ?>
                                <div class="w-full h-48 bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-content-center" style="display:flex;align-items:center;justify-content:center;">
                                    <span style="font-size:3.5rem;">🛍️</span>
                                </div>
                            <?php endif; ?>

                            <div class="p-4">
                                <div class="text-xs text-gray-500 mb-1">
                                    <?= htmlspecialchars($p['category_name'] ?? 'General') ?>
                                </div>
                                <h3 class="font-semibold text-lg text-gray-800 leading-tight" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    <?= htmlspecialchars($p['name']) ?>
                                </h3>
                                <p class="text-gray-500 text-sm mt-1">
                                    by <?= htmlspecialchars($p['vendor_name'] ?? 'Bona Vendor') ?>
                                </p>
                                <div class="flex justify-between items-center mt-3">
                                    <span class="text-xl font-bold text-blue-600">
                                        R <?= number_format($p['price'], 2) ?>
                                    </span>
                                    <button class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700 transition"
                                            onclick="if(!<?= $isLoggedIn ? 'true' : 'false' ?>) { showLoginPrompt(event); } else { addToCart(<?= $p['id'] ?>, this); }">
                                        Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                <?php else: ?>
                    <!-- Empty state — no active products yet -->
                    <div class="col-span-4 text-center py-16 text-gray-400">
                        <div style="font-size:3rem;margin-bottom:.75rem;">📦</div>
                        <p class="text-lg font-medium text-gray-500">No products listed yet.</p>
                        <p class="text-sm mt-1">Check back soon — vendors are setting up their stores!</p>
                        <?php if (!$isLoggedIn): ?>
                            <a href="apply.php" class="inline-block mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                                Become a Vendor →
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- Categories Section -->
        <div class="bg-white py-12">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 text-center mb-8">
                    Shop by Category
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php
                    $cardColours = [
                        'bg-blue-50 hover:bg-blue-100',
                        'bg-green-50 hover:bg-green-100',
                        'bg-yellow-50 hover:bg-yellow-100',
                        'bg-purple-50 hover:bg-purple-100',
                        'bg-pink-50 hover:bg-pink-100',
                        'bg-orange-50 hover:bg-orange-100',
                        'bg-teal-50 hover:bg-teal-100',
                        'bg-red-50 hover:bg-red-100',
                    ];
                    if (count($categoryData) > 0):
                        foreach ($categoryData as $i => $cat):
                            if ($cat['product_count'] == 0) continue;
                            $colour = $cardColours[$i % count($cardColours)];
                            $emoji  = getCategoryEmoji($cat['name']);
                            $count  = $cat['product_count'];
                    ?>
                        <div class="<?= $colour ?> rounded-xl p-6 text-center transition cursor-pointer"
                             onclick="if(!<?= $isLoggedIn ? 'true' : 'false' ?>) { showLoginPrompt(event); }">
                            <div class="text-4xl mb-2"><?= $emoji ?></div>
                            <h3 class="font-semibold"><?= htmlspecialchars($cat['name']) ?></h3>
                            <p class="text-sm text-gray-500"><?= $count ?> <?= $count === 1 ? 'product' : 'products' ?></p>
                        </div>
                    <?php
                        endforeach;
                    else:
                    ?>
                        <div class="col-span-4 text-center py-10 text-gray-400">
                            <div style="font-size:2.5rem;margin-bottom:.5rem;">🗂️</div>
                            <p class="text-sm">No categories with active products yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Call to Action: Become a Vendor (Only show when NOT logged in) -->
        <?php if (!$isLoggedIn): ?>
            <div class="container mx-auto px-4 py-12">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl text-white p-8 md:p-12 text-center">
                    <h2 class="text-2xl md:text-3xl font-bold mb-3">Sell on Bona Markets</h2>
                    <p class="text-lg opacity-90 max-w-2xl mx-auto mb-6">
                        Join thousands of vendors who grow their business with us
                    </p>
                    <a href="register.php" class="inline-block bg-white text-purple-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                        Apply as Vendor →
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Hide the CTA when logged in, show a smaller quick action instead -->
            <div class="container mx-auto px-4 py-8">
                <div class="bg-gray-100 rounded-2xl p-6 md:p-8 text-center border border-gray-200">
                    <p class="text-gray-600">
                        <?php if ($userRole === 'vendor'): ?>
                            🛍️ <strong>Your store is active!</strong> 
                            <a href="vendor/dashboard.php" class="text-blue-600 hover:underline">Manage your products →</a>
                        <?php elseif ($userRole === 'admin'): ?>
                            ⚙️ <strong>Admin panel ready.</strong>
                            <a href="admin/dashboard.php" class="text-blue-600 hover:underline">Go to admin →</a>
                        <?php else: ?>
                            💼 <strong>Want to sell on Bona Markets?</strong>
                            <a href="apply.php" class="text-blue-600 hover:underline ml-2">Apply to become a vendor →</a>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- ============================================================ -->
    <!-- FOOTER -->
    <!-- ============================================================ -->
    <footer class="bg-gray-800 text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-3">Bona Markets</h3>
                    <p class="text-gray-400 text-sm">Your trusted multi-vendor marketplace for authentic African goods.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-3">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="index.php" class="hover:text-white">Home</a></li>
                        <li><a href="products/index.php" class="hover:text-white">Shop</a></li>
                        <li><a href="contact.php" class="hover:text-white">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-3">Support</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-white">Help Center</a></li>
                        <li><a href="#" class="hover:text-white">Returns Policy</a></li>
                        <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-3">Connect</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-white">Facebook</a></li>
                        <li><a href="#" class="hover:text-white">Instagram</a></li>
                        <li><a href="#" class="hover:text-white">Twitter</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400 text-sm">
                <p>&copy; 2026 Bona Markets. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- ============================================================ -->
    <!-- JAVASCRIPT -->
    <!-- ============================================================ -->
    <script>
        // ===== Mobile Menu Toggle =====
        const menuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        if (menuBtn && mobileMenu) {
            menuBtn.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // ===== Login Prompt Modal =====
        function showLoginPrompt(event) {
            event.preventDefault();
            document.getElementById('loginPrompt').classList.add('active');
        }

        function closeLoginPrompt() {
            document.getElementById('loginPrompt').classList.remove('active');
        }

        // Close modal when clicking outside
        document.getElementById('loginPrompt').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLoginPrompt();
            }
        });

        // ===== Add to Cart =====
        function addToCart(productId, btn) {
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Adding…';
            }

            fetch('cart/add.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'product_id=' + productId + '&quantity=1'
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast('✅ ' + data.message, 'success');
                    updateCartBadge(data.cart_count);
                } else {
                    showToast('❌ ' + data.message, 'error');
                }
            })
            .catch(() => showToast('❌ Could not reach server. Please try again.', 'error'))
            .finally(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = 'Add to Cart';
                }
            });
        }

        // ===== Toast Notification =====
        function showToast(message, type) {
            const existing = document.getElementById('cartToast');
            if (existing) existing.remove();

            const toast = document.createElement('div');
            toast.id = 'cartToast';
            toast.textContent = message;
            toast.style.cssText = `
                position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;
                background:${type === 'success' ? '#065f46' : '#991b1b'};
                color:white;padding:.75rem 1.25rem;border-radius:10px;
                font-size:.875rem;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.2);
                animation:slideUp .25s ease;
            `;
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity .4s'; setTimeout(() => toast.remove(), 400); }, 3000);
        }

        // ===== Cart Badge =====
        function updateCartBadge(count) {
            document.querySelectorAll('.cart-badge').forEach(el => {
                el.textContent = count > 0 ? count : '';
                el.style.display = count > 0 ? 'inline' : 'none';
            });
        }

        console.log('Bona Markets homepage loaded – Login/Logout states active!');
    </script>

</body>
</html>