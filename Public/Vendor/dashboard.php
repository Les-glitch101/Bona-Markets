<?php
// ============================================================
// VENDOR DASHBOARD – Complete Single File (FIXED)
// ============================================================

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /Bona-Markets/Public/login.php');
    exit;
}

// Redirect if not a vendor or admin
if ($_SESSION['role'] !== 'vendor' && $_SESSION['role'] !== 'admin') {
    header('Location: /Bona-Markets/Public/index.php');
    exit;
}

// ─── DATABASE CONNECTION ─────────────────────────────────────
require_once '../../Config/database.php';

$user_id = $_SESSION['user_id'];
$userFullName = $_SESSION['fullname'] ?? 'Vendor';
$userEmail = $_SESSION['email'] ?? '';

// ─── GET VENDOR PROFILE ────────────────────────────────────────
$stmt = $pdo->prepare("SELECT * FROM vendor_profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$vendor = $stmt->fetch();

if (!$vendor) {
    header('Location: /Bona-Markets/Public/apply.php');
    exit;
}

$pendingApproval = ($vendor['approved'] == 0);

// ─── GET PRODUCT STATS ──────────────────────────────────────────
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM products WHERE vendor_id = ?");
$stmt->execute([$user_id]);
$productCount = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM products WHERE vendor_id = ? AND stock = 0");
$stmt->execute([$user_id]);
$outOfStock = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM products WHERE vendor_id = ? AND stock <= 3 AND stock > 0");
$stmt->execute([$user_id]);
$lowStock = $stmt->fetch()['total'];

// ─── GET ORDER STATS ────────────────────────────────────────────
$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT o.id) as total, 
           SUM(CASE WHEN o.status = 'pending' THEN 1 ELSE 0 END) as pending,
           SUM(CASE WHEN o.status = 'shipped' THEN 1 ELSE 0 END) as shipped,
           SUM(CASE WHEN o.status = 'delivered' THEN 1 ELSE 0 END) as delivered
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE p.vendor_id = ?
");
$stmt->execute([$user_id]);
$orderStats = $stmt->fetch();

$totalOrders = $orderStats['total'] ?? 0;
$pendingOrders = $orderStats['pending'] ?? 0;
$shippedOrders = $orderStats['shipped'] ?? 0;
$deliveredOrders = $orderStats['delivered'] ?? 0;

// ─── GET REVENUE ─────────────────────────────────────────────────
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(oi.price * oi.quantity), 0) as total_revenue
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE p.vendor_id = ? AND o.status = 'delivered'
");
$stmt->execute([$user_id]);
$totalRevenue = $stmt->fetch()['total_revenue'] ?? 0;

