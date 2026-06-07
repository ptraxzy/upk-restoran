<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/database.php';

try {
    $pdo = db();

    // Hapus data transaksi lama jika ada untuk mencegah tabrakan/duplikasi
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    $pdo->exec('TRUNCATE TABLE ulasan');
    $pdo->exec('TRUNCATE TABLE pembayaran');
    $pdo->exec('TRUNCATE TABLE detail_pesanan');
    $pdo->exec('TRUNCATE TABLE pesanan');
    $pdo->exec('TRUNCATE TABLE karyawan');
    $pdo->exec('TRUNCATE TABLE voucher');
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

    // Ambil ID Pelanggan default
    $stmtUser = $pdo->query("SELECT id_pelanggan FROM pelanggan WHERE username = 'testmember' LIMIT 1");
    $idPelanggan = $stmtUser->fetchColumn();
    if (!$idPelanggan) {
        $passHash = password_hash('secret123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO pelanggan (nama_pelanggan, username, email, password) VALUES ('Test Member', 'testmember', 'member@lumiere.com', '$passHash')");
        $idPelanggan = $pdo->lastInsertId();
    }

    // Masukkan data Karyawan (Kasir)
    $passKasirHash = password_hash('kasir123', PASSWORD_DEFAULT);
    $karyawans = [
        ['Kasir Utama', 'kasir', 'kasir@lumiere.com', $passKasirHash, 'Aktif'],
        ['Ahmad Subarjo', 'ahmad_kasir', 'ahmad@lumiere.com', $passKasirHash, 'Aktif'],
        ['Siti Rahma', 'siti_kasir', 'siti@lumiere.com', $passKasirHash, 'Aktif'],
        ['Budi Santoso', 'budi_kasir', 'budi@lumiere.com', $passKasirHash, 'Aktif']
    ];

    $karyawanIds = [];
    foreach ($karyawans as $k) {
        $stmtK = $pdo->prepare("INSERT INTO karyawan (nama_karyawan, username, email, password, status) VALUES (?, ?, ?, ?, ?)");
        $stmtK->execute([$k[0], $k[1], $k[2], $k[3], $k[4]]);
        $karyawanIds[] = $pdo->lastInsertId();
    }
    // Set ID Karyawan utama untuk relasi transaksi
    $idKaryawan = $karyawanIds[0];

    // Masukkan data Voucher diskon
    $vouchers = [
        ['LUMIERE50K', 'Diskon Grand Opening Rp50.000', 'Nominal', 50000, 200000, 2, '2026-05-15', '2026-06-30', 'Active'],
        ['WAGYU10', 'Promo Akhir Pekan 10%', 'Persentase', 10, 100000, 1, '2026-05-20', '2026-06-15', 'Active'],
        ['WELCOME20', 'Voucher Member Baru 20%', 'Persentase', 20, 50000, 1, '2026-05-01', '2026-07-31', 'Active'],
        ['LUMIEREPAHE', 'Potongan Paket Hemat Rp15.000', 'Nominal', 15000, 75000, 2, '2026-05-22', '2026-06-25', 'Active']
    ];

    foreach ($vouchers as $v) {
        $stmtV = $pdo->prepare("INSERT INTO voucher (kode_voucher, nama_voucher, jenis_voucher, nilai_voucher, minimal_pembelian, minimal_porsi, tanggal_mulai, tanggal_berakhir, status_voucher) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtV->execute([$v[0], $v[1], $v[2], $v[3], $v[4], $v[5], $v[6], $v[7], $v[8]]);
    }

    // List menu bawaan beserta harganya
    $menus = [
        1 => ['nama' => 'Wagyu Ribeye A5', 'harga' => 125000],
        2 => ['nama' => 'Pan-Seared Duck', 'harga' => 85000],
        3 => ['nama' => 'Black Truffle Risotto', 'harga' => 75000],
        4 => ['nama' => 'Hokkaido Scallop', 'harga' => 95000],
        5 => ['nama' => 'Dark Matter', 'harga' => 45000],
        6 => ['nama' => 'Ethereal Rose Nectar', 'harga' => 35000],
        7 => ['nama' => 'Gold Dust Elixir', 'harga' => 50000],
        8 => ['nama' => 'Royal Earl Grey Tea', 'harga' => 25000]
    ];

    // Data komentar ulasan premium
    $comments = [
        5 => [
            "Wagyu A5-nya benar-benar lumer di mulut! Pelayanan luar biasa cepat.",
            "Truffle Risotto terbaik yang pernah saya coba di kota ini. Suasananya sangat premium.",
            "Scallop-nya segar sekali dan saus yuzu-nya memberikan sentuhan asam manis yang pas.",
            "Presentasi hidangan penutup Dark Matter sangat artistik dan rasanya sangat kaya.",
            "Sistem pemesanan QRIS mandiri ini sangat praktis, makanan cepat diantarkan ke meja."
        ],
        4 => [
            "Makanannya lezat dan pelayanannya ramah. Sayang mejanya sempat agak berdebu sedikit.",
            "Porsi bebeknya pas dan rasanya gurih sekali. Overall memuaskan untuk makan malam.",
            "Sangat menyukai konsep self-service portal ini, tidak perlu antre panjang di kasir.",
            "Wagyu-nya mantap, hanya saja proses pembayarannya sempat tertunda beberapa detik."
        ]
    ];

    $noMejaList = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10'];

    // Generate data untuk 14 hari terakhir sampai hari ini
    for ($i = 13; $i >= 0; $i--) {
        $dateStr = date('Y-m-d', strtotime("-$i days"));
        
        $numOrders = rand(3, 7);
        if ($i === 0) {
            $numOrders = 8; 
        }

        for ($j = 0; $j < $numOrders; $j++) {
            $hour = rand(11, 21);
            $minute = rand(0, 59);
            $second = rand(0, 59);
            $datetimeStr = "$dateStr $hour:$minute:$second";

            $noMeja = $noMejaList[array_rand($noMejaList)];

            $statusPesanan = 'Selesai';
            if ($i === 0) {
                if ($j === 0) {
                    $statusPesanan = 'Diproses';
                } elseif ($j === 1) {
                    $statusPesanan = 'Sedang Disiapkan';
                } elseif ($j === 2) {
                    $statusPesanan = 'Siap Saji';
                }
            }

            $selectedMenuIds = array_rand($menus, rand(1, 3));
            if (!is_array($selectedMenuIds)) {
                $selectedMenuIds = [$selectedMenuIds];
            }

            $totalHarga = 0.0;
            $itemsToInsert = [];

            foreach ($selectedMenuIds as $menuId) {
                $qty = rand(1, 2);
                $hargaSatuan = $menus[$menuId]['harga'];
                $subtotal = $qty * $hargaSatuan;
                $totalHarga += $subtotal;

                $itemsToInsert[] = [
                    'id_menu' => $menuId,
                    'jumlah' => $qty,
                    'harga_satuan' => $hargaSatuan
                ];
            }

            // Pilih kasir secara acak untuk melayani pesanan ini
            $currentKaryawanId = $karyawanIds[array_rand($karyawanIds)];

            $stmtOrder = $pdo->prepare("INSERT INTO pesanan (id_pelanggan, id_karyawan, no_meja, tanggal_pesanan, total_harga, status_pesanan) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtOrder->execute([
                $idPelanggan,
                $currentKaryawanId,
                $noMeja,
                $datetimeStr,
                $totalHarga,
                $statusPesanan
            ]);
            $newOrderId = $pdo->lastInsertId();

            foreach ($itemsToInsert as $item) {
                $stmtDetail = $pdo->prepare("INSERT INTO detail_pesanan (id_pesanan, id_menu, jumlah, harga_satuan) VALUES (?, ?, ?, ?)");
                $stmtDetail->execute([
                    $newOrderId,
                    $item['id_menu'],
                    $item['jumlah'],
                    $item['harga_satuan']
                ]);
            }

            if ($statusPesanan === 'Selesai' || $statusPesanan === 'Siap Saji') {
                $statusBayar = 'Lunas';
                $metode = rand(1, 2) === 1 ? 'QRIS' : 'Tunai';
                
                $stmtPayment = $pdo->prepare("INSERT INTO pembayaran (id_pesanan, tanggal_pembayaran, total_bayar, metode, status, trx_id, id_pelanggan, id_karyawan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmtPayment->execute([
                    $newOrderId,
                    $datetimeStr,
                    $totalHarga,
                    $metode,
                    $statusBayar,
                    'TRX-' . strtoupper(substr(md5((string)$newOrderId), 0, 10)),
                    $idPelanggan,
                    $currentKaryawanId
                ]);

                if (rand(1, 10) <= 4) {
                    $rating = rand(4, 5);
                    $commentList = $comments[$rating];
                    $komentar = $commentList[array_rand($commentList)];

                    $stmtReview = $pdo->prepare("INSERT INTO ulasan (id_pesanan, id_pelanggan, rating, komentar, tanggal_ulasan) VALUES (?, ?, ?, ?, ?)");
                    $stmtReview->execute([
                        $newOrderId,
                        $idPelanggan,
                        $rating,
                        $komentar,
                        $datetimeStr
                    ]);
                }
            }
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Simulasi data presentasi berhasil dibuat (Transaksi 14 hari, 4 Kasir/Karyawan baru, dan 4 Voucher aktif)!',
        'detail' => 'Silakan jalankan kembali URL seed_demo.php Anda di browser untuk memperbarui database.'
    ], JSON_PRETTY_PRINT);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
