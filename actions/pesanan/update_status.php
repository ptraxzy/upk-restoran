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

$valid_statuses = ['Menunggu Pembayaran', 'Diproses', 'Sedang Disiapkan', 'Siap Saji', 'Selesai', 'Dibatalkan'];

if ($id_pesanan <= 0 || !in_array($status, $valid_statuses, true)) {
    set_flash('error', 'Permintaan tidak valid.');
    redirect(base_url('kasir/pesanan.php'));
}

$pdo = db();

// Fetch current order status
$stmtOrder = $pdo->prepare("SELECT status_pesanan FROM pesanan WHERE id_pesanan = ?");
$stmtOrder->execute([$id_pesanan]);
$currentStatus = $stmtOrder->fetchColumn();

if (!$currentStatus) {
    set_flash('error', 'Pesanan tidak ditemukan.');
    redirect(base_url('kasir/pesanan.php'));
}

// Lock status if already Completed (Selesai) or Cancelled (Dibatalkan)
if (in_array($currentStatus, ['Selesai', 'Dibatalkan'], true)) {
    set_flash('error', "Gagal: Pesanan yang sudah selesai atau dibatalkan tidak dapat diubah statusnya.");
    redirect(base_url('kasir/pesanan.php'));
}

// Ambil status pembayaran pesanan saat ini
$stmtCheckPay = $pdo->prepare("SELECT status FROM pembayaran WHERE id_pesanan = ?");
$stmtCheckPay->execute([$id_pesanan]);
$payStatus = $stmtCheckPay->fetchColumn();

if ($payStatus !== 'Lunas' && in_array($status, ['Diproses', 'Sedang Disiapkan', 'Siap Saji', 'Selesai'], true)) {
    set_flash('error', 'Gagal: Pesanan belum dibayar. Silakan selesaikan pembayaran terlebih dahulu di kasir!');
    redirect(base_url('kasir/pesanan.php'));
}

$pdo->beginTransaction();
try {
    $id_karyawan = $_SESSION['id_user'] ?? null;
    
    // Update status pesanan
    $stmt = $pdo->prepare("UPDATE pesanan SET status_pesanan = ?, id_karyawan = ? WHERE id_pesanan = ?");
    $stmt->execute([$status, $id_karyawan, $id_pesanan]);
    
    // Sinkronisasi status pembayaran secara otomatis
    if (in_array($status, ['Diproses', 'Sedang Disiapkan', 'Siap Saji', 'Selesai'], true)) {
        $stmtBayar = $pdo->prepare("
            UPDATE pembayaran 
            SET status = 'Lunas', 
                tanggal_pembayaran = COALESCE(tanggal_pembayaran, NOW()),
                id_karyawan = COALESCE(id_karyawan, ?) 
            WHERE id_pesanan = ? AND status != 'Lunas'
        ");
        $stmtBayar->execute([$id_karyawan, $id_pesanan]);
    } elseif ($status === 'Dibatalkan') {
        $stmtBayar = $pdo->prepare("
            UPDATE pembayaran 
            SET status = 'Batal' 
            WHERE id_pesanan = ?
        ");
        $stmtBayar->execute([$id_pesanan]);
    } elseif ($status === 'Menunggu Pembayaran') {
        // Rollback pembayaran back to Menunggu
        $stmtBayar = $pdo->prepare("
            UPDATE pembayaran 
            SET status = 'Menunggu',
                tanggal_pembayaran = NULL
            WHERE id_pesanan = ?
        ");
        $stmtBayar->execute([$id_pesanan]);
    }
    
    $pdo->commit();
    set_flash('success', "Status pesanan #$id_pesanan berhasil diubah menjadi $status.");
} catch (Exception $e) {
    $pdo->rollBack();
    set_flash('error', 'Gagal mengubah status: ' . $e->getMessage());
}

// Dynamically determine the best filter tab for the new status
$new_filter = 'semua';
if ($status === 'Sedang Disiapkan' || $status === 'Diproses') {
    $new_filter = 'disiapkan';
} elseif ($status === 'Siap Saji') {
    $new_filter = 'siap';
}

$redirectUrl = 'kasir/pesanan.php?filter=' . $new_filter;

redirect(base_url($redirectUrl));

