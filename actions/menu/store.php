<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect(base_url('admin/menu_tambah.php'));
}

$nama_menu = $_POST['nama_menu'] ?? '';
$id_kategori = $_POST['id_kategori'] ?? '';
$deskripsi = $_POST['deskripsi'] ?? '';
$harga = $_POST['harga'] ?? 0;
$status = $_POST['status'] ?? 'Tersedia';
$porsi = $_POST['porsi'] ?? 10;
$gambar = $_POST['gambar'] ?? '';

if (empty($nama_menu) || empty($id_kategori) || empty($harga)) {
    set_flash('error', 'Semua field wajib diisi.');
    redirect(base_url('admin/menu_tambah.php'));
}

try {
    $stmt = db()->prepare("INSERT INTO menu (id_kategori, nama_menu, deskripsi, harga, gambar, status, porsi, id_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id_kategori, $nama_menu, $deskripsi, $harga, $gambar, $status, $porsi, $_SESSION['id_user'] ?? null]);

    set_flash('success', 'Menu baru berhasil ditambahkan.');
} catch (Exception $e) {
    set_flash('error', 'Gagal menyimpan menu: ' . $e->getMessage());
}

redirect(base_url('admin/menu.php'));
