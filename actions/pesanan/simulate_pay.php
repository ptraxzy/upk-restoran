<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/database.php';
require_role('pelanggan');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$id_pesanan = (int) ($_GET['id_pesanan'] ?? 0);

if ($id_pesanan <= 0) {
    redirect(base_url('pelanggan/dashboard.php'));
}

$pdo = db();
$pdo->beginTransaction();

try {
    // Update pembayaran
    $stmtBayar = $pdo->prepare("UPDATE pembayaran SET status = 'Lunas' WHERE id_pesanan = ?");
    $stmtBayar->execute([$id_pesanan]);

    // Update pesanan agar masuk ke antrian kasir/dapur
    $stmtPesanan = $pdo->prepare("UPDATE pesanan SET status_pesanan = 'Diproses' WHERE id_pesanan = ?");
    $stmtPesanan->execute([$id_pesanan]);

    $pdo->commit();

    set_flash('success', 'Pembayaran berhasil disimulasikan! Pesanan Anda sedang diproses.');
    redirect(base_url('pelanggan/dashboard.php'));

} catch (Exception $e) {
    $pdo->rollBack();
    set_flash('error', 'Gagal memproses simulasi pembayaran.');
    redirect(base_url('pelanggan/dashboard.php'));
}
