<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

$id = $_GET['id'] ?? null;

if (!$id) {
    set_flash('error', 'Karyawan tidak ditemukan.');
    redirect(base_url('admin/karyawan.php'));
}

try {
    // Terapkan deaktifasi agar relasi database tidak terputus (Soft Deactivate)
    $stmt = db()->prepare("UPDATE user SET status = 'Nonaktif' WHERE id_user = ? AND level IN ('admin', 'kasir')");
    $stmt->execute([$id]);

    set_flash('success', 'Akses karyawan berhasil dinonaktifkan.');
} catch (Exception $e) {
    set_flash('error', 'Gagal menonaktifkan karyawan: ' . $e->getMessage());
}

redirect(base_url('admin/karyawan.php'));
