<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Categories - Bona Market</title>
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

<section class="filters">
    <form method="GET" action="products.php" class="filter-bar">
        <input type="text" name="search" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
        <input type="number" name="price" placeholder="Price up to R20000" value="<?php echo isset($_GET['price']) ? $_GET['price'] : ''; ?>">
        <button type="submit" class="btn">Filter</button>
    </form>
</section>

<section class="products">
    <h2>Product Catalogue</h2>
    <?php
    $catResult = mysqli_query($conn, "SELECT * FROM categories");
    while ($cat = mysqli_fetch_assoc($catResult)) {
        echo "<h3>" . $cat['name'] . "</h3><div class='grid'>";
        $query = "SELECT * FROM products WHERE category_id=" . $cat['id'];

        if (!empty($_GET['search'])) {
            $search = mysqli_real_escape_string($conn, $_GET['search']);
            $query .= " AND name LIKE '%$search%'";
        }
        if (!empty($_GET['price'])) {
            $price = intval($_GET['price']);
            $query .= " AND price <= $price";
        }

        $result = mysqli_query($conn, $query . " LIMIT 4");
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='card'>
                    <img src='assets/" . $row['image'] . "' alt='" . $row['name'] . "'>
                    <h4>" . $row['name'] . "</h4>
                    <p>R" . $row['price'] . "</p>
                    <a href='product.php?id=" . $row['id'] . "' class='btn'>View Details</a>
                  </div>";
        }
        echo "</div><a href='category.php?id=" . $cat['id'] . "' class='btn view-more'>View More</a>";
    }
    ?>
</section>

<footer>
    <p>&copy; 2026 Bona Market. All rights reserved.</p>
</footer>
</body>
</html>
