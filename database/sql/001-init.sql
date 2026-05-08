CREATE DATABASE IF NOT EXISTS db_restoran;
USE db_restoran;

-- Tabel user dipakai dulu untuk login semua role.
CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` enum('admin','kasir','pelanggan') NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TODO: tambahkan tabel kategori, menu, pesanan, dan pembayaran saat endpoint backend dikerjakan.

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
