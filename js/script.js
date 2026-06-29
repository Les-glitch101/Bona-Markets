let currentIndex = 0;

function initSlider() {
    const slides = document.getElementById('slides');
    if (!slides) return;
    
    const totalSlides = document.querySelectorAll('.slide').length;
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const dotsContainer = document.getElementById('dots');

    function updateSlider() {
        slides.style.transform = `translateX(-${currentIndex * 100}%)`;
        document.querySelectorAll('.dot').forEach((dot, idx) => {
            dot.classList.toggle('active', idx === currentIndex);
        });
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % totalSlides;
        updateSlider();
    }

    function prevSlide() {
        currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
        updateSlider();
    }

    if (nextBtn) nextBtn.addEventListener('click', nextSlide);
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);

    if (dotsContainer) {
        for (let i = 0; i < totalSlides; i++) {
            const dot = document.createElement('div');
            dot.classList.add('dot');
            if (i === 0) dot.classList.add('active');
            dot.addEventListener('click', () => {
                currentIndex = i;
                updateSlider();
            });
            dotsContainer.appendChild(dot);
        }
    }

    setInterval(nextSlide, 5000);
}

function renderProducts(products, containerId, formatRands) {
    const container = document.getElementById(containerId);
    if (!container) return;

    if (products.length === 0) {
        container.innerHTML = '<p style="color:#4F6B5A; text-align:center; padding:40px;">No products found.</p>';
        return;
    }

    let html = '';
    products.forEach(p => {
        let imgUrl = p.Image && p.Image.trim() ? p.Image : 'assets/placeholder.jpg';
        html += `
            <div class="product-card" data-product-id="${p.ProductID}">
                <a href="product.php?id=${p.ProductID}" class="product-link">
                    <img class="product-image" src="${imgUrl}" alt="${p.Name}" onerror="this.src='assets/placeholder.jpg'">
                    <div class="product-info">
                        <div class="product-title">${p.Name}</div>
                        <div class="product-price">${formatRands(p.Price)}</div>
                    </div>
                </a>
                <div class="product-actions">
                    <button class="add-btn" onclick="addToCart(${p.ProductID}, this)">Add to Cart</button>
                </div>
            </div>
        `;
    });
    container.innerHTML = html;
}

function updateCartBadge(count) {
    const badge = document.getElementById('cartBadge');
    const floatingBadge = document.getElementById('floatingBadge');
    if (badge) badge.textContent = count;
    if (floatingBadge) floatingBadge.textContent = count;
}

function flyToCart(button, productId) {
    const card = button.closest('.product-card');
    if (!card) return;

    const img = card.querySelector('.product-image');
    if (!img) return;

    const cart = document.getElementById('floatingCart');
    if (!cart) return;

    const cartRect = cart.getBoundingClientRect();
    const imgRect = img.getBoundingClientRect();

    const flyClone = document.createElement('img');
    flyClone.src = img.src;
    flyClone.className = 'fly-product';
    flyClone.style.position = 'fixed';
    flyClone.style.left = imgRect.left + 'px';
    flyClone.style.top = imgRect.top + 'px';
    flyClone.style.width = imgRect.width + 'px';
    flyClone.style.height = imgRect.height + 'px';
    flyClone.style.zIndex = '9999';
    flyClone.style.pointerEvents = 'none';
    flyClone.style.transition = 'all 0.8s cubic-bezier(0.2, 0.8, 0.2, 1)';
    flyClone.style.borderRadius = '12px';
    flyClone.style.boxShadow = '0 8px 30px rgba(28, 42, 34, 0.4)';
    flyClone.style.opacity = '1';

    document.body.appendChild(flyClone);

    void flyClone.offsetWidth;

    const targetX = cartRect.left + cartRect.width / 2 - imgRect.width / 2;
    const targetY = cartRect.top + cartRect.height / 2 - imgRect.height / 2;

    flyClone.style.left = targetX + 'px';
    flyClone.style.top = targetY + 'px';
    flyClone.style.width = '60px';
    flyClone.style.height = '60px';
    flyClone.style.borderRadius = '50%';
    flyClone.style.opacity = '0.7';
    flyClone.style.transform = 'scale(0.8)';

    setTimeout(() => {
        flyClone.remove();
        cart.style.transform = 'scale(1.15)';
        setTimeout(() => {
            cart.style.transform = 'scale(1)';
        }, 200);
    }, 850);
}

