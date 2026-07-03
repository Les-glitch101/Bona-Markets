<?php
// ============================================================
// CHECKOUT PAGE – Bona Markets
// ============================================================
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?redirect=checkout/index.php');
    exit;
}

$isLoggedIn   = true;
$userFullName = $_SESSION['fullname'] ?? '';
$userEmail    = $_SESSION['email']    ?? '';
$userRole     = $_SESSION['role']     ?? 'buyer';

require_once '../../config/database.php';
$user_id = (int) $_SESSION['user_id'];

// Fetch user's saved address from vendor_profiles (if vendor) or users table
$addressHint = '';
$stmt = $pdo->prepare("SELECT city, country FROM vendor_profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$vp = $stmt->fetch();
if ($vp) $addressHint = trim(($vp['city'] ?? '') . ', ' . ($vp['country'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Checkout | Bona Markets</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; background: #ffffff; }
        .field:focus { outline: none; border-color: #4F6B5A; box-shadow: 0 0 0 3px rgba(79,107,90,.12); }
        .field-error { border-color: #dc2626 !important; }
        .err-msg { display:none; color:#dc2626; font-size:.78rem; margin-top:.3rem; }
        .err-msg.show { display:block; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner { animation: spin 1s linear infinite; }
    </style>
</head>
<body class="min-h-screen">

    <!-- ── NAVBAR ── -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="../index.php" class="text-2xl font-bold text-blue-600" style="font-family:'Syne',sans-serif">Bona Markets</a>
            <div class="flex items-center gap-4 text-sm">
                <a href="../cart/index.php" class="text-gray-600 hover:text-blue-600">← Back to Cart</a>
                <span class="text-gray-400">|</span>
                <span class="text-gray-600">👋 <?= htmlspecialchars($userFullName ?: $userEmail) ?></span>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb -->
    <div class="container mx-auto px-4 py-3 max-w-5xl">
        <nav class="text-xs text-gray-400 flex items-center gap-2">
            <span class="text-green-800 font-semibold">1. Cart</span>
            <span>›</span>
            <span class="text-gray-800 font-semibold">2. Checkout</span>
            <span>›</span>
            <span>3. Confirmation</span>
        </nav>
    </div>

    <!-- ── MAIN ── -->
    <div class="container mx-auto px-4 pb-12 max-w-5xl">
        <h1 class="text-2xl font-bold text-gray-800 mb-6" style="font-family:'Syne',sans-serif">Secure Checkout</h1>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

            <!-- LEFT: Forms -->
            <div class="lg:col-span-3">
                <form id="checkoutForm" novalidate>

                    <!-- Shipping Address -->
                    <div class="bg-white rounded-2xl shadow-sm p-6 mb-5">
                        <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-green-800 text-white text-xs flex items-center justify-center font-bold">1</span>
                            Shipping Address
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-gray-700 block mb-1">Full Name <span class="text-red-500">*</span></label>
                                <input id="f_name" type="text" class="field w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm"
                                       placeholder="As it appears on your ID" value="<?= htmlspecialchars($userFullName) ?>" />
                                <p class="err-msg" id="e_name">Please enter your full name.</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="text-sm font-semibold text-gray-700 block mb-1">Street Address <span class="text-red-500">*</span></label>
                                <input id="f_street" type="text" class="field w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm" placeholder="123 Main Street" />
                                <p class="err-msg" id="e_street">Please enter a valid street address.</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-700 block mb-1">City <span class="text-red-500">*</span></label>
                                <input id="f_city" type="text" class="field w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm" placeholder="Johannesburg" />
                                <p class="err-msg" id="e_city">Please enter your city.</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-700 block mb-1">Postal Code <span class="text-red-500">*</span></label>
                                <input id="f_postal" type="text" class="field w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm" placeholder="2001" maxlength="5" />
                                <p class="err-msg" id="e_postal">Please enter a 4–5 digit postal code.</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-700 block mb-1">Province</label>
                                <select id="f_province" class="field w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white">
                                    <option value="">Select province</option>
                                    <option>Gauteng</option><option>Western Cape</option><option>Eastern Cape</option>
                                    <option>KwaZulu-Natal</option><option>Limpopo</option><option>Mpumalanga</option>
                                    <option>North West</option><option>Northern Cape</option><option>Free State</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-700 block mb-1">Phone Number</label>
                                <input id="f_phone" type="tel" class="field w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm" placeholder="+27 82 000 0000" />
                            </div>
                        </div>
                    </div>

                    <!-- Payment Details (Mock) -->
                    <div class="bg-white rounded-2xl shadow-sm p-6 mb-5">
                        <h2 class="font-bold text-gray-800 mb-1 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-green-800 text-white text-xs flex items-center justify-center font-bold">2</span>
                            Payment Details
                        </h2>
                        <p class="text-xs text-gray-400 mb-4 ml-8">🔒 Secured and encrypted. Use any test card number.</p>

                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-semibold text-gray-700 block mb-1">Card Number <span class="text-red-500">*</span></label>
                                <input id="f_card" type="text" class="field w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono tracking-widest"
                                       placeholder="1234 5678 9012 3456" maxlength="19" />
                                <p class="err-msg" id="e_card">Please enter a valid 16-digit card number.</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-semibold text-gray-700 block mb-1">Expiry <span class="text-red-500">*</span></label>
                                    <input id="f_expiry" type="text" class="field w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono"
                                           placeholder="MM/YY" maxlength="5" />
                                    <p class="err-msg" id="e_expiry">Enter a valid expiry (MM/YY).</p>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-gray-700 block mb-1">CVC <span class="text-red-500">*</span></label>
                                    <input id="f_cvc" type="text" class="field w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono"
                                           placeholder="123" maxlength="4" />
                                    <p class="err-msg" id="e_cvc">Enter a valid 3–4 digit CVC.</p>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-700 block mb-1">Name on Card <span class="text-red-500">*</span></label>
                                <input id="f_cardname" type="text" class="field w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm"
                                       placeholder="FULL NAME" value="<?= htmlspecialchars(strtoupper($userFullName)) ?>" />
                                <p class="err-msg" id="e_cardname">Please enter the name on your card.</p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="submitBtn"
                            class="w-full bg-green-800 text-white py-4 rounded-xl font-bold text-base hover:bg-green-700 transition flex items-center justify-center gap-2 disabled:opacity-50">
                        🔒 Place Order
                    </button>
                </form>
            </div>

            <!-- RIGHT: Order Summary -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-20">
                    <h2 class="font-bold text-gray-800 mb-4">Order Summary</h2>
                    <div id="summaryNote" class="text-xs text-gray-400 bg-white border border-gray-100 rounded-lg p-2 mb-3">Loading items…</div>
                    <div id="summaryItems" class="space-y-3 mb-4"></div>
                    <div class="border-t-2 border-green-800 pt-4">
                        <div class="flex justify-between text-sm text-gray-500 mb-1">
                            <span>Subtotal</span><span id="subtotalAmt">R 0.00</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500 mb-3">
                            <span>Shipping</span><span class="text-green-700 font-semibold">Free</span>
                        </div>
                        <div class="flex justify-between font-bold text-gray-800 text-lg">
                            <span>Total</span><span id="totalAmt">R 0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    let checkoutItems = [];

    function fmt(v) {
        return 'R ' + parseFloat(v).toLocaleString('en-ZA', { minimumFractionDigits: 2 });
    }

    // ── Load items from sessionStorage or fall back to cart API ──
    async function loadSummary() {
        const stored = sessionStorage.getItem('checkoutItems');
        if (stored) {
            checkoutItems = JSON.parse(stored);
        } else {
            const res = await fetch('../cart/api.php?action=get');
            checkoutItems = await res.json();
        }

        const note = document.getElementById('summaryNote');
        if (!checkoutItems.length) {
            note.textContent = '⚠ No items selected. Go back to cart.';
            document.getElementById('submitBtn').disabled = true;
            return;
        }
        note.textContent = `${checkoutItems.length} item${checkoutItems.length !== 1 ? 's' : ''} in this order`;

        let total = 0, html = '';
        checkoutItems.forEach(item => {
            const sub = item.price * item.quantity;
            total += sub;
            html += `<div class="flex justify-between items-start text-sm gap-2">
                <span class="text-gray-700 flex-1">${escHtml(item.name)} <span class="text-gray-400">×${item.quantity}</span></span>
                <span class="font-semibold text-gray-800 whitespace-nowrap">${fmt(sub)}</span>
             </div>`;
        });
        document.getElementById('summaryItems').innerHTML = html;
        document.getElementById('subtotalAmt').textContent = fmt(total);
        document.getElementById('totalAmt').textContent    = fmt(total);
    }

    // ── Card formatting ──
    document.getElementById('f_card').addEventListener('input', function() {
        let v = this.value.replace(/\D/g,'');
        this.value = v.replace(/(.{4})/g,'$1 ').trim().slice(0, 19);
        validate('f_card','e_card', v => /^\d{16}$/.test(v.replace(/\s/g,'')));
    });
    document.getElementById('f_expiry').addEventListener('input', function() {
        let v = this.value.replace(/\D/g,'');
        if (v.length >= 2) v = v.slice(0,2) + '/' + v.slice(2,4);
        this.value = v;
        validate('f_expiry','e_expiry', v => /^\d{2}\/\d{2}$/.test(v));
    });
    document.getElementById('f_cvc').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g,'');
        validate('f_cvc','e_cvc', v => /^\d{3,4}$/.test(v));
    });
    document.getElementById('f_postal').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g,'');
    });

    function validate(fieldId, errId, fn) {
        const f = document.getElementById(fieldId);
        const e = document.getElementById(errId);
        if (!f || !e) return fn(f?.value ?? '');
        const ok = fn(f.value);
        f.classList.toggle('field-error', !ok && f.value.length > 0);
        e.classList.toggle('show', !ok && f.value.length > 0);
        return ok;
    }

    function validateAll() {
        let ok = true;
        const rules = [
            ['f_name',     'e_name',     v => v.trim().length >= 2],
            ['f_street',   'e_street',   v => v.trim().length >= 3],
            ['f_city',     'e_city',     v => v.trim().length >= 2],
            ['f_postal',   'e_postal',   v => /^\d{4,5}$/.test(v.trim())],
            ['f_card',     'e_card',     v => /^\d{16}$/.test(v.replace(/\s/g,''))],
            ['f_expiry',   'e_expiry',   v => /^\d{2}\/\d{2}$/.test(v)],
            ['f_cvc',      'e_cvc',      v => /^\d{3,4}$/.test(v)],
            ['f_cardname', 'e_cardname', v => v.trim().length >= 2],
        ];
        rules.forEach(([fid, eid, fn]) => {
            const f = document.getElementById(fid);
            const e = document.getElementById(eid);
            const pass = fn(f.value);
            f.classList.toggle('field-error', !pass);
            e.classList.toggle('show', !pass);
            if (!pass) ok = false;
        });
        return ok;
    }

    // ── Submit ──
    document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        if (!validateAll()) return;
        if (!checkoutItems.length) { alert('No items to checkout. Please go back to your cart.'); return; }

        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="spinner w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> Processing…';

        const address = [
            document.getElementById('f_street').value.trim(),
            document.getElementById('f_city').value.trim(),
            document.getElementById('f_province').value,
            document.getElementById('f_postal').value.trim(),
        ].filter(Boolean).join(', ');

        const mockPaymentId = 'mock_' + Date.now() + '_' + Math.random().toString(36).slice(2, 8);

        try {
            const res = await fetch('../orders/create.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    address:       address,
                    payment_id:    mockPaymentId,
                    buyer_name:    document.getElementById('f_name').value.trim(),
                    phone:         document.getElementById('f_phone').value.trim(),
                    selected_items: checkoutItems,
                })
            });
            const data = await res.json();
            if (data.success) {
                sessionStorage.removeItem('checkoutItems');
                sessionStorage.removeItem('checkoutIds');
                window.location.href = 'processing.php?order_id=' + data.order_id;
            } else {
                throw new Error(data.error || 'Order creation failed');
            }
        } catch (err) {
            alert('Error: ' + err.message);
            btn.disabled = false;
            btn.innerHTML = '🔒 Place Order';
        }
    });

    function escHtml(s) {
        return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
    }

    loadSummary();
    </script>

</body>
</html>