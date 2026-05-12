<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/database.php';
require_role('kasir');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$id_pesanan = (int) ($_GET['id'] ?? 0);
$status = $_GET['status'] ?? '';

$valid_statuses = ['Diproses', 'Sedang Disiapkan', 'Siap Saji', 'Selesai', 'Dibatalkan'];

if ($id_pesanan <= 0 || !in_array($status, $valid_statuses, true)) {
    set_flash('error', 'Permintaan tidak valid.');
    redirect(base_url('kasir/pesanan.php'));
}

$pdo = db();

try {
    $stmt = $pdo->prepare("UPDATE pesanan SET status_pesanan = ? WHERE id_pesanan = ?");
    $stmt->execute([$status, $id_pesanan]);
    
    set_flash('success', "Status pesanan #$id_pesanan berhasil diubah menjadi $status.");
} catch (Exception $e) {
    set_flash('error', 'Gagal mengubah status: ' . $e->getMessage());
}

redirect(base_url('kasir/pesanan.php'));
