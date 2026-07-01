<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Category - Bona Market</title>
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

<section class="category-products">
<?php
if (isset($_GET['id'])) {
    $catId = intval($_GET['id']);
    $catResult = mysqli_query($conn, "SELECT * FROM categories WHERE id=$catId");
    if ($cat = mysqli_fetch_assoc($catResult)) {
        echo "<h2>" . $cat['name'] . "</h2>";
        
        $query = "SELECT * FROM products WHERE category_id=$catId";
        $result = mysqli_query($conn, $query);

        echo "<div class='grid'>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='card'>
                    <img src='assets/" . $row['image'] . "' alt='" . $row['name'] . "'>
                    <h3>" . $row['name'] . "</h3>";
            if (!empty($row['label'])) {
                echo "<span class='product-label'>" . $row['label'] . "</span>";
            }
            echo "<p>Price: R" . $row['price'] . "</p>
                  <p>Stock: " . ($row['stock'] > 0 ? $row['stock'] . " available" : "Out of Stock") . "</p>
                  <div class='buttons'>
                      <a href='product.php?id=" . $row['id'] . "' class='btn'>View Details</a>";
            if ($row['stock'] > 0) {
                echo "<a href='cart.php?action=add&id=" . $row['id'] . "' class='btn'>Add to Cart</a>";
            } else {
                echo "<span class='btn disabled'>Out of Stock</span>";
            }
            echo "</div></div>";
        }
        echo "</div>";
    } else {
        echo "<p>Category not found.</p>";
    }
} else {
    echo "<p>No category selected.</p>";
}
?>
</section>

<footer>
    <p>&copy; 2026 Bona Market. All rights reserved.</p>
</footer>
</body>
</html>
