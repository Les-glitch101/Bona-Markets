<?php
// ============================================================
// CART PAGE – Bona Markets
// ============================================================
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?redirect=cart/index.php');
    exit;
}

$isLoggedIn   = true;
$userFullName = $_SESSION['fullname'] ?? '';
$userEmail    = $_SESSION['email']    ?? '';
$userRole     = $_SESSION['role']     ?? 'buyer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shopping Cart | Bona Markets</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; background: #ffffff; }
        .cart-item { transition: box-shadow .15s; }
        .cart-item:hover { box-shadow: 0 4px 20px rgba(28,42,34,.1); }
        .qty-btn { transition: all .15s; }
        .qty-btn:hover { background: #4F6B5A; color: #fff; }
        /* Delete modal */
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(28,42,34,.65); z-index:9999; align-items:center; justify-content:center; }
        .modal-overlay.active { display:flex; }
        @keyframes popIn { 0%{transform:scale(.9);opacity:0} 100%{transform:scale(1);opacity:1} }
        .modal-box { animation: popIn .25s ease; }
        /* Toast */
        #toast { display:none; position:fixed; bottom:1.5rem; right:1.5rem; z-index:9998;
                 padding:.75rem 1.25rem; border-radius:10px; font-weight:600; font-size:.875rem;
                 box-shadow:0 4px 20px rgba(0,0,0,.2); }
    </style>
