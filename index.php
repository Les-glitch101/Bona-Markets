<?php
/**
 * index.php – Homepage with product grid, category filter, search, floating cart
 * PURPOSE: Display all products with filtering and searching capabilities.
 */
session_start();
require_once 'config/db.php';

// Function to get cart item count for the current user
function getCartCount($pdo, $user_id) {
    $cartStmt = $pdo->prepare("SELECT ID FROM Carts WHERE UserID = ?");
    $cartStmt->execute([$user_id]);
    $cart = $cartStmt->fetch();
    if (!$cart) return 0;
    
    $countStmt = $pdo->prepare("SELECT SUM(Quantity) as total FROM CartItems WHERE CartID = ?");
    $countStmt->execute([$cart['ID']]);
    $result = $countStmt->fetch();
    return $result['total'] ? (int)$result['total'] : 0;
}

$cart_count = getCartCount($pdo, $_SESSION['user_id'] ?? 1);

// Fetch product IDs for the collage
$smartphoneStmt = $pdo->query("SELECT ProductID, Name, Price FROM Products WHERE Category = 'Smartphones' LIMIT 2");
$smartphones = $smartphoneStmt->fetchAll();

$laptopStmt = $pdo->query("SELECT ProductID, Name, Price FROM Products WHERE Category = 'Laptops' LIMIT 1");
$laptops = $laptopStmt->fetchAll();

$audioStmt = $pdo->query("SELECT ProductID, Name, Price FROM Products WHERE Category = 'Audio' LIMIT 1");
$audio = $audioStmt->fetchAll();

$mainPhone = isset($smartphones[0]) ? $smartphones[0] : null;
$secondPhone = isset($smartphones[1]) ? $smartphones[1] : null;
$featuredLaptop = isset($laptops[0]) ? $laptops[0] : null;
$featuredAudio = isset($audio[0]) ? $audio[0] : null;

