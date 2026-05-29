<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('pelanggan');

$id_menu = (int) ($_POST['id_menu'] ?? 0);
$qty = (int) ($_POST['qty'] ?? 1);
$isAjax = isset($_POST['ajax']) && $_POST['ajax'] === '1';

if ($id_menu <= 0 || $qty <= 0) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Item tidak valid.']);
        exit;
    }
    set_flash('error', 'Item tidak valid.');
    redirect(base_url('pelanggan/dashboard.php'));
}

$pdo = db();
$stmt = $pdo->prepare('SELECT id_menu, nama_menu FROM menu WHERE id_menu = ? AND status = ?');
$stmt->execute([$id_menu, 'Tersedia']);
$menu = $stmt->fetch();

if (!$menu) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Menu tidak ditemukan atau tidak tersedia.']);
        exit;
    }
    set_flash('error', 'Menu tidak ditemukan atau tidak tersedia.');
    redirect(base_url('pelanggan/dashboard.php'));
}

$userId = $_SESSION['id_user'] ?? 0;
if ($userId <= 0) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu.']);
        exit;
    }
    set_flash('error', 'Silakan login terlebih dahulu.');
    redirect(base_url('login.php'));
}

$exists = $pdo->prepare('SELECT id_keranjang, qty FROM keranjang WHERE id_pelanggan = ? AND id_menu = ?');
$exists->execute([$userId, $id_menu]);
$cartItem = $exists->fetch();

if ($cartItem) {
    $update = $pdo->prepare('UPDATE keranjang SET qty = qty + ? WHERE id_keranjang = ?');
    $update->execute([$qty, $cartItem['id_keranjang']]);
} else {
    $insert = $pdo->prepare('INSERT INTO keranjang (id_pelanggan, id_menu, qty) VALUES (?, ?, ?)');
    $insert->execute([$userId, $id_menu, $qty]);
}

if ($isAjax) {
    header('Content-Type: application/json');
    require_once __DIR__ . '/../includes/ui.php';
    echo json_encode([
        'success' => true,
        'message' => $menu['nama_menu'] . ' berhasil ditambahkan ke keranjang.',
        'cart_count' => cart_count()
    ]);
    exit;
}

set_flash('success', $menu['nama_menu'] . ' berhasil ditambahkan ke keranjang.');
redirect(base_url('pelanggan/dashboard.php'));
