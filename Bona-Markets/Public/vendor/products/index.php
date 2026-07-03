<?php
// ============================================================
// VENDOR PRODUCTS – Index (List all products for vendor)
// ============================================================
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}
if ($_SESSION['role'] !== 'vendor' && $_SESSION['role'] !== 'admin') {
    header('Location: /index.php');
    exit;
}

require_once __DIR__ . '/../../../config/database.php';

$user_id      = $_SESSION['user_id'];
$userFullName = $_SESSION['fullname'] ?? 'Vendor';

// ─── FETCH PRODUCTS ──────────────────────────────────────────
$stmt = $pdo->prepare("
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.vendor_id = ?
    ORDER BY p.created_at DESC
");
$stmt->execute([$user_id]);
$products = $stmt->fetchAll();

// ─── FLASH MESSAGES ──────────────────────────────────────────
$success = $_SESSION['flash_success'] ?? '';
$error   = $_SESSION['flash_error']   ?? '';
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>My Products | Bona Markets Vendor</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="../../assets/css/vendor-dashboard.css" />
    <style>
        body { font-family: 'Inter', sans-serif; background: var(--cream, #faf8f4); color: var(--ink, #1a1208); }
        .page-wrap { max-width: 1100px; margin: 2rem auto; padding: 0 1.5rem; }
        .page-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.75rem; }
        .page-title { font-family: 'Syne', sans-serif; font-size: 1.6rem; font-weight: 700; margin: 0; }
        .page-sub { font-size: .875rem; color: var(--muted, #9c8f7a); margin: .25rem 0 0; }
        .btn { display: inline-flex; align-items: center; gap: .4rem; padding: .55rem 1.1rem; border-radius: 8px; font-size: .875rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all .16s; }
        .btn-primary { background: var(--gold, #e8952a); color: #1a1208; }
        .btn-primary:hover { background: #d4841a; }
        .btn-outline { background: transparent; border: 1.5px solid var(--border, #e4ddd3); color: var(--ink, #1a1208); }
        .btn-outline:hover { border-color: var(--gold, #e8952a); color: var(--gold, #e8952a); }
        .btn-danger { background: #fdecea; border: 1.5px solid #f5c6c0; color: #c04b1e; }
        .btn-danger:hover { background: #f9d5d0; }
        .btn-sm { padding: .35rem .8rem; font-size: .8rem; }
        .alert { padding: .85rem 1.1rem; border-radius: 10px; margin-bottom: 1.25rem; font-size: .875rem; }
        .alert-success { background: #e8f5ee; border: 1px solid #3a7d5e; color: #2d6b4f; }
        .alert-error   { background: #fdecea; border: 1px solid #f5c6c0; color: #c04b1e; }
        .toolbar { display: flex; gap: .75rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
        .search-wrap { position: relative; flex: 1; min-width: 200px; }
        .search-wrap svg { position: absolute; left: .75rem; top: 50%; transform: translateY(-50%); color: var(--muted, #9c8f7a); pointer-events: none; }
        .search-input { width: 100%; padding: .55rem .75rem .55rem 2.2rem; border: 1.5px solid var(--border, #e4ddd3); border-radius: 8px; font-size: .875rem; background: white; }
        .search-input:focus { outline: none; border-color: var(--gold, #e8952a); }
        .select-filter { padding: .55rem .9rem; border: 1.5px solid var(--border, #e4ddd3); border-radius: 8px; font-size: .875rem; background: white; cursor: pointer; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.25rem; }
        .product-card { background: white; border-radius: 14px; overflow: hidden; border: 1px solid var(--border, #e4ddd3); transition: box-shadow .18s; }
        .product-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .product-thumb { height: 160px; background: var(--warm-grey, #f0ede8); position: relative; overflow: hidden; }
        .product-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .product-thumb .no-img { display: flex; align-items: center; justify-content: center; height: 100%; font-size: 3rem; }
        .product-thumb-badge { position: absolute; top: .6rem; left: .6rem; }
        .badge { display: inline-flex; align-items: center; gap: .3rem; padding: .25rem .65rem; border-radius: 999px; font-size: .72rem; font-weight: 600; }
        .badge-success { background: #e8f5ee; color: #2d6b4f; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger  { background: #fdecea; color: #c04b1e; }
        .badge-neutral { background: #e9ecef; color: #495057; }
        .badge-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .product-info { padding: 1rem; }
        .product-name { font-family: 'Syne', sans-serif; font-weight: 700; font-size: .95rem; margin-bottom: .3rem; }
        .product-meta { font-size: .78rem; color: var(--muted, #9c8f7a); margin-bottom: .4rem; }
        .product-price { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 1.05rem; color: var(--gold, #e8952a); margin-bottom: .85rem; }
        .product-actions { display: flex; gap: .5rem; align-items: center; }
        .empty-state { text-align: center; padding: 3.5rem 1rem; color: var(--muted, #9c8f7a); }
        .empty-state .icon { font-size: 3.5rem; margin-bottom: .75rem; }
        .empty-state p { font-size: .9rem; margin-bottom: 1rem; }
        .back-link { font-size: .85rem; color: var(--muted, #9c8f7a); text-decoration: none; }
        .back-link:hover { color: var(--gold, #e8952a); }
    </style>
</head>
<body>

<div class="page-wrap">

    <p style="margin-bottom:1rem;"><a class="back-link" href="/vendor/dashboard.php">← Back to Dashboard</a></p>

    <div class="page-header">
        <div>
            <h1 class="page-title">My Products</h1>
            <p class="page-sub">Manage your <?= count($products) ?> listed item<?= count($products) !== 1 ? 's' : '' ?>.</p>
        </div>
        <a class="btn btn-primary" href="create.php">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Product
        </a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (count($products) > 0): ?>

        <div class="toolbar">
            <div class="search-wrap">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input class="search-input" type="text" placeholder="Search products…" id="searchInput" oninput="filterProducts()" />
            </div>
            <select class="select-filter" id="statusFilter" onchange="filterProducts()">
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="low">Low Stock</option>
                <option value="out">Out of Stock</option>
                <option value="draft">Draft</option>
            </select>
        </div>

        <div class="product-grid" id="productGrid">
            <?php foreach ($products as $p): ?>
                <?php
                    $stockStatus = $p['stock'] == 0 ? 'out' : ($p['stock'] <= 3 ? 'low' : 'active');
                    if ($p['status'] === 'draft') $stockStatus = 'draft';
                ?>
                <div class="product-card"
                     data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>"
                     data-stock-status="<?= $stockStatus ?>">

                    <div class="product-thumb" style="background:<?= $p['stock'] == 0 ? '#fdecea' : '#f0ede8' ?>">
                        <?php if ($p['image_url']): ?>
                            <img src="/<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" />
                        <?php else: ?>
                            <div class="no-img">🛍️</div>
                        <?php endif; ?>
                        <span class="product-thumb-badge">
                            <?php if ($p['status'] === 'draft'): ?>
                                <span class="badge badge-neutral"><span class="badge-dot"></span>Draft</span>
                            <?php elseif ($p['stock'] == 0): ?>
                                <span class="badge badge-danger"><span class="badge-dot"></span>Out of Stock</span>
                            <?php elseif ($p['stock'] <= 3): ?>
                                <span class="badge badge-warning"><span class="badge-dot"></span>Low Stock</span>
                            <?php else: ?>
                                <span class="badge badge-success"><span class="badge-dot"></span>Active</span>
                            <?php endif; ?>
                        </span>
                    </div>

                    <div class="product-info">
                        <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                        <div class="product-meta">
                            <?= htmlspecialchars($p['category_name'] ?? 'Uncategorised') ?> · <?= $p['stock'] ?> in stock
                        </div>
                        <div class="product-price">R <?= number_format($p['price'], 2) ?></div>
                        <div class="product-actions">
                            <a class="btn btn-outline btn-sm" href="edit.php?id=<?= $p['id'] ?>">Edit</a>
                            <form method="POST" action="delete.php" style="margin:0;"
                                  onsubmit="return confirm('Delete &quot;<?= htmlspecialchars(addslashes($p['name'])) ?>&quot;? This cannot be undone.')">
                                <input type="hidden" name="product_id" value="<?= $p['id'] ?>" />
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="empty-state">
            <div class="icon">📦</div>
            <p>You haven't listed any products yet.</p>
            <a class="btn btn-primary" href="create.php">Add Your First Product</a>
        </div>
    <?php endif; ?>

</div>

<script>
function filterProducts() {
    const q      = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    document.querySelectorAll('.product-card').forEach(card => {
        const nameMatch   = card.dataset.name.includes(q);
        const statusMatch = status === 'all' || card.dataset.stockStatus === status;
        card.style.display = (nameMatch && statusMatch) ? '' : 'none';
    });
}
// Auto-dismiss alerts
document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => { el.style.transition = 'opacity .5s'; el.style.opacity = 0; setTimeout(() => el.remove(), 500); }, 3500);
});
</script>

</body>
</html>
