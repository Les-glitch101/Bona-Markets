/* ──────────────────────────────────────────────────────────────
   BONA MARKETS – VENDOR DASHBOARD JAVASCRIPT
   ────────────────────────────────────────────────────────────── */

/* ── NAVIGATION ─────────────────────────── */
function navigate(page, el) {
    // Hide all pages
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
    
    // Show the selected page
    const targetPage = document.getElementById('page-' + page);
    if (targetPage) {
        targetPage.classList.add('active');
    }
    
    // Update sidebar active state
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    if (el) {
        el.classList.add('active');
    }
    
    // Close sidebar on mobile
    if (window.innerWidth <= 768) {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        if (sidebar) sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('active');
    }
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/* ── SIDEBAR TOGGLE (Mobile) ────────────── */
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar) return;
    
    sidebar.classList.toggle('open');
    
    // Create overlay if it doesn't exist
    let overlayEl = document.getElementById('sidebar-overlay');
    if (!overlayEl) {
        overlayEl = document.createElement('div');
        overlayEl.id = 'sidebar-overlay';
        overlayEl.className = 'sidebar-overlay';
        overlayEl.onclick = function() {
            sidebar.classList.remove('open');
            overlayEl.classList.remove('active');
        };
        document.body.appendChild(overlayEl);
    }
    overlayEl.classList.toggle('active');
}

