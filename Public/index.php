<?php
// ============================================================
// INDEX PAGE – Bona Markets Homepage
// ============================================================

session_start();

// Get user info from session
$isLoggedIn = isset($_SESSION['user_id']);
$userFullName = $isLoggedIn ? $_SESSION['fullname'] : '';
$userEmail = $isLoggedIn ? $_SESSION['email'] : '';
$userRole = $isLoggedIn ? $_SESSION['role'] : '';

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
                    <a href="#" class="text-gray-600 hover:text-blue-600 font-medium">Categories</a>
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
                <a href="#" class="text-gray-600 hover:text-blue-600 py-1">Categories</a>
                <a href="#" class="text-gray-600 hover:text-blue-600 py-1">About</a>
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
                                <a href="#" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                                    Browse Products
                                </a>
                                <a href="apply.php" class="border border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">
                                    Become a Vendor
                                </a>
                            <?php else: ?>
                                <a href="#" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition" onclick="showLoginPrompt(event)">
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
            <div class="bg-white rounded-lg shadow-md p-4 flex flex-col md:flex-row gap-3">
                <input type="text" placeholder="Search products..."
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       onclick="if(!<?= $isLoggedIn ? 'true' : 'false' ?>) showLoginPrompt(event)">
                <select class="px-4 py-2 border border-gray-300 rounded-lg bg-white" onclick="if(!<?= $isLoggedIn ? 'true' : 'false' ?>) showLoginPrompt(event)">
                    <option>All Categories</option>
                    <option>Electronics</option>
                    <option>Clothing</option>
                    <option>Home & Garden</option>
                    <option>Accessories</option>
                </select>
                <select class="px-4 py-2 border border-gray-300 rounded-lg bg-white" onclick="if(!<?= $isLoggedIn ? 'true' : 'false' ?>) showLoginPrompt(event)">
                    <option>Newest First</option>
                    <option>Price: Low to High</option>
                    <option>Price: High to Low</option>
                </select>
                <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition"
                        onclick="if(!<?= $isLoggedIn ? 'true' : 'false' ?>) { showLoginPrompt(event); } else { alert('Search functionality coming soon!'); }">
                    Search
                </button>
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
                            <a href="#" class="text-blue-600 hover:underline ml-2">Start shopping →</a>
                        
                            <?php elseif ($userRole === 'admin'): ?>
                            <a href="admin/dashboard.php" class="text-blue-600 hover:underline ml-2">Go to admin panel →</a>
                        <?php else: ?>
                            <a href="#" class="text-blue-600 hover:underline ml-2" onclick="showLoginPrompt(event)">Start shopping →</a>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Featured Products Section -->
        <div class="container mx-auto px-4 py-12">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Featured Products</h2>
                <a href="#" class="text-blue-600 hover:underline" onclick="if(!<?= $isLoggedIn ? 'true' : 'false' ?>) { showLoginPrompt(event); return false; }">View All →</a>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

                <!-- Product Card 1 -->
                <div class="product-card bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
                    <img src="https://placehold.co/400x300/3B82F6/white?text=Headphones" alt="Product"
                         class="w-full h-48 object-cover">
                    <div class="p-4">
                        <div class="text-xs text-gray-500 mb-1">Electronics</div>
                        <h3 class="font-semibold text-lg text-gray-800">Wireless Headphones</h3>
                        <p class="text-gray-500 text-sm mt-1">by TechStore</p>
                        <div class="flex items-center mt-2">
                            <span class="text-yellow-400">★★★★☆</span>
                            <span class="text-xs text-gray-500 ml-2">(24)</span>
                        </div>
                        <div class="flex justify-between items-center mt-3">
                            <span class="text-xl font-bold text-blue-600">R79.99</span>
                            <button class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700 transition"
                                    onclick="if(!<?= $isLoggedIn ? 'true' : 'false' ?>) { showLoginPrompt(event); } else { alert('Product added to cart!'); }">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product Card 2 -->
                <div class="product-card bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
                    <img src="https://placehold.co/400x300/10B981/white?text=Watch" alt="Product"
                         class="w-full h-48 object-cover">
                    <div class="p-4">
                        <div class="text-xs text-gray-500 mb-1">Accessories</div>
                        <h3 class="font-semibold text-lg text-gray-800">Smart Watch Pro</h3>
                        <p class="text-gray-500 text-sm mt-1">by GadgetHub</p>
                        <div class="flex items-center mt-2">
                            <span class="text-yellow-400">★★★★★</span>
                            <span class="text-xs text-gray-500 ml-2">(102)</span>
                        </div>
                        <div class="flex justify-between items-center mt-3">
                            <span class="text-xl font-bold text-blue-600">R199.99</span>
                            <button class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700 transition"
                                    onclick="if(!<?= $isLoggedIn ? 'true' : 'false' ?>) { showLoginPrompt(event); } else { alert('Product added to cart!'); }">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product Card 3 -->
                <div class="product-card bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
                    <img src="https://placehold.co/400x300/F59E0B/white?text=Jacket" alt="Product"
                         class="w-full h-48 object-cover">
                    <div class="p-4">
                        <div class="text-xs text-gray-500 mb-1">Clothing</div>
                        <h3 class="font-semibold text-lg text-gray-800">Denim Jacket</h3>
                        <p class="text-gray-500 text-sm mt-1">by FashionHub</p>
                        <div class="flex items-center mt-2">
                            <span class="text-yellow-400">★★★★☆</span>
                            <span class="text-xs text-gray-500 ml-2">(56)</span>
                        </div>
                        <div class="flex justify-between items-center mt-3">
                            <span class="text-xl font-bold text-blue-600">R89.99</span>
                            <button class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700 transition"
                                    onclick="if(!<?= $isLoggedIn ? 'true' : 'false' ?>) { showLoginPrompt(event); } else { alert('Product added to cart!'); }">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product Card 4 -->
                <div class="product-card bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
                    <img src="https://placehold.co/400x300/EF4444/white?text=Lamp" alt="Product"
                         class="w-full h-48 object-cover">
                    <div class="p-4">
                        <div class="text-xs text-gray-500 mb-1">Home & Garden</div>
                        <h3 class="font-semibold text-lg text-gray-800">Smart LED Lamp</h3>
                        <p class="text-gray-500 text-sm mt-1">by HomeStyle</p>
                        <div class="flex items-center mt-2">
                            <span class="text-yellow-400">★★★☆☆</span>
                            <span class="text-xs text-gray-500 ml-2">(18)</span>
                        </div>
                        <div class="flex justify-between items-center mt-3">
                            <span class="text-xl font-bold text-blue-600">R34.99</span>
                            <button class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700 transition"
                                    onclick="if(!<?= $isLoggedIn ? 'true' : 'false' ?>) { showLoginPrompt(event); } else { alert('Product added to cart!'); }">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Section -->
        <div class="bg-white py-12">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 text-center mb-8">
                    Shop by Category
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 rounded-xl p-6 text-center hover:bg-blue-100 transition cursor-pointer"
                         onclick="if(!<?= $isLoggedIn ? 'true' : 'false' ?>) { showLoginPrompt(event); } else { alert('Showing Electronics...'); }">
                        <div class="text-4xl mb-2">📱</div>
                        <h3 class="font-semibold">Electronics</h3>
                        <p class="text-sm text-gray-500">48 products</p>
                    </div>
                    <div class="bg-green-50 rounded-xl p-6 text-center hover:bg-green-100 transition cursor-pointer"
                         onclick="if(!<?= $isLoggedIn ? 'true' : 'false' ?>) { showLoginPrompt(event); } else { alert('Showing Clothing...'); }">
                        <div class="text-4xl mb-2">👕</div>
                        <h3 class="font-semibold">Clothing</h3>
                        <p class="text-sm text-gray-500">126 products</p>
                    </div>
                    <div class="bg-yellow-50 rounded-xl p-6 text-center hover:bg-yellow-100 transition cursor-pointer"
                         onclick="if(!<?= $isLoggedIn ? 'true' : 'false' ?>) { showLoginPrompt(event); } else { alert('Showing Home & Garden...'); }">
                        <div class="text-4xl mb-2">🏠</div>
                        <h3 class="font-semibold">Home & Garden</h3>
                        <p class="text-sm text-gray-500">67 products</p>
                    </div>
                    <div class="bg-purple-50 rounded-xl p-6 text-center hover:bg-purple-100 transition cursor-pointer"
                         onclick="if(!<?= $isLoggedIn ? 'true' : 'false' ?>) { showLoginPrompt(event); } else { alert('Showing Accessories...'); }">
                        <div class="text-4xl mb-2">⌚</div>
                        <h3 class="font-semibold">Accessories</h3>
                        <p class="text-sm text-gray-500">34 products</p>
                    </div>
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

        console.log('Bona Markets homepage loaded – Login/Logout states active!');
    </script>

</body>
</html>
