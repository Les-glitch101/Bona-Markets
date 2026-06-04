<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Bona Markets' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <a href="/" class="text-2xl font-bold text-blue-600">Bona Markets</a>
        <div class="hidden md:flex space-x-6">
            <a href="/" class="text-gray-600">Home</a>
            <a href="/products/index.php" class="text-gray-600">Shop</a>
            <a href="/cart/index.php" class="text-gray-600">Cart</a>
        </div>
        <div class="hidden md:flex space-x-4">
            <a href="/login.php" class="text-blue-600">Login</a>
            <a href="/register.php" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Sign Up</a>
        </div>
    </div>
</nav>
<main>
