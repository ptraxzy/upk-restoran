<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('pelanggan');

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$userId = $_SESSION['id_user'] ?? 0;

if ($id > 0 && $userId > 0) {
    $stmt = db()->prepare('DELETE FROM keranjang WHERE id_keranjang = ? AND id_user = ?');
    $stmt->execute([$id, $userId]);
    set_flash('success', 'Item dihapus dari keranjang.');
}

redirect(base_url('pelanggan/keranjang.php'));
