<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

// TODO: validasi item keranjang lalu buat pesanan.
set_flash('error', 'Penyimpanan pesanan belum dihubungkan ke database.');
redirect(base_url('kasir/pesanan.php'));
