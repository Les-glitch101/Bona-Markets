<?php
// ============================================================
// VENDOR PRODUCTS – Edit (Update an existing product)
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

$user_id = $_SESSION['user_id'];
$error   = '';

// ─── LOAD PRODUCT ────────────────────────────────────────────
$product_id = intval($_GET['id'] ?? 0);
if ($product_id <= 0) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND vendor_id = ?");
$stmt->execute([$product_id, $user_id]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['flash_error'] = 'Product not found or you do not have permission to edit it.';
    header('Location: index.php');
    exit;
}

// ─── FETCH CATEGORIES ────────────────────────────────────────
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// ─── HANDLE FORM SUBMISSION ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']        ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = floatval($_POST['price']   ?? 0);
    $stock       = intval($_POST['stock']     ?? 0);
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $status      = in_array($_POST['status'] ?? '', ['active', 'draft', 'archived']) ? $_POST['status'] : 'active';

    if (empty($name)) {
        $error = 'Product name is required.';
    } elseif ($price <= 0) {
        $error = 'Price must be greater than R 0.';
    } elseif ($stock < 0) {
        $error = 'Stock quantity cannot be negative.';
    } else {
        $image_url = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $error = 'Image upload failed. Please try again.';
            } else {
                $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $mime    = mime_content_type($_FILES['image']['tmp_name']);
                if (!in_array($mime, $allowed)) {
                    $error = 'Invalid image type. JPEG, PNG, GIF, or WEBP only.';
                } elseif ($_FILES['image']['size'] > 10 * 1024 * 1024) {
                    $error = 'Image too large. Maximum size is 10 MB.';
                } else {
                    $uploadDir = __DIR__ . '/../../uploads/products/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    $ext      = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $fileName = uniqid() . '.' . $ext;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                        $image_url = 'uploads/products/' . $fileName;
                    } else {
                        $error = 'Could not save the uploaded image.';
                    }
                }
            }
        }

        if (empty($error)) {
            try {
                if ($image_url) {
                    $stmt = $pdo->prepare("
                        UPDATE products
                        SET name=?, description=?, price=?, category_id=?, stock=?, status=?, image_url=?
                        WHERE id=? AND vendor_id=?
                    ");
                    $stmt->execute([$name, $description, $price, $category_id, $stock, $status, $image_url, $product_id, $user_id]);
                } else {
                    $stmt = $pdo->prepare("
                        UPDATE products
                        SET name=?, description=?, price=?, category_id=?, stock=?, status=?
                        WHERE id=? AND vendor_id=?
                    ");
                    $stmt->execute([$name, $description, $price, $category_id, $stock, $status, $product_id, $user_id]);
                }

                $_SESSION['flash_success'] = "Product \"$name\" updated successfully!";
                header('Location: index.php');
                exit;
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }

    // Re-populate form with submitted values on error
    $product['name']        = $_POST['name']        ?? $product['name'];
    $product['description'] = $_POST['description'] ?? $product['description'];
    $product['price']       = $_POST['price']       ?? $product['price'];
    $product['stock']       = $_POST['stock']       ?? $product['stock'];
    $product['category_id'] = $_POST['category_id'] ?? $product['category_id'];
    $product['status']      = $_POST['status']      ?? $product['status'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Edit Product | Bona Markets Vendor</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="../../assets/css/vendor-dashboard.css" />
    <style>
        body { font-family: 'Inter', sans-serif; background: var(--cream, #faf8f4); color: var(--ink, #1a1208); }
        .page-wrap { max-width: 860px; margin: 2rem auto; padding: 0 1.5rem; }
        .page-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.75rem; }
        .page-title { font-family: 'Syne', sans-serif; font-size: 1.6rem; font-weight: 700; margin: 0; }
        .page-sub { font-size: .875rem; color: var(--muted, #9c8f7a); margin: .25rem 0 0; }
        .btn { display: inline-flex; align-items: center; gap: .4rem; padding: .55rem 1.1rem; border-radius: 8px; font-size: .875rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all .16s; }
        .btn-primary { background: var(--gold, #e8952a); color: #1a1208; }
        .btn-primary:hover { background: #d4841a; }
        .btn-outline { background: transparent; border: 1.5px solid var(--border, #e4ddd3); color: var(--ink, #1a1208); }
        .btn-outline:hover { border-color: var(--gold, #e8952a); color: var(--gold, #e8952a); }
        .alert { padding: .85rem 1.1rem; border-radius: 10px; margin-bottom: 1.25rem; font-size: .875rem; }
        .alert-error { background: #fdecea; border: 1px solid #f5c6c0; color: #c04b1e; }
        .card { background: white; border-radius: 14px; border: 1px solid var(--border, #e4ddd3); padding: 1.5rem; margin-bottom: 1.25rem; }
        .card-title { font-family: 'Syne', sans-serif; font-weight: 700; font-size: 1rem; display: block; margin-bottom: 1.1rem; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .form-group { display: flex; flex-direction: column; gap: .4rem; }
        .form-group.full { grid-column: 1 / -1; }
        label { font-size: .82rem; font-weight: 600; color: var(--ink, #1a1208); }
        input[type=text], input[type=number], textarea, select.form-select, input[type=file] {
            padding: .6rem .85rem; border: 1.5px solid var(--border, #e4ddd3); border-radius: 8px;
            font-size: .875rem; font-family: 'Inter', sans-serif; background: white; transition: border .15s;
        }
        input:focus, textarea:focus, select:focus { outline: none; border-color: var(--gold, #e8952a); }
        textarea { resize: vertical; }
        .form-hint { font-size: .75rem; color: var(--muted, #9c8f7a); }
        .current-image { margin-top: .5rem; display: flex; align-items: center; gap: .75rem; }
        .current-image img { width: 72px; height: 72px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border, #e4ddd3); }
        .action-row { display: flex; gap: .75rem; flex-wrap: wrap; margin-top: .5rem; }
        .action-row .btn { flex: 1; justify-content: center; }
        .back-link { font-size: .85rem; color: var(--muted, #9c8f7a); text-decoration: none; }
        .back-link:hover { color: var(--gold, #e8952a); }
        .required { color: var(--rust, #c04b1e); }
        @media (max-width: 600px) { .form-grid { grid-template-columns: 1fr; } .form-group.full { grid-column: 1; } }
    </style>
</head>
<body>

<div class="page-wrap">

    <p style="margin-bottom:1rem;"><a class="back-link" href="index.php">← Back to Products</a></p>

    <div class="page-header">
        <div>
            <h1 class="page-title">Edit Product</h1>
            <p class="page-sub">Updating: <strong><?= htmlspecialchars($product['name']) ?></strong></p>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="edit.php?id=<?= $product_id ?>" enctype="multipart/form-data">

        <!-- Product Details -->
        <div class="card">
            <span class="card-title">Product Details</span>
            <div class="form-grid">
                <div class="form-group full">
                    <label>Product Name <span class="required">*</span></label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($product['name']) ?>" />
                </div>
                <div class="form-group full">
                    <label>Description</label>
                    <textarea name="description" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select class="form-select" name="category_id">
                        <option value="">Select category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Listing Status</label>
                    <select class="form-select" name="status">
                        <option value="active"   <?= $product['status'] === 'active'   ? 'selected' : '' ?>>Active — visible to buyers</option>
                        <option value="draft"    <?= $product['status'] === 'draft'    ? 'selected' : '' ?>>Draft — save for later</option>
                        <option value="archived" <?= $product['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Pricing & Inventory -->
        <div class="card">
            <span class="card-title">Pricing &amp; Inventory</span>
            <div class="form-grid">
                <div class="form-group">
                    <label>Price (ZAR) <span class="required">*</span></label>
                    <input type="number" name="price" step="0.01" min="0.01" required
                           value="<?= htmlspecialchars($product['price']) ?>" />
                </div>
                <div class="form-group">
                    <label>Stock Quantity <span class="required">*</span></label>
                    <input type="number" name="stock" min="0" required
                           value="<?= htmlspecialchars($product['stock']) ?>" />
                </div>
            </div>
        </div>

        <!-- Product Image -->
        <div class="card">
            <span class="card-title">Product Image</span>
            <?php if ($product['image_url']): ?>
                <div class="current-image">
                    <img src="/<?= htmlspecialchars($product['image_url']) ?>" alt="Current image" />
                    <span style="font-size:.82rem;color:var(--muted, #9c8f7a);">Current image — upload a new one below to replace it.</span>
                </div>
                <div style="margin-top:.85rem;">
            <?php else: ?>
                <div>
                    <p style="font-size:.82rem;color:var(--muted, #9c8f7a);margin-bottom:.5rem;">No image uploaded yet.</p>
            <?php endif; ?>
                <div class="form-group">
                    <label><?= $product['image_url'] ? 'Replace Image (optional)' : 'Upload Image' ?></label>
                    <input type="file" name="image" accept="image/*" />
                    <span class="form-hint">JPEG, PNG, GIF, or WEBP — max 10 MB</span>
                </div>
            </div>
        </div>

        <div class="action-row">
            <a class="btn btn-outline" href="index.php">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>

    </form>
</div>

</body>
</html>
