<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect(base_url('admin/pengaturan.php'));
}

$fields = [
    'nama_restoran' => $_POST['nama_restoran'] ?? '',
    'nomor_telepon' => $_POST['nomor_telepon'] ?? '',
    'alamat_lengkap' => $_POST['alamat_lengkap'] ?? '',
    'jam_operasional' => $_POST['jam_operasional'] ?? '',
    'mode_layanan' => $_POST['mode_layanan'] ?? '',
    'arah_visual' => $_POST['arah_visual'] ?? '',
    'nada_layanan' => $_POST['nada_layanan'] ?? ''
];

// Validasi input wajib
if (empty($fields['nama_restoran']) || empty($fields['nomor_telepon']) || empty($fields['alamat_lengkap'])) {
    set_flash('error', 'Nama restoran, nomor telepon, dan alamat wajib diisi.');
    redirect(base_url('admin/pengaturan.php'));
}

try { 
    $pdo = db();
    $stmt = $pdo->prepare("
        INSERT INTO pengaturan (kunci, nilai)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)
    ");

    foreach ($fields as $key => $value) {
        $stmt->execute([$key, trim($value)]);
    }

    set_flash('success', 'Pengaturan restoran berhasil diperbarui.');
} catch (Exception $e) {
    set_flash('error', 'Gagal memperbarui pengaturan: ' . $e->getMessage());
}

redirect(base_url('admin/pengaturan.php'));
