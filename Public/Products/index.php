<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Bona Market</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="logo">Bona Market</div>
    <nav>
        <a href="index.php">Home</a>
        <a href="products.php">Categories</a>
        <a href="about.php">About</a>
        <a href="cart.php">Cart</a>
    </nav>
</header>

<section class="banner">
    <h1>Welcome to Bona Market</h1>
    <p>🚚 Free Shipping on All Orders</p>
</section>

<section class="categories">
    <h2>Shop by Category</h2>
    <div class="grid">
        <?php
        $result = mysqli_query($conn, "SELECT * FROM categories");
        while ($row = mysqli_fetch_assoc($result)) {
            // Map category to image
            $images = [
                "Electronics" => "smartphone.jpg",
                "Clothing" => "shirt.jpg",
                "Home & Garden" => "blanket.jpg",
                "Accessories" => "handbag.jpg",
                "Beauty & Wellness" => "perfume.jpg",
                "Handcrafts" => "beads.jpg",
                "Food & Spices" => "spices.jpg",
                "Art & Decor" => "painting.jpg",
                "Jewellery" => "earrings.jpg"
            ];
            $img = isset($images[$row['name']]) ? $images[$row['name']] : "logo.jpg";

            echo "<div class='card'>
                    <img src='assets/$img' alt='" . $row['name'] . "'>
                    <p>" . $row['name'] . "</p>
                    <a href='category.php?id=" . $row['id'] . "' class='btn'>View More</a>
                  </div>";
        }
        ?>
    </div>
</section>

<section class="new-products">
    <h2>New Products</h2>
    <div class="grid">
        <?php
        $result = mysqli_query($conn, "SELECT * FROM products WHERE label='NEW' LIMIT 4");
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='card'>
                    <img src='assets/" . $row['image'] . "' alt='" . $row['name'] . "'>
                    <p>" . $row['name'] . " - R" . $row['price'] . "</p>
                  </div>";
        }
        ?>
    </div>
</section>

<section class="best-sellers">
    <h2>Best Selling</h2>
    <div class="grid">
        <?php
        $result = mysqli_query($conn, "SELECT * FROM products WHERE label='BEST SELLER' LIMIT 4");
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='card'>
                    <img src='assets/" . $row['image'] . "' alt='" . $row['name'] . "'>
                    <p>" . $row['name'] . " - R" . $row['price'] . "</p>
                  </div>";
        }
        ?>
    </div>
</section>

<section class="low-stock">
    <h2>Low / Out of Stock</h2>
    <div class="grid">
        <?php
        $result = mysqli_query($conn, "SELECT * FROM products WHERE stock <= 5");
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='card'>
                    <img src='assets/" . $row['image'] . "' alt='" . $row['name'] . "'>
                    <p>" . $row['name'] . " - R" . $row['price'] . "</p>
                    <p><strong>Status:</strong> " . ($row['stock'] > 0 ? "Low Stock (" . $row['stock'] . ")" : "Out of Stock") . "</p>
                  </div>";
        }
        ?>
    </div>
</section>

<footer>
    <p>&copy; 2026 Bona Market. All rights reserved.</p>
</footer>
</body>
</html>
