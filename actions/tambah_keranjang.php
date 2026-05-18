<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('pelanggan');

$id_menu = (int) ($_POST['id_menu'] ?? 0);
$qty = (int) ($_POST['qty'] ?? 1);

if ($id_menu <= 0 || $qty <= 0) {
    set_flash('error', 'Item tidak valid.');
    redirect(base_url('pelanggan/dashboard.php'));
}

$pdo = db();
$stmt = $pdo->prepare('SELECT id_menu, nama_menu FROM menu WHERE id_menu = ? AND status = ?');
$stmt->execute([$id_menu, 'Tersedia']);
$menu = $stmt->fetch();

if (!$menu) {
    set_flash('error', 'Menu tidak ditemukan atau tidak tersedia.');
    redirect(base_url('pelanggan/dashboard.php'));
}

$userId = $_SESSION['user_id'] ?? 0;
if ($userId <= 0) {
    set_flash('error', 'Silakan login terlebih dahulu.');
    redirect(base_url('login.php'));
}

$exists = $pdo->prepare('SELECT id_keranjang, qty FROM keranjang WHERE user_id = ? AND id_menu = ?');
$exists->execute([$userId, $id_menu]);
$cartItem = $exists->fetch();

if ($cartItem) {
    $update = $pdo->prepare('UPDATE keranjang SET qty = qty + ? WHERE id_keranjang = ?');
    $update->execute([$qty, $cartItem['id_keranjang']]);
} else {
    $insert = $pdo->prepare('INSERT INTO keranjang (user_id, id_menu, qty) VALUES (?, ?, ?)');
    $insert->execute([$userId, $id_menu, $qty]);
}

set_flash('success', $menu['nama_menu'] . ' berhasil ditambahkan ke keranjang.');
redirect(base_url('pelanggan/dashboard.php'));