/* ── PRODUCTS ────────────────────────────── */
function renderProducts(list) {
    const grid = document.getElementById('product-grid');
    if (!grid) return;
    
    // If no list provided, use the PHP data
    const products = list || (typeof phpProducts !== 'undefined' ? phpProducts : []);
    
    if (!products || products.length === 0) {
        grid.innerHTML = `
            <div class="empty-state" style="grid-column:1/-1">
                <div class="icon">📦</div>
                <p>You haven't listed any products yet.</p>
                <button class="btn btn-primary" onclick="navigate('add-product', document.querySelector('[onclick*=\"add-product\"]'))">
                    Add Your First Product
                </button>
            </div>
        `;
        return;
    }
    
    grid.innerHTML = products.map(p => {
        const name = p.name || '';
        const price = p.price || 0;
        const stock = p.stock || 0;
        const category = p.category_name || p.cat || 'Uncategorized';
        const imageUrl = p.image_url || '';
        const id = p.id || 0;
        const description = p.description || '';
        const status = p.status || 'active';
        const categoryId = p.category_id || null;
        
        return `
            <div class="product-card" data-product-id="${id}" 
                 data-name="${name.toLowerCase()}"
                 data-category="${category}"
                 data-stock="${stock}"
                 data-status="${stock == 0 ? 'outofstock' : (stock <= 3 ? 'lowstock' : 'active')}">
                <div class="product-thumb" style="background:${stock == 0 ? '#fdecea' : 'var(--warm-grey)'}">
                    ${imageUrl 
                        ? `<img src="/Bona-Markets/Public/${imageUrl}" alt="${name}" style="width:100%;height:100%;object-fit:cover;" />` 
                        : `<span style="font-size:3rem;">🛍️</span>`
                    }
                    <span class="product-thumb-badge">
                        ${stock == 0
                            ? '<span class="badge badge-danger">Out of Stock</span>'
                            : stock <= 3
                            ? '<span class="badge badge-warning">Low Stock</span>'
                            : '<span class="badge badge-success">Active</span>'}
                    </span>
                </div>
                <div class="product-info">
                    <div class="product-name">${name}</div>
                    <div class="product-meta">
                        <span>${category}</span> · 
                        <span>${stock} in stock</span>
                    </div>
                    <div class="product-price">R ${Number(price).toFixed(2)}</div>
                    <div class="product-actions">
                        <button class="btn btn-outline btn-sm" onclick="openEditModal(${id}, '${name.replace(/'/g, "\\'")}', '${description.replace(/'/g, "\\'")}', ${price}, ${categoryId}, ${stock}, '${status}')">
                            Edit
                        </button>
                        <button class="btn-icon delete-btn" onclick="deleteProduct(${id}, '${name.replace(/'/g, "\\'")}', this)" title="Delete" style="color:#dc3545;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14H6L5 6"/>
                                <path d="M10 11v6M14 11v6"/>
                                <path d="M9 6V4h6v2"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

/* ── DELETE PRODUCT (AJAX) ───────────────── */
function deleteProduct(productId, productName, buttonElement) {
    // Show confirmation
    if (!confirm(`Are you sure you want to delete "${productName}"? This action cannot be undone.`)) {
        return;
    }
    
    // Find the product card
    const productCard = buttonElement.closest('.product-card');
    if (!productCard) {
        console.error('Product card not found');
        return;
    }
    
    // Show loading state
    const originalHtml = buttonElement.innerHTML;
    buttonElement.innerHTML = '⏳';
    buttonElement.disabled = true;
    buttonElement.style.opacity = '0.5';
    
    // Send AJAX request
    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `delete_product=1&product_id=${productId}&ajax=1`
    })
    .then(response => response.text())
    .then(data => {
        // Check if deletion was successful
        if (data.includes('Product deleted successfully') || data.includes('"success":true')) {
            // Animate and remove the product card
            productCard.style.transition = 'all 0.4s ease';
            productCard.style.opacity = '0';
            productCard.style.transform = 'translateX(-30px)';
            productCard.style.height = productCard.offsetHeight + 'px';
            
            setTimeout(() => {
                productCard.style.height = '0';
                productCard.style.margin = '0';
                productCard.style.padding = '0';
                productCard.style.overflow = 'hidden';
                
                setTimeout(() => {
                    productCard.remove();
                    showToast(`"${productName}" deleted successfully!`, 'success');
                    
                    // Update product count
                    const productCount = document.querySelectorAll('.product-card').length;
                    const countBadge = document.querySelector('.nav-count');
                    if (countBadge) {
                        countBadge.textContent = productCount;
                    }
                    
                    // Check if no products left
                    if (productCount === 0) {
                        const grid = document.getElementById('product-grid');
                        if (grid) {
                            grid.innerHTML = `
                                <div class="empty-state" style="grid-column:1/-1">
                                    <div class="icon">📦</div>
                                    <p>You haven't listed any products yet.</p>
                                    <button class="btn btn-primary" onclick="navigate('add-product', document.querySelector('[onclick*=\"add-product\"]'))">
                                        Add Your First Product
                                    </button>
                                </div>
                            `;
                        }
                    }
                    
                    // Remove from low stock section if present
                    document.querySelectorAll('.low-stock-item').forEach(item => {
                        if (item.textContent.includes(productName)) {
                            item.remove();
                        }
                    });
                }, 300);
            }, 300);
        } else {
            // Deletion failed
            buttonElement.innerHTML = originalHtml;
            buttonElement.disabled = false;
            buttonElement.style.opacity = '1';
            showToast('Failed to delete product. Please try again.', 'error');
            console.error('Delete failed:', data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        buttonElement.innerHTML = originalHtml;
        buttonElement.disabled = false;
        buttonElement.style.opacity = '1';
        showToast('Network error. Please try again.', 'error');
    });
}

/* ── FILTER PRODUCTS ──────────────────────── */
function filterProducts(q = '') {
    const searchInput = document.getElementById('productSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    const search = (q || (searchInput ? searchInput.value : '')).toLowerCase();
    const category = categoryFilter ? categoryFilter.value : 'all';
    const status = statusFilter ? statusFilter.value : 'all';
    
    const products = document.querySelectorAll('.product-card');
    let visibleCount = 0;
    
    products.forEach(card => {
        const name = card.dataset.name || '';
        const cat = card.dataset.category || '';
        const stock = parseInt(card.dataset.stock) || 0;
        const prodStatus = card.dataset.status || 'active';
        
        let show = true;
        
        // Search filter
        if (search && !name.includes(search)) {
            show = false;
        }
        
        // Category filter
        if (category !== 'all' && cat !== category) {
            show = false;
        }
        
        // Status filter
        if (status !== 'all') {
            if (status === 'active' && prodStatus !== 'active') show = false;
            if (status === 'outofstock' && stock !== 0) show = false;
            if (status === 'lowstock' && (stock === 0 || stock > 3)) show = false;
            if (status === 'draft' && prodStatus !== 'draft') show = false;
        }
        
        if (show) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Show/hide empty state
    const grid = document.getElementById('product-grid');
    if (grid) {
        let emptyMsg = grid.querySelector('.no-results');
        if (visibleCount === 0) {
            if (!emptyMsg) {
                emptyMsg = document.createElement('div');
                emptyMsg.className = 'no-results';
                emptyMsg.style.cssText = 'grid-column:1/-1;text-align:center;padding:2rem;color:var(--muted);';
                emptyMsg.innerHTML = '<p style="font-size:1.1rem;font-weight:500;">No products match your filters</p>';
                grid.appendChild(emptyMsg);
            }
        } else if (emptyMsg) {
            emptyMsg.remove();
        }
    }
}

/* ── ORDERS ──────────────────────────────── */
function renderOrders() {
    const tbody = document.getElementById('orders-tbody');
    if (!tbody) return;
    
    // Use PHP data if available
    const orders = typeof phpOrders !== 'undefined' ? phpOrders : [];
    
    if (!orders || orders.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;color:var(--muted);padding:1rem;">No orders yet</td></tr>`;
        return;
    }
    
    const statusMap = {
        pending: '<span class="badge badge-warning"><span class="badge-dot"></span>Pending</span>',
        shipped: '<span class="badge badge-neutral"><span class="badge-dot"></span>Shipped</span>',
        delivered: '<span class="badge badge-success"><span class="badge-dot"></span>Delivered</span>',
        cancelled: '<span class="badge badge-danger"><span class="badge-dot"></span>Cancelled</span>',
    };
    
    tbody.innerHTML = orders.map(o => {
        const id = o.id || '';
        const orderId = id.toString().padStart(4, '0');
        const customerName = o.customer_name || o.customer || 'Guest';
        const initials = (customerName.substring(0, 2) || 'G').toUpperCase();
        const products = o.products || o.product || '—';
        const date = o.created_at ? new Date(o.created_at).toLocaleDateString('en-ZA', {day:'2-digit', month:'short', year:'numeric'}) : (o.date || '');
        const amount = o.total || o.amount || 0;
        const status = o.status || 'pending';
        
        return `
            <tr>
                <td><span class="order-id">#BM-${orderId}</span></td>
                <td>
                    <div class="customer-cell">
                        <div class="customer-avatar">${initials}</div>
                        ${customerName}
                    </div>
                </td>
                <td>${products}</td>
                <td style="color:var(--muted);font-size:.84rem">${date}</td>
                <td><span class="amount-cell">R ${Number(amount).toLocaleString()}</span></td>
                <td>${statusMap[status] || '<span class="badge badge-neutral">Unknown</span>'}</td>
                <td>
                    <button class="btn btn-outline btn-sm" onclick="openOrderModal('${id}')">View</button>
                </td>
            </tr>
        `;
    }).join('');
}

/* ── CHART ───────────────────────────────── */
function renderChart() {
    const chart = document.getElementById('sales-chart');
    if (!chart) return;
    
    const sales = typeof phpSales !== 'undefined' ? phpSales : [42, 68, 55, 90, 74, 115, 88];
    const max = Math.max(...sales);
    chart.innerHTML = sales.map((v, i) => `
        <div class="bar ${i === 5 ? 'highlight' : ''}" style="height:${Math.round((v/max)*100)}%" title="R ${v*100}"></div>
    `).join('');
}

let currentOrderId = null;
let currentOrderStatus = null;

/* ── MODAL: ORDER ────────────────────────── */
function openOrderModal(id) {
    currentOrderId = id;
    const orders = typeof phpOrders !== 'undefined' ? phpOrders : [];
    const o = orders.find(x => String(x.id) === String(id));
    
    if (!o) {
        showToast('Order not found', 'warning');
        return;
    }
    
    const titleEl = document.getElementById('order-modal-title');
    const bodyEl = document.getElementById('order-modal-body');
    const modal = document.getElementById('order-modal');
    const markBtn = document.getElementById('markShippedBtn');
    
    // Store the current order status
    currentOrderStatus = o.status || 'pending';
    
    const customerName = o.customer_name || o.customer || 'Guest';
    const date = o.created_at ? new Date(o.created_at).toLocaleDateString('en-ZA', {day:'2-digit', month:'short', year:'numeric'}) : (o.date || '');
    const products = o.products || o.product || '—';
    const amount = o.total || o.amount || 0;
    const status = o.status || 'pending';
    const address = o.address || o.shipping_address || 'No shipping address provided';
    
    // ✅ Show/hide "Mark as Shipped" button based on status
    if (markBtn) {
        if (status === 'delivered' || status === 'cancelled') {
            markBtn.style.display = 'none';
        } else {
            markBtn.style.display = 'inline-flex';
            // Update button text based on status
            if (status === 'shipped') {
                markBtn.textContent = '✅ Already Shipped';
                markBtn.disabled = true;
                markBtn.style.opacity = '0.6';
                markBtn.style.cursor = 'not-allowed';
            } else {
                markBtn.textContent = '🚚 Mark as Shipped';
                markBtn.disabled = false;
                markBtn.style.opacity = '1';
                markBtn.style.cursor = 'pointer';
            }
        }
    }
    
    // Get the status badge class
    const statusClass = status === 'pending' ? 'badge-warning' : 
                        status === 'shipped' ? 'badge-neutral' : 
                        status === 'delivered' ? 'badge-success' : 'badge-danger';
    
    if (titleEl) titleEl.textContent = 'Order #BM-' + String(id).padStart(4, '0');
    if (bodyEl) {
        bodyEl.innerHTML = `
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.2rem">
                <div>
                    <div style="font-size:.75rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem">Customer</div>
                    <div style="font-weight:600">${customerName}</div>
                </div>
                <div>
                    <div style="font-size:.75rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem">Date</div>
                    <div>${date}</div>
                </div>
                <div>
                    <div style="font-size:.75rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem">Products</div>
                    <div>${products}</div>
                </div>
                <div>
                    <div style="font-size:.75rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem">Amount</div>
                    <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:1.1rem;color:var(--gold)">R ${Number(amount).toFixed(2)}</div>
                </div>
                <div>
                    <div style="font-size:.75rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem">Status</div>
                    <div><span class="badge ${statusClass}"><span class="badge-dot"></span>${status.charAt(0).toUpperCase() + status.slice(1)}</span></div>
                </div>
            </div>
            <div style="background:var(--cream);border-radius:var(--radius);padding:.9rem 1rem;">
                <div style="font-size:.82rem;font-weight:600;margin-bottom:.3rem">Shipping Address</div>
                <div style="font-size:.85rem;color:var(--muted);line-height:1.6">${address}</div>
            </div>
            ${status === 'delivered' ? `
            <div style="background:#e8f5ee;border:1px solid #3a7d5e;border-radius:var(--radius);padding:.9rem 1rem;margin-top:1rem;text-align:center;">
                <p style="color:#2d6b4f;font-weight:600;font-size:.9rem;">✅ Order has been delivered</p>
                <p style="color:#2d6b4f;font-size:.8rem;margin-top:.2rem;">This order is complete and cannot be modified.</p>
            </div>
            ` : ''}
            ${status === 'cancelled' ? `
            <div style="background:#fdecea;border:1px solid #f5c6c0;border-radius:var(--radius);padding:.9rem 1rem;margin-top:1rem;text-align:center;">
                <p style="color:#c04b1e;font-weight:600;font-size:.9rem;">❌ Order has been cancelled</p>
                <p style="color:#c04b1e;font-size:.8rem;margin-top:.2rem;">This order cannot be modified.</p>
            </div>
            ` : ''}
        `;
    }
    if (modal) modal.classList.add('open');
}

function closeOrderModal() {
    const modal = document.getElementById('order-modal');
    if (modal) modal.classList.remove('open');
    // Reset the button state
    const markBtn = document.getElementById('markShippedBtn');
    if (markBtn) {
        markBtn.style.display = 'inline-flex';
        markBtn.disabled = false;
        markBtn.style.opacity = '1';
        markBtn.style.cursor = 'pointer';
        markBtn.textContent = '🚚 Mark as Shipped';
    }
}

function markShipped() {
    if (!currentOrderId) {
        showToast('No order selected.', 'warning');
        return;
    }
    
    // ✅ Prevent marking as shipped if already delivered or cancelled
    if (currentOrderStatus === 'delivered') {
        showToast('This order has already been delivered.', 'warning');
        return;
    }
    if (currentOrderStatus === 'cancelled') {
        showToast('This order has been cancelled.', 'warning');
        return;
    }
    if (currentOrderStatus === 'shipped') {
        showToast('This order has already been marked as shipped.', 'warning');
        return;
    }
    
    // Show loading state on button
    const btn = document.getElementById('markShippedBtn');
    const originalText = btn ? btn.textContent : 'Mark as Shipped';
    if (btn) {
        btn.textContent = '⏳ Updating...';
        btn.disabled = true;
    }
    
    // ✅ Use the current page URL (dashboard.php)
    fetch(window.location.href, {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `update_order_status=1&order_id=${currentOrderId}&status=shipped`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP error ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            closeOrderModal();
            showToast('Order #BM-' + String(currentOrderId).padStart(4, '0') + ' marked as shipped! 🚚', 'success');
            
            // Update local data
            const orders = typeof phpOrders !== 'undefined' ? phpOrders : [];
            const o = orders.find(x => String(x.id) === String(currentOrderId));
            if (o) {
                o.status = 'shipped';
                if (typeof renderOrders === 'function') {
                    renderOrders();
                }
            }
        } else {
            showToast(data.message || 'Could not update order.', 'warning');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Could not reach server. Please try again.', 'warning');
    })
    .finally(() => {
        if (btn) {
            btn.textContent = originalText;
            btn.disabled = false;
        }
    });
}

/* ── MODAL: WITHDRAW ─────────────────────── */
function openWithdrawModal() {
    const modal = document.getElementById('withdraw-modal');
    if (modal) modal.classList.add('open');
}

function closeWithdrawModal() {
    const modal = document.getElementById('withdraw-modal');
    if (modal) modal.classList.remove('open');
}

function submitWithdraw() {
    const amt = document.getElementById('withdraw-amount');
    if (!amt || !amt.value || amt.value < 100) {
        showToast('Enter a valid amount (min R 100)', 'warning');
        return;
    }
    closeWithdrawModal();
    showToast('Payouts are not yet available — this feature is coming soon.', 'warning');
}

/* ── EDIT PRODUCT MODAL ───────────────────── */
function openEditModal(id, name, description, price, categoryId, stock, status, image_url) {
    console.log('Opening edit modal for product ID:', id);

    const productIdInput  = document.getElementById('edit-prod-id');
    const nameInput       = document.getElementById('edit-prod-name');
    const descInput       = document.getElementById('edit-prod-desc');
    const priceInput      = document.getElementById('edit-prod-price');
    const stockInput      = document.getElementById('edit-prod-stock');
    const statusSelect    = document.getElementById('edit-prod-status');
    const categorySelect  = document.getElementById('edit-prod-cat');

    if (productIdInput)  productIdInput.value  = id;
    if (nameInput)       nameInput.value        = name        || '';
    if (descInput)       descInput.value        = description || '';
    if (priceInput)      priceInput.value       = price       || 0;
    if (stockInput)      stockInput.value       = stock       || 0;

    // Set status dropdown
    if (statusSelect) {
        for (let i = 0; i < statusSelect.options.length; i++) {
            statusSelect.options[i].selected = (statusSelect.options[i].value === status);
        }
    }

    // Set category dropdown
    if (categorySelect) {
        for (let i = 0; i < categorySelect.options.length; i++) {
            if (String(categorySelect.options[i].value) === String(categoryId)) {
                categorySelect.selectedIndex = i;
                break;
            }
        }
    }

    // Show current image preview if available
    const imgPreview = document.getElementById('edit-current-img-preview');
    const imgEl      = document.getElementById('edit-current-img');
    if (image_url && imgPreview && imgEl) {
        imgEl.src = '/Bona-Markets/Public/' + image_url;
        imgPreview.style.display = 'block';
    } else if (imgPreview) {
        imgPreview.style.display = 'none';
    }

    // Clear any previously chosen file
    const fileInput = document.getElementById('edit-prod-image');
    if (fileInput) fileInput.value = '';

    // Update modal title
    const titleEl = document.getElementById('edit-modal-title');
    if (titleEl) titleEl.textContent = 'Edit — ' + (name || 'Product');

    // Open the modal
    const modal = document.getElementById('edit-product-modal');
    if (modal) modal.classList.add('open');
}

function closeEditModal() {
    const modal = document.getElementById('edit-product-modal');
    if (modal) modal.classList.remove('open');
}

/* ── TOAST ───────────────────────────────── */
function showToast(msg, type = 'success') {
    const wrap = document.getElementById('toast-wrap');
    if (!wrap) return;
    
    const colors = {
        success: '#2d6a4f',
        error: '#dc3545',
        warning: '#e8952a',
        info: '#2196F3'
    };
    
    const icons = {
        success: '✅',
        error: '❌',
        warning: '⚠️',
        info: 'ℹ️'
    };
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.cssText = `
        background: ${colors[type] || colors.info};
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideInToast 0.3s ease;
        font-size: 14px;
        font-weight: 500;
        min-width: 250px;
        display: flex;
        align-items: center;
        gap: 10px;
    `;
    toast.innerHTML = `<span>${icons[type] || 'ℹ️'}</span><span>${msg}</span>`;
    wrap.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(30px)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => {
            if (toast.parentNode) toast.remove();
        }, 300);
    }, 3000);
}

