<?php
// ============================================================
// VENDOR / settings/save.php
// Saves the vendor's editable store profile fields.
// ============================================================
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorised.']);
    exit;
}

require_once __DIR__ . '/../../config/database.php';

$user_id     = (int) $_SESSION['user_id'];
$data        = json_decode(file_get_contents('php://input'), true);
$storeName   = trim($data['store_name'] ?? '');
$storeBio    = trim($data['store_bio']  ?? '');
$phone       = trim($data['phone']      ?? '');
$city        = trim($data['city']       ?? '');

if (empty($storeName)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Store name cannot be empty.']);
    exit;
}

$stmt = $pdo->prepare("
    UPDATE vendor_profiles
    SET business_name = ?, description = ?, phone = ?, city = ?
    WHERE user_id = ?
");
$stmt->execute([$storeName, $storeBio, $phone, $city, $user_id]);

echo json_encode(['success' => true, 'message' => 'Store profile saved.']);
