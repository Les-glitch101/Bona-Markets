<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - BonaMarkets</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Layout and colors match Nordic Pine palette */
        body {
            background: #FDFDFB;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: 'Poppins', system-ui, sans-serif;
            padding: 20px;
        }
        .checkout-container {
            max-width: 700px;
            width: 100%;
            background: #EEF2EE;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 8px 30px rgba(28, 42, 34, 0.2);
            margin: 0 auto;
        }
        .checkout-container h1 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #1C2A22;
            border-left: 5px solid #4F6B5A;
            padding-left: 15px;
        }
        .cart-summary {
            background: #FDFDFB;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 28px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 4px 0;
            color: #1C2A22;
        }
        .total-line {
            border-top: 2px solid #4F6B5A;
            margin-top: 12px;
            padding-top: 12px;
            font-weight: bold;
            font-size: 1.2rem;
            color: #1C2A22;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1C2A22;
        }
        .form-group label .required {
            color: #CFD8CF;
            font-weight: 700;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 14px 18px;
            border-radius: 8px;
            border: 2px solid #CFD8CF;
            background: #FDFDFB;
            font-size: 1rem;
            transition: 0.2s;
            font-family: inherit;
            color: #1C2A22;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #4F6B5A;
            box-shadow: 0 0 0 3px rgba(79, 107, 90, 0.15);
        }
        .form-group textarea {
            resize: vertical;
            min-height: 60px;
        }
        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #4F6B5A;
        }
        .address-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .address-row .form-group {
            flex: 1;
            min-width: 150px;
        }
        .card-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .card-row .form-group {
            flex: 1;
            min-width: 120px;
        }
        .checkout-btn {
            background: #4F6B5A;
            border: none;
            padding: 16px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1.1rem;
            cursor: pointer;
            width: 100%;
            color: #FDFDFB;
            transition: 0.25s;
            margin-top: 10px;
        }
        .checkout-btn:hover {
            background: #CFD8CF;
            color: #1C2A22;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 107, 90, 0.3);
        }
        .checkout-btn:disabled {
            background: #CFD8CF;
            color: #4F6B5A;
            cursor: not-allowed;
            transform: none;
        }
        .back-link {
            color: #4F6B5A;
            display: inline-block;
            margin-top: 20px;
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
        .selected-items-note {
            background: #EEF2EE;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: #1C2A22;
            border-left: 4px solid #4F6B5A;
        }
        .error-message {
            color: #CFD8CF;
            font-size: 0.85rem;
            margin-top: 5px;
            display: none;
        }
        .error-message.visible {
            display: block;
        }
        .input-error {
            border-color: #CFD8CF !important;
        }
        .test-card-note {
            background: #EEF2EE;
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 0.85rem;
            color: #1C2A22;
            border-left: 4px solid #4F6B5A;
            margin-top: 5px;
        }
        .test-card-note strong {
            color: #4F6B5A;
        }
        @media (max-width: 640px) {
            .checkout-container { padding: 20px; }
            .address-row { flex-direction: column; gap: 0; }
            .card-row { flex-direction: column; gap: 0; }
        }
    </style>
</head>
<body>
<div class="checkout-container">
    <h1>Secure Checkout</h1>
    
    <div class="selected-items-note" id="selectedItemsNote">Loading selected items...</div>
    <div id="cart-summary" class="cart-summary">Loading cart...</div>

    <form id="checkout-form">
        <!-- Address fields -->
        <div class="form-group">
            <label for="street">Street Address <span class="required">*</span></label>
            <input type="text" id="street" placeholder="" required>
            <div class="error-message" id="street-error">Please enter your street address.</div>
        </div>

        <div class="form-group">
            <label for="city">City <span class="required">*</span></label>
            <input type="text" id="city" placeholder="" required>
            <div class="error-message" id="city-error">Please enter your city.</div>
        </div>

        <div class="address-row">
            <div class="form-group">
                <label for="postal_code">Postal Code <span class="required">*</span></label>
                <input type="text" id="postal_code" placeholder="" required>
                <div class="error-message" id="postal-error">Please enter a valid postal code (4-5 digits).</div>
            </div>
        </div>

        <!-- Mock card fields -->
        <div class="form-group">
            <label for="card_number">Card Number <span class="required">*</span></label>
            <input type="text" id="card_number" placeholder="" maxlength="19" required>
            <div class="error-message" id="card-error">Please enter a valid 16-digit card number.</div>
        </div>
        <div class="card-row">
            <div class="form-group">
                <label for="expiry">Expiry Date <span class="required">*</span></label>
                <input type="text" id="expiry" placeholder="" maxlength="5" required>
                <div class="error-message" id="expiry-error">Please enter a valid expiry date (MM/YY).</div>
            </div>
            <div class="form-group">
                <label for="cvc">CVC <span class="required">*</span></label>
                <input type="text" id="cvc" placeholder="" maxlength="4" required>
                <div class="error-message" id="cvc-error">Please enter a valid 3 or 4 digit CVC.</div>
            </div>
        </div>

       

        <button type="submit" class="checkout-btn" id="submit-btn">Place Order</button>
    </form>

    <a href="cart.php" class="back-link">Back to Cart</a>
</div>

<script>
    let selectedCartItems = [];

    function formatRands(price) {
        return 'R ' + price.toLocaleString('en-ZA', { minimumFractionDigits: 2 });
    }

    // ---- Card validation ----
    function validateCardNumber(cardNumber) {
        const clean = cardNumber.replace(/\s/g, '');
        return /^[0-9]{16}$/.test(clean);
    }

    function validateExpiry(expiry) {
        if (!/^\d{2}\/\d{2}$/.test(expiry)) return false;
        const parts = expiry.split('/');
        const month = parseInt(parts[0]);
        const year = parseInt(parts[1]);
        if (month < 1 || month > 12) return false;
        const now = new Date();
        const currentYear = now.getFullYear() % 100;
        const currentMonth = now.getMonth() + 1;
        if (year < currentYear) return false;
        if (year === currentYear && month < currentMonth) return false;
        return true;
    }

    function validateCVC(cvc) {
        return /^[0-9]{3,4}$/.test(cvc);
    }

    // ---- Card input formatting ----
    document.getElementById('card_number').addEventListener('input', function() {
        let value = this.value.replace(/\s/g, '');
        value = value.replace(/[^0-9]/g, '');
        let formatted = '';
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) formatted += ' ';
            formatted += value[i];
        }
        this.value = formatted;
        const errorDiv = document.getElementById('card-error');
        if (validateCardNumber(this.value)) {
            this.classList.remove('input-error');
            errorDiv.classList.remove('visible');
        } else if (this.value.length > 0) {
            this.classList.add('input-error');
            errorDiv.classList.add('visible');
        } else {
            this.classList.remove('input-error');
            errorDiv.classList.remove('visible');
        }
    });

    document.getElementById('expiry').addEventListener('input', function() {
        let value = this.value.replace(/[^0-9]/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        this.value = value;
        const errorDiv = document.getElementById('expiry-error');
        if (validateExpiry(this.value)) {
            this.classList.remove('input-error');
            errorDiv.classList.remove('visible');
        } else if (this.value.length > 0) {
            this.classList.add('input-error');
            errorDiv.classList.add('visible');
        } else {
            this.classList.remove('input-error');
            errorDiv.classList.remove('visible');
        }
    });

    document.getElementById('cvc').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        const errorDiv = document.getElementById('cvc-error');
        if (validateCVC(this.value)) {
            this.classList.remove('input-error');
            errorDiv.classList.remove('visible');
        } else if (this.value.length > 0) {
            this.classList.add('input-error');
            errorDiv.classList.add('visible');
        } else {
            this.classList.remove('input-error');
            errorDiv.classList.remove('visible');
        }
    });

    // ---- Address validation ----
    function validateStreet(value) {
        return value.trim().length >= 3 && /[a-zA-Z]/.test(value);
    }

    function validateCity(value) {
        return value.trim().length >= 2 && /[a-zA-Z]/.test(value);
    }

    function validatePostalCode(value) {
        return /^[0-9]{4,5}$/.test(value.trim());
    }

    function setupValidation(inputId, errorId, validator) {
        const input = document.getElementById(inputId);
        const error = document.getElementById(errorId);
        if (!input) return;
        
        input.addEventListener('input', function() {
            if (validator(this.value)) {
                this.classList.remove('input-error');
                error.classList.remove('visible');
            } else if (this.value.length > 0) {
                this.classList.add('input-error');
                error.classList.add('visible');
            } else {
                this.classList.remove('input-error');
                error.classList.remove('visible');
            }
        });
    }

    setupValidation('street', 'street-error', validateStreet);
    setupValidation('city', 'city-error', validateCity);
    setupValidation('postal_code', 'postal-error', validatePostalCode);

    // ---- Load selected cart items ----
    async function loadSelectedItems() {
        const storedItems = localStorage.getItem('selectedCartItems');
        
        if (storedItems) {
            selectedCartItems = JSON.parse(storedItems);
        } else {
            const res = await fetch('api/cart.php');
            const allItems = await res.json();
            selectedCartItems = allItems;
        }

        const noteDiv = document.getElementById('selectedItemsNote');
        if (selectedCartItems.length === 0) {
            noteDiv.innerHTML = '<strong>No items selected.</strong> Please go back to your cart and select items to checkout.';
            document.getElementById('submit-btn').disabled = true;
            return;
        } else {
            noteDiv.innerHTML = '<strong>Checking out:</strong> ' + selectedCartItems.length + ' item(s) selected.';
        }

        const container = document.getElementById('cart-summary');
        let html = '<h3>Order Summary</h3>';
        let total = 0;
        selectedCartItems.forEach(item => {
            total += item.price * item.quantity;
            html += `<div class="summary-item">
                        <span>${item.name} x ${item.quantity}</span>
                        <span>${formatRands(item.price * item.quantity)}</span>
                     </div>`;
        });
        html += `<div class="total-line">Total: ${formatRands(total)}</div>`;
        container.innerHTML = html;
    }

    // ---- Form submit ----
    document.getElementById('checkout-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validate all fields
        const street = document.getElementById('street').value.trim();
        const city = document.getElementById('city').value.trim();
        const postalCode = document.getElementById('postal_code').value.trim();
        const cardNumber = document.getElementById('card_number').value.trim();
        const expiry = document.getElementById('expiry').value.trim();
        const cvc = document.getElementById('cvc').value.trim();

        let hasError = false;

        if (!validateStreet(street)) {
            document.getElementById('street').classList.add('input-error');
            document.getElementById('street-error').classList.add('visible');
            document.getElementById('street').focus();
            hasError = true;
        }

        if (!validateCity(city)) {
            document.getElementById('city').classList.add('input-error');
            document.getElementById('city-error').classList.add('visible');
            if (!hasError) { document.getElementById('city').focus(); }
            hasError = true;
        }

        if (!validatePostalCode(postalCode)) {
            document.getElementById('postal_code').classList.add('input-error');
            document.getElementById('postal-error').classList.add('visible');
            if (!hasError) { document.getElementById('postal_code').focus(); }
            hasError = true;
        }

        if (!validateCardNumber(cardNumber)) {
            document.getElementById('card_number').classList.add('input-error');
            document.getElementById('card-error').classList.add('visible');
            if (!hasError) { document.getElementById('card_number').focus(); }
            hasError = true;
        }

        if (!validateExpiry(expiry)) {
            document.getElementById('expiry').classList.add('input-error');
            document.getElementById('expiry-error').classList.add('visible');
            if (!hasError) { document.getElementById('expiry').focus(); }
            hasError = true;
        }

        if (!validateCVC(cvc)) {
            document.getElementById('cvc').classList.add('input-error');
            document.getElementById('cvc-error').classList.add('visible');
            if (!hasError) { document.getElementById('cvc').focus(); }
            hasError = true;
        }

        if (hasError) {
            alert('Please fill in all required fields correctly.');
            return;
        }

        if (selectedCartItems.length === 0) {
            alert('No items selected. Please go back to your cart.');
            return;
        }

        const fullAddress = street + ', ' + city + ', ' + postalCode;
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Processing...';

        try {
            // Create order with mock payment ID
            const mockPaymentId = 'mock_pay_' + Date.now() + '_' + Math.random().toString(36).substr(2, 6);
            
            const orderRes = await fetch('api/orders.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    address: fullAddress,
                    payment_id: mockPaymentId,
                    selected_items: selectedCartItems
                })
            });
            const orderData = await orderRes.json();
            
            if (orderData.success) {
                // Pass order ID to processing page (hidden from user)
                window.location.href = 'processing.php?order_id=' + orderData.order_id;
            } else {
                throw new Error(orderData.error || 'Order creation failed');
            }
        } catch (error) {
            alert('Error: ' + error.message);
            submitBtn.disabled = false;
            submitBtn.textContent = 'Place Order';
        }
    });

    loadSelectedItems();
</script>
</body>
</html>