// ─── GET RECENT ORDERS ──────────────────────────────────────────
$stmt = $pdo->prepare("
    SELECT o.id, o.created_at, o.status, o.total,
           u.fullname as customer_name, u.email as customer_email,
           GROUP_CONCAT(CONCAT(p.name, ' x', oi.quantity) SEPARATOR ', ') as products
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    JOIN users u ON o.user_id = u.id
    WHERE p.vendor_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT 5
");
$stmt->execute([$user_id]);
$recentOrders = $stmt->fetchAll();

// ─── GET PRODUCTS FOR GRID ──────────────────────────────────────
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.vendor_id = ?
    ORDER BY p.created_at DESC
");
$stmt->execute([$user_id]);
$products = $stmt->fetchAll();

// ─── GET CATEGORIES ─────────────────────────────────────────────
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// ─── GET GREETING ────────────────────────────────────────────────
$hour = date('H');
if ($hour < 12) {
    $greeting = 'morning';
} elseif ($hour < 18) {
    $greeting = 'afternoon';
} else {
    $greeting = 'evening';
}

// ─── HANDLE ADD PRODUCT FORM ────────────────────────────────────
$addError = '';
$addSuccess = '';
$newProductId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    
    if (empty($name)) {
        $addError = 'Please enter a product name.';
    } elseif ($price <= 0) {
        $addError = 'Please enter a valid price (must be greater than 0).';
    } elseif ($stock < 0) {
        $addError = 'Stock quantity cannot be negative.';
    } else {
        $image_url = null;
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $addError = 'Image upload error.';
            } else {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $fileType = mime_content_type($_FILES['image']['tmp_name']);
                
                if (!in_array($fileType, $allowedTypes)) {
                    $addError = 'Invalid file type. Please upload JPEG, PNG, GIF, or WEBP images only.';
                } elseif ($_FILES['image']['size'] > 10 * 1024 * 1024) {
                    $addError = 'File is too large. Maximum size is 10MB.';
                } else {
                    $uploadDir = __DIR__ . '/../uploads/products/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $fileName = uniqid() . '.' . $fileExtension;
                    $filePath = $uploadDir . $fileName;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                        $image_url = 'uploads/products/' . $fileName;
                    } else {
                        $addError = 'Failed to save image.';
                    }
                }
            }
        }
        
        if (empty($addError)) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO products (
                        vendor_id, category_id, name, description, 
                        price, image_url, stock, status, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $user_id, $category_id, $name, $description,
                    $price, $image_url, $stock, $status
                ]);
                $newProductId = $pdo->lastInsertId();
                $addSuccess = 'Product "' . htmlspecialchars($name) . '" added successfully!';
                
                // Refresh product list
                $stmt = $pdo->prepare("
                    SELECT p.*, c.name as category_name 
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.vendor_id = ?
                    ORDER BY p.created_at DESC
                ");
                $stmt->execute([$user_id]);
                $products = $stmt->fetchAll();
                $productCount = count($products);
                
            } catch (PDOException $e) {
                $addError = 'Database error: ' . $e->getMessage();
            }
        }
    }
}
// ─── HANDLE EDIT PRODUCT ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $product_id = intval($_POST['product_id']);
    $data = [
        'name'        => trim($_POST['name'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'price'       => floatval($_POST['price'] ?? 0),
        'category_id' => !empty($_POST['category_id']) ? intval($_POST['category_id']) : null,
        'stock'       => intval($_POST['stock'] ?? 0),
        'status'      => $_POST['status'] ?? 'active',
    ];

    // Handle image upload if a new one was provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($_FILES['image']['tmp_name']);
        if (in_array($fileType, $allowedTypes) && $_FILES['image']['size'] <= 10 * 1024 * 1024) {
            $uploadDir = __DIR__ . '/../uploads/products/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $fileName = uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                $data['image_url'] = 'uploads/products/' . $fileName;
            }
        }
    }

    $stmt = $pdo->prepare("
        UPDATE products 
        SET name=?, description=?, price=?, category_id=?, stock=?, status=?
        " . (!empty($data['image_url']) ? ", image_url=?" : "") . "
        WHERE id=? AND vendor_id=?
    ");

    $params = [$data['name'], $data['description'], $data['price'], $data['category_id'], $data['stock'], $data['status']];
    if (!empty($data['image_url'])) $params[] = $data['image_url'];
    $params[] = $product_id;
    $params[] = $user_id;

    $stmt->execute($params);
    $editSuccess = 'Product updated successfully!';

    // Refresh product list
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.vendor_id = ? ORDER BY p.created_at DESC");
    $stmt->execute([$user_id]);
    $products = $stmt->fetchAll();
}

