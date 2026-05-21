<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/database.php';

$pdo = db();

try {
    echo "1. Creating user table if not exists...\n";
    $pdo->exec("
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
    ");

    echo "2. Copying data from admin to user...\n";
    try {
        $pdo->exec("
            INSERT IGNORE INTO `user` (id_user, nama_user, username, email, password, level)
            SELECT id_admin, nama_admin, username, email, password, 'admin' FROM admin;
        ");
    } catch (Exception $e) {
        echo "   (Skipped admin copy or table doesn't exist: " . $e->getMessage() . ")\n";
    }

    echo "3. Copying data from kasir to user...\n";
    try {
        $pdo->exec("
            INSERT IGNORE INTO `user` (id_user, nama_user, username, email, password, level)
            SELECT id_kasir, nama_kasir, username, email, password, 'kasir' FROM kasir;
        ");
    } catch (Exception $e) {
        echo "   (Skipped kasir copy or table doesn't exist: " . $e->getMessage() . ")\n";
    }

    echo "4. Copying data from pelanggan to user...\n";
    try {
        $pdo->exec("
            INSERT IGNORE INTO `user` (id_user, nama_user, username, email, password, level)
            SELECT id_pelanggan, nama_pelanggan, username, email, password, 'pelanggan' FROM pelanggan;
        ");
    } catch (Exception $e) {
        echo "   (Skipped pelanggan copy or table doesn't exist: " . $e->getMessage() . ")\n";
    }

    echo "5. Adjusting menu table...\n";
    $columnsMenu = $pdo->query("SHOW COLUMNS FROM menu")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('id_user', $columnsMenu)) {
        $pdo->exec("ALTER TABLE menu ADD COLUMN id_user INT DEFAULT NULL");
    }
    
    if (in_array('id_admin', $columnsMenu)) {
        // Drop existing foreign keys on id_admin if any
        try {
            $pdo->exec("ALTER TABLE menu DROP FOREIGN KEY fk_menu_admin");
        } catch (Exception $e) {}
        try {
            $pdo->exec("ALTER TABLE menu DROP FOREIGN KEY menu_ibfk_2");
        } catch (Exception $e) {}
        
        $pdo->exec("UPDATE menu SET id_user = id_admin WHERE id_admin IS NOT NULL");
        $pdo->exec("ALTER TABLE menu DROP COLUMN id_admin");
    }

    echo "6. Adjusting keranjang table...\n";
    $columnsKeranjang = $pdo->query("SHOW COLUMNS FROM keranjang")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('id_user', $columnsKeranjang)) {
        $pdo->exec("ALTER TABLE keranjang ADD COLUMN id_user INT NOT NULL");
    }

    if (in_array('id_pelanggan', $columnsKeranjang)) {
        try {
            $pdo->exec("ALTER TABLE keranjang DROP FOREIGN KEY fk_keranjang_pelanggan");
        } catch (Exception $e) {}
        try {
            $pdo->exec("ALTER TABLE keranjang DROP FOREIGN KEY keranjang_ibfk_3");
        } catch (Exception $e) {}

        $pdo->exec("UPDATE keranjang SET id_user = id_pelanggan");
        $pdo->exec("ALTER TABLE keranjang DROP COLUMN id_pelanggan");
    }

    echo "7. Adjusting pesanan table...\n";
    $columnsPesanan = $pdo->query("SHOW COLUMNS FROM pesanan")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('id_user', $columnsPesanan)) {
        $pdo->exec("ALTER TABLE pesanan ADD COLUMN id_user INT DEFAULT NULL");
    }

    if (in_array('id_pelanggan', $columnsPesanan)) {
        try {
            $pdo->exec("ALTER TABLE pesanan DROP FOREIGN KEY fk_pesanan_pelanggan");
        } catch (Exception $e) {}
        try {
            $pdo->exec("ALTER TABLE pesanan DROP FOREIGN KEY pesanan_ibfk_2");
        } catch (Exception $e) {}

        $pdo->exec("UPDATE pesanan SET id_user = id_pelanggan");
        $pdo->exec("ALTER TABLE pesanan DROP COLUMN id_pelanggan");
    }

    echo "8. Re-establishing foreign keys and index unique constraints...\n";
    // Drop existing FK on user if any
    try {
        $pdo->exec("ALTER TABLE keranjang DROP FOREIGN KEY fk_keranjang_user");
    } catch (Exception $e) {}
    try {
        $pdo->exec("ALTER TABLE pesanan DROP FOREIGN KEY fk_pesanan_user");
    } catch (Exception $e) {}
    try {
        $pdo->exec("ALTER TABLE menu DROP FOREIGN KEY fk_menu_user");
    } catch (Exception $e) {}

    // Add FKs
    $pdo->exec("ALTER TABLE keranjang ADD CONSTRAINT fk_keranjang_user FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE CASCADE");
    $pdo->exec("ALTER TABLE pesanan ADD CONSTRAINT fk_pesanan_user FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE SET NULL");
    $pdo->exec("ALTER TABLE menu ADD CONSTRAINT fk_menu_user FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE SET NULL");

    echo "9. Dropping redundant admin, kasir, pelanggan tables...\n";
    $pdo->exec("DROP TABLE IF EXISTS admin");
    $pdo->exec("DROP TABLE IF EXISTS kasir");
    $pdo->exec("DROP TABLE IF EXISTS pelanggan");

    echo "SUCCESS: Revert to single user table complete with preserved data!\n";
} catch (Exception $e) {
    echo "FAILED WITH EXCEPTION: " . $e->getMessage() . "\n";
    exit(1);
}