async function addToCart(productId, button) {
    if (!button || button.disabled) return;
    button.disabled = true;
    button.textContent = 'Adding...';

    try {
        const response = await fetch('api/cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, quantity: 1 })
        });
        const data = await response.json();

        if (data.success) {
            const currentCount = parseInt(document.getElementById('cartBadge').textContent) || 0;
            updateCartBadge(currentCount + 1);
            flyToCart(button, productId);
            button.textContent = 'Added!';
            setTimeout(() => {
                button.textContent = 'Add to Cart';
                button.disabled = false;
            }, 1500);
        } else {
            button.textContent = 'Error';
            setTimeout(() => {
                button.textContent = 'Add to Cart';
                button.disabled = false;
            }, 1500);
        }
    } catch (error) {
        button.textContent = 'Error';
        setTimeout(() => {
            button.textContent = 'Add to Cart';
            button.disabled = false;
        }, 1500);
    }
}

// -------- CART SELECTION --------
let cartItems = [];
let selectedItems = new Set();

function formatRands(price) {
    return 'R ' + price.toLocaleString('en-ZA', { minimumFractionDigits: 2 });
}

async function loadCartWithSelection() {
    const container = document.getElementById('cart-items');
    if (!container) return;

    const response = await fetch('api/cart.php');
    cartItems = await response.json();
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
        if (summaryDiv) summaryDiv.style.display = 'none';
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
                    <button class="qty-btn" onclick="updateQtyWithSelection(${item.cart_item_id}, ${item.quantity - 1})">-</button>
                    <span style="min-width:35px; text-align:center;">${item.quantity}</span>
                    <button class="qty-btn" onclick="updateQtyWithSelection(${item.cart_item_id}, ${item.quantity + 1})">+</button>
                </div>
                <div class="item-actions">
                    <div class="item-total">${formatRands(itemTotal)}</div>
                    <button class="delete-btn" onclick="deleteItemWithConfirm(${item.cart_item_id})">Delete</button>
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
            updateCartSummary();
            updateSelectAllState();
        });
    });

    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
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
            updateCartSummary();
        });
    }

    if (selectedItems.size === 0 && cartItems.length > 0) {
        cartItems.forEach(item => selectedItems.add(item.cart_item_id));
        document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = true);
        if (selectAll) selectAll.checked = true;
    }

    updateCartSummary();
    if (summaryDiv) summaryDiv.style.display = 'block';
    updateSelectAllState();
}

function updateCartSummary() {
    const totalSpan = document.getElementById('total-amount');
    const selectedInfo = document.getElementById('selectedInfo');
    const checkoutBtn = document.getElementById('checkout-btn');

    if (!totalSpan) return;

    let total = 0;
    let count = 0;
    cartItems.forEach(item => {
        if (selectedItems.has(item.cart_item_id)) {
            total += item.price * item.quantity;
            count++;
        }
    });

    totalSpan.innerText = formatRands(total);
    if (selectedInfo) selectedInfo.innerText = `Selected: ${count} item${count !== 1 ? 's' : ''}`;
    if (checkoutBtn) checkoutBtn.disabled = count === 0;
}

function updateSelectAllState() {
    const checkboxes = document.querySelectorAll('.item-checkbox');
    const selectAll = document.getElementById('selectAll');
    if (!selectAll) return;
    const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
    if (checkboxes.length > 0 && checkedCount === checkboxes.length) {
        selectAll.checked = true;
    } else {
        selectAll.checked = false;
    }
}

async function updateQtyWithSelection(cartItemId, newQty) {
    if (newQty < 0) return;
    await fetch('api/cart.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ cart_item_id: cartItemId, quantity: newQty })
    });
    if (newQty === 0) {
        selectedItems.delete(cartItemId);
    }
    loadCartWithSelection();
}

async function deleteItemWithConfirm(cartItemId) {
    if (confirm('Are you sure you want to remove this item?')) {
        await fetch(`api/cart.php?cart_item_id=${cartItemId}`, { method: 'DELETE' });
        selectedItems.delete(cartItemId);
        loadCartWithSelection();
    }
}

