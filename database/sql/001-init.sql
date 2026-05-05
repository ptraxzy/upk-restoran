CREATE DATABASE IF NOT EXISTS db_restoran;
USE db_restoran;

CREATE TABLE IF NOT EXISTS `kategori` (
  `id_kategori` int NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(50) NOT NULL,
  PRIMARY KEY (`id_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` enum('admin','kasir','pelanggan') NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `menu` (
  `id_menu` int NOT NULL AUTO_INCREMENT,
  `nama_menu` varchar(100) NOT NULL,
  `harga` int NOT NULL,
  `status_menu` enum('tersedia','habis') NOT NULL,
  `id_kategori` int NOT NULL,
  PRIMARY KEY (`id_menu`),
  KEY `idx_menu_kategori` (`id_kategori`),
  CONSTRAINT `fk_menu_kategori`
    FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `transaksi` (
  `id_transaksi` int NOT NULL AUTO_INCREMENT,
  `tgl_transaksi` datetime NOT NULL,
  `total_bayar` int NOT NULL,
  `id_user` int NOT NULL,
  `id_menu` int NOT NULL,
  PRIMARY KEY (`id_transaksi`),
  KEY `idx_transaksi_user` (`id_user`),
  KEY `idx_transaksi_menu` (`id_menu`),
  CONSTRAINT `fk_transaksi_user`
    FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT `fk_transaksi_menu`
    FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
