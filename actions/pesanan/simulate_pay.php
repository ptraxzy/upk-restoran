<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/database.php';
// Allow pelanggan, admin, and kasir to simulate QRIS payment
if (!isset($_SESSION['id_user']) || !in_array($_SESSION['user_role'] ?? $_SESSION['level'] ?? '', ['pelanggan', 'admin', 'kasir'])) {
    set_flash('error', 'Akses ditolak.');
    redirect(base_url());
}

$id_pesanan = (int) ($_GET['id_pesanan'] ?? 0);

if ($id_pesanan <= 0) {
    redirect(base_url());
}

$pdo = db();
$pdo->beginTransaction();

try {
    $role = $_SESSION['user_role'] ?? $_SESSION['level'] ?? '';
    
    // Update pembayaran status and audit info
    if ($role === 'pelanggan') {
        $stmtBayar = $pdo->prepare("
            UPDATE pembayaran 
            SET status = 'Lunas', 
                id_pelanggan = ? 
            WHERE id_pesanan = ?
        ");
        $stmtBayar->execute([$_SESSION['id_user'], $id_pesanan]);
        
        $stmtPesanan = $pdo->prepare("UPDATE pesanan SET status_pesanan = 'Diproses' WHERE id_pesanan = ?");
        $stmtPesanan->execute([$id_pesanan]);
    } else {
        $id_karyawan = ($role === 'kasir') ? $_SESSION['id_user'] : null;
        
        $stmtBayar = $pdo->prepare("
            UPDATE pembayaran 
            SET status = 'Lunas', 
                id_karyawan = ? 
            WHERE id_pesanan = ?
        ");
        $stmtBayar->execute([$id_karyawan, $id_pesanan]);
        
        $stmtPesanan = $pdo->prepare("UPDATE pesanan SET status_pesanan = 'Diproses', id_karyawan = ? WHERE id_pesanan = ?");
        $stmtPesanan->execute([$id_karyawan, $id_pesanan]);
    }

    $pdo->commit();

    set_flash('success', 'Pembayaran berhasil disimulasikan!');
} catch (Exception $e) {
    $pdo->rollBack();
    set_flash('error', 'Gagal memproses simulasi pembayaran: ' . $e->getMessage());
}

// Redirect back to source dashboard
if ($role === 'admin') {
    redirect(base_url('admin/pesanan.php'));
} elseif ($role === 'kasir') {
    redirect(base_url('kasir/pesanan.php'));
} else {
    redirect(base_url('pelanggan/dashboard.php'));
}

