-- Tabel admin: khusus akun administrator
CREATE TABLE IF NOT EXISTS `admin` (
  `id_admin` int NOT NULL AUTO_INCREMENT,
  `nama_admin` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  PRIMARY KEY (`id_admin`),
  UNIQUE KEY `admin_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel karyawan: khusus akun kasir/karyawan operasional
CREATE TABLE IF NOT EXISTS `karyawan` (
  `id_karyawan` int NOT NULL AUTO_INCREMENT,
  `nama_karyawan` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  PRIMARY KEY (`id_karyawan`),
  UNIQUE KEY `karyawan_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel pelanggan: khusus akun member/pembeli
CREATE TABLE IF NOT EXISTS `pelanggan` (
  `id_pelanggan` int NOT NULL AUTO_INCREMENT,
  `nama_pelanggan` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Aktif',
  PRIMARY KEY (`id_pelanggan`),
  UNIQUE KEY `pelanggan_username_unique` (`username`),
  UNIQUE KEY `pelanggan_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `password_resets_token_unique` (`token`),
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel kategori, menu, pesanan, detail_pesanan, dan pembayaran.
CREATE TABLE IF NOT EXISTS `kategori` (
  `id_kategori` int NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(50) NOT NULL,
  `id_admin` int DEFAULT NULL,
  PRIMARY KEY (`id_kategori`),
  FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `menu` (
  `id_menu` int NOT NULL AUTO_INCREMENT,
  `id_kategori` int NOT NULL,
  `nama_menu` varchar(100) NOT NULL,
  `deskripsi` text,
  `harga` decimal(10,2) NOT NULL,
  `gambar` varchar(255),
  `status` enum('Tersedia', 'Habis') DEFAULT 'Tersedia',
  `porsi` int DEFAULT 10,
  `id_admin` int DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int DEFAULT NULL,
  PRIMARY KEY (`id_menu`),
  FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE,
  FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `keranjang` (
  `id_keranjang` int NOT NULL AUTO_INCREMENT,
  `id_pelanggan` int NOT NULL,
  `id_menu` int NOT NULL,
  `qty` int NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_keranjang`),
  UNIQUE KEY `keranjang_pelanggan_menu_unique` (`id_pelanggan`, `id_menu`),
  KEY `keranjang_menu_index` (`id_menu`),
  FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE,
  FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pesanan` (
  `id_pesanan` int NOT NULL AUTO_INCREMENT,
  `id_pelanggan` int DEFAULT NULL,
  `id_karyawan` int DEFAULT NULL,
  `no_meja` varchar(10) NOT NULL,
  `tanggal_pesanan` datetime DEFAULT CURRENT_TIMESTAMP,
  `total_harga` decimal(12,2) NOT NULL,
  `status_pesanan` enum('Menunggu Pembayaran', 'Diproses', 'Sedang Disiapkan', 'Siap Saji', 'Selesai', 'Dibatalkan') DEFAULT 'Menunggu Pembayaran',
  PRIMARY KEY (`id_pesanan`),
  FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE SET NULL,
  FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `detail_pesanan` (
  `id_detail` int NOT NULL AUTO_INCREMENT,
  `id_pesanan` int NOT NULL,
  `id_menu` int NOT NULL,
  `jumlah` int NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_detail`),
  FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
  FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pembayaran` (
  `id_pembayaran` int NOT NULL AUTO_INCREMENT,
  `id_pesanan` int NOT NULL,
  `tanggal_pembayaran` datetime DEFAULT CURRENT_TIMESTAMP,
  `total_bayar` decimal(12,2) NOT NULL,
  `metode` enum('QRIS', 'Tunai', 'Kartu Kredit') NOT NULL,
  `status` enum('Menunggu', 'Lunas', 'Gagal') DEFAULT 'Menunggu',
  `trx_id` varchar(100),
  `id_pelanggan` int DEFAULT NULL,
  `id_karyawan` int DEFAULT NULL,
  PRIMARY KEY (`id_pembayaran`),
  FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
  FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE SET NULL,
  FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id_karyawan`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `voucher` (
  `id_voucher` int NOT NULL AUTO_INCREMENT,
  `kode_voucher` varchar(50) NOT NULL,
  `nama_voucher` varchar(255) NOT NULL,
  `jenis_voucher` enum('Persentase','Nominal') NOT NULL,
  `nilai_voucher` decimal(15,2) NOT NULL,
  `minimal_pembelian` decimal(15,2) NOT NULL DEFAULT '0.00',
  `minimal_porsi` int NOT NULL DEFAULT 0,
  `tanggal_mulai` date NOT NULL,
  `tanggal_berakhir` date NOT NULL,
  `status_voucher` enum('Active','Scheduled','Expired') DEFAULT 'Active',
  `id_admin` int DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int DEFAULT NULL,
  PRIMARY KEY (`id_voucher`),
  UNIQUE KEY `voucher_kode_unique` (`kode_voucher`),
  FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed data menu
INSERT IGNORE INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES 
(1, 'Hidangan Utama'), 
(2, 'Hidangan Pembuka'), 
(3, 'Pencuci Mulut'), 
(4, 'Minuman');

INSERT IGNORE INTO `menu` (`id_menu`, `id_kategori`, `nama_menu`, `deskripsi`, `harga`, `gambar`, `status`, `porsi`) VALUES
(1, 1, 'Wagyu Ribeye A5', 'A5 Japanese Wagyu, black garlic butter, smoked sea salt.', 125000, 'https://images.unsplash.com/photo-1558030006-450675393462?auto=format&fit=crop&w=1200&q=80', 'Tersedia', 18),
(2, 1, 'Pan-Seared Duck', 'Dry-aged duck breast, cherry reduction, parsnip puree.', 85000, 'https://images.unsplash.com/photo-1625943555419-56a2cb596640?auto=format&fit=crop&w=1200&q=80', 'Tersedia', 22),
(3, 1, 'Black Truffle Risotto', 'Acquerello rice, wild mushrooms, shaved black truffle.', 75000, 'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&w=1200&q=80', 'Tersedia', 12),
(4, 2, 'Hokkaido Scallop', 'Yuzu plum hijau fermentasi, lobak es, busa kedelai putih, jeruk mirin.', 95000, 'https://images.unsplash.com/photo-1511690656952-34342bb7c2f2?auto=format&fit=crop&w=800&q=80', 'Tersedia', 10),
(5, 3, 'Dark Matter', 'Kakao eksklusif single-origin, praline wijen hitam, balsamic dust.', 45000, 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?auto=format&fit=crop&w=1200&q=80', 'Tersedia', 15);

-- Seed data admin
INSERT INTO `admin` (`nama_admin`, `username`, `password`)
SELECT 'Administrator', 'admin', 'admin123'
WHERE NOT EXISTS (SELECT 1 FROM `admin` WHERE `username` = 'admin');

-- Seed data karyawan
INSERT INTO `karyawan` (`nama_karyawan`, `username`, `password`)
SELECT 'Kasir Utama', 'kasir', 'kasir123'
WHERE NOT EXISTS (SELECT 1 FROM `karyawan` WHERE `username` = 'kasir');

-- Seed data pelanggan
INSERT INTO `pelanggan` (`nama_pelanggan`, `username`, `password`)
SELECT 'Test Member', 'testmember', 'secret123'
WHERE NOT EXISTS (SELECT 1 FROM `pelanggan` WHERE `username` = 'testmember');

CREATE TABLE IF NOT EXISTS `ulasan` (
  `id_ulasan` INT NOT NULL AUTO_INCREMENT,
  `id_pesanan` INT NOT NULL,
  `id_pelanggan` INT NOT NULL,
  `rating` INT NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `komentar` TEXT,
  `tanggal_ulasan` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_ulasan`),
  UNIQUE KEY `ulasan_pesanan_unique` (`id_pesanan`),
  FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
  FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel pengaturan (key-value store)
CREATE TABLE IF NOT EXISTS `pengaturan` (
  `kunci` varchar(100) NOT NULL,
  `nilai` text,
  PRIMARY KEY (`kunci`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
