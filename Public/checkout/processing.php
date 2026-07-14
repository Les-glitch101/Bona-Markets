<?php
// ============================================================
// PROCESSING PAGE – Bona Markets
// Verifies order exists, then animates before redirecting to success
// ============================================================
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../../config/database.php';

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($order_id <= 0) {
    header('Location: index.php');
    exit;
}

// Verify this order belongs to the logged-in user
$stmt = $pdo->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, (int)$_SESSION['user_id']]);
if (!$stmt->fetch()) {
    header('Location: ../cart/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Processing Order | Bona Markets</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; background: #ffffff; }
        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0.6); opacity: .3; }
            40%            { transform: scale(1.2); opacity: 1;   }
        }
        .dot { animation: bounce 1.2s ease-in-out infinite; }
        .dot:nth-child(2) { animation-delay: .2s; }
        .dot:nth-child(3) { animation-delay: .4s; }
        @keyframes fillBar { from { width: 0 } to { width: 100% } }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">

    <div class="bg-white border border-gray-100 rounded-3xl shadow-xl p-12 max-w-md w-full text-center">

        <!-- Logo -->
        <a href="../index.php" class="text-xl font-bold text-blue-600 block mb-8" style="font-family:'Syne',sans-serif">Bona Markets</a>

        <!-- Animated dots -->
        <div class="flex justify-center gap-3 mb-6">
            <div class="dot w-4 h-4 rounded-full bg-blue-500"></div>
            <div class="dot w-4 h-4 rounded-full bg-blue-500"></div>
            <div class="dot w-4 h-4 rounded-full bg-blue-500"></div>
        </div>

        <h2 class="text-xl font-bold text-gray-800 mb-2">Processing Your Order</h2>
        <p class="text-gray-500 text-sm mb-8">Please wait while we confirm your payment…</p>

        <!-- Progress bar -->
        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden mb-4">
            <div id="progressBar" class="h-2 bg-blue-500 rounded-full" style="width:0%;transition:width .5s ease"></div>
        </div>
        <p class="text-xs text-gray-400" id="statusText">Initialising payment…</p>
    </div>

    <script>
    const steps = [
        { pct: 15, msg: 'Initialising payment…' },
        { pct: 35, msg: 'Validating card details…' },
        { pct: 60, msg: 'Processing payment…' },
        { pct: 85, msg: 'Payment confirmed!' },
        { pct: 100, msg: 'Finalising your order…' },
    ];

    const bar  = document.getElementById('progressBar');
    const text = document.getElementById('statusText');
    const delays = [300, 500, 600, 500, 500];

    async function run() {
        for (let i = 0; i < steps.length; i++) {
            await new Promise(r => setTimeout(r, delays[i]));
            bar.style.width  = steps[i].pct + '%';
            text.textContent = steps[i].msg;
        }
        await new Promise(r => setTimeout(r, 400));
        window.location.href = 'success.php?order_id=<?= $order_id ?>';
    }

    run();
    </script>

</body>
</html>