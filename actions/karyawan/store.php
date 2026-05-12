<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect(base_url('admin/karyawan/create.php'));
}

// TODO: validasi input dan insert ke tabel karyawan.
set_flash('error', 'Penyimpanan karyawan belum dihubungkan ke database.');
redirect(base_url('admin/karyawan/index.php'));
