<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect(base_url('admin/diskon.php'));
}

$id_voucher = $_POST['id_voucher'] ?? null;
$kode_voucher = $_POST['kode_voucher'] ?? '';
$nama_voucher = $_POST['nama_voucher'] ?? '';
$jenis_voucher = $_POST['jenis_voucher'] ?? 'Persentase';
$nilai_voucher = (float)($_POST['nilai_voucher'] ?? 0);
$tanggal_mulai = $_POST['tanggal_mulai'] ?? date('Y-m-d');
$tanggal_berakhir = $_POST['tanggal_berakhir'] ?? date('Y-m-d');
$status_voucher = $_POST['status_voucher'] ?? 'Active';

if (!$id_voucher || empty($kode_voucher) || empty($nama_voucher) || empty($nilai_voucher) || empty($tanggal_mulai) || empty($tanggal_berakhir)) {
    set_flash('error', 'Semua field wajib diisi.');
    redirect(base_url('admin/diskon_edit.php?id=' . $id_voucher));
}

try {
    $stmt = db()->prepare("
        UPDATE voucher 
        SET kode_voucher = ?, nama_voucher = ?, jenis_voucher = ?, nilai_voucher = ?, tanggal_mulai = ?, tanggal_berakhir = ?, status_voucher = ?
        WHERE id_voucher = ?
    ");
    $stmt->execute([
        strtoupper(trim($kode_voucher)),
        trim($nama_voucher),
        $jenis_voucher,
        $nilai_voucher,
        $tanggal_mulai,
        $tanggal_berakhir,
        $status_voucher,
        $id_voucher
    ]);

    set_flash('success', 'Voucher berhasil diperbarui.');
} catch (Exception $e) {
    set_flash('error', 'Gagal memperbarui voucher: ' . $e->getMessage());
}

redirect(base_url('admin/diskon.php'));
