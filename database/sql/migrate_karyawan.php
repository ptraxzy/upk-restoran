<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/database.php';

try {
    $pdo = db();

    echo "Memulai migrasi kolom id_karyawan...\n";

    // Migrasi skema: integrasi relasi karyawan ke entitas pesanan
    $stmtPesananCheck = $pdo->query("SHOW COLUMNS FROM pesanan LIKE 'id_karyawan'");
    if (!$stmtPesananCheck->fetch()) {
        echo "Menambahkan kolom id_karyawan ke tabel pesanan...\n";
        $pdo->exec("
            ALTER TABLE pesanan 
            ADD COLUMN id_karyawan INT DEFAULT NULL,
            ADD CONSTRAINT fk_pesanan_karyawan FOREIGN KEY (id_karyawan) REFERENCES user(id_user) ON DELETE SET NULL
        ");
        echo "Kolom id_karyawan berhasil ditambahkan ke tabel pesanan.\n";
    } else {
        echo "Kolom id_karyawan sudah ada di tabel pesanan.\n";
    }

    // Migrasi skema: integrasi relasi karyawan ke entitas pembayaran
    $stmtPembayaranCheck = $pdo->query("SHOW COLUMNS FROM pembayaran LIKE 'id_karyawan'");
    if (!$stmtPembayaranCheck->fetch()) {
        echo "Menambahkan kolom id_karyawan ke tabel pembayaran...\n";
        $pdo->exec("
            ALTER TABLE pembayaran 
            ADD COLUMN id_karyawan INT DEFAULT NULL,
            ADD CONSTRAINT fk_pembayaran_karyawan FOREIGN KEY (id_karyawan) REFERENCES user(id_user) ON DELETE SET NULL
        ");
        echo "Kolom id_karyawan berhasil ditambahkan ke tabel pembayaran.\n";
    } else {
        echo "Kolom id_karyawan sudah ada di tabel pembayaran.\n";
    }

    echo "Migrasi database berhasil diselesaikan!\n";

} catch (Exception $e) {
    echo "Gagal menjalankan migrasi database: " . $e->getMessage() . "\n";
    exit(1);
}
