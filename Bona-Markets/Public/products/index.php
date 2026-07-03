<?php
// ============================================================
// PRODUCTS PAGE – Bona Markets Catalogue
// ============================================================

session_start();

// Get user info from session
$isLoggedIn = isset($_SESSION['user_id']);
$userFullName = $isLoggedIn ? $_SESSION['fullname'] : '';
$userEmail = $isLoggedIn ? $_SESSION['email'] : '';
$userRole = $isLoggedIn ? $_SESSION['role'] : '';

// ─── DATABASE CONNECTION ─────────────────────────────────────
require_once '../../config/database.php';

// ─── GET FILTERS FROM URL ────────────────────────────────────
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? '';
$vendorId = $_GET['vendor'] ?? '';

// ─── BUILD PRODUCT QUERY ─────────────────────────────────────
$sql = "
    SELECT
        p.*,
        c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.status = 'active'
";

$params = [];

if (!empty($search)) {
    $sql .= " AND p.name LIKE ?";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category;
}

if (!empty($vendorId)) {
    $sql .= " AND p.vendor_id = ?";
    $params[] = (int) $vendorId;
}

switch ($sort) {
    case 'price_low':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY p.price DESC";
        break;
    default:
        $sql .= " ORDER BY p.created_at DESC";
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ─── GET CATEGORIES FOR FILTER ──────────────────────────────
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <title>Shop | Bona Markets</title>

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
        .product-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: #f3f4f6;
        }
        .category-badge {
            display: inline-block;
            background: #dbeafe;
            color: #1e40af;
            padding: 0.2rem 0.7rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6b7280;
        }
        .empty-state .icon {
            font-size: 3rem;
            margin-bottom: .75rem;
        }
        .empty-state p {
            font-size: .9rem;
            max-width: 280px;
            margin: 0 auto .75rem;
        }
        input:focus, select:focus {
            outline: none;
            ring: 2px solid #3B82F6;
        }
        /* Login prompt modal */
        .login-prompt-overlay {
            display: none; position: fixed; inset: 0; z-index: 9999;
            background: rgba(0,0,0,.55); align-items: center; justify-content: center;
        }
        .login-prompt-overlay.active { display: flex; }
        .login-prompt {
            background: white; border-radius: 16px; padding: 2.5rem 2rem;
            max-width: 360px; width: 90%; text-align: center; box-shadow: 0 25px 50px rgba(0,0,0,.2);
        }
        .login-prompt .icon { font-size: 2.5rem; margin-bottom: .75rem; }
        .login-prompt h3 { font-size: 1.2rem; font-weight: 700; margin-bottom: .5rem; }
        .login-prompt p { color: #6b7280; font-size: .9rem; margin-bottom: 1.25rem; }
        .login-prompt .btn-group { display: flex; gap: .75rem; justify-content: center; margin-bottom: .75rem; }
        .login-prompt .btn-group a {
            flex: 1; padding: .6rem 1rem; border-radius: 8px; font-weight: 600;
            font-size: .875rem; text-decoration: none; transition: all .15s;
        }
        .login-prompt .btn-login { background: #2563eb; color: white; }
        .login-prompt .btn-login:hover { background: #1d4ed8; }
        .login-prompt .btn-signup { background: #f3f4f6; color: #1f2937; }
        .login-prompt .btn-signup:hover { background: #e5e7eb; }
        .login-prompt .btn-close {
            background: none; border: none; color: #9ca3af; font-size: .8rem;
            cursor: pointer; text-decoration: underline;
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Login Prompt Modal -->
    <div class="login-prompt-overlay" id="loginPrompt">
        <div class="login-prompt">
            <div class="icon">🔐</div>
            <h3>Login Required</h3>
            <p>Please log in or create an account to add items to your cart.</p>
            <div class="btn-group">
                <a href="../login.php" class="btn-login">Login</a>
                <a href="../register.php" class="btn-signup">Sign Up</a>
            </div>
            <button class="btn-close" onclick="closeLoginPrompt()">Cancel</button>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- NAVBAR (Same as main site) -->
    <!-- ============================================================ -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <a href="../index.php" class="text-2xl md:text-3xl font-bold text-blue-600 tracking-tight">
                    Bona Markets
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="../index.php" class="text-gray-600 hover:text-blue-600 font-medium">Shop</a>
                    <a href="index.php" class="text-blue-600 font-semibold">Categories</a>
                    <a href="../about.php" class="text-gray-600 hover:text-blue-600 font-medium">About</a>
                    <a href="../contact.php" class="text-gray-600 hover:text-blue-600 font-medium">Contact</a>
                </div>

                <!-- Desktop Auth Buttons (Dynamic) -->
                <div class="hidden md:flex items-center space-x-4">
                    <?php if ($isLoggedIn): ?>
                        <?php if ($userRole === 'vendor'): ?>
                            <a href="../vendor/dashboard.php" class="text-gray-600 hover:text-blue-600 font-medium">Vendor Dashboard</a>
                        <?php endif; ?>
                        <?php if ($userRole === 'admin'): ?>
                            <a href="../admin/dashboard.php" class="text-gray-600 hover:text-blue-600 font-medium">Admin Panel</a>
                        <?php endif; ?>
                        <a href="../cart/index.php" class="text-gray-600 hover:text-blue-600 font-medium">Cart 🛒</a>
                        <a href="../orders/index.php" class="text-gray-600 hover:text-blue-600 font-medium">Orders</a>
                        <span class="text-gray-600">👋 <?= htmlspecialchars($userFullName ?: $userEmail) ?></span>
                        <a href="../logout.php" class="text-red-500 hover:text-red-700 font-medium">Logout</a>
                    <?php else: ?>
                        <a href="../login.php" class="text-gray-600 hover:text-blue-600 font-medium">Login</a>
                        <a href="../register.php" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition">
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
                <a href="../index.php" class="text-gray-600 hover:text-blue-600 py-1">Shop</a>
                <a href="index.php" class="text-blue-600 font-semibold py-1">Categories</a>
                <a href="../about.php" class="text-gray-600 hover:text-blue-600 py-1">About</a>
                <a href="../contact.php" class="text-gray-600 hover:text-blue-600 py-1">Contact</a>
                <div class="flex flex-col space-y-2 pt-2">
                    <?php if ($isLoggedIn): ?>
                        <?php if ($userRole === 'vendor'): ?>
                            <a href="../vendor/dashboard.php" class="text-gray-600 py-1">Vendor Dashboard</a>
                        <?php endif; ?>
                        <?php if ($userRole === 'admin'): ?>
                            <a href="../admin/dashboard.php" class="text-gray-600 py-1">Admin Panel</a>
                        <?php endif; ?>
                        <a href="../cart/index.php" class="text-gray-600 py-1">Cart 🛒</a>
                        <a href="../orders/index.php" class="text-gray-600 py-1">Orders</a>
                        <span class="text-gray-600 py-1">👋 <?= htmlspecialchars($userFullName ?: $userEmail) ?></span>
                        <a href="../logout.php" class="text-red-500 py-1">Logout</a>
                    <?php else: ?>
                        <a href="../login.php" class="text-gray-600 hover:text-blue-600 py-1">Login</a>
                        <a href="../register.php" class="bg-blue-600 text-white text-center px-4 py-2 rounded-lg">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- ============================================================ -->
    <!-- HERO BANNER (Smaller for product page) -->
    <!-- ============================================================ -->
    <section class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
        <div class="container mx-auto px-4 py-8 md:py-12 text-center">
            <h1 class="text-2xl md:text-4xl font-bold mb-2">
                Browse Products
            </h1>
            <p class="text-md md:text-lg opacity-90 max-w-2xl mx-auto">
                Discover unique products from trusted vendors around the world
            </p>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- MAIN CONTENT -->
    <!-- ============================================================ -->
    <div class="container mx-auto px-4 py-8 -mt-4">

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6 mb-8">
            <form method="GET" class="flex flex-col md:flex-row gap-3">
                <input type="text" name="search" placeholder="Search products..."
                       value="<?= htmlspecialchars($search) ?>"
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                
                <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($category == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <select name="sort" class="px-4 py-2 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Newest First</option>
                    <option value="price_low" <?= ($sort == 'price_low') ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_high" <?= ($sort == 'price_high') ? 'selected' : '' ?>>Price: High to Low</option>
                </select>
                
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Search
                </button>
                
                <?php if (!empty($search) || !empty($category) || !empty($sort)): ?>
                    <a href="index.php" class="text-gray-500 hover:text-gray-700 px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition text-center">
                        Clear Filters
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Results Count -->
        <div class="flex justify-between items-center mb-4">
            <p class="text-gray-500 text-sm">
                <?= count($products) ?> product<?= count($products) !== 1 ? 's' : '' ?> found
            </p>
        </div>

        <!-- Product Grid -->
        <?php if (count($products) > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($products as $product): ?>
                    <div class="product-card bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
                        <!-- Product Image -->
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="/Bona-Markets/Public/<?= htmlspecialchars($product['image_url']) ?>" 
                                 alt="<?= htmlspecialchars($product['name']) ?>" 
                                 class="product-image">
                        <?php else: ?>
                            <div class="product-image flex items-center justify-center bg-gray-100 text-4xl text-gray-300">
                                🛍️
                            </div>
                        <?php endif; ?>
                        
                        <div class="p-4">
                            <!-- Category Badge -->
                            <?php if (!empty($product['category_name'])): ?>
                                <span class="category-badge">
                                    <?= htmlspecialchars($product['category_name']) ?>
                                </span>
                            <?php endif; ?>
                            
                            <!-- Product Name -->
                            <h3 class="font-semibold text-lg text-gray-800 mt-2">
                                <?= htmlspecialchars($product['name']) ?>
                            </h3>
                            
                            <!-- Stock Info -->
                            <p class="text-sm text-gray-500 mt-1">
                                <?php if ($product['stock'] > 0): ?>
                                    <?= $product['stock'] ?> in stock
                                <?php else: ?>
                                    <span class="text-red-500 font-medium">Out of Stock</span>
                                <?php endif; ?>
                            </p>
                            
                            <!-- Price -->
                            <p class="text-xl font-bold text-blue-600 mt-2">
                                R <?= number_format($product['price'], 2) ?>
                            </p>
                            
                            <!-- Actions -->
                            <div class="flex gap-2 mt-4">
                                <a href="show.php?id=<?= $product['id'] ?>" 
                                   class="flex-1 text-center bg-blue-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                                    View
                                </a>
                                <?php if ($product['stock'] > 0): ?>
                                    <button onclick="if(!<?= $isLoggedIn ? 'true' : 'false' ?>) { showLoginPrompt(event); } else { addToCart(<?= $product['id'] ?>, this); }"
                                            class="flex-1 bg-green-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition">
                                        Add to Cart
                                    </button>
                                <?php else: ?>
                                    <button class="flex-1 bg-gray-300 text-gray-500 py-2 rounded-lg text-sm font-medium cursor-not-allowed" disabled>
                                        Out of Stock
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                <div class="text-5xl mb-4">🔍</div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">No Products Found</h2>
                <p class="text-gray-500 max-w-md mx-auto">
                    We couldn't find any products matching your search criteria. Try adjusting your filters or browse all products.
                </p>
                <a href="index.php" class="inline-block mt-6 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    View All Products
                </a>
            </div>
        <?php endif; ?>

    </div>

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
                        <li><a href="../index.php" class="hover:text-white">Home</a></li>
                        <li><a href="index.php" class="hover:text-white">Shop</a></li>
                        <li><a href="../about.php" class="hover:text-white">About Us</a></li>
                        <li><a href="../contact.php" class="hover:text-white">Contact Us</a></li>
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
                <p>&copy; <?= date('Y') ?> Bona Markets. All rights reserved.</p>
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
            menuBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
        }

        // ===== Login Prompt =====
        function showLoginPrompt(event) {
            if (event) event.preventDefault();
            document.getElementById('loginPrompt').classList.add('active');
        }
        function closeLoginPrompt() {
            document.getElementById('loginPrompt').classList.remove('active');
        }
        document.getElementById('loginPrompt').addEventListener('click', function(e) {
            if (e.target === this) closeLoginPrompt();
        });

        // ===== Add to Cart =====
        function addToCart(productId, btn) {
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Adding…';
            }

            fetch('../cart/add.php', {
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

        // Slide-up animation for toast
        const style = document.createElement('style');
        style.textContent = '@keyframes slideUp { from { transform: translateY(20px); opacity:0; } to { transform: translateY(0); opacity:1; } }';
        document.head.appendChild(style);
    </script>

</body>
</html>