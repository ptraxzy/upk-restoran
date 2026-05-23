<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/database.php';

// Allow both admin and kasir to confirm cash payments
if (!is_logged_in() || !in_array($_SESSION['user_level'] ?? '', ['admin', 'kasir'])) {
    set_flash('error', 'Akses ditolak.');
    redirect(base_url());
}

$id_pesanan = (int) ($_GET['id_pesanan'] ?? 0);

if ($id_pesanan <= 0) {
    set_flash('error', 'ID Pesanan tidak valid.');
    redirect($_SERVER['HTTP_REFERER'] ?? base_url());
}

$pdo = db();
$pdo->beginTransaction();

try {
    // 1. Fetch current payment details
    $stmtCheck = $pdo->prepare("SELECT * FROM pembayaran WHERE id_pesanan = ?");
    $stmtCheck->execute([$id_pesanan]);
    $pembayaran = $stmtCheck->fetch();

    if (!$pembayaran) {
        throw new Exception("Data pembayaran tidak ditemukan.");
    }

    // 2. Update payment record to Lunas
    $stmtBayar = $pdo->prepare("
        UPDATE pembayaran 
        SET status = 'Lunas', 
            metode = 'Tunai',
            tanggal_pembayaran = NOW(),
            id_karyawan = ? 
        WHERE id_pesanan = ?
    ");
    $stmtBayar->execute([$_SESSION['id_user'] ?? null, $id_pesanan]);

    // 3. Update order status to 'Diproses'
    $stmtPesanan = $pdo->prepare("UPDATE pesanan SET status_pesanan = 'Diproses', id_karyawan = ? WHERE id_pesanan = ?");
    $stmtPesanan->execute([$_SESSION['id_user'] ?? null, $id_pesanan]);

    $pdo->commit();

    set_flash('success', 'Pembayaran Tunai berhasil dikonfirmasi! Pesanan dikirim ke dapur.');
} catch (Exception $e) {
    $pdo->rollBack();
    set_flash('error', 'Gagal memproses konfirmasi pembayaran: ' . $e->getMessage());
}

redirect($_SERVER['HTTP_REFERER'] ?? base_url('admin/pesanan.php'));
