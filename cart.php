<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - BonaMarkets</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .cart-container {
            max-width: 1100px;
            margin: 0 auto;
            background: #EEF2EE;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 8px 30px rgba(28, 42, 34, 0.15);
        }
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .cart-header h1 {
            font-size: 1.9rem;
            color: #1C2A22;
            border-left: 5px solid #4F6B5A;
            padding-left: 15px;
        }
        .cart-header .cart-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .cart-header .select-all {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1C2A22;
            font-weight: 500;
            margin-top: 10px;
        }
        .cart-header .select-all input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #4F6B5A;
            cursor: pointer;
        }
        .continue-shopping-btn {
            display: inline-block;
            background: #1C2A22;
            color: #FDFDFB;
            padding: 8px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.25s;
            font-size: 0.9rem;
        }
        .continue-shopping-btn:hover {
            background: #4F6B5A;
            transform: scale(1.02);
        }
        .cart-item {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #FDFDFB;
            border-radius: 12px;
            margin-bottom: 15px;
            padding: 12px 18px;
            flex-wrap: wrap;
            border-left: 6px solid #4F6B5A;
            transition: 0.1s;
        }
        .cart-item:hover {
            background: #EEF2EE;
        }
        .cart-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #4F6B5A;
            cursor: pointer;
            flex-shrink: 0;
        }
        .cart-item img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            background: #CFD8CF;
        }
        .item-details {
            flex: 2;
            min-width: 150px;
        }
        .item-name {
            font-weight: 700;
            font-size: 1.05rem;
            color: #1C2A22;
        }
        .item-price {
            color: #4F6B5A;
            font-weight: 600;
        }
        .item-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #CFD8CF;
            padding: 6px 12px;
            border-radius: 8px;
        }
        .qty-btn {
            background: #4F6B5A;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            color: #FDFDFB;
            transition: 0.2s;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qty-btn:hover {
            background: #CFD8CF;
            color: #1C2A22;
            transform: scale(1.05);
        }
        .item-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .item-total {
            font-weight: bold;
            min-width: 100px;
            text-align: right;
            color: #1C2A22;
        }
        .delete-btn {
            background: #1C2A22;
            border: none;
            color: #FDFDFB;
            padding: 6px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            transition: 0.25s;
            font-family: 'Poppins', system-ui, sans-serif;
        }
        .delete-btn:hover {
            background: #4F6B5A;
            color: #FDFDFB;
            transform: scale(1.05);
        }
        .cart-summary-area {
            margin-top: 30px;
            text-align: right;
            border-top: 2px solid #4F6B5A;
            padding-top: 20px;
        }
        .cart-total {
            font-size: 1.7rem;
            font-weight: 800;
            color: #1C2A22;
        }
        .checkout-btn-cart {
            background: #4F6B5A;
            border: none;
            padding: 14px 40px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1.1rem;
            cursor: pointer;
            margin-top: 15px;
            color: #FDFDFB;
            transition: 0.25s;
        }
        .checkout-btn-cart:hover {
            background: #CFD8CF;
            color: #1C2A22;
            transform: scale(1.02);
            box-shadow: 0 4px 20px rgba(79, 107, 90, 0.3);
        }
        .checkout-btn-cart:disabled {
            background: #CFD8CF;
            color: #4F6B5A;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .selected-info {
            color: #4F6B5A;
            font-size: 0.9rem;
            margin-top: 10px;
        }
        .empty-cart {
            text-align: center;
            padding: 50px 30px;
            background: #FDFDFB;
            border-radius: 12px;
        }
        .empty-cart p {
            font-size: 1.2rem;
            color: #1C2A22;
            margin-bottom: 20px;
        }
        .shop-now-btn {
            display: inline-block;
            background: #4F6B5A;
            color: #FDFDFB;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1rem;
            transition: 0.25s;
            border: none;
        }
        .shop-now-btn:hover {
            background: #CFD8CF;
            color: #1C2A22;
            transform: scale(1.02);
            box-shadow: 0 4px 15px rgba(79, 107, 90, 0.3);
        }
        .cart-header-left {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* ---------- CUSTOM MODAL ---------- */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(28, 42, 34, 0.7);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.active {
            display: flex;
        }
        .modal-box {
            background: #FDFDFB;
            border-radius: 16px;
            padding: 35px 40px;
            max-width: 450px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(28, 42, 34, 0.3);
            animation: modalFadeIn 0.3s ease-out;
        }
        @keyframes modalFadeIn {
            0% { transform: scale(0.9); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .modal-box .modal-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1C2A22;
            margin-bottom: 10px;
        }
        .modal-box .modal-message {
            color: #4F6B5A;
            font-size: 1rem;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        .modal-box .modal-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        .modal-box .modal-actions button {
            padding: 10px 30px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: 0.2s;
            font-family: 'Poppins', system-ui, sans-serif;
        }
        .modal-box .modal-actions .confirm-delete {
            background: #1C2A22;
            color: #FDFDFB;
        }
        .modal-box .modal-actions .confirm-delete:hover {
            background: #4F6B5A;
            color: #FDFDFB;
            transform: scale(1.02);
        }
        .modal-box .modal-actions .cancel-delete {
            background: #CFD8CF;
            color: #1C2A22;
        }
        .modal-box .modal-actions .cancel-delete:hover {
            background: #EEF2EE;
            transform: scale(1.02);
        }

        @media (max-width: 700px) {
            .cart-item { flex-direction: column; align-items: flex-start; }
            .item-total { text-align: left; margin-top: 8px; }
            .cart-header { flex-direction: column; align-items: flex-start; }
            .cart-header .cart-actions { width: 100%; justify-content: space-between; }
        }
    </style>
</head>
<body>
<div class="cart-container">
    <div class="cart-header">
        <div class="cart-header-left">
            <h1>Shopping Cart</h1>
            <div class="select-all">
                <input type="checkbox" id="selectAll">
                <label for="selectAll">Select All</label>
            </div>
        </div>
        <div class="cart-actions">
            <a href="index.php" class="continue-shopping-btn">Continue Shopping</a>
        </div>
    </div>
    <div id="cart-items"></div>
    <div id="cart-summary" style="display: none;">
        <div class="cart-summary-area">
            <div class="selected-info" id="selectedInfo">Selected: 0 items</div>
            <div class="cart-total">Total: <span id="total-amount">R 0.00</span></div>
            <button id="checkout-btn" class="checkout-btn-cart">Proceed to Checkout</button>
        </div>
    </div>
</div>

<!-- ===== CUSTOM DELETE CONFIRMATION MODAL ===== -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-title">Delete Item</div>
        <div class="modal-message">Are you sure you want to remove this item from your cart?</div>
        <div class="modal-actions">
            <button class="cancel-delete" id="cancelDelete">Cancel</button>
            <button class="confirm-delete" id="confirmDelete">Delete</button>
        </div>
    </div>
</div>

<script>
    let cartItems = [];
    let selectedItems = new Set();
    let pendingDeleteId = null;

    function formatRands(price) {
        return 'R ' + price.toLocaleString('en-ZA', { minimumFractionDigits: 2 });
    }

    // -------- MODAL FUNCTIONS --------
    function showDeleteModal(itemId) {
        pendingDeleteId = itemId;
        document.getElementById('deleteModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function hideDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
        document.body.style.overflow = '';
        pendingDeleteId = null;
    }

    document.getElementById('cancelDelete').addEventListener('click', hideDeleteModal);
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) hideDeleteModal();
    });

    document.getElementById('confirmDelete').addEventListener('click', async function() {
        if (pendingDeleteId !== null) {
            await fetch(`api/cart.php?cart_item_id=${pendingDeleteId}`, { method: 'DELETE' });
            selectedItems.delete(pendingDeleteId);
            pendingDeleteId = null;
            hideDeleteModal();
            loadCart();
        }
    });

    // -------- LOAD CART --------
    async function loadCart() {
        const response = await fetch('api/cart.php');
        cartItems = await response.json();
        const container = document.getElementById('cart-items');
        const summaryDiv = document.getElementById('cart-summary');
        const totalSpan = document.getElementById('total-amount');
        const selectedInfo = document.getElementById('selectedInfo');

        if (!cartItems.length) {
            container.innerHTML = `
                <div class="empty-cart">
                    <p>Your cart is empty.</p>
                    <a href="index.php" class="shop-now-btn">Shop Now</a>
                </div>
            `;
            summaryDiv.style.display = 'none';
            selectedItems.clear();
            return;
        }

        let html = '';
        cartItems.forEach(item => {
            const isChecked = selectedItems.has(item.cart_item_id);
            const itemTotal = item.price * item.quantity;
            const imgUrl = item.image ? item.image : 'assets/placeholder.jpg';
            html += `
                <div class="cart-item" data-item-id="${item.cart_item_id}">
                    <input type="checkbox" class="item-checkbox" data-id="${item.cart_item_id}" ${isChecked ? 'checked' : ''}>
                    <img src="${imgUrl}" alt="${item.name}" onerror="this.src='assets/placeholder.jpg'">
                    <div class="item-details">
                        <div class="item-name">${item.name}</div>
                        <div class="item-price">${formatRands(item.price)} each</div>
                    </div>
                    <div class="item-quantity">
                        <button class="qty-btn" onclick="updateQty(${item.cart_item_id}, ${item.quantity - 1})">-</button>
                        <span style="min-width:35px; text-align:center;">${item.quantity}</span>
                        <button class="qty-btn" onclick="updateQty(${item.cart_item_id}, ${item.quantity + 1})">+</button>
                    </div>
                    <div class="item-actions">
                        <div class="item-total">${formatRands(itemTotal)}</div>
                        <button class="delete-btn" onclick="showDeleteModal(${item.cart_item_id})">Delete</button>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;

        document.querySelectorAll('.item-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                const id = parseInt(this.dataset.id);
                if (this.checked) {
                    selectedItems.add(id);
                } else {
                    selectedItems.delete(id);
                }
                updateSummary();
                updateSelectAllState();
            });
        });

        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
                const id = parseInt(cb.dataset.id);
                if (this.checked) {
                    selectedItems.add(id);
                } else {
                    selectedItems.delete(id);
                }
            });
            updateSummary();
        });

        if (selectedItems.size === 0 && cartItems.length > 0) {
            cartItems.forEach(item => selectedItems.add(item.cart_item_id));
            document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = true);
            document.getElementById('selectAll').checked = true;
        }

        updateSummary();
        summaryDiv.style.display = 'block';
        updateSelectAllState();
    }

    function updateSummary() {
        const totalSpan = document.getElementById('total-amount');
        const selectedInfo = document.getElementById('selectedInfo');
        const checkoutBtn = document.getElementById('checkout-btn');

        let total = 0;
        let count = 0;
        cartItems.forEach(item => {
            if (selectedItems.has(item.cart_item_id)) {
                total += item.price * item.quantity;
                count++;
            }
        });

        totalSpan.innerText = formatRands(total);
        selectedInfo.innerText = 'Selected: ' + count + ' item' + (count !== 1 ? 's' : '');
        checkoutBtn.disabled = count === 0;
    }

    function updateSelectAllState() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const selectAll = document.getElementById('selectAll');
        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
        if (checkboxes.length > 0 && checkedCount === checkboxes.length) {
            selectAll.checked = true;
        } else {
            selectAll.checked = false;
        }
    }

    async function updateQty(cartItemId, newQty) {
        if (newQty < 0) return;
        await fetch('api/cart.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cart_item_id: cartItemId, quantity: newQty })
        });
        if (newQty === 0) {
            selectedItems.delete(cartItemId);
        }
        loadCart();
    }

    document.getElementById('checkout-btn').addEventListener('click', function() {
        if (this.disabled) return;
        const selectedData = cartItems.filter(item => selectedItems.has(item.cart_item_id));
        localStorage.setItem('selectedCartItems', JSON.stringify(selectedData));
        localStorage.setItem('selectedCartIds', JSON.stringify(Array.from(selectedItems)));
        window.location.href = 'checkout.php';
    });

    loadCart();
</script>
</body>
</html>