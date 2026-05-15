<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/database.php';

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        // Jangan hapus admin sendiri (opsional safety)
        $stmt = db()->prepare("DELETE FROM user WHERE id_user = ? AND level IN ('admin', 'kasir')");
        $stmt->execute([$id]);
        set_flash('success', 'Karyawan berhasil dihapus.');
    } catch (Exception $e) {
        set_flash('error', 'Gagal menghapus karyawan: ' . $e->getMessage());
    }
}

redirect(base_url('admin/karyawan.php'));
