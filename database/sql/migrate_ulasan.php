<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/database.php';

try {
    $pdo = db();

    echo "Memulai migrasi tabel ulasan...\n";

    // Buat tabel ulasan jika belum ada
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `ulasan` (
            `id_ulasan` INT NOT NULL AUTO_INCREMENT,
            `id_pesanan` INT NOT NULL,
            `id_user` INT NOT NULL,
            `rating` INT NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
            `komentar` TEXT,
            `tanggal_ulasan` DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id_ulasan`),
            UNIQUE KEY `ulasan_pesanan_unique` (`id_pesanan`),
            FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
            FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    echo "Tabel ulasan berhasil dibuat di database!\n";

} catch (Exception $e) {
    echo "Gagal menjalankan migrasi tabel ulasan: " . $e->getMessage() . "\n";
    exit(1);
}