// ─── HANDLE DELETE PRODUCT ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $product_id = intval($_POST['product_id']);

    // Verify ownership first
    $check = $pdo->prepare("SELECT id FROM products WHERE id = ? AND vendor_id = ?");
    $check->execute([$product_id, $user_id]);

    if ($check->fetch()) {
        // Clear from carts first
        $pdo->prepare("DELETE FROM cart WHERE product_id = ?")->execute([$product_id]);
        // Delete product
        $pdo->prepare("DELETE FROM products WHERE id = ? AND vendor_id = ?")->execute([$product_id, $user_id]);
        $deleteSuccess = 'Product deleted.';

        // Refresh product list
        $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.vendor_id = ? ORDER BY p.created_at DESC");
        $stmt->execute([$user_id]);
        $products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vendor Dashboard | Bona Markets</title>

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet" />

    <!-- External CSS -->
    <link rel="stylesheet" href="../assets/css/vendor-dashboard.css" />

    <style>
        .alert-success {
            background: #e8f5ee;
            border: 1px solid #3a7d5e;
            border-radius: 10px;
            padding: .8rem 1rem;
            margin-bottom: 1.5rem;
            color: #2d6b4f;
        }
        .alert-error {
            background: #fdecea;
            border: 1px solid #f5c6c0;
            border-radius: 10px;
            padding: .8rem 1rem;
            margin-bottom: 1.5rem;
            color: #c04b1e;
        }
        .upload-zone {
            border: 2px dashed var(--border);
            border-radius: var(--radius-lg);
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all .18s;
            color: var(--muted);
            font-size: .88rem;
        }
        .upload-zone:hover {
            border-color: var(--gold);
            background: #fff8ed;
            color: var(--gold);
        }
        .upload-zone .upload-icon {
            font-size: 2rem;
            margin-bottom: .5rem;
        }
        .upload-zone.dragover {
            border-color: var(--gold);
            background: #fff8ed;
        }
        .image-preview {
            margin-top: 1rem;
            display: none;
            text-align: center;
        }
        .image-preview img {
            max-width: 100%;
            max-height: 150px;
            border-radius: 8px;
            border: 1px solid var(--border);
        }
        .image-preview .remove-btn {
            display: block;
            margin-top: 0.3rem;
            font-size: .75rem;
            color: var(--rust);
            cursor: pointer;
            background: none;
            border: none;
            text-decoration: underline;
        }
        .image-preview .remove-btn:hover {
            color: #a03a15;
        }
        .product-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-thumb .no-image {
            font-size: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body>

    <!-- ============================================================ -->
    <!-- TOPBAR -->
    <!-- ============================================================ -->
    <header class="topbar">
        <div class="container">
            <div class="topbar-inner">
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()"
                        aria-label="Toggle navigation">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <line x1="3" y1="6" x2="21" y2="6" />
                            <line x1="3" y1="12" x2="21" y2="12" />
                            <line x1="3" y1="18" x2="21" y2="18" />
                        </svg>
                    </button>
                    <div class="brand">
                        <span class="brand-dot"></span>
                        Bona<span>Markets</span>
                    </div>
                </div>
                <div class="topbar-right">
                    <span class="topbar-badge">Vendor Portal</span>
                    <button class="notif-btn" onclick="showToast('You have <?= $pendingOrders ?> pending orders!','warning')">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                            <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                        </svg>
                        <?php if ($pendingOrders > 0): ?>
                            <span class="dot"></span>
                        <?php endif; ?>
                    </button>
                    <div class="topbar-avatar" title="<?= htmlspecialchars($userFullName) ?>">
                        <?= strtoupper(substr($userFullName, 0, 2)) ?>
                    </div>
                    <a href="/Bona-Markets/Public/logout.php" style="color:#9c8f7a;text-decoration:none;font-size:.85rem;padding:0.25rem 0.5rem;">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="shell">

        <!-- ============================================================ -->
        <!-- SIDEBAR -->
        <!-- ============================================================ -->
        <nav class="sidebar" id="sidebar">
            <button class="sidebar-close" onclick="toggleSidebar()">✕</button>

            <span class="sidebar-section-label">Overview</span>
            <a class="nav-item active" onclick="navigate('dashboard',this)">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
                <span>Dashboard</span>
            </a>

            <span class="sidebar-section-label">Store</span>
            <a class="nav-item" onclick="navigate('products',this)">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                    <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                </svg>
                <span>Products</span>
                <span class="nav-count"><?= $productCount ?></span>
            </a>
            <a class="nav-item" onclick="navigate('add-product',this)">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="16" />
                    <line x1="8" y1="12" x2="16" y2="12" />
                </svg>
                <span>Add Product</span>
            </a>
            <a class="nav-item" onclick="navigate('orders',this)">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                </svg>
                <span>Orders</span>
                <span class="nav-count"><?= $totalOrders ?></span>
            </a>

            <div class="sidebar-divider"></div>

            <span class="sidebar-section-label">Finance</span>
            <a class="nav-item" onclick="navigate('payouts',this)">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="2" y="5" width="20" height="14" rx="2" />
                    <line x1="2" y1="10" x2="22" y2="10" />
                </svg>
                <span>Payouts</span>
            </a>

            <div class="sidebar-divider"></div>

            <span class="sidebar-section-label">Account</span>
            <a class="nav-item" onclick="navigate('reviews',this)">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                </svg>
                <span>Reviews</span>
                <span class="nav-count">0</span>
            </a>
            <a class="nav-item" onclick="navigate('settings',this)">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="3" />
                    <path
                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" />
                </svg>
                <span>Settings</span>
            </a>
        </nav>

        <!-- ============================================================ -->
        <!-- MAIN CONTENT -->
        <!-- ============================================================ -->
        <main class="main">

            <!-- ============================================================ -->
            <!-- PAGE: DASHBOARD -->
            <!-- ============================================================ -->
            <div class="page active" id="page-dashboard">

                <div class="page-header">
                    <div>
                        <h1 class="page-title">Good <?= $greeting ?>, <?= htmlspecialchars($userFullName) ?> 👋</h1>
                        <p class="page-sub">Here's what's happening with your store today.</p>
                    </div>
                    <button class="btn btn-primary" onclick="navigate('add-product', this)">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Add Product
                    </button>
                </div>

                <?php if ($pendingApproval): ?>
                    <div style="background:#fff8ed;border:1px solid #e8952a;border-radius:16px;padding:1rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                        <span style="font-size:1.5rem;">⏳</span>
                        <div style="flex:1;">
                            <p style="font-weight:600;color:#1a1208;">Your vendor application is pending approval</p>
                            <p style="font-size:.85rem;color:#7a6e5f;">Our team is reviewing your application. You'll be notified via email once approved.</p>
                        </div>
                        <a href="/Bona-Markets/Public/apply.php" style="background:#e8952a;color:#1a1208;padding:.4rem 1rem;border-radius:8px;text-decoration:none;font-weight:600;font-size:.85rem;">View Status</a>
                    </div>
                <?php endif; ?>

                <div class="stats-grid">
                    <div class="stat-card gold">
                        <div class="stat-label">Total Revenue</div>
                        <div class="stat-value">R <?= number_format($totalRevenue, 2) ?></div>
                        <div class="stat-delta delta-up">All time earnings</div>
                    </div>
                    <div class="stat-card rust">
                        <div class="stat-label">Active Orders</div>
                        <div class="stat-value"><?= $pendingOrders + $shippedOrders ?></div>
                        <div class="stat-delta <?= $pendingOrders > 0 ? 'delta-up' : '' ?>">
                            <?php if ($pendingOrders > 0): ?>
                                <?= $pendingOrders ?> awaiting shipment
                            <?php else: ?>
                                All orders processed
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="stat-card sage">
                        <div class="stat-label">Products Listed</div>
                        <div class="stat-value"><?= $productCount ?></div>
                        <div class="stat-delta <?= $lowStock > 0 ? 'delta-down' : 'delta-up' ?>">
                            <?php if ($lowStock > 0): ?>
                                <?= $lowStock ?> low in stock
                            <?php else: ?>
                                All stocked up
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="stat-card ink">
                        <div class="stat-label">Store Rating</div>
                        <div class="stat-value">—</div>
                        <div class="stat-delta">No reviews yet</div>
                    </div>
                </div>

                <div class="dashboard-grid">
                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Sales — Last 7 Days</span>
                            <span style="font-size:.78rem;color:var(--muted);"><?= date('F Y') ?></span>
                        </div>
                        <?php if ($totalRevenue > 0): ?>
                            <div class="mini-chart" id="sales-chart"></div>
                            <div class="chart-labels">
                                <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
                            </div>
                        <?php else: ?>
                            <div style="text-align:center;padding:1.5rem 0.5rem;color:var(--muted);">
                                <p style="font-size:.9rem;">No sales data available yet</p>
                                <p style="font-size:.78rem;">Start selling to see your sales chart here.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <span class="card-title">Recent Orders</span>
                            <button class="btn btn-outline btn-sm" onclick="navigate('orders', this)">View all</button>
                        </div>
                        <div class="table-wrap">
                            <?php if (count($recentOrders) > 0): ?>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Order</th>
                                            <th>Customer</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentOrders as $order): ?>
                                            <tr>
                                                <td><span class="order-id">#BM-<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></span></td>
                                                <td><span><?= htmlspecialchars($order['customer_name'] ?? 'Guest') ?></span></td>
                                                <td><span class="amount-cell">R <?= number_format($order['total'], 2) ?></span></td>
                                                <td>
                                                    <?php
                                                    $statusClass = match ($order['status']) {
                                                        'pending' => 'badge-warning',
                                                        'shipped' => 'badge-neutral',
                                                        'delivered' => 'badge-success',
                                                        default => 'badge-danger'
                                                    };
                                                    ?>
                                                    <span class="badge <?= $statusClass ?>">
                                                        <span class="badge-dot"></span>
                                                        <?= ucfirst($order['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p style="text-align:center;color:var(--muted);padding:1rem 0;">No orders yet</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- PAGE: PRODUCTS -->
            <!-- ============================================================ -->
            <div class="page" id="page-products">

                <div class="page-header">
                    <div>
                        <h1 class="page-title">My Products</h1>
                        <p class="page-sub">Manage your listed items and inventory.</p>
                    </div>
                    <button class="btn btn-primary" onclick="navigate('add-product', this)">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        New Product
                    </button>
                </div>

                <div class="toolbar">
                    <div class="search-wrap">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <input class="search-input" type="text" placeholder="Search products…" oninput="filterProducts(this.value)" />
                    </div>
                    <select class="select-filter" onchange="filterProducts()">
                        <option>All Categories</option>
                        <option>Textiles & Fashion</option>
                        <option>Handcrafts</option>
                        <option>Food & Spices</option>
                        <option>Jewellery</option>
                    </select>
                    <select class="select-filter">
                        <option>All Status</option>
                        <option>Active</option>
                        <option>Out of Stock</option>
                        <option>Draft</option>
                    </select>
                </div>

                <?php if (count($products) > 0): ?>
                    <div class="product-grid" id="product-grid">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <div class="product-thumb" style="background:<?= $product['stock'] == 0 ? '#fdecea' : 'var(--warm-grey)' ?>">
                                    <?php if ($product['image_url']): ?>
                                        <img src="/Bona-Markets/Public/<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
                                    <?php else: ?>
                                        <span class="no-image">🛍️</span>
                                    <?php endif; ?>
                                    <span class="product-thumb-badge">
                                        <?php if ($product['stock'] == 0): ?>
                                            <span class="badge badge-danger">Out of Stock</span>
                                        <?php elseif ($product['stock'] <= 3): ?>
                                            <span class="badge badge-warning">Low Stock</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="product-info">
                                    <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                                    <div class="product-meta">
                                        <span><?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?></span>·
                                        <span><?= $product['stock'] ?> in stock</span>
                                    </div>
                                    <div class="product-price">R <?= number_format($product['price'], 2) ?></div>
                                   <div class="product-actions">
    <button class="btn btn-outline btn-sm" onclick="openEditModal(<?= htmlspecialchars(json_encode($product)) ?>)">Edit</button>
    <form method="POST" action="" style="margin-left:auto" onsubmit="return confirm('Delete <?= htmlspecialchars($product['name']) ?>? This cannot be undone.')">
        <input type="hidden" name="delete_product" value="1" />
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>" />
        <button type="submit" class="btn-icon" title="Delete">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="3 6 5 6 21 6" />
                <path d="M19 6l-1 14H6L5 6" />
                <path d="M10 11v6M14 11v6" />
                <path d="M9 6V4h6v2" />
            </svg>
        </button>
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
                        <button class="btn btn-primary" onclick="navigate('add-product', this)">
                            Add Your First Product
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ============================================================ -->
            <!-- PAGE: ADD PRODUCT -->
            <!-- ============================================================ -->
            <div class="page" id="page-add-product">

                <div class="page-header">
                    <div>
                        <h1 class="page-title">Add New Product</h1>
                        <p class="page-sub">List a new item in your store.</p>
                    </div>
                    <button class="btn btn-outline" onclick="navigate('products', this)">← Back to Products</button>
                </div>

                <?php if ($addError): ?>
                    <div class="alert-error">
                        <p style="text-align:center;"><?= htmlspecialchars($addError) ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($addSuccess): ?>
                    <div class="alert-success">
                        <p style="text-align:center;">
                            <?= htmlspecialchars($addSuccess) ?>
                            <?php if ($newProductId): ?>
                                <br><small><a href="/Bona-Markets/Public/products/show.php?id=<?= $newProductId ?>" style="color:var(--gold);text-decoration:underline;">View Product</a></small>
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data" id="addProductForm">
                    <input type="hidden" name="add_product" value="1" />
                    
                    <div class="add-product-grid">
                        <div>
                            <div class="card">
                                <div class="card-header"><span class="card-title">Product Details</span></div>
                                <div class="form-grid">
                                    <div class="form-group full">
                                        <label>Product Name <span style="color:var(--rust)">*</span></label>
                                        <input type="text" name="name" placeholder="e.g. Kente Cloth Tote Bag" required
                                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" />
                                    </div>
                                    <div class="form-group full">
                                        <label>Description</label>
                                        <textarea name="description" placeholder="Describe your product — materials, origin, use…" rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select class="form-select" name="category_id">
                                            <option value="">Select category</option>
                                            <?php foreach ($categories as $cat): ?>
                                                <option value="<?= $cat['id'] ?>" <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cat['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Tags</label>
                                        <input type="text" name="tags" placeholder="handmade, african, cotton…" 
                                               value="<?= htmlspecialchars($_POST['tags'] ?? '') ?>" />
                                        <span class="form-hint">Separate with commas</span>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header"><span class="card-title">Pricing & Inventory</span></div>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Price (ZAR) <span style="color:var(--rust)">*</span></label>
                                        <input type="number" name="price" placeholder="0.00" step="0.01" required 
                                               value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" />
                                    </div>
                                    <div class="form-group">
                                        <label>Compare-at Price</label>
                                        <input type="number" name="compare_price" placeholder="0.00" step="0.01" 
                                               value="<?= htmlspecialchars($_POST['compare_price'] ?? '') ?>" />
                                        <span class="form-hint">Optional — shows a strikethrough price</span>
                                    </div>
                                    <div class="form-group">
                                        <label>Stock Quantity <span style="color:var(--rust)">*</span></label>
                                        <input type="number" name="stock" placeholder="0" required 
                                               value="<?= htmlspecialchars($_POST['stock'] ?? '0') ?>" />
                                    </div>
                                    <div class="form-group">
                                        <label>SKU</label>
                                        <input type="text" name="sku" placeholder="BM-SKU-001" 
                                               value="<?= htmlspecialchars($_POST['sku'] ?? '') ?>" />
                                    </div>
                                    <div class="form-group">
                                        <label>Weight (kg)</label>
                                        <input type="number" name="weight" placeholder="0.0" step="0.1" 
                                               value="<?= htmlspecialchars($_POST['weight'] ?? '') ?>" />
                                    </div>
                                    <div class="form-group">
                                        <label>Country of Origin</label>
                                        <input type="text" name="origin" placeholder="e.g. Ghana" 
                                               value="<?= htmlspecialchars($_POST['origin'] ?? '') ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="card">
                                <div class="card-header"><span class="card-title">Product Images</span></div>
                                <div class="upload-zone" id="uploadZone">
                                    <div class="upload-icon">🖼️</div>
                                    <strong>Click to upload or drag & drop</strong>
                                    <p style="margin-top:.3rem;font-size:.8rem">PNG, JPG, GIF, WEBP up to 10 MB</p>
                                    <input type="file" id="imageInput" name="image" accept="image/*" style="display:none;" />
                                </div>
                                <div class="image-preview" id="imagePreview">
                                    <img id="previewImg" src="#" alt="Preview" />
                                    <button type="button" class="remove-btn" onclick="removeImage()">Remove image</button>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header"><span class="card-title">Status & Visibility</span></div>
                                <div class="form-group" style="margin-bottom:1rem">
                                    <label>Listing Status</label>
                                    <select class="form-select" name="status">
                                        <option value="active" <?= (isset($_POST['status']) && $_POST['status'] === 'active') ? 'selected' : '' ?>>Active — visible to buyers</option>
                                        <option value="draft" <?= (isset($_POST['status']) && $_POST['status'] === 'draft') ? 'selected' : '' ?>>Draft — save for later</option>
                                        <option value="archived" <?= (isset($_POST['status']) && $_POST['status'] === 'archived') ? 'selected' : '' ?>>Archived</option>
                                    </select>
                                </div>
                                <div class="toggle-wrap">
                                    <div>
                                        <div class="toggle-label">Feature in store</div>
                                        <div class="toggle-sub">Pin to your storefront homepage</div>
                                    </div>
                                    <label class="toggle"><input type="checkbox" name="featured" checked /><span class="toggle-slider"></span></label>
                                </div>
                                <div class="toggle-wrap">
                                    <div>
                                        <div class="toggle-label">Allow reviews</div>
                                    </div>
                                    <label class="toggle"><input type="checkbox" name="allow_reviews" checked /><span class="toggle-slider"></span></label>
                                </div>
                            </div>

                            <div class="action-buttons">
                                <button type="submit" class="btn btn-primary" style="flex:1">Save Product</button>
                                <button type="button" class="btn btn-outline" onclick="saveDraft()">Save Draft</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ============================================================ -->
            <!-- PAGE: ORDERS (EMPTY - NO DEMO DATA) -->
            <!-- ============================================================ -->
            <div class="page" id="page-orders">

                <div class="page-header">
                    <div>
                        <h1 class="page-title">Orders</h1>
                        <p class="page-sub">Track and manage incoming customer orders.</p>
                    </div>
                </div>

                <div class="toolbar">
                    <div class="search-wrap">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                        <input class="search-input" type="text" placeholder="Search by order or customer…" />
                    </div>
                    <select class="select-filter">
                        <option>All Orders</option>
                        <option>Pending</option>
                        <option>Shipped</option>
                        <option>Delivered</option>
                        <option>Cancelled</option>
                    </select>
                </div>

                <div class="card card-table">
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Products</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" style="text-align:center;color:var(--muted);padding:2rem;">
                                        <div style="font-size:2rem;margin-bottom:0.5rem;">📦</div>
                                        <p style="font-weight:500;color:var(--ink);">No orders yet</p>
                                        <p style="font-size:.85rem;">When customers place orders, they'll appear here.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- PAGE: PAYOUTS -->
            <!-- ============================================================ -->
            <div class="page" id="page-payouts">

                <div class="page-header">
                    <div>
                        <h1 class="page-title">Payouts</h1>
                        <p class="page-sub">Track earnings and request withdrawals.</p>
                    </div>
                    <button class="btn btn-primary" onclick="openWithdrawModal()">Request Payout</button>
                </div>

                <div class="payout-banner">
                    <div>
                        <div class="payout-label">Available Balance</div>
                        <div class="payout-amount"><span>R</span> <?= number_format($totalRevenue * 0.92, 2) ?></div>
                        <div class="payout-meta">Next automatic payout · <?= date('d F Y', strtotime('+30 days')) ?></div>
                    </div>
                    <div style="text-align:right">
                        <div class="payout-label">Total Earned (All Time)</div>
                        <div style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:700;color:white">R
                            <?= number_format($totalRevenue, 2) ?></div>
                        <div class="payout-meta">Commission (8%) · R <?= number_format($totalRevenue * 0.08, 2) ?> paid</div>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card gold">
                        <div class="stat-label">This Month</div>
                        <div class="stat-value">R <?= number_format($totalRevenue * 0.4, 2) ?></div>
                        <div class="stat-delta delta-up">↑ 12.4% vs last month</div>
                    </div>
                    <div class="stat-card sage">
                        <div class="stat-label">Pending Clearance</div>
                        <div class="stat-value">R <?= number_format($totalRevenue * 0.2, 2) ?></div>
                        <div class="stat-delta">Clears in 3–5 days</div>
                    </div>
                    <div class="stat-card ink">
                        <div class="stat-label">Last Payout</div>
                        <div class="stat-value">R <?= number_format($totalRevenue * 0.3, 2) ?></div>
                        <div class="stat-delta"><?= date('d F Y', strtotime('-30 days')) ?></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><span class="card-title">Payout History</span></div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Reference</th>
                                    <th>Method</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" style="text-align:center;color:var(--muted);padding:1.5rem;">
                                        <p style="font-weight:500;color:var(--ink);">No payout history yet</p>
                                        <p style="font-size:.85rem;">Payouts will appear here once you start earning.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- PAGE: REVIEWS -->
            <!-- ============================================================ -->
            <div class="page" id="page-reviews">

                <div class="page-header">
                    <div>
                        <h1 class="page-title">Customer Reviews</h1>
                        <p class="page-sub">See what buyers are saying about your products.</p>
                    </div>
                </div>

                <div class="stats-grid reviews-stats">
                    <div class="stat-card gold">
                        <div class="stat-label">Overall Rating</div>
                        <div class="stat-value">—</div>
                        <div class="stat-delta">No reviews yet</div>
                    </div>
                    <div class="stat-card sage">
                        <div class="stat-label">5 Stars</div>
                        <div class="stat-value">0</div>
                        <div class="stat-delta">0% of reviews</div>
                    </div>
                    <div class="stat-card ink">
                        <div class="stat-label">4 Stars</div>
                        <div class="stat-value">0</div>
                    </div>
                    <div class="stat-card rust">
                        <div class="stat-label">3 Stars or Below</div>
                        <div class="stat-value">0</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <span class="card-title">All Reviews</span>
                        <span style="font-size:.78rem;color:var(--muted);">0 reviews total</span>
                    </div>
                    <div style="text-align:center;padding:2.5rem 1rem;color:var(--muted);">
                        <div style="font-size:3rem;margin-bottom:.75rem;">⭐</div>
                        <p style="font-size:.9rem;font-weight:500;color:var(--ink);">No reviews yet</p>
                        <p style="font-size:.82rem;">When customers review your products, they'll appear here.</p>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- PAGE: SETTINGS -->
            <!-- ============================================================ -->
            <div class="page" id="page-settings">

                <div class="page-header">
                    <div>
                        <h1 class="page-title">Store Settings</h1>
                        <p class="page-sub">Manage your vendor profile and preferences.</p>
                    </div>
                    <button class="btn btn-primary" onclick="saveSettings()">Save Changes</button>
                </div>

                <div class="store-hero">
                    <div class="store-logo">🛍️</div>
                    <div class="store-info">
                        <h2><?= htmlspecialchars($vendor['business_name'] ?? 'My Store') ?></h2>
                        <p>Member since <?= date('F Y', strtotime($vendor['applied_at'] ?? 'now')) ?> · <?= htmlspecialchars($vendor['city'] ?? 'Johannesburg') ?>, <?= htmlspecialchars($vendor['country'] ?? 'South Africa') ?></p>
                    </div>
                </div>

                <div class="settings-grid">
                    <div>
                        <div class="card">
                            <div class="card-header"><span class="card-title">Store Profile</span></div>
                            <div class="form-grid">
                                <div class="form-group full">
                                    <label>Store Name</label>
                                    <input type="text" id="settings-store-name" value="<?= htmlspecialchars($vendor['business_name'] ?? '') ?>" />
                                </div>
                                <div class="form-group full">
                                    <label>Store Bio</label>
                                    <textarea id="settings-store-bio" rows="3"><?= htmlspecialchars($vendor['description'] ?? '') ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="tel" id="settings-phone" value="<?= htmlspecialchars($vendor['phone'] ?? '') ?>" />
                                </div>
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" id="settings-city" value="<?= htmlspecialchars($vendor['city'] ?? '') ?>" />
                                </div>
                                <div class="form-group full">
                                    <label>Store URL</label>
                                    <input type="url" id="settings-url" value="bonamarkets.co.za/<?= strtolower(str_replace(' ', '-', $vendor['business_name'] ?? 'store')) ?>" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="card">
                            <div class="card-header"><span class="card-title">Banking Details</span></div>
                            <div class="form-grid">
                                <div class="form-group full">
                                    <label>Account Holder Name</label>
                                    <input type="text" id="settings-account-holder" value="<?= htmlspecialchars($userFullName) ?>" />
                                </div>
                                <div class="form-group">
                                    <label>Bank Name</label>
                                    <input type="text" id="settings-bank-name" placeholder="e.g. FNB" />
                                </div>
                                <div class="form-group">
                                    <label>Account Number</label>
                                    <input type="text" id="settings-account-number" placeholder="1234567890" />
                                </div>
                                <div class="form-group">
                                    <label>Branch Code</label>
                                    <input type="text" id="settings-branch-code" placeholder="250655" />
                                </div>
                                <div class="form-group">
                                    <label>Account Type</label>
                                    <select class="form-select" id="settings-account-type">
                                        <option>Cheque / Current</option>
                                        <option>Savings</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header"><span class="card-title">Notifications</span></div>
                            <div class="toggle-wrap">
                                <div>
                                    <div class="toggle-label">New order alerts</div>
                                    <div class="toggle-sub">Email & SMS when an order comes in</div>
                                </div>
                                <label class="toggle"><input type="checkbox" checked /><span class="toggle-slider"></span></label>
                            </div>
                            <div class="toggle-wrap">
                                <div>
                                    <div class="toggle-label">Payout confirmations</div>
                                </div>
                                <label class="toggle"><input type="checkbox" checked /><span class="toggle-slider"></span></label>
                            </div>
                            <div class="toggle-wrap">
                                <div>
                                    <div class="toggle-label">Review notifications</div>
                                </div>
                                <label class="toggle"><input type="checkbox" /><span class="toggle-slider"></span></label>
                            </div>
                            <div class="toggle-wrap">
                                <div>
                                    <div class="toggle-label">Marketing updates</div>
                                    <div class="toggle-sub">Tips and promotions from Bona Markets</div>
                                </div>
                                <label class="toggle"><input type="checkbox" checked /><span class="toggle-slider"></span></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- ============================================================ -->
    <!-- FOOTER -->
    <!-- ============================================================ -->
    <footer class="vendor-footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <h3 class="footer-brand">Bona <span>Markets</span></h3>
                    <p class="footer-desc">Your trusted multi-vendor marketplace for authentic African goods.</p>
                </div>
                <div>
                    <h4 class="footer-heading">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="/Bona-Markets/Public/index.php">Home</a></li>
                        <li><a href="dashboard.php">Vendor Dashboard</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-heading">Vendor Support</h4>
                    <ul class="footer-links">
                        <li><a href="#" onclick="showToast('Help Center coming soon!','warning')">Help Center</a></li>
                        <li><a href="../contact.php" onclick="showToast('Contact support: vendor@bonamarkets.com','success')">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-heading">Legal</h4>
                    <ul class="footer-links">
                        <li><a href="#" onclick="showToast('Terms of Service','warning')">Terms of Service</a></li>
                        <li><a href="#" onclick="showToast('Privacy Policy','warning')">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Bona Markets. All rights reserved.</p>
                <p class="footer-version">Vendor Portal v1.0</p>
            </div>
        </div>
    </footer>

    <!-- ============================================================ -->
    <!-- MODALS -->
    <!-- ============================================================ -->

    <div class="modal-overlay" id="withdraw-modal">
        <div class="modal">
            <div class="modal-header">
                <h3>Request Payout</h3>
                <button class="modal-close" onclick="closeWithdrawModal()">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-group" style="margin-bottom:1rem">
                    <label>Withdrawal Amount (ZAR)</label>
                    <input type="number" placeholder="Enter amount" id="withdraw-amount" />
                    <span class="form-hint">Available balance: R <?= number_format($totalRevenue * 0.92, 2) ?></span>
                </div>
                <div class="form-group">
                    <label>Destination Account</label>
                    <select class="form-select">
                        <option>FNB Cheque ••••7823</option>
                    </select>
                </div>
                <div style="background:var(--cream);border-radius:var(--radius);padding:.9rem 1rem;margin-top:1rem;font-size:.83rem;color:var(--muted);line-height:1.6">
                    Payouts are processed within 1–3 business days. An 8% platform commission is already deducted from your available balance.
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeWithdrawModal()">Cancel</button>
                <button class="btn btn-primary" onclick="submitWithdraw()">Confirm Request</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="order-modal">
        <div class="modal">
            <div class="modal-header">
                <h3 id="order-modal-title">Order Details</h3>
                <button class="modal-close" onclick="closeOrderModal()">✕</button>
            </div>
            <div class="modal-body" id="order-modal-body"></div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeOrderModal()">Close</button>
                <button class="btn btn-primary" onclick="markShipped()">Mark as Shipped</button>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TOAST CONTAINER -->
    <!-- ============================================================ -->
    <div class="toast-wrap" id="toast-wrap"></div>

    <!-- ============================================================ -->
    <!-- EXTERNAL JAVASCRIPT -->
    <!-- ============================================================ -->
    <script src="../assets/js/vendor-dashboard.js"></script>

    <!-- ============================================================ -->
    <!-- PASS PHP DATA TO JAVASCRIPT -->
    <!-- ============================================================ -->
    <script>
        // ── PASS PHP DATA TO JAVASCRIPT ─────────────────────────────────────
        const phpProducts = <?= json_encode($products) ?>;
        const phpOrders = <?= json_encode($recentOrders) ?>;
        const phpSales = [42, 68, 55, 90, 74, 115, 88];
        const isPendingApproval = <?= $pendingApproval ? 'true' : 'false' ?>;
        
        console.log('PHP Data loaded:');
        console.log('Products:', phpProducts.length);
        console.log('Orders:', phpOrders.length);
        console.log('Pending Approval:', isPendingApproval);
    </script>

</body>
</html>