function proceedToCheckout() {
    if (selectedItems.size === 0) return;
    const selectedData = cartItems.filter(item => selectedItems.has(item.cart_item_id));
    localStorage.setItem('selectedCartItems', JSON.stringify(selectedData));
    localStorage.setItem('selectedCartIds', JSON.stringify(Array.from(selectedItems)));
    window.location.href = 'checkout.php';
}

async function loadSelectedItemsSummary() {
    const container = document.getElementById('cart-summary');
    if (!container) return;

    const storedItems = localStorage.getItem('selectedCartItems');
    let selectedItemsData = [];

    if (storedItems) {
        selectedItemsData = JSON.parse(storedItems);
    } else {
        const res = await fetch('api/cart.php');
        const allItems = await res.json();
        selectedItemsData = allItems;
    }

    const noteDiv = document.getElementById('selectedItemsNote');
    if (noteDiv) {
        if (selectedItemsData.length === 0) {
            noteDiv.innerHTML = '<strong>No items selected.</strong> Please go back to your cart and select items to checkout.';
            document.getElementById('submit-btn').disabled = true;
            return;
        } else {
            noteDiv.innerHTML = `<strong>Checking out:</strong> ${selectedItemsData.length} item(s) selected.`;
        }
    }

    let html = '<h3>Order Summary</h3>';
    let total = 0;
    selectedItemsData.forEach(item => {
        total += item.price * item.quantity;
        html += `<div class="summary-item">
                    <span>${item.name} x ${item.quantity}</span>
                    <span>${formatRands(item.price * item.quantity)}</span>
                 </div>`;
    });
    html += `<div class="total-line">Total: ${formatRands(total)}</div>`;
    container.innerHTML = html;

    window.selectedCheckoutItems = selectedItemsData;
}

