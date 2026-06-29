<?php
session_start();
require_once 'config/db.php';

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Fallback: check session if order ID not in URL
if ($order_id == 0 && isset($_SESSION['last_order_id'])) {
    $order_id = $_SESSION['last_order_id'];
}

// Fetch order details
if ($order_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM Orders WHERE ID = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - BonaMarkets</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: #FDFDFB;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: 'Poppins', system-ui, sans-serif;
            padding: 20px;
        }
        .success-card {
            background: #EEF2EE;
            border-radius: 32px;
            padding: 50px 40px;
            max-width: 550px;
            width: 100%;
            text-align: center;
            box-shadow: 0 8px 30px rgba(28, 42, 34, 0.2);
        }
        .success-card h1 {
            color: #1C2A22;
            font-size: 1.8rem;
            margin: 15px 0 10px;
        }
        .success-card p {
            color: #4F6B5A;
            font-size: 1rem;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        .success-card .order-number {
            font-weight: bold;
            color: #4F6B5A;
            font-size: 1.1rem;
        }
        .checkmark-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
        }
        .checkmark-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #4F6B5A;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: popIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            transform: scale(0);
        }
        .checkmark {
            color: #FDFDFB;
            font-size: 3.5rem;
            font-weight: bold;
            animation: drawCheck 0.5s ease-in-out 0.3s forwards;
            opacity: 0;
        }
        @keyframes popIn {
            0% { transform: scale(0); }
            70% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }
        @keyframes drawCheck {
            0% { opacity: 0; transform: scale(0.5); }
            100% { opacity: 1; transform: scale(1); }
        }
        .button-group {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        .success-btn {
            display: inline-block;
            background: #4F6B5A;
            color: #FDFDFB;
            padding: 14px 40px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1rem;
            transition: 0.25s;
        }
        .success-btn:hover {
            background: #CFD8CF;
            color: #1C2A22;
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(79, 107, 90, 0.3);
        }
        .success-btn-secondary {
            display: inline-block;
            color: #4F6B5A;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: 0.2s;
            margin-left: 10px;
        }
        .success-btn-secondary:hover {
            color: #CFD8CF;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="success-card">
    <div class="checkmark-wrapper">
        <div class="checkmark-circle">
            <span class="checkmark">✓</span>
        </div>
    </div>

    <?php if (isset($order) && $order): ?>
        <h1>Order Placed Successfully!</h1>
        <p>
            Thank you for your order.<br>
            <span class="order-number">Order #<?php echo $order['ID']; ?></span><br>
            We will notify you when your order is shipped.
        </p>
    <?php else: ?>
        <h1>Order Placed Successfully!</h1>
        <p>
            Thank you for your order.<br>
            <span class="order-number">Order Confirmed</span><br>
            We will notify you when your order is shipped.
        </p>
    <?php endif; ?>

    <div class="button-group">
        <a href="orders.php" class="success-btn">View My Orders</a>
        <a href="index.php" class="success-btn-secondary">Continue Shopping</a>
    </div>
</div>
</body>
</html>