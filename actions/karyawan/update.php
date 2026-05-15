<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/database.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect(base_url('admin/karyawan.php'));
}

$id_user = $_POST['id_user'] ?? null;
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$level = $_POST['level'] ?? 'kasir';

if (!$id_user || empty($username)) {
    set_flash('error', 'Username wajib diisi.');
    redirect(base_url('admin/karyawan_edit.php?id=' . $id_user));
}

try {
    // Cek duplicate username (selain dirinya sendiri)
    $stmtCheck = db()->prepare("SELECT id_user FROM user WHERE username = ? AND id_user != ?");
    $stmtCheck->execute([$username, $id_user]);
    if ($stmtCheck->fetch()) {
        set_flash('error', 'Username sudah digunakan oleh karyawan lain.');
        redirect(base_url('admin/karyawan_edit.php?id=' . $id_user));
    }

    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = db()->prepare("UPDATE user SET username = ?, password = ?, level = ? WHERE id_user = ?");
        $stmt->execute([$username, $password, $level, $id_user]);
    } else {
        $stmt = db()->prepare("UPDATE user SET username = ?, level = ? WHERE id_user = ?");
        $stmt->execute([$username, $level, $id_user]);
    }

    set_flash('success', 'Data karyawan berhasil diperbarui.');
} catch (Exception $e) {
    set_flash('error', 'Gagal memperbarui data: ' . $e->getMessage());
}

redirect(base_url('admin/karyawan.php'));
