<!-- Inside the desktop menu, replace the auth section -->
<div class="hidden md:flex items-center space-x-4">
    <?php if (isset($_SESSION['user_id'])): ?>
        <span class="text-gray-600">Welcome, <?= htmlspecialchars($_SESSION['email'] ?? 'User') ?></span>
        <a href="logout.php" class="text-red-500 hover:text-red-700 font-medium">Logout</a>
    <?php else: ?>
        <a href="login.php" class="text-gray-600 hover:text-blue-600 font-medium">Login</a>
        <a href="register.php" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition">
            Sign Up
        </a>
    <?php endif; ?>
</div>

<!-- For mobile menu -->
<div class="flex flex-col space-y-2 pt-2">
    <?php if (isset($_SESSION['user_id'])): ?>
        <span class="text-gray-600 py-1">Welcome, <?= htmlspecialchars($_SESSION['email'] ?? 'User') ?></span>
        <a href="logout.php" class="text-red-500 py-1">Logout</a>
    <?php else: ?>
        <a href="login.php" class="text-gray-600 hover:text-blue-600 py-1">Login</a>
        <a href="register.php" class="bg-blue-600 text-white text-center px-4 py-2 rounded-lg">Sign Up</a>
    <?php endif; ?>
</div>