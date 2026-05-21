<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/database.php';
require_role('kasir');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect(base_url('kasir/pembayaran.php'));
}

$id_pesanan = (int)($_POST['id_pesanan'] ?? 0);
$metode = $_POST['metode'] ?? 'Tunai';
$total_bayar = (float)($_POST['total_bayar'] ?? 0);

if ($id_pesanan <= 0) {
    set_flash('error', 'Pesanan tidak valid.');
    redirect(base_url('kasir/pembayaran.php'));
}

$pdo = db();
$pdo->beginTransaction();

try {
    // 1. Update status pesanan to 'Diproses'
    $stmtP = $pdo->prepare("UPDATE pesanan SET status_pesanan = 'Diproses' WHERE id_pesanan = ?");
    $stmtP->execute([$id_pesanan]);

    $stmtB = $pdo->prepare("
        UPDATE pembayaran 
        SET status = 'Lunas', 
            metode = ?, 
            tanggal_pembayaran = NOW(), 
            id_user = ?
        WHERE id_pesanan = ?
    ");
    $stmtB->execute([$metode, $_SESSION['id_user'] ?? null, $id_pesanan]);

    $pdo->commit();

    set_flash('success', 'Pembayaran berhasil diproses! Struk siap dicetak.');
    redirect(base_url('kasir/pembayaran_cetak.php?id=' . $id_pesanan));
} catch (Exception $e) {
    $pdo->rollBack();
    set_flash('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
    redirect(base_url('kasir/pembayaran_cetak.php?id=' . $id_pesanan));
}
