<?php
include 'db.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? '';

$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE 1";

if ($search) $sql .= " AND p.name LIKE '%".$conn->real_escape_string($search)."%'";
if ($category) $sql .= " AND c.name='".$conn->real_escape_string($category)."'";
if ($sort=="low") $sql .= " ORDER BY p.price ASC";
elseif ($sort=="high") $sql .= " ORDER BY p.price DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Products - Bona Markets</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <nav>
    <a href="index.php">Home</a> | 
    <a href="products.php">Categories</a> | 
    <a href="about.php">About</a> | 
    <a href="cart.php">Cart</a>
  </nav>

  <form method="GET" class="filters">
    <input type="text" name="search" placeholder="Search products..." value="<?php echo $search; ?>">
    <select name="category">
      <option value="">All Categories</option>
      <?php
      $cats = $conn->query("SELECT * FROM categories");
      while($c = $cats->fetch_assoc()) {
        $selected = ($category==$c['name']) ? "selected" : "";
        echo "<option $selected>".$c['name']."</option>";
      }
      ?>
    </select>
    <select name="sort">
      <option value="">Sort by Price</option>
      <option value="low" <?php if($sort=="low") echo "selected"; ?>>Low to High</option>
      <option value="high" <?php if($sort=="high") echo "selected"; ?>>High to Low</option>
    </select>
    <button type="submit">Filter</button>
  </form>

  <h1>All Products</h1>
  <div class="grid">
    <?php while($p = $result->fetch_assoc()): ?>
      <div class="card">
        <img src="assets/<?php echo $p['image']; ?>" alt="<?php echo $p['name']; ?>">
        <h3><?php echo $p['name']; ?></h3>
        <p>Category: <?php echo $p['category_name']; ?></p>
        <p>Price: R<?php echo $p['price']; ?></p>
        <p><?php echo $p['stock']>0 ? $p['stock']." in stock" : "Out of Stock"; ?></p>
        <a href="product.php?id=<?php echo $p['id']; ?>" class="btn">View More</a>
      </div>
    <?php endwhile; ?>
  </div>

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
