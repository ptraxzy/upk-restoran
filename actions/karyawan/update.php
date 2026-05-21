<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect(base_url('admin/karyawan.php'));
}

$id_user = $_POST['id_user'] ?? null;
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$level = $_POST['level'] ?? 'kasir';
$status = $_POST['status'] ?? 'Aktif';

if (!$id_user || empty($username)) {
    set_flash('error', 'Username wajib diisi.');
    redirect(base_url('admin/karyawan_edit.php?id=' . $id_user));
}

try {
    // Cek duplicate username (selain dirinya sendiri)
    $stmtCheck = db()->prepare("SELECT 1 FROM user WHERE username = ? AND id_user != ? LIMIT 1");
    $stmtCheck->execute([$username, $id_user]);

    if ($stmtCheck->fetch()) {
        set_flash('error', 'Username sudah digunakan oleh karyawan lain.');
        redirect(base_url('admin/karyawan_edit.php?id=' . $id_user));
    }

    // Dapatkan data user lama
    $oldUser = db()->prepare("SELECT password FROM user WHERE id_user = ?");
    $oldUser->execute([$id_user]);
    $userRecord = $oldUser->fetch();

    if (!$userRecord) {
        set_flash('error', 'Karyawan tidak ditemukan.');
        redirect(base_url('admin/karyawan.php'));
    }

    // Dapatkan password (hashed)
    $passwordHash = null;
    if (!empty($password)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    } else {
        $passwordHash = $userRecord['password'];
    }

    // Update user
    $up = db()->prepare("UPDATE user SET username = ?, nama_user = ?, password = ?, level = ?, status = ? WHERE id_user = ?");
    $up->execute([$username, $username, $passwordHash, $level, $status, $id_user]);

    set_flash('success', 'Data karyawan berhasil diperbarui.');
} catch (Exception $e) {
    set_flash('error', 'Gagal memperbarui data: ' . $e->getMessage());
}

redirect(base_url('admin/karyawan.php'));