/* ── SAVE SETTINGS ────────────────────────── */
function saveSettings() {
    const storeName = document.getElementById('settings-store-name');
    const storeBio  = document.getElementById('settings-store-bio');
    const phone     = document.getElementById('settings-phone');
    const city      = document.getElementById('settings-city');

    if (!storeName || !storeName.value.trim()) {
        showToast('Store name cannot be empty', 'warning');
        return;
    }

    fetch('settings/save.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            store_name: storeName.value.trim(),
            store_bio:  storeBio ? storeBio.value.trim() : '',
            phone:      phone ? phone.value.trim() : '',
            city:       city ? city.value.trim() : ''
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Store profile saved!', 'success');
        } else {
            showToast(data.message || 'Could not save changes.', 'warning');
        }
    })
    .catch(() => showToast('Could not reach server. Please try again.', 'warning'));
}

/* ── ADD PRODUCT FORM ─────────────────────── */
document.addEventListener('DOMContentLoaded', function() {
    // Auto-close alert messages
    document.querySelectorAll('.alert-success, .alert-error').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'all 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                if (alert.parentNode) alert.remove();
            }, 500);
        }, 3000);
    });
    
    // Image upload handling
    const uploadZone = document.getElementById('uploadZone');
    const imageInput = document.getElementById('imageInput');
    
    if (uploadZone) {
        uploadZone.addEventListener('click', function() {
            if (imageInput) imageInput.click();
        });
    }

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                handleFile(file);
            }
        });
    }

    // Drag and drop
    if (uploadZone) {
        uploadZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        uploadZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        uploadZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            const file = e.dataTransfer.files[0];
            if (file && imageInput) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                imageInput.files = dataTransfer.files;
                handleFile(file);
            }
        });
    }
    
    // Close modals on overlay click
    document.querySelectorAll('.modal-overlay').forEach(m => {
        m.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('open');
                if (this.id === 'edit-modal') {
                    this.style.display = 'none';
                }
            }
        });
    });
    
    // Escape key to close modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEditModal();
            closeOrderModal();
            closeWithdrawModal();
        }
    });
    
    // Close sidebar overlay on resize
    window.addEventListener('resize', function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        if (window.innerWidth > 768) {
            if (sidebar) sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('active');
        }
    });
    
    // Add toast animation CSS if not present
    if (!document.getElementById('toast-styles')) {
        const style = document.createElement('style');
        style.id = 'toast-styles';
        style.textContent = `
            @keyframes slideInToast {
                from { transform: translateX(30px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            .toast-wrap {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
                display: flex;
                flex-direction: column;
                align-items: flex-end;
                gap: 5px;
            }
        `;
        document.head.appendChild(style);
    }
    
    // Initialize all components
    renderProducts();
    renderOrders();
    renderChart();
    
    console.log('✅ Bona Markets Vendor Dashboard loaded successfully!');
    console.log('📦 Products:', typeof phpProducts !== 'undefined' ? phpProducts.length : 0);
    console.log('📋 Orders:', typeof phpOrders !== 'undefined' ? phpOrders.length : 0);
});

