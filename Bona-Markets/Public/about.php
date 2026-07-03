<?php
// ============================================================
// ABOUT PAGE – Bona Markets Mission & Vision
// ============================================================

session_start();

// Get user info from session
$isLoggedIn = isset($_SESSION['user_id']);
$userFullName = $isLoggedIn ? $_SESSION['fullname'] : '';
$userEmail = $isLoggedIn ? $_SESSION['email'] : '';
$userRole = $isLoggedIn ? $_SESSION['role'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <title>About Us | Bona Markets</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .stat-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }

        .value-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .value-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }

        .team-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .team-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }

        .role-tag {
            font-size: 0.65rem;
            background: #e5e7eb;
            color: #4b5563;
            padding: 0.15rem 0.6rem;
            border-radius: 9999px;
            display: inline-block;
            margin-top: 0.25rem;
        }
    </style>
</head>

<body class="bg-gray-50">

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
                    <a href="about.php" class="text-blue-600 font-semibold border-b-2 border-blue-600 pb-0.5">About</a>
                    <a href="contact.php" class="text-gray-600 hover:text-blue-600 font-medium">Contact</a>
                </div>

                <!-- Desktop Auth Buttons -->
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
                <a href="about.php" class="text-blue-600 font-semibold py-1">About</a>
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
    <!-- HERO BANNER -->
    <!-- ============================================================ -->
    <section class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
        <div class="container mx-auto px-4 py-16 md:py-20 text-center">
            <h1 class="text-3xl md:text-5xl font-bold mb-4">
                About Bona Markets
            </h1>
            <p class="text-lg md:text-xl opacity-90 max-w-3xl mx-auto">
                Empowering African artisans and entrepreneurs to share their unique products with the world.
            </p>
            <?php if ($isLoggedIn): ?>
                <p class="mt-4 text-sm text-blue-100">
                    👋 Welcome, <strong><?= htmlspecialchars($userFullName ?: $userEmail) ?></strong>! Thank you for being part of our community.
                </p>
            <?php endif; ?>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- MISSION & VISION -->
    <!-- ============================================================ -->
    <div class="container mx-auto px-4 py-12 -mt-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

            <!-- Mission -->
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-xl transition">
                <div class="text-5xl mb-4">🎯</div>
                <h2 class="text-2xl font-bold text-gray-800 mb-3">Our Mission</h2>
                <p class="text-gray-600 leading-relaxed">
                    To create a vibrant, trusted marketplace that connects African makers, artisans, and small businesses with conscious consumers worldwide — celebrating the richness of African culture, creativity, and craftsmanship.
                </p>
            </div>

            <!-- Vision -->
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center hover:shadow-xl transition">
                <div class="text-5xl mb-4">🌍</div>
                <h2 class="text-2xl font-bold text-gray-800 mb-3">Our Vision</h2>
                <p class="text-gray-600 leading-relaxed">
                    To become the leading online destination for authentic African products — a platform where every purchase tells a story, supports a family, and uplifts communities across the continent.
                </p>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- STORY SECTION -->
    <!-- ============================================================ -->
    <section class="bg-white py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <div class="text-5xl mb-4">📖</div>
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Our Story</h2>
                <p class="text-gray-600 leading-relaxed mb-4">
                    Bona Markets was born from a simple idea: <strong class="text-gray-800">every product has a story, and every story deserves to be told.</strong>
                </p>
                <p class="text-gray-600 leading-relaxed mb-4">
                    Founded in 2025, our platform was created to bridge the gap between African artisans and the global market. We believe that the continent's rich heritage of craftsmanship, textiles, art, and culinary traditions should be accessible to everyone — while ensuring that the creators are fairly compensated.
                </p>
                <p class="text-gray-600 leading-relaxed">
                    Today, Bona Markets is home to hundreds of vendors from across Africa, offering thousands of unique, handcrafted products. We're proud to be part of a movement that celebrates African excellence, one product at a time.
                </p>
            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- STATS / IMPACT SECTION -->
    <!-- ============================================================ -->
    <section class="bg-gray-100 py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-4">Our Impact So Far</h2>
            <p class="text-gray-500 text-center mb-10 max-w-2xl mx-auto">
                Together with our community of vendors and buyers, we're making a difference.
            </p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

                <div class="bg-white rounded-2xl shadow-md p-6 text-center stat-card">
                    <div class="text-3xl font-bold text-blue-600">150+</div>
                    <p class="text-gray-500 text-sm mt-1">Vendors</p>
                </div>

                <div class="bg-white rounded-2xl shadow-md p-6 text-center stat-card">
                    <div class="text-3xl font-bold text-blue-600">2,000+</div>
                    <p class="text-gray-500 text-sm mt-1">Products</p>
                </div>

                <div class="bg-white rounded-2xl shadow-md p-6 text-center stat-card">
                    <div class="text-3xl font-bold text-blue-600">5,000+</div>
                    <p class="text-gray-500 text-sm mt-1">Happy Customers</p>
                </div>

                <div class="bg-white rounded-2xl shadow-md p-6 text-center stat-card">
                    <div class="text-3xl font-bold text-blue-600">12</div>
                    <p class="text-gray-500 text-sm mt-1">African Countries</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- VALUES SECTION -->
    <!-- ============================================================ -->
    <section class="bg-white py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-4">Our Values</h2>
            <p class="text-gray-500 text-center mb-10 max-w-2xl mx-auto">
                These principles guide everything we do at Bona Markets.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Value 1 -->
                <div class="bg-gray-50 rounded-2xl p-6 text-center value-card border border-gray-100">
                    <div class="text-4xl mb-3">🤝</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Community</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        We believe in building a supportive community where vendors, buyers, and partners grow together.
                    </p>
                </div>

                <!-- Value 2 -->
                <div class="bg-gray-50 rounded-2xl p-6 text-center value-card border border-gray-100">
                    <div class="text-4xl mb-3">💎</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Authenticity</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Every product on our platform is genuine, reflecting the true heritage and craftsmanship of its origin.
                    </p>
                </div>

                <!-- Value 3 -->
                <div class="bg-gray-50 rounded-2xl p-6 text-center value-card border border-gray-100">
                    <div class="text-4xl mb-3">🌱</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Sustainability</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        We promote sustainable practices, fair trade, and ethical sourcing to protect our planet and people.
                    </p>
                </div>

                <!-- Value 4 -->
                <div class="bg-gray-50 rounded-2xl p-6 text-center value-card border border-gray-100">
                    <div class="text-4xl mb-3">🚀</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Innovation</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        We embrace technology to create seamless shopping experiences and empower African entrepreneurs.
                    </p>
                </div>

                <!-- Value 5 -->
                <div class="bg-gray-50 rounded-2xl p-6 text-center value-card border border-gray-100">
                    <div class="text-4xl mb-3">❤️</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Integrity</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Trust is at the heart of everything we do — from product quality to customer service.
                    </p>
                </div>

                <!-- Value 6 -->
                <div class="bg-gray-50 rounded-2xl p-6 text-center value-card border border-gray-100">
                    <div class="text-4xl mb-3">🌟</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Excellence</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        We strive for excellence in every product, every interaction, and every experience.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- TEAM SECTION – WITH YOUR ACTUAL TEAM -->
    <!-- ============================================================ -->
    <section class="bg-gray-100 py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-4">Meet the Team</h2>
            <p class="text-gray-500 text-center mb-10 max-w-2xl mx-auto">
                The passionate people behind Bona Markets.
            </p>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">

                <!-- Lesiamo – Project Manager + Frontend UI -->
                <div class="bg-white rounded-2xl shadow-md p-6 text-center team-card">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center text-3xl mx-auto mb-3">👨</div>
                    <h4 class="font-bold text-gray-800">Lesiamo</h4>
                    <p class="text-sm text-gray-500">Project Manager</p>
                    <span class="role-tag">Frontend UI</span>
                </div>

                <!-- Gabrielle – Database -->
                <div class="bg-white rounded-2xl shadow-md p-6 text-center team-card">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center text-3xl mx-auto mb-3">👩</div>
                    <h4 class="font-bold text-gray-800">Gabrielle</h4>
                    <p class="text-sm text-gray-500">Database</p>
                    <span class="role-tag">Backend</span>
                </div>

                <!-- Timothy – Authentication -->
                <div class="bg-white rounded-2xl shadow-md p-6 text-center team-card">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center text-3xl mx-auto mb-3">👨</div>
                    <h4 class="font-bold text-gray-800">Timothy</h4>
                    <p class="text-sm text-gray-500">Authentication</p>
                    <span class="role-tag">Security</span>
                </div>

                <!-- Karabelo – Vendor Module -->
                <div class="bg-white rounded-2xl shadow-md p-6 text-center team-card">
                    <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center text-3xl mx-auto mb-3">👨</div>
                    <h4 class="font-bold text-gray-800">Karabelo</h4>
                    <p class="text-sm text-gray-500">Vendor Module</p>
                    <span class="role-tag">Backend</span>
                </div>

                <!-- Amogelang – Product Management -->
                <div class="bg-white rounded-2xl shadow-md p-6 text-center team-card">
                    <div class="w-20 h-20 bg-pink-100 rounded-full flex items-center justify-center text-3xl mx-auto mb-3">👩</div>
                    <h4 class="font-bold text-gray-800">Amogelang</h4>
                    <p class="text-sm text-gray-500">Product Management</p>
                    <span class="role-tag">Full Stack</span>
                </div>

                <!-- Bianca – Buyer Marketplace -->
                <div class="bg-white rounded-2xl shadow-md p-6 text-center team-card">
                    <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center text-3xl mx-auto mb-3">👩</div>
                    <h4 class="font-bold text-gray-800">Bianca</h4>
                    <p class="text-sm text-gray-500">Buyer Marketplace</p>
                    <span class="role-tag">Frontend</span>
                </div>

                <!-- Amanda – Shopping Cart -->
                <div class="bg-white rounded-2xl shadow-md p-6 text-center team-card">
                    <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center text-3xl mx-auto mb-3">👩</div>
                    <h4 class="font-bold text-gray-800">Amanda</h4>
                    <p class="text-sm text-gray-500">Shopping Cart</p>
                    <span class="role-tag">Full Stack</span>
                </div>

                <!-- Molemo – Admin Dashboard -->
                <div class="bg-white rounded-2xl shadow-md p-6 text-center team-card">
                    <div class="w-20 h-20 bg-cyan-100 rounded-full flex items-center justify-center text-3xl mx-auto mb-3">👨</div>
                    <h4 class="font-bold text-gray-800">Molemo</h4>
                    <p class="text-sm text-gray-500">Admin Dashboard</p>
                    <span class="role-tag">Backend</span>
                </div>

                <!-- Dan – Testing -->
                <div class="bg-white rounded-2xl shadow-md p-6 text-center team-card">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center text-3xl mx-auto mb-3">👨</div>
                    <h4 class="font-bold text-gray-800">Dan</h4>
                    <p class="text-sm text-gray-500">Testing</p>
                    <span class="role-tag">QA</span>
                </div>

                <!-- Omphile – Documentation -->
                <div class="bg-white rounded-2xl shadow-md p-6 text-center team-card">
                    <div class="w-20 h-20 bg-teal-100 rounded-full flex items-center justify-center text-3xl mx-auto mb-3">👨</div>
                    <h4 class="font-bold text-gray-800">Omphile</h4>
                    <p class="text-sm text-gray-500">Documentation</p>
                    <span class="role-tag">Content</span>
                </div>

            </div>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- CALL TO ACTION – Only show for non-logged-in users -->
    <!-- ============================================================ -->
    <?php if (!$isLoggedIn): ?>
        <section class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-12">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-2xl md:text-3xl font-bold mb-4">Join the Bona Markets Community</h2>
                <p class="text-lg opacity-90 max-w-2xl mx-auto mb-6">
                    Whether you're a buyer looking for authentic African products or a vendor ready to share your craft — we welcome you.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="register.php" class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                        Sign Up Today
                    </a>
                    <a href="contact.php" class="border border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">
                        Contact Us
                    </a>
                </div>
            </div>
        </section>
    <?php else: ?>
        <!-- Optional: Show something else for logged-in users (or nothing at all) -->
        <section class="bg-gray-100 py-8">
            <div class="container mx-auto px-4 text-center">
                <p class="text-gray-600">
                    👋 Welcome back, <strong><?= htmlspecialchars($userFullName ?: $userEmail) ?></strong>!
                    <span class="block text-sm text-gray-500 mt-1">
                        Thank you for being part of the Bona Markets community.
                    </span>
                </p>
            </div>
        </section>
    <?php endif; ?>

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
                        <li><a href="about.php" class="hover:text-white">About Us</a></li>
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

        console.log('Bona Markets About page loaded!');
    </script>

</body>

</html>