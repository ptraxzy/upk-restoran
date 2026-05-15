<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/database.php';

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $stmt = db()->prepare("DELETE FROM menu WHERE id_menu = ?");
        $stmt->execute([$id]);
        set_flash('success', 'Menu berhasil dihapus.');
    } catch (Exception $e) {
        set_flash('error', 'Gagal menghapus menu: ' . $e->getMessage());
    }
}

redirect(base_url('admin/menu.php'));
