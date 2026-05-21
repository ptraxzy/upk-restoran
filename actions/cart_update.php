<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('pelanggan');

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$qty = isset($_GET['qty']) ? (int) $_GET['qty'] : 1;
$userId = $_SESSION['id_user'] ?? 0;

if ($id > 0 && $userId > 0 && $qty > 0) {
    $stmt = db()->prepare('UPDATE keranjang SET qty = ? WHERE id_keranjang = ? AND id_user = ?');
    $stmt->execute([$qty, $id, $userId]);
    set_flash('success', 'Jumlah pesanan berhasil diperbarui.');
}

redirect(base_url('pelanggan/keranjang.php'));
