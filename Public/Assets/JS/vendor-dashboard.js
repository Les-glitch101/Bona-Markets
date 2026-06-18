/* ── DATA ───────────────────────────────── */
const PRODUCTS = [
    { id: 1, name: 'Kente Cloth Tote Bag', cat: 'Textiles & Fashion', price: 320, stock: 14, status: 'active', emoji: '👜' },
    { id: 2, name: 'Beaded Ndebele Necklace', cat: 'Jewellery', price: 580, stock: 6, status: 'active', emoji: '📿' },
    { id: 3, name: 'Moroccan Leather Pouch', cat: 'Handcrafts', price: 460, stock: 2, status: 'active', emoji: '🟤' },
    { id: 4, name: 'Ankara Print Cushion Cover', cat: 'Textiles & Fashion', price: 245, stock: 20, status: 'active', emoji: '🛋️' },
    { id: 5, name: 'Rooibos & Baobab Tea Blend', cat: 'Food & Spices', price: 120, stock: 50, status: 'active', emoji: '🍵' },
    { id: 6, name: 'Hand-Carved Soapstone Bowl', cat: 'Handcrafts', price: 850, stock: 0, status: 'out_of_stock', emoji: '🪨' },
    { id: 7, name: 'Shea Butter Body Cream', cat: 'Beauty & Wellness', price: 195, stock: 30, status: 'active', emoji: '🧴' },
    { id: 8, name: 'Maasai Beaded Bracelet Set', cat: 'Jewellery', price: 340, stock: 12, status: 'active', emoji: '💛' },
];

const ORDERS = [
    { id: '#BM-0041', customer: 'Sipho Molefe', initials: 'SM', product: 'Kente Cloth Tote Bag', qty: 1, date: '12 Jun 2025', amount: 320, status: 'pending' },
    { id: '#BM-0040', customer: 'Zanele Khumalo', initials: 'ZK', product: 'Beaded Ndebele Necklace', qty: 1, date: '11 Jun 2025', amount: 860, status: 'shipped' },
    { id: '#BM-0039', customer: 'Kwame Darko', initials: 'KD', product: 'Moroccan Leather Pouch', qty: 2, date: '10 Jun 2025', amount: 1240, status: 'delivered' },
    { id: '#BM-0038', customer: 'Amina Bah', initials: 'AB', product: 'Rooibos & Baobab Tea Blend', qty: 3, date: '09 Jun 2025', amount: 360, status: 'delivered' },
    { id: '#BM-0037', customer: 'Tendai Moyo', initials: 'TM', product: 'Ankara Print Cushion Cover', qty: 2, date: '08 Jun 2025', amount: 490, status: 'pending' },
    { id: '#BM-0036', customer: 'Fatou Diallo', initials: 'FD', product: 'Shea Butter Body Cream', qty: 1, date: '07 Jun 2025', amount: 195, status: 'cancelled' },
    { id: '#BM-0035', customer: 'Chidi Okafor', initials: 'CO', product: 'Maasai Beaded Bracelet Set', qty: 1, date: '06 Jun 2025', amount: 340, status: 'shipped' },
];

const SALES = [42, 68, 55, 90, 74, 115, 88];

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
    const overlay = document.getElementById('sidebar-overlay');
    
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
    
    if (!list.length) {
        grid.innerHTML = `<div class="empty-state" style="grid-column:1/-1"><div class="icon">📦</div><p>No products found. Try a different search.</p></div>`;
        return;
    }
    
    grid.innerHTML = list.map(p => `
      <div class="product-card">
        <div class="product-thumb" style="background:${p.stock===0?'#fdecea':'var(--warm-grey)'}">
          <span>${p.emoji}</span>
          <span class="product-thumb-badge">
            ${p.stock === 0
              ? '<span class="badge badge-danger">Out of Stock</span>'
              : p.stock <= 3
              ? '<span class="badge badge-warning">Low Stock</span>'
              : '<span class="badge badge-success">Active</span>'}
          </span>
        </div>
        <div class="product-info">
          <div class="product-name">${p.name}</div>
          <div class="product-meta">
            <span>${p.cat}</span>·
            <span>${p.stock} in stock</span>
          </div>
          <div class="product-price">R ${p.price.toLocaleString()}</div>
          <div class="product-actions">
            <button class="btn btn-outline btn-sm" onclick="showToast('Opening editor for ${p.name.replace(/'/g,'&#39;')}','warning')">Edit</button>
            <button class="btn-icon" onclick="showToast('${p.name.replace(/'/g,'&#39;')} duplicated','success')" title="Duplicate">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            </button>
            <button class="btn-icon" style="margin-left:auto" onclick="showToast('${p.name.replace(/'/g,'&#39;')} removed','warning')" title="Delete">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
            </button>
          </div>
        </div>
      </div>
    `).join('');
}

function filterProducts(q = '') {
    const searchInput = document.querySelector('.search-input');
    const query = q || (searchInput ? searchInput.value : '');
    
    const filtered = query
        ? PRODUCTS.filter(p => p.name.toLowerCase().includes(query.toLowerCase()) || p.cat.toLowerCase().includes(query.toLowerCase()))
        : PRODUCTS;
    renderProducts(filtered);
}

