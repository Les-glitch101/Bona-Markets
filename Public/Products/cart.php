<?php
include 'db.php';
session_start();

// Add product to cart
if (isset($_GET['action']) && $_GET['action'] == "add" && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if (!isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = 1;
    } else {
        $_SESSION['cart'][$id]++;
    }
    header("Location: cart.php");
    exit;
}

// Remove product from cart
if (isset($_GET['action']) && $_GET['action'] == "remove" && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    header("Location: cart.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart - Bona Market</title>
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

<section class="cart">
    <h2>Your Shopping Cart</h2>
    <?php
    if (!empty($_SESSION['cart'])) {
        echo "<table>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>";
        $grandTotal = 0;
        foreach ($_SESSION['cart'] as $id => $qty) {
            $result = mysqli_query($conn, "SELECT * FROM products WHERE id=$id");
            if ($row = mysqli_fetch_assoc($result)) {
                $total = $row['price'] * $qty;
                $grandTotal += $total;
                echo "<tr>
                        <td>" . $row['name'] . "</td>
                        <td>R" . $row['price'] . "</td>
                        <td>" . $qty . "</td>
                        <td>R" . $total . "</td>
                        <td><a href='cart.php?action=remove&id=" . $row['id'] . "' class='btn'>Remove</a></td>
                      </tr>";
            }
        }
        echo "<tr>
                <td colspan='3'><strong>Grand Total</strong></td>
                <td colspan='2'><strong>R" . $grandTotal . "</strong></td>
              </tr>";
        echo "</table>";
        echo "<a href='checkout.php' class='btn add'>Proceed to Checkout</a>";
    } else {
        echo "<p>Your cart is empty.</p>";
    }
    ?>
</section>

<footer>
    <p>&copy; 2026 Bona Market. All rights reserved.</p>
</footer>
</body>
</html>
