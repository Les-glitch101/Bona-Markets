<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Product Details - Bona Markets</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <nav>
        <a href="index.php">Home</a>
        <a href="products.php">Categories</a>
        <a href="about.php">About</a>
        <a href="cart.php">Cart</a>
    </nav>
</header>

<section class="product-details">
<?php
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = mysqli_query($conn, "SELECT p.*, c.name AS category_name 
                                   FROM products p 
                                   JOIN categories c ON p.category_id = c.id 
                                   WHERE p.id = $id");
    if ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='details'>
                <img src='assets/" . $row['image'] . "' alt='" . $row['name'] . "'>
                <div class='info'>
                    <h2>" . $row['name'] . "</h2>";

        // Show label if exists
        if (!empty($row['label'])) {
            echo "<span class='product-label'>" . $row['label'] . "</span>";
        }

        echo "<p><strong>Category:</strong> " . $row['category_name'] . "</p>
              <p><strong>Price:</strong> R" . $row['price'] . "</p>
              <p><strong>Stock:</strong> " . ($row['stock'] > 0 ? $row['stock'] . " available" : "Out of Stock") . "</p>
              <p><strong>Description:</strong> " . $row['description'] . "</p>";

        if ($row['stock'] > 0) {
            echo "<a href='cart.php?action=add&id=" . $row['id'] . "' class='btn'>Add to Cart</a>";
        } else {
            echo "<span class='btn disabled'>Out of Stock</span>";
        }

        echo "</div></div>";
    } else {
        echo "<p>Product not found.</p>";
    }
} else {
    echo "<p>No product selected.</p>";
}
?>
</section>

<footer>
    <p>&copy; 2026 Bona Markets. All rights reserved.</p>
</footer>
</body>
</html>
