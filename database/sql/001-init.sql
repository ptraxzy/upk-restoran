CREATE DATABASE IF NOT EXISTS db_restoran;
USE db_restoran;

-- Tabel user dipakai dulu untuk login semua role.
CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `nama_user` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `level` enum('admin','kasir','pelanggan') NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `user_username_unique` (`username`),
  UNIQUE KEY `user_email_unique` (`email`)
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
  PRIMARY KEY (`id_kategori`)
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
  PRIMARY KEY (`id_menu`),
  FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pesanan` (
  `id_pesanan` int NOT NULL AUTO_INCREMENT,
  `id_user` int DEFAULT NULL,
  `no_meja` varchar(10) NOT NULL,
  `tanggal_pesanan` datetime DEFAULT CURRENT_TIMESTAMP,
  `total_harga` decimal(12,2) NOT NULL,
  `status_pesanan` enum('Menunggu Pembayaran', 'Diproses', 'Sedang Disiapkan', 'Siap Saji', 'Selesai', 'Dibatalkan') DEFAULT 'Menunggu Pembayaran',
  PRIMARY KEY (`id_pesanan`),
  FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL
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
  PRIMARY KEY (`id_pembayaran`),
  FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed data menu
INSERT IGNORE INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES 
(1, 'Hidangan Utama'), 
(2, 'Hidangan Pembuka'), 
(3, 'Pencuci Mulut'), 
(4, 'Minuman');

INSERT IGNORE INTO `menu` (`id_menu`, `id_kategori`, `nama_menu`, `deskripsi`, `harga`, `gambar`, `status`, `porsi`) VALUES
(1, 1, 'Wagyu Ribeye A5', 'A5 Japanese Wagyu, black garlic butter, smoked sea salt.', 420000, 'https://images.unsplash.com/photo-1558030006-450675393462?auto=format&fit=crop&w=1200&q=80', 'Tersedia', 18),
(2, 1, 'Pan-Seared Duck', 'Dry-aged duck breast, cherry reduction, parsnip puree.', 280000, 'https://images.unsplash.com/photo-1625943555419-56a2cb596640?auto=format&fit=crop&w=1200&q=80', 'Tersedia', 22),
(3, 1, 'Black Truffle Risotto', 'Acquerello rice, wild mushrooms, shaved black truffle.', 195000, 'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&w=1200&q=80', 'Tersedia', 12),
(4, 2, 'Hokkaido Scallop', 'Yuzu plum hijau fermentasi, lobak es, busa kedelai putih, jeruk mirin.', 250000, 'https://images.unsplash.com/photo-1511690656952-34342bb7c2f2?auto=format&fit=crop&w=800&q=80', 'Tersedia', 10),
(5, 3, 'Dark Matter', 'Kakao eksklusif single-origin, praline wijen hitam, balsamic dust.', 82000, 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?auto=format&fit=crop&w=1200&q=80', 'Tersedia', 15);

INSERT INTO `user` (`username`, `password`, `level`)
SELECT 'admin', 'admin123', 'admin'
WHERE NOT EXISTS (
  SELECT 1 FROM `user` WHERE `username` = 'admin'
);

INSERT INTO `user` (`username`, `password`, `level`)
SELECT 'kasir', 'kasir123', 'kasir'
WHERE NOT EXISTS (
  SELECT 1 FROM `user` WHERE `username` = 'kasir'
);

INSERT INTO `user` (`username`, `password`, `level`)
SELECT 'admin.ops', 'admin456', 'admin'
WHERE NOT EXISTS (
  SELECT 1 FROM `user` WHERE `username` = 'admin.ops'
);

INSERT INTO `user` (`username`, `password`, `level`)
SELECT 'admin.floor', 'admin789', 'admin'
WHERE NOT EXISTS (
  SELECT 1 FROM `user` WHERE `username` = 'admin.floor'
);

INSERT INTO `user` (`username`, `password`, `level`)
SELECT 'kasir.senja', 'kasir456', 'kasir'
WHERE NOT EXISTS (
  SELECT 1 FROM `user` WHERE `username` = 'kasir.senja'
);

INSERT INTO `user` (`username`, `password`, `level`)
SELECT 'kasir.raka', 'kasir789', 'kasir'
WHERE NOT EXISTS (
  SELECT 1 FROM `user` WHERE `username` = 'kasir.raka'
);

INSERT INTO `user` (`username`, `password`, `level`)
SELECT 'testmember', 'secret123', 'pelanggan'
WHERE NOT EXISTS (
  SELECT 1 FROM `user` WHERE `username` = 'testmember'
);

INSERT INTO `user` (`username`, `password`, `level`)
SELECT 'member.ayla', 'member456', 'pelanggan'
WHERE NOT EXISTS (
  SELECT 1 FROM `user` WHERE `username` = 'member.ayla'
);

INSERT INTO `user` (`username`, `password`, `level`)
SELECT 'member.nara', 'member789', 'pelanggan'
WHERE NOT EXISTS (
  SELECT 1 FROM `user` WHERE `username` = 'member.nara'
);