</head>
<body class="min-h-screen">

    <!-- ── NAVBAR ── -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="../index.php" class="text-2xl font-bold text-blue-600" style="font-family:'Syne',sans-serif">Bona Markets</a>
            <div class="hidden md:flex items-center space-x-6 text-sm font-medium">
                <a href="../products/index.php" class="text-gray-600 hover:text-blue-600">Shop</a>
                <a href="index.php" class="text-blue-600 font-semibold">Cart 🛒</a>
                <a href="../orders/index.php" class="text-gray-600 hover:text-blue-600">My Orders</a>
                <?php if ($userRole === 'vendor'): ?>
                    <a href="../vendor/dashboard.php" class="text-gray-600 hover:text-blue-600">Dashboard</a>
                <?php endif; ?>
                <span class="text-gray-500">👋 <?= htmlspecialchars($userFullName ?: $userEmail) ?></span>
                <a href="../logout.php" class="text-red-500 hover:text-red-700">Logout</a>
            </div>
        </div>
    </nav>

    <!-- ── MAIN ── -->
    <div class="container mx-auto px-4 py-8 max-w-5xl">

        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800" style="font-family:'Syne',sans-serif">Shopping Cart</h1>
                <p class="text-sm text-gray-500 mt-1">Review and manage your selected items.</p>
            </div>
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                    <input type="checkbox" id="selectAll" class="w-4 h-4 accent-green-700" />
                    Select All
                </label>
                <a href="../products/index.php"
                   class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-700 transition">
                    Continue Shopping
                </a>
            </div>
        </div>

        <!-- Cart items inject here -->
        <div id="cart-items"></div>

        <!-- Summary -->
        <div id="cart-summary" style="display:none" class="mt-6 border-t-2 border-green-800 pt-6">
            <div class="flex flex-col items-end gap-3">
                <p class="text-sm text-gray-500" id="selectedInfo">Selected: 0 items</p>
                <p class="text-2xl font-bold text-gray-800">Total: <span id="total-amount">R 0.00</span></p>
                <button id="checkout-btn" disabled
                        class="bg-green-800 text-white px-8 py-3 rounded-xl font-bold text-base hover:bg-green-700 transition disabled:opacity-40 disabled:cursor-not-allowed">
                    Proceed to Checkout →
                </button>
            </div>
        </div>
    </div>

    <!-- ── DELETE CONFIRMATION MODAL ── -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box bg-white rounded-2xl p-8 max-w-sm w-11/12 text-center shadow-2xl">
            <div class="text-4xl mb-3">🗑️</div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Remove Item?</h3>
            <p class="text-gray-500 text-sm mb-6">Are you sure you want to remove this item from your cart?</p>
            <div class="flex gap-3 justify-center">
                <button id="cancelDelete" class="flex-1 bg-gray-100 text-gray-700 py-2.5 rounded-lg font-semibold hover:bg-gray-200 transition">Cancel</button>
                <button id="confirmDelete" class="flex-1 bg-red-600 text-white py-2.5 rounded-lg font-semibold hover:bg-red-700 transition">Remove</button>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div id="toast"></div>

    <script>
    let cartItems = [];
    let selectedItems = new Set();
    let pendingDeleteId = null;

    function fmt(price) {
        return 'R ' + parseFloat(price).toLocaleString('en-ZA', { minimumFractionDigits: 2 });
    }

    // ── TOAST ──
    function showToast(msg, type = 'success') {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.style.background = type === 'success' ? '#14532d' : '#991b1b';
        t.style.color = '#fff';
        t.style.display = 'block';
        clearTimeout(t._timer);
        t._timer = setTimeout(() => { t.style.display = 'none'; }, 3000);
    }

    // ── DELETE MODAL ──
    function showDeleteModal(id) {
        pendingDeleteId = id;
        document.getElementById('deleteModal').classList.add('active');
    }
    function hideDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
        pendingDeleteId = null;
    }
    document.getElementById('cancelDelete').addEventListener('click', hideDeleteModal);
    document.getElementById('deleteModal').addEventListener('click', e => { if (e.target === e.currentTarget) hideDeleteModal(); });

    document.getElementById('confirmDelete').addEventListener('click', async function() {
        if (pendingDeleteId === null) return;
        const id = pendingDeleteId;
        hideDeleteModal();
        await fetch(`api.php?action=remove&cart_item_id=${id}`, { method: 'DELETE' });
        selectedItems.delete(id);
        showToast('Item removed from cart.');
        loadCart();
    });

    // ── LOAD CART ──
    async function loadCart() {
        const res = await fetch('api.php?action=get');
        cartItems = await res.json();
        const container   = document.getElementById('cart-items');
        const summaryDiv  = document.getElementById('cart-summary');

        if (!cartItems.length) {
            container.innerHTML = `
                <div class="bg-white rounded-2xl shadow-sm p-14 text-center">
                    <div class="text-5xl mb-4">🛒</div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Your cart is empty</h2>
                    <p class="text-gray-500 mb-6">Browse our products and add items you love.</p>
                    <a href="../products/index.php" class="inline-block bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition">Shop Now</a>
                </div>`;
            summaryDiv.style.display = 'none';
            selectedItems.clear();
            return;
        }

        let html = '';
        cartItems.forEach(item => {
            const checked  = selectedItems.has(item.id);
            const imgSrc = item.image_url ? `/Bona-Markets/Public/${item.image_url}` : '';
            const imgHtml  = imgSrc
                ? `<img src="${imgSrc}" alt="${item.name}" class="w-20 h-20 object-cover rounded-xl bg-gray-50 flex-shrink-0" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">`
                : '';
            const fallback = `<div class="w-20 h-20 rounded-xl bg-gray-50 flex items-center justify-center text-3xl flex-shrink-0" ${imgSrc ? 'style="display:none"' : ''}>🛍️</div>`;

            html += `
            <div class="cart-item bg-white rounded-2xl shadow-sm mb-4 p-4 flex flex-wrap items-center gap-4 border-l-4 border-green-800" data-id="${item.id}">
                <input type="checkbox" class="item-checkbox w-4 h-4 accent-green-700 flex-shrink-0 cursor-pointer" data-id="${item.id}" ${checked ? 'checked' : ''}>
                ${imgHtml}${fallback}
                <div class="flex-1 min-w-32">
                    <p class="font-bold text-gray-800 text-base">${escHtml(item.name)}</p>
                    <p class="text-green-800 font-semibold text-sm mt-0.5">${fmt(item.price)} each</p>
                    ${item.category ? `<span class="inline-block text-xs bg-blue-50 text-blue-700 rounded-full px-2 py-0.5 mt-1">${escHtml(item.category)}</span>` : ''}
                </div>
                <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-3 py-2">
                    <button class="qty-btn w-8 h-8 rounded-lg bg-white border border-gray-200 font-bold text-lg flex items-center justify-center" onclick="updateQty(${item.id}, ${item.quantity - 1})">−</button>
                    <span class="w-8 text-center font-semibold">${item.quantity}</span>
                    <button class="qty-btn w-8 h-8 rounded-lg bg-white border border-gray-200 font-bold text-lg flex items-center justify-center" onclick="updateQty(${item.id}, ${item.quantity + 1})">+</button>
                </div>
                <div class="font-bold text-gray-800 min-w-24 text-right">${fmt(item.price * item.quantity)}</div>
                <button class="text-red-500 hover:text-red-700 transition ml-2 text-sm font-semibold" onclick="showDeleteModal(${item.id})">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                </button>
            </div>`;
        });

        container.innerHTML = html;
        summaryDiv.style.display = 'block';

        // Auto-select all on first load
        if (selectedItems.size === 0) {
            cartItems.forEach(i => selectedItems.add(i.id));
        }

        // Wire checkboxes
        document.querySelectorAll('.item-checkbox').forEach(cb => {
            const id = parseInt(cb.dataset.id);
            cb.checked = selectedItems.has(id);
            cb.addEventListener('change', function() {
                if (this.checked) selectedItems.add(id); else selectedItems.delete(id);
                updateSummary();
                syncSelectAll();
            });
        });

        // Select All
        document.getElementById('selectAll').addEventListener('change', function() {
            document.querySelectorAll('.item-checkbox').forEach(cb => {
                cb.checked = this.checked;
                const id = parseInt(cb.dataset.id);
                if (this.checked) selectedItems.add(id); else selectedItems.delete(id);
            });
            updateSummary();
        });

        updateSummary();
        syncSelectAll();
    }

    function updateSummary() {
        let total = 0, count = 0;
        cartItems.forEach(item => {
            if (selectedItems.has(item.id)) {
                total += item.price * item.quantity;
                count++;
            }
        });
        document.getElementById('total-amount').textContent = fmt(total);
        document.getElementById('selectedInfo').textContent  = `Selected: ${count} item${count !== 1 ? 's' : ''}`;
        document.getElementById('checkout-btn').disabled = count === 0;
    }

    function syncSelectAll() {
        const cbs = document.querySelectorAll('.item-checkbox');
        const checked = [...cbs].filter(c => c.checked).length;
        document.getElementById('selectAll').checked = cbs.length > 0 && checked === cbs.length;
    }

    async function updateQty(cartItemId, newQty) {
        if (newQty < 0) return;
        if (newQty === 0) { showDeleteModal(cartItemId); return; }
        await fetch('api.php?action=update', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cart_item_id: cartItemId, quantity: newQty })
        });
        loadCart();
    }

    // ── CHECKOUT ──
    document.getElementById('checkout-btn').addEventListener('click', function() {
        if (this.disabled) return;
        const selected = cartItems.filter(i => selectedItems.has(i.id));
        sessionStorage.setItem('checkoutItems', JSON.stringify(selected));
        sessionStorage.setItem('checkoutIds',   JSON.stringify([...selectedItems]));
        window.location.href = '../checkout/index.php';
    });

    function escHtml(str) {
        return String(str).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
    }

    loadCart();
    </script>

</body>
</html>