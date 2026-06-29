<?php
session_start();
require_once 'config/db.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$product_id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM Products WHERE ProductID = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['Name']); ?> - BonaMarkets</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .product-detail-container {
            max-width: 1200px;
            margin: 20px auto;
            background: #EEF2EE;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 8px 30px rgba(28, 42, 34, 0.15);
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
        }
        .product-detail-image {
            flex: 1;
            min-width: 300px;
        }
        .product-detail-image img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 16px;
            background: #CFD8CF;
        }
        .product-detail-info {
            flex: 1;
            min-width: 300px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .product-detail-info .category {
            display: inline-block;
            background: #1C2A22;
            color: #FDFDFB;
            padding: 4px 14px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            align-self: flex-start;
        }
        .product-detail-info .product-name {
            font-size: 2rem;
            color: #1C2A22;
            margin: 0;
            font-weight: 700;
            border: none;
            padding: 0;
        }
        .product-detail-info .price {
            font-size: 2.2rem;
            font-weight: 800;
            color: #4F6B5A;
        }
        .product-detail-info .description {
            font-size: 1rem;
            line-height: 1.8;
            color: #1C2A22;
            background: #FDFDFB;
            padding: 16px 20px;
            border-radius: 8px;
            border-left: 4px solid #4F6B5A;
        }
        .add-to-cart-btn {
            background: #4F6B5A;
            border: none;
            color: #FDFDFB;
            font-weight: 700;
            padding: 14px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            transition: 0.25s;
            width: 100%;
            max-width: 300px;
            margin-top: 5px;
        }
        .add-to-cart-btn:hover {
            background: #CFD8CF;
            color: #1C2A22;
            transform: scale(1.02);
            box-shadow: 0 4px 15px rgba(79, 107, 90, 0.3);
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #4F6B5A;
            text-decoration: none;
            font-weight: 500;
            transition: 0.2s;
            padding: 8px 16px;
            border-radius: 8px;
        }
        .back-link:hover {
            color: #CFD8CF;
            background: rgba(79, 107, 90, 0.1);
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .product-detail-container { flex-direction: column; padding: 20px; }
            .product-detail-image img { height: 280px; }
            .product-detail-info .product-name { font-size: 1.6rem; }
            .product-detail-info .price { font-size: 1.8rem; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="navbar">
        <div class="logo">Bona Markets</div>
        <div class="nav-links">
            <a href="orders.php">Orders</a>
            <a href="cart.php" class="cart-nav-link">Cart</a>
        </div>
    </div>

    <div class="product-detail-container">
        <div class="product-detail-image">
            <img src="<?php echo htmlspecialchars($product['Image'] ?? 'assets/placeholder.jpg'); ?>" 
                 alt="<?php echo htmlspecialchars($product['Name']); ?>"
                 onerror="this.src='assets/placeholder.jpg'">
        </div>
        <div class="product-detail-info">
            <span class="category"><?php echo htmlspecialchars($product['Category'] ?? 'Uncategorized'); ?></span>
            <h1 class="product-name"><?php echo htmlspecialchars($product['Name']); ?></h1>
            <div class="price">R <?php echo number_format($product['Price'], 2); ?></div>
            <div class="description">
                <?php echo nl2br(htmlspecialchars($product['Description'] ?? 'No description available.')); ?>
            </div>
            <button class="add-to-cart-btn" onclick="addToCart(<?php echo $product['ProductID']; ?>)">
                Add to Cart
            </button>
        </div>
    </div>

    <a href="index.php" class="back-link">Back to Products</a>
</div>

<script>
    async function addToCart(productId) {
        const response = await fetch('api/cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, quantity: 1 })
        });
        const data = await response.json();
        if (data.success) {
            alert('Added to cart!');
        } else {
            alert('Error adding item to cart.');
        }
    }
</script>
</body>
</html>