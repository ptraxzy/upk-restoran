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
            SET deleted_at = NOW(), 
                deleted_by = ? 
            WHERE id_menu = ?
        ");
        $stmt->execute([$_SESSION['id_user'] ?? null, $id]);
        set_flash('success', 'Menu berhasil diarsipkan (dihapus).');
    } catch (Exception $e) {
        set_flash('error', 'Gagal menghapus menu: ' . $e->getMessage());
    }
}

redirect(base_url('admin/menu.php'));
