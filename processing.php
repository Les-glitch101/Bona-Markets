<?php
session_start();
require_once 'config/db.php';

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_id == 0) {
    header('Location: checkout.php');
    exit;
}

// Verify order exists
$stmt = $pdo->prepare("SELECT * FROM Orders WHERE ID = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: checkout.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment - BonaMarkets</title>
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
            margin: 0;
        }
        .processing-card {
            background: #EEF2EE;
            border-radius: 32px;
            padding: 50px 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 8px 30px rgba(28, 42, 34, 0.2);
        }
        .processing-card h2 {
            color: #1C2A22;
            font-size: 1.6rem;
            margin-bottom: 10px;
        }
        .processing-card p {
            color: #4F6B5A;
            font-size: 1rem;
            margin-bottom: 25px;
        }
        .loader {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin: 20px 0;
        }
        .loader .dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #4F6B5A;
            animation: bounce 1.2s ease-in-out infinite;
        }
        .loader .dot:nth-child(2) { animation-delay: 0.2s; }
        .loader .dot:nth-child(3) { animation-delay: 0.4s; }
        @keyframes bounce {
            0%, 80%, 100% {
                transform: scale(0.6);
                opacity: 0.3;
            }
            40% {
                transform: scale(1.2);
                opacity: 1;
            }
        }
        .progress-bar {
            width: 100%;
            height: 4px;
            background: #CFD8CF;
            border-radius: 4px;
            margin-top: 25px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            width: 0%;
            background: #4F6B5A;
            border-radius: 4px;
            transition: width 2s ease-in-out;
        }
        .status-text {
            color: #4F6B5A;
            font-size: 0.9rem;
            margin-top: 15px;
            font-weight: 500;
        }
    </style>
</head>
<body>
<div class="processing-card">
    <h2>Processing Payment</h2>
    <p>Please wait while we confirm your order...</p>
    
    <div class="loader">
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>
    
    <div class="progress-bar">
        <div class="progress-fill" id="progressFill"></div>
    </div>
    
    <div class="status-text" id="statusText">Initializing payment...</div>
</div>

<script>
    const progressFill = document.getElementById('progressFill');
    const statusText = document.getElementById('statusText');
    
    const statusMessages = [
        'Initializing payment...',
        'Validating card details...',
        'Processing payment...',
        'Payment confirmed!',
        'Finalizing your order...'
    ];
    
    function updateProgress(percent, message) {
        progressFill.style.width = percent + '%';
        if (message) statusText.textContent = message;
    }
    
    function delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
    
    async function processPayment() {
        try {
            updateProgress(5, statusMessages[0]);
            await delay(400);
            
            updateProgress(25, statusMessages[1]);
            await delay(500);
            
            updateProgress(55, statusMessages[2]);
            await delay(600);
            
            updateProgress(80, statusMessages[3]);
            await delay(400);
            
            updateProgress(100, statusMessages[4]);
            await delay(500);
            
            // Redirect to success page with order ID
            window.location.href = 'success.php?order_id=<?php echo $order_id; ?>';
            
        } catch (error) {
            statusText.textContent = 'Error: ' + error.message;
            statusText.style.color = '#CFD8CF';
            await delay(2000);
            window.location.href = 'checkout.php';
        }
    }
    
    processPayment();
</script>
</body>
</html>