/* ── ORDERS ──────────────────────────────── */
function renderOrders() {
    const tbody = document.getElementById('orders-tbody');
    if (!tbody) return;
    
    const statusMap = {
        pending: '<span class="badge badge-warning"><span class="badge-dot"></span>Pending</span>',
        shipped: '<span class="badge badge-neutral"><span class="badge-dot"></span>Shipped</span>',
        delivered: '<span class="badge badge-success"><span class="badge-dot"></span>Delivered</span>',
        cancelled: '<span class="badge badge-danger"><span class="badge-dot"></span>Cancelled</span>',
    };
    
    tbody.innerHTML = ORDERS.map(o => `
      <tr>
        <td><span class="order-id">${o.id}</span></td>
        <td>
          <div class="customer-cell">
            <div class="customer-avatar">${o.initials}</div>
            ${o.customer}
          </div>
        </td>
        <td>${o.product} × ${o.qty}</td>
        <td style="color:var(--muted);font-size:.84rem">${o.date}</td>
        <td><span class="amount-cell">R ${o.amount.toLocaleString()}</span></td>
        <td>${statusMap[o.status]}</td>
        <td>
          <button class="btn btn-outline btn-sm" onclick="openOrderModal('${o.id}')">View</button>
        </td>
      </tr>
    `).join('');
}

/* ── CHART ───────────────────────────────── */
function renderChart() {
    const chart = document.getElementById('sales-chart');
    if (!chart) return;
    
    const max = Math.max(...SALES);
    chart.innerHTML = SALES.map((v, i) => `
      <div class="bar ${i === 5 ? 'highlight' : ''}" style="height:${Math.round((v/max)*100)}%" title="R ${v*100}"></div>
    `).join('');
}

/* ── MODAL: ORDER ────────────────────────── */
function openOrderModal(id) {
    const o = ORDERS.find(x => x.id === id);
    if (!o) return;
    
    const titleEl = document.getElementById('order-modal-title');
    const bodyEl = document.getElementById('order-modal-body');
    const modal = document.getElementById('order-modal');
    
    if (titleEl) titleEl.textContent = 'Order ' + o.id;
    if (bodyEl) {
        bodyEl.innerHTML = `
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.2rem">
            <div><div style="font-size:.75rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem">Customer</div><div style="font-weight:600">${o.customer}</div></div>
            <div><div style="font-size:.75rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem">Date</div><div>${o.date}</div></div>
            <div><div style="font-size:.75rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem">Product</div><div>${o.product}</div></div>
            <div><div style="font-size:.75rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem">Qty</div><div>${o.qty}</div></div>
            <div><div style="font-size:.75rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem">Amount</div><div style="font-family:'Syne',sans-serif;font-weight:700;font-size:1.1rem;color:var(--gold)">R ${o.amount.toLocaleString()}</div></div>
            <div><div style="font-size:.75rem;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem">Status</div><div>${o.status}</div></div>
          </div>
          <div style="background:var(--cream);border-radius:var(--radius);padding:.9rem 1rem;">
            <div style="font-size:.82rem;font-weight:600;margin-bottom:.3rem">Shipping Address</div>
            <div style="font-size:.85rem;color:var(--muted)">12 Vilakazi Street, Soweto, Johannesburg, 1804</div>
          </div>
        `;
    }
    if (modal) modal.classList.add('open');
}

function closeOrderModal() {
    const modal = document.getElementById('order-modal');
    if (modal) modal.classList.remove('open');
}

function markShipped() {
    closeOrderModal();
    showToast('Order marked as shipped!', 'success');
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
    showToast('Payout request submitted!', 'success');
}

/* ── SAVE PRODUCT ────────────────────────── */
function saveProduct() {
    const name = document.getElementById('prod-name');
    if (!name || !name.value.trim()) {
        showToast('Please enter a product name', 'warning');
        return;
    }
    showToast(`"${name.value}" listed successfully!`, 'success');
    navigate('products', document.querySelector('[onclick*="products"]'));
}

/* ── TOAST ───────────────────────────────── */
function showToast(msg, type = 'success') {
    const wrap = document.getElementById('toast-wrap');
    if (!wrap) return;
    
    const icons = { success: '✓', warning: '!' };
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    el.innerHTML = `<span class="t-icon">${icons[type] || '✓'}</span><span>${msg}</span>`;
    wrap.appendChild(el);
    setTimeout(() => el.remove(), 3200);
}

/* ── CLOSE MODAL ON OVERLAY CLICK ─────────── */
document.addEventListener('DOMContentLoaded', function() {
    // Close modals when clicking overlay
    document.querySelectorAll('.modal-overlay').forEach(m => {
        m.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('open');
            }
        });
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
    
    // Initialize all components
    renderProducts(PRODUCTS);
    renderOrders();
    renderChart();
    
    console.log('Bona Markets Vendor Portal loaded successfully!');
});