// -------- VALIDATION --------
function validateAddress(address) {
    if (address.length < 5) return false;
    const hasLetters = /[a-zA-Z]/.test(address);
    const hasValidChars = /^[a-zA-Z0-9\s,.\'\-/]+$/.test(address);
    return hasLetters && hasValidChars && address.length >= 5;
}

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

function showError(inputId, errorId) {
    const input = document.getElementById(inputId);
    const error = document.getElementById(errorId);
    if (input) input.classList.add('input-error');
    if (error) error.classList.add('visible');
}

function hideError(inputId, errorId) {
    const input = document.getElementById(inputId);
    const error = document.getElementById(errorId);
    if (input) input.classList.remove('input-error');
    if (error) error.classList.remove('visible');
}

function validateField(inputId, errorId, validator, value) {
    if (validator(value)) {
        hideError(inputId, errorId);
        return true;
    } else {
        showError(inputId, errorId);
        return false;
    }
}

// -------- ORDER HISTORY --------
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
    const container = document.getElementById('orders-list');
    if (!container) return;

    try {
        const response = await fetch('api/orders.php');
        const orders = await response.json();

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

            const address = order.Address || 'No address provided';

            html += `
                <div class="order-card">
                    <div class="order-header">
                        <span class="order-id">Order #${order.ID}</span>
                        <span>${orderDate}</span>
                        <span class="order-status">${order.Status}</span>
                    </div>
                    <div class="order-body">
                        <div class="address-block">
                            <span class="address-label">Shipping Address</span>
                            <span class="address-text">${escapeHtml(address)}</span>
                        </div>
                        ${itemsHtml}
                        <div class="total-row">Total: ${formatRands(order.Total)}</div>
                        <div class="payment-id">Payment ID: ${escapeHtml(order.PaymentID || 'N/A')}</div>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    } catch (error) {
        container.innerHTML = '<div class="empty-message">Error loading orders. Please try again.</div>';
    }
}

// -------- PAYMENT PROCESSING --------
function delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function processPayment() {
    const address = window.checkoutAddress || '';
    const progressFill = document.getElementById('progressFill');
    const statusText = document.getElementById('statusText');

    const statusMessages = [
        'Initializing payment...',
        'Validating card details...',
        'Processing payment...',
        'Payment confirmed!',
        'Creating your order...'
    ];

    function updateProgress(percent, message) {
        if (progressFill) progressFill.style.width = percent + '%';
        if (statusText && message) statusText.textContent = message;
    }

    try {
        updateProgress(5, statusMessages[0]);
        await delay(400);

        updateProgress(25, statusMessages[1]);
        await delay(500);

        updateProgress(55, statusMessages[2]);
        await delay(600);

        const checkoutRes = await fetch('api/checkout.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ address: address })
        });
        const paymentData = await checkoutRes.json();

        if (paymentData.error) {
            throw new Error(paymentData.error);
        }

        updateProgress(80, statusMessages[3]);
        await delay(400);

        const orderRes = await fetch('api/orders.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                address: address,
                payment_id: paymentData.paymentIntentId
            })
        });
        const orderData = await orderRes.json();

        if (!orderData.success) {
            throw new Error(orderData.error || 'Order creation failed');
        }

        updateProgress(100, statusMessages[4]);
        await delay(400);

        window.location.href = 'success.php?order_id=' + orderData.order_id;

    } catch (error) {
        if (statusText) {
            statusText.textContent = 'Error: ' + error.message;
            statusText.style.color = '#b85c3a';
        }
        await delay(2000);
        window.location.href = 'checkout.php';
    }
}

// -------- INITIALIZATION --------
document.addEventListener('DOMContentLoaded', function() {
    initSlider();

    if (document.getElementById('cart-items') && document.querySelector('.cart-header')) {
        loadCartWithSelection();
        const checkoutBtn = document.getElementById('checkout-btn');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', proceedToCheckout);
        }
    }

    if (document.getElementById('orders-list')) {
        loadOrders();
    }

    if (document.getElementById('selectedItemsNote')) {
        loadSelectedItemsSummary();
    }

    const checkoutForm = document.getElementById('payment-form');
    if (checkoutForm) {
        const addressField = document.getElementById('address');
        if (addressField) {
            addressField.addEventListener('input', function() {
                validateField('address', 'address-error', validateAddress, this.value);
            });
        }

        const cardField = document.getElementById('card_number');
        if (cardField) {
            cardField.addEventListener('input', function() {
                let value = this.value.replace(/\s/g, '');
                value = value.replace(/[^0-9]/g, '');
                let formatted = '';
                for (let i = 0; i < value.length; i++) {
                    if (i > 0 && i % 4 === 0) {
                        formatted += ' ';
                    }
                    formatted += value[i];
                }
                this.value = formatted;
                validateField('card_number', 'card-error', validateCardNumber, this.value);
            });
        }

        const expiryField = document.getElementById('expiry');
        if (expiryField) {
            expiryField.addEventListener('input', function() {
                let value = this.value.replace(/[^0-9]/g, '');
                if (value.length >= 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                this.value = value;
                validateField('expiry', 'expiry-error', validateExpiry, this.value);
            });
        }

        const cvcField = document.getElementById('cvc');
        if (cvcField) {
            cvcField.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                validateField('cvc', 'cvc-error', validateCVC, this.value);
            });
        }

        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const address = document.getElementById('address').value;
            const cardNumber = document.getElementById('card_number')?.value || '';
            const expiry = document.getElementById('expiry')?.value || '';
            const cvc = document.getElementById('cvc')?.value || '';

            const isAddressValid = validateField('address', 'address-error', validateAddress, address);
            const isCardValid = validateField('card_number', 'card-error', validateCardNumber, cardNumber);
            const isExpiryValid = validateField('expiry', 'expiry-error', validateExpiry, expiry);
            const isCvcValid = validateField('cvc', 'cvc-error', validateCVC, cvc);

            if (isAddressValid && isCardValid && isExpiryValid && isCvcValid) {
                this.submit();
            } else {
                if (!isAddressValid) {
                    document.getElementById('address').focus();
                } else if (!isCardValid) {
                    document.getElementById('card_number').focus();
                } else if (!isExpiryValid) {
                    document.getElementById('expiry').focus();
                } else if (!isCvcValid) {
                    document.getElementById('cvc').focus();
                }
                alert('Please fix the errors before proceeding.');
            }
        });
    }
});