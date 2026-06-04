<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';

$currentRole = $_SESSION['user_role'] ?? $_SESSION['level'] ?? null;
if ($currentRole !== 'pelanggan') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$idPesanan = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userId = $_SESSION['id_user'] ?? 0;

if ($idPesanan <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
    exit;
}

$pdo = db();
$stmt = $pdo->prepare('
    SELECT status_pesanan 
    FROM pesanan 
    WHERE id_pesanan = ? AND id_pelanggan = ?
');
$stmt->execute([$idPesanan, $userId]);
$order = $stmt->fetch();

if (!$order) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Not found']);
    exit;
}

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'data' => [
        'status_pesanan' => $order['status_pesanan']
    ]
]);
