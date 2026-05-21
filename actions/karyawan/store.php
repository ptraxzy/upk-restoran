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
$password = $_POST['password'] ?? '';
$level = $_POST['level'] ?? 'kasir';

if (empty($username) || empty($password)) {
    set_flash('error', 'Username dan Password wajib diisi.');
    redirect(base_url('admin/karyawan_tambah.php'));
}

if (!in_array($level, ['admin', 'kasir'])) {
    $level = 'kasir';
}

try {
    // Cek apakah username sudah ada di user
    $stmtCheck = db()->prepare("SELECT 1 FROM user WHERE username = ? LIMIT 1");
    $stmtCheck->execute([$username]);

    if ($stmtCheck->fetch()) {
        set_flash('error', 'Username sudah terdaftar.');
        redirect(base_url('admin/karyawan_tambah.php'));
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare("INSERT INTO user (nama_user, username, password, level) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $username, $hashedPassword, $level]);

    set_flash('success', 'Karyawan berhasil ditambahkan.');
} catch (Exception $e) {
    set_flash('error', 'Gagal menyimpan data karyawan: ' . $e->getMessage());
}

redirect(base_url('admin/karyawan.php'));
