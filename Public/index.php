<?php
// Start session to check login status and show logout messages
session_start();

// Check if logout success message should be shown
$logoutMessage = '';
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $logoutMessage = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>You have been successfully logged out.</span>
                        </div>
                      </div>';
}

// Check if login success message should be shown (from future login.php)
$loginMessage = '';
if (isset($_GET['login']) && $_GET['login'] === 'success') {
    $loginMessage = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Welcome back! You are now logged in.</span>
                        </div>
                      </div>';
}

// Check if registration success message should be shown
$registerMessage = '';
if (isset($_GET['registered']) && $_GET['registered'] === 'success') {
    $registerMessage = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Account created successfully! Please log in.</span>
                            </div>
                          </div>';
}

// Get user info from session (if logged in)
$isLoggedIn = isset($_SESSION['user_id']);
$userEmail = $isLoggedIn ? $_SESSION['email'] : '';
$userRole = $isLoggedIn ? $_SESSION['role'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Bona Markets | Multi-Vendor Marketplace</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            opacity: 0.5;
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- ========== NAVBAR ========== -->
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
                    <a href="#" class="text-gray-600 hover:text-blue-600 font-medium">About</a>
                    <a href="#" class="text-gray-600 hover:text-blue-600 font-medium">Contact</a>
                </div>

                <!-- Desktop Auth Buttons (dynamic based on login status) -->
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
                        <span class="text-gray-600">👋 <?= htmlspecialchars($userEmail) ?></span>
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
                <a href="#" class="text-gray-600 hover:text-blue-600 py-1">Contact</a>
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
                        <span class="text-gray-600 py-1">👋 <?= htmlspecialchars($userEmail) ?></span>
                        <a href="logout.php" class="text-red-500 py-1">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-600 hover:text-blue-600 py-1">Login</a>
                        <a href="register.php" class="bg-blue-600 text-white text-center px-4 py-2 rounded-lg">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- ========== MAIN CONTENT ========== -->
    <main>

        <!-- Success/Error Messages -->
        <div class="container mx-auto px-4 pt-6">
            <?php 
            echo $logoutMessage;
            echo $loginMessage;
            echo $registerMessage;
            ?>
        </div>

        <!-- Hero Banner -->
        <section class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
            <div class="container mx-auto px-4 py-12 md:py-16 text-center">
                <h1 class="text-3xl md:text-5xl font-bold mb-4">
                    Welcome to Bona Markets
                </h1>
                <p class="text-lg md:text-xl opacity-90 max-w-2xl mx-auto">
                    Discover unique products from trusted vendors around the world
                </p>
                <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                        Start Shopping
                    </a>
                    <a href="vendor/apply.php" class="border border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">
                        Become a Vendor
                    </a>
                </div>
            </div>
        </section>

        <!-- Search & Filter Bar (Week 3 will make it work) -->
        <div class="container mx-auto px-4 -mt-6">
            <div class="bg-white rounded-lg shadow-md p-4 flex flex-col md:flex-row gap-3">
                <input type="text" placeholder="Search products..." 
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <select class="px-4 py-2 border border-gray-300 rounded-lg bg-white">
                    <option>All Categories</option>
                    <option>Electronics</option>
                    <option>Clothing</option>
                    <option>Home & Garden</option>
                    <option>Accessories</option>
                </select>
                <select class="px-4 py-2 border border-gray-300 rounded-lg bg-white">
                    <option>Newest First</option>
                    <option>Price: Low to High</option>
                    <option>Price: High to Low</option>
                </select>
                <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Search
                </button>
            </div>
        </div>

        <!-- Welcome Message for Logged-in Users -->
        <?php if ($isLoggedIn): ?>
        <div class="container mx-auto px-4 py-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-blue-700">
                    Welcome back, <strong><?= htmlspecialchars($userEmail) ?></strong>! 
                    <?php if ($userRole === 'vendor'): ?>
                        <a href="vendor/dashboard.php" class="text-blue-600 hover:underline ml-2">Go to your vendor dashboard →</a>
                    <?php elseif ($userRole === 'admin'): ?>
                        <a href="admin/dashboard.php" class="text-blue-600 hover:underline ml-2">Go to admin panel →</a>
                    <?php else: ?>
                        <a href="#" class="text-blue-600 hover:underline ml-2">Start shopping →</a>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Featured Products Section -->
        <div class="container mx-auto px-4 py-12">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Featured Products</h2>
                <a href="#" class="text-blue-600 hover:underline">View All →</a>
            </div>

            <!-- Product Grid (Responsive: 1 → 2 → 3 → 4 columns) -->
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
                            <button class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700 transition">
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
                            <button class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700 transition">
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
                            <button class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700 transition">
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
                            <button class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700 transition">
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
                    <div class="bg-blue-50 rounded-xl p-6 text-center hover:bg-blue-100 transition cursor-pointer">
                        <div class="text-4xl mb-2">📱</div>
                        <h3 class="font-semibold">Electronics</h3>
                        <p class="text-sm text-gray-500">48 products</p>
                    </div>
                    <div class="bg-green-50 rounded-xl p-6 text-center hover:bg-green-100 transition cursor-pointer">
                        <div class="text-4xl mb-2">👕</div>
                        <h3 class="font-semibold">Clothing</h3>
                        <p class="text-sm text-gray-500">126 products</p>
                    </div>
                    <div class="bg-yellow-50 rounded-xl p-6 text-center hover:bg-yellow-100 transition cursor-pointer">
                        <div class="text-4xl mb-2">🏠</div>
                        <h3 class="font-semibold">Home & Garden</h3>
                        <p class="text-sm text-gray-500">67 products</p>
                    </div>
                    <div class="bg-purple-50 rounded-xl p-6 text-center hover:bg-purple-100 transition cursor-pointer">
                        <div class="text-4xl mb-2">⌚</div>
                        <h3 class="font-semibold">Accessories</h3>
                        <p class="text-sm text-gray-500">34 products</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action: Become a Vendor -->
        <div class="container mx-auto px-4 py-12">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl text-white p-8 md:p-12 text-center">
                <h2 class="text-2xl md:text-3xl font-bold mb-3">Sell on Bona Markets</h2>
                <p class="text-lg opacity-90 max-w-2xl mx-auto mb-6">
                    Join thousands of vendors who grow their business with us
                </p>
                <a href="vendor/apply.php" class="inline-block bg-white text-purple-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                    Apply as Vendor →
                </a>
            </div>
        </div>
    </main>

    <!-- ========== FOOTER ========== -->
    <footer class="bg-gray-800 text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-3">Bona Markets</h3>
                    <p class="text-gray-400 text-sm">Your trusted multi-vendor marketplace</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-3">Shop</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="index.php" class="hover:text-white">All Products</a></li>
                        <li><a href="#" class="hover:text-white">Categories</a></li>
                        <li><a href="#" class="hover:text-white">Best Sellers</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-3">Sell</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="vendor/apply.php" class="hover:text-white">Become a Vendor</a></li>
                        <li><a href="#" class="hover:text-white">Vendor Dashboard</a></li>
                        <li><a href="#" class="hover:text-white">Pricing</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-3">Support</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-white">Help Center</a></li>
                        <li><a href="#" class="hover:text-white">Contact Us</a></li>
                        <li><a href="#" class="hover:text-white">Returns Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400 text-sm">
                <p>&copy; 2025 Bona Markets. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- ========== JAVASCRIPT ========== -->
    <script>
        // Mobile Menu Toggle
        const menuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        if (menuBtn && mobileMenu) {
            menuBtn.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Simple Add to Cart simulation (Week 1 demo)
        function addToCart(productId) {
            alert('Demo: Product ' + productId + ' added to cart. (Real cart coming in Week 4)');
            console.log('Add to cart clicked for product:', productId);
        }

        // Attach add to cart handlers to all buttons
        document.querySelectorAll('.product-card button').forEach((btn, index) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                addToCart(index + 1);
            });
        });

        console.log('Bona Markets homepage loaded – Week 1 ready!');
    </script>
</body>
</html>