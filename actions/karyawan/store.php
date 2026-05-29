<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect(base_url('admin/karyawan_tambah.php'));
}

$username = $_POST['username'] ?? '';
$nama_karyawan = $_POST['nama_karyawan'] ?? $username;
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    set_flash('error', 'Username dan Password wajib diisi.');
    redirect(base_url('admin/karyawan_tambah.php'));
}

try {
    // Cek apakah username sudah ada di karyawan
    $stmtCheck = db()->prepare("SELECT 1 FROM karyawan WHERE username = ? LIMIT 1");
    $stmtCheck->execute([$username]);

    if ($stmtCheck->fetch()) {
        set_flash('error', 'Username sudah terdaftar.');
        redirect(base_url('admin/karyawan_tambah.php'));
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare("INSERT INTO karyawan (nama_karyawan, username, password) VALUES (?, ?, ?)");
    $stmt->execute([$nama_karyawan, $username, $hashedPassword]);

    set_flash('success', 'Karyawan berhasil ditambahkan.');
} catch (Exception $e) {
    set_flash('error', 'Gagal menyimpan data karyawan: ' . $e->getMessage());
}

redirect(base_url('admin/karyawan.php'));
