<?php
// ============================================================
// VENDOR PRODUCTS – Delete
// Accepts POST only; verifies ownership before deleting.
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

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../../../config/database.php';

$user_id    = $_SESSION['user_id'];
$product_id = intval($_POST['product_id'] ?? 0);
$is_ajax    = !empty($_POST['ajax']);

if ($product_id <= 0) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
        exit;
    }
    $_SESSION['flash_error'] = 'Invalid product ID.';
    header('Location: index.php');
    exit;
}

// ─── VERIFY OWNERSHIP ────────────────────────────────────────
$stmt = $pdo->prepare("SELECT id, name, image_url FROM products WHERE id = ? AND vendor_id = ?");
$stmt->execute([$product_id, $user_id]);
$product = $stmt->fetch();

if (!$product) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Product not found or permission denied.']);
        exit;
    }
    $_SESSION['flash_error'] = 'Product not found or you do not have permission to delete it.';
    header('Location: index.php');
    exit;
}

// ─── DELETE ──────────────────────────────────────────────────
try {
    // Remove from carts first (FK constraint)
    $pdo->prepare("DELETE FROM cart WHERE product_id = ?")->execute([$product_id]);

    // Delete the product
    $pdo->prepare("DELETE FROM products WHERE id = ? AND vendor_id = ?")->execute([$product_id, $user_id]);

    // Optionally remove the image file from disk
    if ($product['image_url']) {
        $imagePath = __DIR__ . '/../../' . $product['image_url'];
        if (file_exists($imagePath)) {
            @unlink($imagePath);
        }
    }

    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Product deleted successfully.']);
        exit;
    }

    $_SESSION['flash_success'] = '"' . $product['name'] . '" was deleted successfully.';
    header('Location: index.php');
    exit;

} catch (PDOException $e) {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
    }
    $_SESSION['flash_error'] = 'Database error: ' . $e->getMessage();
    header('Location: index.php');
    exit;
}
