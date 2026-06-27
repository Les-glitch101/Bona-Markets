<?php
include 'db.php';

// Get product ID from URL
$id = $_GET['id'] ?? null;

// Query product details
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE p.id=" . intval($id);
$result = $conn->query($sql);
$product = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $product ? $product['name'] : "Product Not Found"; ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Navigation -->
  <nav>
    <a href="index.php">Home</a> | 
    <a href="products.php">Categories</a> | 
    <a href="about.php">About</a> | 
    <a href="cart.php">Cart</a>
  </nav>

  <!-- Product Details -->
  <?php if($product): ?>
    <div class="product-detail">
      <div class="left">
        <img src="assets/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
      </div>
      <div class="right">
        <h1><?php echo $product['name']; ?></h1>
        <p><strong>Category:</strong> <?php echo $product['category_name']; ?></p>
        <p><strong>Price:</strong> R<?php echo $product['price']; ?></p>
        <p><strong>Stock:</strong> <?php echo $product['stock'] > 0 ? $product['stock']." available" : "Out of Stock"; ?></p>
        <p><strong>Description:</strong> <?php echo $product['description'] ?? "No description available."; ?></p>
        <a href="cart.php?id=<?php echo $product['id']; ?>" class="btn">Add to Cart</a>
      </div>
    </div>
  <?php else: ?>
    <h1>Product Not Found</h1>
  <?php endif; ?>

  <!-- Footer -->
  <footer>
    <div class="footer-links">
      <a href="index.php">Home</a> | 
      <a href="products.php">Categories</a> | 
      <a href="about.php">About</a> | 
      <a href="cart.php">Cart</a>
    </div>
    <p>&copy; 2026 Bona Markets. All rights reserved.</p>
  </footer>
</body>
</html>
