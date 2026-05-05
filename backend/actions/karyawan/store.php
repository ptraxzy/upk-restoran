<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect(frontend_url('admin/karyawan/create.php'));
}

set_flash('success', 'Data karyawan berhasil disimpan.');
redirect(frontend_url('admin/karyawan/index.php'));
