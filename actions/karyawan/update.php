<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect(base_url('admin/karyawan.php'));
}

$id_karyawan = $_POST['id_karyawan'] ?? null;
$username = $_POST['username'] ?? '';
$nama_karyawan = $_POST['nama_karyawan'] ?? $username;
$password = $_POST['password'] ?? '';
$status = $_POST['status'] ?? 'Aktif';

if (!$id_karyawan || empty($username)) {
    set_flash('error', 'Username wajib diisi.');
    redirect(base_url('admin/karyawan_edit.php?id=' . $id_karyawan));
}

try {
    // Cek duplicate username (selain dirinya sendiri)
    $stmtCheck = db()->prepare("SELECT 1 FROM karyawan WHERE username = ? AND id_karyawan != ? LIMIT 1");
    $stmtCheck->execute([$username, $id_karyawan]);

    if ($stmtCheck->fetch()) {
        set_flash('error', 'Username sudah digunakan oleh karyawan lain.');
        redirect(base_url('admin/karyawan_edit.php?id=' . $id_karyawan));
    }

    // Dapatkan data karyawan lama
    $oldUser = db()->prepare("SELECT password FROM karyawan WHERE id_karyawan = ?");
    $oldUser->execute([$id_karyawan]);
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

    // Update karyawan
    $up = db()->prepare("UPDATE karyawan SET username = ?, nama_karyawan = ?, password = ?, status = ? WHERE id_karyawan = ?");
    $up->execute([$username, $nama_karyawan, $passwordHash, $status, $id_karyawan]);

    set_flash('success', 'Data karyawan berhasil diperbarui.');
} catch (Exception $e) {
    set_flash('error', 'Gagal memperbarui data: ' . $e->getMessage());
}

redirect(base_url('admin/karyawan.php'));
