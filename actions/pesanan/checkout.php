<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/database.php';
require_role('pelanggan');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(base_url('pelanggan/keranjang.php'));
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    set_flash('error', 'Keranjang Anda kosong.');
    redirect(base_url('pelanggan/keranjang.php'));
}

$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += ($item['harga'] * $item['jumlah']);
}

$discount = (float) ($_SESSION['active_discount'] ?? 0);
$tax = ($subtotal - $discount) * 0.11;
$total = ($subtotal - $discount) + $tax;

$pdo = db();
$pdo->beginTransaction();

try {
    // 1. Buat pesanan baru
    $stmt = $pdo->prepare("
        INSERT INTO pesanan (id_user, no_meja, total_harga, status_pesanan)
        VALUES (?, ?, ?, 'Menunggu Pembayaran')
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        $_SESSION['meja_aktif'] ?? '01',
        $total
    ]);
    $id_pesanan = (int) $pdo->lastInsertId();

    // 2. Simpan detail pesanan
    $stmtDetail = $pdo->prepare("
        INSERT INTO detail_pesanan (id_pesanan, id_menu, jumlah, harga_satuan)
        VALUES (?, ?, ?, ?)
    ");
    foreach ($cart as $item) {
        $stmtDetail->execute([
            $id_pesanan,
            $item['id_menu'],
            $item['jumlah'],
            $item['harga']
        ]);
    }

    // 3. Simpan data pembayaran (status Menunggu)
    $trx_id = 'ORD-' . date('ymd') . str_pad((string)$id_pesanan, 4, '0', STR_PAD_LEFT);
    $stmtBayar = $pdo->prepare("
        INSERT INTO pembayaran (id_pesanan, total_bayar, metode, status, trx_id)
        VALUES (?, ?, 'QRIS', 'Menunggu', ?)
    ");
    $stmtBayar->execute([$id_pesanan, $total, $trx_id]);

    $pdo->commit();

    // Clear cart session because order is placed
    unset($_SESSION['cart']);
    unset($_SESSION['active_voucher']);
    unset($_SESSION['active_discount']);

    redirect(base_url("pelanggan/keranjang_checkout.php?action=pay&trx={$trx_id}&id_pesanan={$id_pesanan}"));

} catch (Exception $e) {
    $pdo->rollBack();
    set_flash('error', 'Gagal memproses pesanan: ' . $e->getMessage());
    redirect(base_url('pelanggan/keranjang_checkout.php'));
}
