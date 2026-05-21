<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect(base_url('admin/diskon_tambah.php'));
}

$kode_voucher = $_POST['kode_voucher'] ?? '';
$nama_voucher = $_POST['nama_voucher'] ?? '';
$jenis_voucher = $_POST['jenis_voucher'] ?? 'Persentase';
$nilai_voucher = (float)($_POST['nilai_voucher'] ?? 0);
$tanggal_mulai = $_POST['tanggal_mulai'] ?? date('Y-m-d');
$tanggal_berakhir = $_POST['tanggal_berakhir'] ?? date('Y-m-d');
$status_voucher = $_POST['status_voucher'] ?? 'Active';

if (empty($kode_voucher) || empty($nama_voucher) || empty($nilai_voucher) || empty($tanggal_mulai) || empty($tanggal_berakhir)) {
    set_flash('error', 'Semua field wajib diisi.');
    redirect(base_url('admin/diskon_tambah.php'));
}

try {
    $stmt = db()->prepare("
        INSERT INTO voucher (kode_voucher, nama_voucher, jenis_voucher, nilai_voucher, tanggal_mulai, tanggal_berakhir, status_voucher, id_user)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        strtoupper(trim($kode_voucher)),
        trim($nama_voucher),
        $jenis_voucher,
        $nilai_voucher,
        $tanggal_mulai,
        $tanggal_berakhir,
        $status_voucher,
        $_SESSION['id_user'] ?? null
    ]);

    set_flash('success', 'Voucher baru berhasil ditambahkan.');
} catch (Exception $e) {
    set_flash('error', 'Gagal menambahkan voucher: ' . $e->getMessage());
}

redirect(base_url('admin/diskon.php'));
