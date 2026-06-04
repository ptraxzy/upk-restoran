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
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

    // Ambil ID Pelanggan default
    $stmtUser = $pdo->query("SELECT id_pelanggan FROM pelanggan WHERE username = 'testmember' LIMIT 1");
    $idPelanggan = $stmtUser->fetchColumn();
    if (!$idPelanggan) {
        // Buat jika tidak ada
        $passHash = password_hash('secret123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO pelanggan (nama_pelanggan, username, email, password) VALUES ('Test Member', 'testmember', 'member@lumiere.com', '$passHash')");
        $idPelanggan = $pdo->lastInsertId();
    }

    // Ambil ID Karyawan default
    $stmtStaff = $pdo->query("SELECT id_karyawan FROM karyawan WHERE username = 'kasir' LIMIT 1");
    $idKaryawan = $stmtStaff->fetchColumn();
    if (!$idKaryawan) {
        // Buat jika tidak ada
        $passHash = password_hash('kasir123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO karyawan (nama_karyawan, username, email, password) VALUES ('Kasir Utama', 'kasir', 'kasir@lumiere.com', '$passHash')");
        $idKaryawan = $pdo->lastInsertId();
    }

    // List menu bawaan beserta harganya
    $menus = [
        1 => ['nama' => 'Wagyu Ribeye A5', 'harga' => 125000],
        2 => ['nama' => 'Pan-Seared Duck', 'harga' => 85000],
        3 => ['nama' => 'Black Truffle Risotto', 'harga' => 75000],
        4 => ['nama' => 'Hokkaido Scallop', 'harga' => 95000],
        5 => ['nama' => 'Dark Matter', 'harga' => 45000]
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
    $orderIndex = 1;

    // Generate data untuk 14 hari terakhir sampai hari ini
    for ($i = 13; $i >= 0; $i--) {
        $dateStr = date('Y-m-d', strtotime("-$i days"));
        
        // Targetkan jumlah pesanan harian (makin dekat ke hari ini, makin ramai/tinggi penjualannya)
        $numOrders = rand(3, 7);
        if ($i === 0) {
            $numOrders = 8; // Buat hari ini lebih sibuk untuk demo real-time
        }

        for ($j = 0; $j < $numOrders; $j++) {
            // Generate jam acak
            $hour = rand(11, 21);
            $minute = rand(0, 59);
            $second = rand(0, 59);
            $datetimeStr = "$dateStr $hour:$minute:$second";

            // Pilih nomor meja secara acak
            $noMeja = $noMejaList[array_rand($noMejaList)];

            // Tentukan status pesanan
            $statusPesanan = 'Selesai';
            if ($i === 0) {
                // Hari ini ada beberapa order aktif untuk simulasi status
                if ($j === 0) {
                    $statusPesanan = 'Diproses';
                } elseif ($j === 1) {
                    $statusPesanan = 'Sedang Disiapkan';
                } elseif ($j === 2) {
                    $statusPesanan = 'Siap Saji';
                }
            }

            // Pilih 1 sampai 3 menu acak untuk pesanan ini
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

            // Masukkan ke tabel pesanan
            $stmtOrder = $pdo->prepare("INSERT INTO pesanan (id_pelanggan, id_karyawan, no_meja, tanggal_pesanan, total_harga, status_pesanan) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtOrder->execute([
                $idPelanggan,
                $idKaryawan,
                $noMeja,
                $datetimeStr,
                $totalHarga,
                $statusPesanan
            ]);
            $newOrderId = $pdo->lastInsertId();

            // Masukkan detail pesanan
            foreach ($itemsToInsert as $item) {
                $stmtDetail = $pdo->prepare("INSERT INTO detail_pesanan (id_pesanan, id_menu, jumlah, harga_satuan) VALUES (?, ?, ?, ?)");
                $stmtDetail->execute([
                    $newOrderId,
                    $item['id_menu'],
                    $item['jumlah'],
                    $item['harga_satuan']
                ]);
            }

            // Tentukan status pembayaran
            // Jika pesanan selain yang masih diproses/disiapkan hari ini, status lunas
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
                    $idKaryawan
                ]);

                // Tambahkan rating ulasan secara acak (sekitar 40% kemungkinan)
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
        'message' => 'Simulasi data presentasi berhasil dibuat untuk 14 hari terakhir!',
        'detail' => 'Silakan hapus file seed_demo.php dari root folder demi alasan keamanan setelah presentasi selesai.'
    ], JSON_PRETTY_PRINT);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