$mainPhoneId = $mainPhone ? $mainPhone['ProductID'] : 1;
$secondPhoneId = $secondPhone ? $secondPhone['ProductID'] : 2;
$laptopId = $featuredLaptop ? $featuredLaptop['ProductID'] : 3;
$audioId = $featuredAudio ? $featuredAudio['ProductID'] : 4;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bona Markets</title>
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <!-- External CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container">
    <!--  NAVBAR  -->
    <div class="navbar">
        <div class="logo">Bona Markets</div>
        <div class="nav-links">
            <a href="orders.php">Orders</a>
            <a href="cart.php" class="cart-nav-link">
                Cart
                <span class="cart-badge" id="cartBadge"><?php echo $cart_count; ?></span>
            </a>
        </div>
    </div>

    <!--COLLAGE HERO SLIDER-->
    <div class="hero-collage">
        <a href="product.php?id=<?php echo $mainPhoneId; ?>" class="collage-item collage-main">
            <img src="assets/smartphone1.jpg" alt="Smartphone" onerror="this.src='assets/placeholder.jpg'">
            <div class="collage-overlay">
                <span class="tag">Best Seller</span>
                <h3><?php echo $mainPhone ? htmlspecialchars($mainPhone['Name']) : 'Latest Smartphone'; ?></h3>
                <p>Premium display • 5G • All-day battery</p>
                <span class="price-tag"><?php echo $mainPhone ? 'R ' . number_format($mainPhone['Price'], 2) : 'R 14,999.00'; ?></span>
            </div>
        </a>

        <div class="collage-item collage-top-right">
            <div class="shipping-banner">
                <h3>Free Shipping</h3>
                <p>On orders over R500</p>
                <a href="#products-section" class="shop-now-link">Shop Now </a>
            </div>
        </div>

        <a href="product.php?id=<?php echo $secondPhoneId; ?>" class="collage-item collage-top-right-2">
            <img src="assets/smartphone2.jpg" alt="Smartphone" onerror="this.src='assets/placeholder.jpg'">
            <div class="collage-overlay">
                <span class="tag">Smartphone</span>
                <h3><?php echo $secondPhone ? htmlspecialchars($secondPhone['Name']) : 'Galaxy S24 Ultra'; ?></h3>
                <span class="price-tag"><?php echo $secondPhone ? 'R ' . number_format($secondPhone['Price'], 2) : 'R 21,999.00'; ?></span>
            </div>
        </a>

        <a href="product.php?id=<?php echo $audioId; ?>" class="collage-item collage-bottom-left">
            <img src="assets/headphones1.jpg" alt="Headphones" onerror="this.src='assets/placeholder.jpg'">
            <div class="collage-overlay">
                <span class="tag">Audio</span>
                <h3><?php echo $featuredAudio ? htmlspecialchars($featuredAudio['Name']) : 'Premium Headphones'; ?></h3>
                <span class="price-tag"><?php echo $featuredAudio ? 'R ' . number_format($featuredAudio['Price'], 2) : 'R 399.00'; ?></span>
            </div>
        </a>

        <a href="product.php?id=<?php echo $laptopId; ?>" class="collage-item collage-bottom-right">
            <img src="assets/laptop1.jpg" alt="Laptop" onerror="this.src='assets/placeholder.jpg'">
            <div class="collage-overlay">
                <span class="tag">Laptop</span>
                <h3><?php echo $featuredLaptop ? htmlspecialchars($featuredLaptop['Name']) : 'Gaming Laptop'; ?></h3>
                <span class="price-tag"><?php echo $featuredLaptop ? 'R ' . number_format($featuredLaptop['Price'], 2) : 'R 26,000.00'; ?></span>
            </div>
        </a>
    </div>

    <!--  FILTER + SEARCH BAR  -->
    <div class="filter-search-bar">
        <div class="filter-group">
            <label>Filter:</label>
            <select id="categoryFilter">
                <option value="all">All Products</option>
                <?php
                $catStmt = $pdo->query("SELECT DISTINCT Category FROM Products WHERE Category IS NOT NULL ORDER BY Category");
                while ($cat = $catStmt->fetch()) {
                    echo '<option value="' . htmlspecialchars($cat['Category']) . '">' . htmlspecialchars($cat['Category']) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="search-group">
            <label>Search:</label>
            <input type="text" id="searchInput" placeholder="Type product name...">
        </div>
    </div>

    <!--PRODUCT GRID  -->
    <div class="section-title" id="products-section"> Products That We Offer</div>
    <div class="product-grid" id="products"></div>
</div>

<!--  FOOTER  -->
<div class="footer-bottom">
    <p>&copy; 2026 BonaMarkets. All Rights Reserved. | <a href="index.php">Home</a> | <a href="cart.php">Cart</a> | <a href="orders.php">Orders</a></p>
</div>

<!-- FLOATING CART BUTTON -->
<a href="cart.php" class="floating-cart" id="floatingCart">
    View Cart
    <span class="badge" id="floatingBadge"><?php echo $cart_count; ?></span>
</a>

<!--  EXTERNAL JAVASCRIPT  -->
<script src="js/script.js"></script>

<script>
    
    // PAGE-SPECIFIC JAVASCRIPT
   
    const allProducts = <?php
        $stmt = $pdo->query("SELECT ProductID, Name, Price, Image, Category FROM Products");
        echo json_encode($stmt->fetchAll());
    ?>;

    function formatRands(price) {
        return 'R ' + price.toLocaleString('en-ZA', { minimumFractionDigits: 2 });
    }

    function renderProducts() {
        const category = document.getElementById('categoryFilter').value;
        const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();

        let filtered = allProducts;

        if (category !== 'all') {
            filtered = filtered.filter(p => p.Category === category);
        }

        if (searchTerm !== '') {
            filtered = filtered.filter(p => p.Name.toLowerCase().includes(searchTerm));
        }

        const container = document.getElementById('products');

        if (filtered.length === 0) {
            container.innerHTML = '<p style="color:#e8d5b5; text-align:center; padding:40px;">No products found.</p>';
            return;
        }

        let html = '';
        filtered.forEach(p => {
            let imgUrl = p.Image && p.Image.trim() ? p.Image : 'assets/placeholder.jpg';
            html += `
                <div class="product-card" data-product-id="${p.ProductID}">
                    <a href="product.php?id=${p.ProductID}" class="product-link">
                        <img class="product-image" src="${imgUrl}" alt="${p.Name}" onerror="this.src='assets/placeholder.jpg'">
                        <div class="product-info">
                            <div class="product-title">${p.Name}</div>
                            <div class="product-price">${formatRands(p.Price)}</div>
                        </div>
                    </a>
                    <div class="product-actions">
                        <button class="add-btn" onclick="addToCart(${p.ProductID}, this)">Add to Cart</button>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    document.getElementById('categoryFilter').addEventListener('change', renderProducts);
    document.getElementById('searchInput').addEventListener('input', renderProducts);

    renderProducts();
</script>
</body>
</html>