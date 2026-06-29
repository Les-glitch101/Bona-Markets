<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - BonaMarkets</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: #FDFDFB;
            font-family: 'Poppins', system-ui, sans-serif;
            padding: 20px;
            color: #1C2A22;
            min-height: 100vh;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .page-header h1 {
            font-size: 1.9rem;
            color: #1C2A22;
            border-left: 5px solid #4F6B5A;
            padding-left: 15px;
        }
        .btn-secondary {
            display: inline-block;
            background: #1C2A22;
            color: #FDFDFB;
            padding: 10px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.25s;
        }
        .btn-secondary:hover {
            background: #4F6B5A;
            transform: scale(1.02);
        }
        .order-card {
            background: #EEF2EE;
            border-radius: 16px;
            margin-bottom: 25px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(28, 42, 34, 0.15);
        }
        .order-card:hover {
            border: 2px solid #4F6B5A;
        }
        .order-header {
            background: #1C2A22;
            padding: 14px 24px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            color: #FDFDFB;
        }
        .order-id {
            font-weight: bold;
            font-size: 1.1rem;
        }
        .order-body {
            padding: 20px 24px;
        }
        .address-block {
            background: #FDFDFB;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #4F6B5A;
        }
        .address-block .address-label {
            font-weight: 600;
            color: #1C2A22;
            display: block;
            margin-bottom: 4px;
        }
        .address-block .address-text {
            color: #1C2A22;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th,
        .items-table td {
            text-align: left;
            padding: 8px 4px;
            border-bottom: 1px solid #CFD8CF;
        }
        .items-table th {
            color: #1C2A22;
            font-weight: 600;
        }
        .total-row {
            text-align: right;
            font-size: 1.2rem;
            margin-top: 16px;
            font-weight: bold;
            color: #1C2A22;
        }
        .payment-id {
            font-size: 0.75rem;
            margin-top: 12px;
            color: #4F6B5A;
        }
        .empty-message {
            text-align: center;
            padding: 60px;
            background: #EEF2EE;
            border-radius: 16px;
        }
        @media (max-width: 640px) {
            .page-header { flex-direction: column; align-items: flex-start; }
            .order-header { flex-direction: column; align-items: flex-start; }
            .order-body { padding: 15px; }
            .items-table th, .items-table td { padding: 5px 2px; font-size: 0.9rem; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="page-header">
        <h1>Order History</h1>
        <a href="index.php" class="btn-secondary">Back to Shop</a>
    </div>
    <div id="orders-list"></div>
</div>

<script>
    function formatRands(price) {
        return 'R ' + price.toLocaleString('en-ZA', { minimumFractionDigits: 2 });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    async function loadOrders() {
        const res = await fetch('api/orders.php');
        const orders = await res.json();
        const container = document.getElementById('orders-list');

        if (!orders.length) {
            container.innerHTML = `
                <div class="empty-message">
                    You have no orders yet.<br>
                    <a href="index.php" style="display:inline-block; margin-top:15px; color:#4F6B5A; font-weight:bold;">Start shopping</a>
                </div>
            `;
            return;
        }

        let html = '';
        orders.forEach(order => {
            const orderDate = new Date(order.Created_At).toLocaleString();

            let itemsHtml = `
                <table class="items-table">
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
            `;

            if (order.items && order.items.length) {
                order.items.forEach(item => {
                    const subtotal = item.Price * item.Quantity;
                    itemsHtml += `
                        <tr>
                            <td>${escapeHtml(item.Name)}</td>
                            <td>${item.Quantity}</td>
                            <td>${formatRands(item.Price)}</td>
                            <td>${formatRands(subtotal)}</td>
                        </tr>
                    `;
                });
            } else {
                itemsHtml += '<tr><td colspan="4">No item details available</td></tr>';
            }
            itemsHtml += '</table>';

            html += `
                <div class="order-card">
                    <div class="order-header">
                        <span class="order-id">Order #${order.ID}</span>
                        <span>${orderDate}</span>
                    </div>
                    <div class="order-body">
                        <div class="address-block">
                            <span class="address-label">Shipping Address</span>
                            <span class="address-text">${escapeHtml(order.Address || 'No address provided')}</span>
                        </div>
                        ${itemsHtml}
                        <div class="total-row">Total: ${formatRands(order.Total)}</div>
                        <div class="payment-id">Payment ID: ${escapeHtml(order.PaymentID || 'N/A')}</div>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    loadOrders();
</script>
</body>
</html>