<?php
$id = $_GET['id'] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
  <title>Cart - Bona Markets</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Your Cart</h1>
  <?php if($id): ?>
    <p>Product with ID <?php echo $id; ?> added to cart.</p>
  <?php else: ?>
    <p>No products in cart yet.</p>
  <?php endif; ?>
</body>
</html>
