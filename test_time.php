<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/database.php';

header('Content-Type: application/json');

try {
    $pdo = db();

    // Ambil waktu PHP dan MySQL
    $phpTime = date('Y-m-d H:i:s');
    $dbTime = $pdo->query("SELECT NOW()")->fetchColumn();
    $dbTimezone = $pdo->query("SELECT @@system_time_zone, @@global.time_zone, @@session.time_zone")->fetch();

    // Ambil 10 pesanan terbaru berdasarkan ID terbaru (auto increment)
    $stmt = $pdo->query("
        SELECT p.id_pesanan, p.id_pelanggan, pl.username, p.no_meja, p.tanggal_pesanan, p.total_harga, p.status_pesanan
        FROM pesanan p
        LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
        ORDER BY p.id_pesanan DESC
        LIMIT 10
    ");
    $latestOrders = $stmt->fetchAll();

    echo json_encode([
        'server_time' => [
            'php_current_time' => $phpTime,
            'db_current_time' => $dbTime,
            'db_timezone_info' => $dbTimezone,
        ],
        'latest_10_orders_by_id' => $latestOrders
    ], JSON_PRETTY_PRINT);

} catch (Throwable $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
