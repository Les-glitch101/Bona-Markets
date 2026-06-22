<?php
// ============================================================
// CONTACT PAGE – With Session Integration
// ============================================================

// Start session to check login status
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
    <title>Contact Us | Bona Markets</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        input:focus, textarea:focus, select:focus {
            outline: none;
            ring: 2px solid #3B82F6;
        }
        details summary {
            cursor: pointer;
            list-style: none;
        }
        details summary::-webkit-details-marker {
            display: none;
        }
        details summary::after {
            content: '▼';
            float: right;
            font-size: 0.75rem;
            color: #6B7280;
            transition: transform 0.2s ease;
        }
        details[open] summary::after {
            transform: rotate(180deg);
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- ============================================================ -->
    <!-- NAVBAR (Dynamic based on login status) -->
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
                    <a href="contact.php" class="text-blue-600 font-semibold border-b-2 border-blue-600 pb-0.5">Contact</a>
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
                <a href="contact.php" class="text-blue-600 font-semibold py-1">Contact</a>
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
                Get in Touch
            </h1>
            <p class="text-lg md:text-xl opacity-90 max-w-2xl mx-auto">
                Have questions about Bona Markets? We'd love to hear from you.
                <br class="hidden sm:block" />
                Our team is here to help.
            </p>
            <?php if ($isLoggedIn): ?>
                <p class="mt-4 text-sm text-blue-100">
                    👋 Welcome, <strong><?= htmlspecialchars($userFullName ?: $userEmail) ?></strong>! We're here to help.
                </p>
            <?php endif; ?>
        </div>
    </section>

    <!-- ============================================================ -->
    <!-- CONTACT FORM + INFO -->
    <!-- ============================================================ -->
    <div class="container mx-auto px-4 py-12 -mt-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- LEFT: Contact Form (2/3 width) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Send Us a Message</h2>
                    <p class="text-gray-500 text-sm mb-6">
                        We'll get back to you within 24–48 hours.
                    </p>

                    <!-- Demo Alert -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-6">
                        <p class="text-blue-700 text-xs text-center">
                            <?php if ($isLoggedIn): ?>
                                📬 <span class="font-medium">Welcome back!</span> Your name and email are pre-filled.
                            <?php else: ?>
                                📬 <span class="font-medium">Demo Mode:</span> This form is for demonstration purposes.
                                <br class="sm:hidden" />
                                <span class="hidden sm:inline">Submissions will show a success message.</span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <form id="contactForm" onsubmit="handleContactForm(event)">
                        <!-- Name + Email Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="contactName" class="block text-gray-700 font-medium mb-2">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="contactName" name="name"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Your name"
                                       value="<?= htmlspecialchars($userFullName) ?>"
                                       <?= $isLoggedIn ? 'readonly style="background:#f3f4f6;"' : '' ?>
                                       required />
                                <?php if ($isLoggedIn): ?>
                                    <p class="text-xs text-gray-400 mt-1">Prefilled from your account</p>
                                <?php endif; ?>
                            </div>
                            <div>
                                <label for="contactEmail" class="block text-gray-700 font-medium mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="contactEmail" name="email"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="you@example.com"
                                       value="<?= htmlspecialchars($userEmail) ?>"
                                       <?= $isLoggedIn ? 'readonly style="background:#f3f4f6;"' : '' ?>
                                       required />
                                <?php if ($isLoggedIn): ?>
                                    <p class="text-xs text-gray-400 mt-1">Prefilled from your account</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Subject -->
                        <div class="mb-4">
                            <label for="contactSubject" class="block text-gray-700 font-medium mb-2">
                                Subject <span class="text-red-500">*</span>
                            </label>
                            <select id="contactSubject" name="subject"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                <option value="">Select a topic...</option>
                                <option value="general">General Inquiry</option>
                                <option value="vendor">Become a Vendor</option>
                                <option value="order">Order Issue</option>
                                <option value="product">Product Question</option>
                                <option value="account">Account Help</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Message -->
                        <div class="mb-4">
                            <label for="contactMessage" class="block text-gray-700 font-medium mb-2">
                                Message <span class="text-red-500">*</span>
                            </label>
                            <textarea id="contactMessage" name="message" rows="5"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-y"
                                      placeholder="Tell us how we can help..." required></textarea>
                        </div>

                        <!-- Error / Success Messages -->
                        <div id="contactError" class="hidden bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                            <p class="text-red-600 text-sm text-center" id="contactErrorText"></p>
                        </div>
                        <div id="contactSuccess" class="hidden bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
                            <p class="text-green-600 text-sm text-center" id="contactSuccessText">
                                ✅ Your message has been sent! We'll get back to you soon.
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                                class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition transform active:scale-98">
                            Send Message
                        </button>

                        <p class="text-xs text-gray-400 mt-4 text-center">
                            By submitting this form, you agree to our
                            <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>.
                        </p>
                    </form>
                </div>
            </div>

            <!-- RIGHT: Contact Info (1/3 width) -->
            <div class="lg:col-span-1 space-y-6">

                <!-- Contact Details Card -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Contact Information</h3>

                    <div class="space-y-4">
                        <!-- Email -->
                        <div class="flex items-start gap-3">
                            <div class="bg-blue-100 rounded-full p-2 flex-shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Email</p>
                                <a href="mailto:support@bonamarkets.com" class="text-blue-600 hover:underline text-sm">
                                    support@bonamarkets.com
                                </a>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="flex items-start gap-3">
                            <div class="bg-green-100 rounded-full p-2 flex-shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Phone</p>
                                <a href="tel:+27123456789" class="text-blue-600 hover:underline text-sm">
                                    +27 12 345 6789
                                </a>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="flex items-start gap-3">
                            <div class="bg-purple-100 rounded-full p-2 flex-shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Address</p>
                                <p class="text-gray-600 text-sm">12 Vilakazi Street</p>
                                <p class="text-gray-600 text-sm">Soweto, Johannesburg</p>
                                <p class="text-gray-600 text-sm">South Africa</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Hours Card -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Business Hours</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Monday – Friday</span>
                            <span class="font-medium text-gray-800">9:00 AM – 6:00 PM</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Saturday</span>
                            <span class="font-medium text-gray-800">10:00 AM – 4:00 PM</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Sunday</span>
                            <span class="font-medium text-gray-400">Closed</span>
                        </div>
                        <div class="border-t border-gray-200 mt-3 pt-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Response Time</span>
                                <span class="font-medium text-green-600">24 – 48 hours</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Links Card -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Follow Us</h3>
                    <div class="flex gap-3">
                        <a href="#" class="bg-blue-50 hover:bg-blue-100 text-blue-600 p-3 rounded-full transition" aria-label="Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"/>
                            </svg>
                        </a>
                        <a href="#" class="bg-sky-50 hover:bg-sky-100 text-sky-500 p-3 rounded-full transition" aria-label="Twitter">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                        <a href="#" class="bg-pink-50 hover:bg-pink-100 text-pink-600 p-3 rounded-full transition" aria-label="Instagram">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <rect x="2" y="2" width="20" height="20" rx="5"/>
                                <circle cx="12" cy="12" r="5"/>
                                <circle cx="17.5" cy="6.5" r="1.5"/>
                            </svg>
                        </a>
                        <a href="#" class="bg-green-50 hover:bg-green-100 text-green-600 p-3 rounded-full transition" aria-label="WhatsApp">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Link to Vendor Apply -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-lg p-6 text-center text-white">
                    <h3 class="text-lg font-bold mb-2">Sell on Bona Markets?</h3>
                    <p class="text-sm opacity-90 mb-4">Join thousands of vendors growing their business with us.</p>
                    <a href="vendor/apply.php" class="inline-block bg-white text-blue-600 px-6 py-2 rounded-lg font-semibold hover:bg-gray-100 transition">
                        Become a Vendor →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- FAQ SECTION -->
    <!-- ============================================================ -->
    <section class="bg-white py-12">
        <div class="container mx-auto px-4">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 text-center mb-8">
                Frequently Asked Questions
            </h2>
            <div class="max-w-3xl mx-auto space-y-4">

                <details class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                    <summary class="font-semibold text-gray-800 cursor-pointer">How do I become a vendor?</summary>
                    <p class="text-gray-600 text-sm mt-3">Click "Become a Vendor" on our homepage, fill out the application form, and our team will review your application within 2-3 business days.</p>
                </details>

                <details class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                    <summary class="font-semibold text-gray-800 cursor-pointer">What payment methods do you accept?</summary>
                    <p class="text-gray-600 text-sm mt-3">We accept all major credit cards (Visa, Mastercard, American Express) via Stripe, as well as PayPal. All payments are secure and encrypted.</p>
                </details>

                <details class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                    <summary class="font-semibold text-gray-800 cursor-pointer">How long does shipping take?</summary>
                    <p class="text-gray-600 text-sm mt-3">Shipping times vary by vendor and location. Most orders are processed within 1-3 business days and delivered within 5-10 business days.</p>
                </details>

                <details class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                    <summary class="font-semibold text-gray-800 cursor-pointer">What is your return policy?</summary>
                    <p class="text-gray-600 text-sm mt-3">Each vendor has their own return policy. Please check the product listing for specific return information. Our support team can help mediate any disputes.</p>
                </details>

                <details class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                    <summary class="font-semibold text-gray-800 cursor-pointer">Is Bona Markets safe to use?</summary>
                    <p class="text-gray-600 text-sm mt-3">Absolutely! We use industry-standard security (SSL encryption, Stripe for payments) and all vendors are vetted before being approved to sell on our platform.</p>
                </details>
            </div>
        </div>
    </section>

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

        // ===== Contact Form Handler =====
        function handleContactForm(event) {
            event.preventDefault();

            const name = document.getElementById('contactName').value;
            const email = document.getElementById('contactEmail').value;
            const subject = document.getElementById('contactSubject').value;
            const message = document.getElementById('contactMessage').value;
            const errorDiv = document.getElementById('contactError');
            const errorText = document.getElementById('contactErrorText');
            const successDiv = document.getElementById('contactSuccess');

            // Hide previous messages
            errorDiv.classList.add('hidden');
            successDiv.classList.add('hidden');

            // Validation
            if (!name.trim()) {
                errorText.innerText = 'Please enter your full name.';
                errorDiv.classList.remove('hidden');
                return;
            }

            if (!email.trim() || !email.includes('@')) {
                errorText.innerText = 'Please enter a valid email address.';
                errorDiv.classList.remove('hidden');
                return;
            }

            if (!subject) {
                errorText.innerText = 'Please select a subject.';
                errorDiv.classList.remove('hidden');
                return;
            }

            if (!message.trim() || message.trim().length < 10) {
                errorText.innerText = 'Please enter a message (minimum 10 characters).';
                errorDiv.classList.remove('hidden');
                return;
            }

            // Demo: Show success message
            successDiv.classList.remove('hidden');

            // Log the submission (for demo)
            console.log('Contact form submitted:', { name, email, subject, message });

            // Reset form after 3 seconds (optional)
            setTimeout(function() {
                // Optionally hide success message after a while
                // successDiv.classList.add('hidden');
            }, 5000);
        }

        console.log('Bona Markets Contact page loaded with session support!');
    </script>

</body>
</html>
