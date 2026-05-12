<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$id_menu = (int) ($_POST['id_menu'] ?? 0);
$jumlah = (int) ($_POST['qty'] ?? 1);

if ($id_menu <= 0 || $jumlah <= 0) {
    set_flash('error', 'Item tidak valid.');
    redirect(base_url('pelanggan/menu.php'));
}

$pdo = db();
$stmt = $pdo->prepare("SELECT id_menu, nama_menu, harga, gambar FROM menu WHERE id_menu = ?");
$stmt->execute([$id_menu]);
$menu = $stmt->fetch();

if (!$menu) {
    set_flash('error', 'Menu tidak ditemukan.');
    redirect(base_url('pelanggan/menu.php'));
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add or update cart
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['id_menu'] === $id_menu) {
        $item['jumlah'] += $jumlah;
        $found = true;
        break;
    }
}

if (!$found) {
    $_SESSION['cart'][] = [
        'id_menu' => $menu['id_menu'],
        'nama_menu' => $menu['nama_menu'],
        'harga' => $menu['harga'],
        'gambar' => $menu['gambar'],
        'jumlah' => $jumlah
    ];
}

set_flash('success', $menu['nama_menu'] . ' ditambahkan ke keranjang.');
redirect(base_url('pelanggan/keranjang.php'));
