<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $stmt = db()->prepare("
            UPDATE menu 
            SET deleted_at = NULL, 
                deleted_by = NULL 
            WHERE id_menu = ?
        ");
        $stmt->execute([$id]);
        set_flash('success', 'Menu berhasil dipulihkan ke katalog aktif.');
    } catch (Exception $e) {
        set_flash('error', 'Gagal memulihkan menu: ' . $e->getMessage());
    }
}

redirect(base_url('admin/menu_riwayat_hapus.php'));
