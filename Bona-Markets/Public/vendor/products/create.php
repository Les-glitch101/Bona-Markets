<?php
// ============================================================
// VENDOR PRODUCTS – Create (Add new product)
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
$error        = '';
$success      = '';
$formData     = [];

// ─── FETCH CATEGORIES ──────────────────────────────────────
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// ─── HANDLE FORM SUBMISSION ────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = $_POST;

    $name        = trim($_POST['name']        ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = floatval($_POST['price']   ?? 0);
    $stock       = intval($_POST['stock']     ?? 0);
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $status      = in_array($_POST['status'] ?? '', ['active', 'draft', 'archived']) ? $_POST['status'] : 'active';

    // Validate
    if (empty($name)) {
        $error = 'Product name is required.';
    } elseif ($price <= 0) {
        $error = 'Price must be greater than R 0.';
    } elseif ($stock < 0) {
        $error = 'Stock quantity cannot be negative.';
    } else {
        // Handle image upload
        $image_url = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $error = 'Image upload failed. Please try again.';
            } else {
                $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $mime    = mime_content_type($_FILES['image']['tmp_name']);
                if (!in_array($mime, $allowed)) {
                    $error = 'Invalid image type. Please upload JPEG, PNG, GIF, or WEBP.';
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
                $stmt = $pdo->prepare("
                    INSERT INTO products (vendor_id, category_id, name, description, price, image_url, stock, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$user_id, $category_id, $name, $description, $price, $image_url, $stock, $status]);

                $_SESSION['flash_success'] = "Product \"$name\" added successfully!";
                header('Location: index.php');
                exit;
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Add Product | Bona Markets Vendor</title>
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
        input[type=text], input[type=number], input[type=url], textarea, select.form-select {
            padding: .6rem .85rem; border: 1.5px solid var(--border, #e4ddd3); border-radius: 8px;
            font-size: .875rem; font-family: 'Inter', sans-serif; background: white; transition: border .15s;
        }
        input:focus, textarea:focus, select:focus { outline: none; border-color: var(--gold, #e8952a); }
        textarea { resize: vertical; }
        .form-hint { font-size: .75rem; color: var(--muted, #9c8f7a); }
        .upload-zone { border: 2px dashed var(--border, #e4ddd3); border-radius: 12px; padding: 2rem; text-align: center; cursor: pointer; transition: all .18s; color: var(--muted, #9c8f7a); font-size: .88rem; }
        .upload-zone:hover { border-color: var(--gold, #e8952a); background: #fff8ed; color: var(--gold, #e8952a); }
        .upload-zone.dragover { border-color: var(--gold, #e8952a); background: #fff8ed; }
        .upload-icon { font-size: 2rem; margin-bottom: .5rem; }
        .image-preview { margin-top: 1rem; display: none; text-align: center; }
        .image-preview img { max-width: 100%; max-height: 160px; border-radius: 8px; border: 1px solid var(--border, #e4ddd3); }
        .remove-btn { background: none; border: none; color: #c04b1e; font-size: .78rem; cursor: pointer; text-decoration: underline; margin-top: .3rem; display: block; }
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
            <h1 class="page-title">Add New Product</h1>
            <p class="page-sub">List a new item in your store.</p>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">

        <!-- Product Details -->
        <div class="card">
            <span class="card-title">Product Details</span>
            <div class="form-grid">
                <div class="form-group full">
                    <label>Product Name <span class="required">*</span></label>
                    <input type="text" name="name" placeholder="e.g. Kente Cloth Tote Bag" required
                           value="<?= htmlspecialchars($formData['name'] ?? '') ?>" />
                </div>
                <div class="form-group full">
                    <label>Description</label>
                    <textarea name="description" rows="4" placeholder="Describe your product — materials, origin, use…"><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select class="form-select" name="category_id">
                        <option value="">Select category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= (isset($formData['category_id']) && $formData['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
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
                    <input type="number" name="price" placeholder="0.00" step="0.01" min="0.01" required
                           value="<?= htmlspecialchars($formData['price'] ?? '') ?>" />
                </div>
                <div class="form-group">
                    <label>Stock Quantity <span class="required">*</span></label>
                    <input type="number" name="stock" placeholder="0" min="0" required
                           value="<?= htmlspecialchars($formData['stock'] ?? '0') ?>" />
                </div>
            </div>
        </div>

        <!-- Image & Status -->
        <div class="card">
            <span class="card-title">Image &amp; Status</span>
            <div class="form-grid">
                <div class="form-group full">
                    <label>Product Image</label>
                    <div class="upload-zone" id="uploadZone" onclick="document.getElementById('imageInput').click()">
                        <div class="upload-icon">🖼️</div>
                        <strong>Click to upload or drag &amp; drop</strong>
                        <p style="margin-top:.3rem;font-size:.8rem">PNG, JPG, GIF, WEBP — max 10 MB</p>
                        <input type="file" id="imageInput" name="image" accept="image/*" style="display:none" />
                    </div>
                    <div class="image-preview" id="imagePreview">
                        <img id="previewImg" src="#" alt="Preview" />
                        <button type="button" class="remove-btn" onclick="removeImage()">Remove image</button>
                    </div>
                </div>
                <div class="form-group">
                    <label>Listing Status</label>
                    <select class="form-select" name="status">
                        <option value="active"   <?= ($formData['status'] ?? '') === 'active'   ? 'selected' : '' ?>>Active — visible to buyers</option>
                        <option value="draft"    <?= ($formData['status'] ?? '') === 'draft'    ? 'selected' : '' ?>>Draft — save for later</option>
                        <option value="archived" <?= ($formData['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="action-row">
            <a class="btn btn-outline" href="index.php">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Product</button>
        </div>

    </form>
</div>

<script>
const zone   = document.getElementById('uploadZone');
const input  = document.getElementById('imageInput');
const preview = document.getElementById('imagePreview');
const img    = document.getElementById('previewImg');

input.addEventListener('change', () => showPreview(input.files[0]));

zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('dragover'); });
zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
zone.addEventListener('drop', e => {
    e.preventDefault(); zone.classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (file) { const dt = new DataTransfer(); dt.items.add(file); input.files = dt.files; showPreview(file); }
});

function showPreview(file) {
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => { img.src = e.target.result; preview.style.display = 'block'; };
    reader.readAsDataURL(file);
}

function removeImage() {
    input.value = '';
    preview.style.display = 'none';
    img.src = '#';
}
</script>

</body>
</html>
