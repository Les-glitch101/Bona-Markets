<?php
// Start session for future PHP integration (Week 2)
session_start();

// Week 1: This is a static demo page
// Week 2: This will connect to database and real authentication

// If user is already logged in (Week 2+), redirect to appropriate dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: /admin/dashboard.php');
        exit;
    } elseif ($_SESSION['role'] === 'vendor') {
        header('Location: /vendor/dashboard.php');
        exit;
    } else {
        header('Location: /index.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Login | Bona Markets</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        input {
            transition: all 0.2s ease;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- ========== MAIN CONTENT ========== -->
    <main class="min-h-screen flex items-center justify-center py-12 px-4">
        
        <!-- Login Card -->
        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">
            
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-8 text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">Welcome Back</h1>
                <p class="text-blue-100 text-sm mt-1">Sign in to your account</p>
            </div>
            
            <!-- Card Body -->
            <div class="p-6 md:p-8">
                
                <!-- Demo Alert -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-6">
                    <p class="text-blue-700 text-xs text-center">
                        🔐 <span class="font-medium">Demo Mode:</span> Enter any email/password to simulate login
                        <br>(Real authentication coming in Week 2)
                    </p>
                </div>
                
                <!-- Login Form -->
                <form id="loginForm" onsubmit="handleLogin(event)">
                    <!-- Email Field -->
                    <div class="mb-5">
                        <label for="email" class="block text-gray-700 font-medium mb-2">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <input type="email" id="email" name="email" 
                                   class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="you@example.com"
                                   value="demo@bonamarkets.com"
                                   required>
                        </div>
                    </div>
                    
                    <!-- Password Field -->
                    <div class="mb-5">
                        <label for="password" class="block text-gray-700 font-medium mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input type="password" id="password" name="password" 
                                   class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="••••••••"
                                   value="demo123"
                                   required>
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="text-right mt-1">
                            <a href="#" class="text-xs text-blue-600 hover:underline">Forgot password?</a>
                        </div>
                    </div>
                    
                    <!-- Remember Me -->
                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" class="h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                    </div>
                    
                    <!-- Error Message -->
                    <div id="errorMessage" class="hidden bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                        <p class="text-red-600 text-sm text-center" id="errorText"></p>
                    </div>
                    
                    <!-- Success Message -->
                    <div id="successMessage" class="hidden bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
                        <p class="text-green-600 text-sm text-center" id="successText">Login successful! Redirecting...</p>
                    </div>
                    
                    <!-- Login Button -->
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition transform active:scale-98">
                        Sign In
                    </button>
                    
                    <!-- Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Or continue with</span>
                        </div>
                    </div>
                    
                    <!-- Social Login -->
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" class="flex items-center justify-center gap-2 border border-gray-300 rounded-lg py-2 hover:bg-gray-50 transition">
                            <svg class="h-5 w-5" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            <span class="text-sm">Google</span>
                        </button>
                        <button type="button" class="flex items-center justify-center gap-2 border border-gray-300 rounded-lg py-2 hover:bg-gray-50 transition">
                            <svg class="h-5 w-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z"/>
                            </svg>
                            <span class="text-sm">Facebook</span>
                        </button>
                    </div>
                    
                    <!-- Sign Up Link -->
                    <p class="text-center text-gray-600 text-sm mt-6">
                        Don't have an account?
                        <a href="register.php" class="text-blue-600 font-semibold hover:underline">Sign up</a>
                    </p>
                </form>
            </div>
        </div>
    </main>

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

        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Change icon (simple visual feedback)
                if (type === 'text') {
                    togglePassword.innerHTML = `<svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                    </svg>`;
                } else {
                    togglePassword.innerHTML = `<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>`;
                }
            });
        }

        // Handle Login Form Submission
        function handleLogin(event) {
            event.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorDiv = document.getElementById('errorMessage');
            const errorText = document.getElementById('errorText');
            const successDiv = document.getElementById('successMessage');
            
            // Hide previous messages
            errorDiv.classList.add('hidden');
            successDiv.classList.add('hidden');
            
            // Basic validation
            if (!email || !password) {
                errorText.innerText = 'Please enter both email and password';
                errorDiv.classList.remove('hidden');
                return;
            }
            
            if (!email.includes('@')) {
                errorText.innerText = 'Please enter a valid email address';
                errorDiv.classList.remove('hidden');
                return;
            }
            
            if (password.length < 3) {
                errorText.innerText = 'Password must be at least 3 characters';
                errorDiv.classList.remove('hidden');
                return;
            }
            
            // Week 1: Demo mode – simulate successful login
            console.log('Demo login:', { email, password });
            
            // Show success message
            successDiv.classList.remove('hidden');
            
            // Redirect to index.php (homepage) after 1.5 seconds
            setTimeout(function() {
                window.location.href = 'index.php';  // ← Fixed: now points to .php
            }, 1500);
        }
        
        console.log('Login page loaded – redirects to index.php');
    </script>
</body>
</html>
