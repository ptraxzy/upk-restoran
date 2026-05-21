<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect(base_url('admin/menu.php'));
}

$id_menu = $_POST['id_menu'] ?? null;
$nama_menu = $_POST['nama_menu'] ?? '';
$id_kategori = $_POST['id_kategori'] ?? '';
$deskripsi = $_POST['deskripsi'] ?? '';
$harga = $_POST['harga'] ?? 0;
$status = $_POST['status'] ?? 'Tersedia';
$porsi = $_POST['porsi'] ?? 10;
$gambar = $_POST['gambar'] ?? '';

if (!$id_menu || empty($nama_menu) || empty($id_kategori) || empty($harga)) {
    set_flash('error', 'Semua field wajib diisi.');
    redirect(base_url('admin/menu_edit.php?id=' . $id_menu));
}

try {
    $stmt = db()->prepare("
        UPDATE menu
        SET id_kategori = ?, nama_menu = ?, deskripsi = ?, harga = ?, gambar = ?, status = ?, porsi = ?
        WHERE id_menu = ?
    ");
    $stmt->execute([$id_kategori, $nama_menu, $deskripsi, $harga, $gambar, $status, $porsi, $id_menu]);

    set_flash('success', 'Menu berhasil diperbarui.');
} catch (Exception $e) {
    set_flash('error', 'Gagal memperbarui menu: ' . $e->getMessage());
}

redirect(base_url('admin/menu.php'));
