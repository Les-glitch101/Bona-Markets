<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Bona Markets</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <nav>
    <a href="index.php">Home</a> | 
    <a href="products.php">Categories</a> | 
    <a href="about.php">About</a> | 
    <a href="cart.php">Cart</a>
  </nav>

  <header class="banner">
    <h1>Welcome to Bona Markets</h1>
    <p>Free Shipping on All Orders 🚚</p>
  </header>

  <section>
    <h2>Shop by Category</h2>
    <div class="grid">
      <?php
      $cats = $conn->query("SELECT * FROM categories");
      while($c = $cats->fetch_assoc()) {
        echo "
          <div class='card'>
            <img src='assets/".$c['name'].".jpg' alt='".$c['name']."' style='width:120px;height:120px;'><br>
            <a href='products.php?category=".$c['name']."'>".$c['name']."</a>
          </div>
        ";
      }
      ?>
    </div>
  </section>

  <section>
    <h2>Best Sellers</h2>
    <?php
    $best = $conn->query("SELECT * FROM products ORDER BY stock DESC LIMIT 3");
    while($p = $best->fetch_assoc()) {
      echo "<p>".$p['name']." - R".$p['price']."</p>";
    }
    ?>
  </section>

  <section>
    <h2>Most Looked Up Products</h2>
    <p>Trending items will appear here.</p>
  </section>

  <section>
    <h2>See Other Products</h2>
    <a href="products.php" class="btn">Browse All Products</a>
  </section>

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