function handleFile(file) {
    const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!validTypes.includes(file.type)) {
        showToast('Invalid file type. Please upload JPEG, PNG, GIF, or WEBP images only.', 'warning');
        const input = document.getElementById('imageInput');
        if (input) input.value = '';
        return;
    }

    if (file.size > 10 * 1024 * 1024) {
        showToast('File is too large. Maximum size is 10MB.', 'warning');
        const input = document.getElementById('imageInput');
        if (input) input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(event) {
        const previewImg = document.getElementById('previewImg');
        const imagePreview = document.getElementById('imagePreview');
        const uploadZone = document.getElementById('uploadZone');
        if (previewImg) previewImg.src = event.target.result;
        if (imagePreview) imagePreview.style.display = 'block';
        if (uploadZone) uploadZone.style.display = 'none';
        showToast('Image uploaded successfully!', 'success');
    };
    reader.readAsDataURL(file);
}

function removeImage() {
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const uploadZone = document.getElementById('uploadZone');
    const previewImg = document.getElementById('previewImg');
    
    if (imageInput) imageInput.value = '';
    if (imagePreview) imagePreview.style.display = 'none';
    if (uploadZone) uploadZone.style.display = 'block';
    if (previewImg) previewImg.src = '#';
    showToast('Image removed', 'warning');
}

function saveDraft() {
    const statusSelect = document.querySelector('select[name="status"]');
    if (statusSelect) {
        statusSelect.value = 'draft';
    }
    const form = document.getElementById('addProductForm');
    if (form) form.submit();
}

function saveProduct() {
    const name = document.getElementById('prod-name');
    if (!name || !name.value.trim()) {
        showToast('Please enter a product name', 'warning');
        return;
    }
    showToast(`"${name.value}" listed successfully!`, 'success');
    navigate('products', document.querySelector('[onclick*="products"]'));
}

/* ── END OF FILE ──────────────────────────── */