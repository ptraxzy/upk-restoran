<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

// TODO: simpan pembayaran dan update status pesanan.
set_flash('error', 'Pembayaran belum dihubungkan ke database.');
redirect(base_url('karyawan/pembayaran/index.php'));
