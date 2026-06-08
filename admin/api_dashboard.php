<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

require_once __DIR__ . '/../includes/database.php';
$pdo = db();

header('Content-Type: application/json');

// Revenue today
$stmtRev = $pdo->query("SELECT COALESCE(SUM(total_bayar), 0) FROM pembayaran WHERE status = 'Lunas' AND DATE(tanggal_pembayaran) = CURDATE()");
$revToday = (float)$stmtRev->fetchColumn();

// Orders today
$stmtPesanan = $pdo->query("SELECT COUNT(*) FROM pesanan WHERE DATE(tanggal_pesanan) = CURDATE()");
$pesananToday = (int)$stmtPesanan->fetchColumn();

// Available menu
$stmtMenu = $pdo->query("SELECT COUNT(*) FROM menu WHERE status = 'Tersedia'");
$menuAktif = (int)$stmtMenu->fetchColumn();

// Active kasir only (admin not counted)
$countKasir = (int) $pdo->query("SELECT COUNT(*) FROM karyawan WHERE status = 'Aktif'")->fetchColumn();
$timAktif = $countKasir;

// Weekly chart data (7 days)
$dayTranslations = [
    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
];

$last7Days = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $englishDay = date('l', strtotime($date));
    $indoDay = $dayTranslations[$englishDay] ?? $englishDay;
    $formattedDate = date('d M', strtotime($date));
    $last7Days[$date] = [
        'label' => $indoDay . ' (' . $formattedDate . ')',
        'revenue' => 0.0,
        'orders' => 0
    ];
}

$stmtRevPast = $pdo->prepare("
    SELECT DATE(py.tanggal_pembayaran) AS tanggal, SUM(py.total_bayar) AS total
    FROM pembayaran py
    JOIN pesanan p ON py.id_pesanan = p.id_pesanan
    WHERE py.status = 'Lunas' 
      AND py.tanggal_pembayaran >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY DATE(py.tanggal_pembayaran)
");
$stmtRevPast->execute();
while ($row = $stmtRevPast->fetch(PDO::FETCH_ASSOC)) {
    $date = $row['tanggal'];
    if (isset($last7Days[$date])) {
        $last7Days[$date]['revenue'] = (float)$row['total'];
    }
}

$stmtOrderPast = $pdo->prepare("
    SELECT DATE(py.tanggal_pembayaran) AS tanggal, COUNT(*) AS total
    FROM pesanan p
    JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
    WHERE py.status = 'Lunas'
      AND py.tanggal_pembayaran >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY DATE(py.tanggal_pembayaran)
");
$stmtOrderPast->execute();
while ($row = $stmtOrderPast->fetch(PDO::FETCH_ASSOC)) {
    $date = $row['tanggal'];
    if (isset($last7Days[$date])) {
        $last7Days[$date]['orders'] = (int)$row['total'];
    }
}

$chartLabels = [];
$chartRevenue = [];
$chartOrders = [];
foreach ($last7Days as $data) {
    $chartLabels[] = $data['label'];
    $chartRevenue[] = $data['revenue'];
    $chartOrders[] = $data['orders'];
}

// Recent orders
$stmtRecent = $pdo->query("
    SELECT p.id_pesanan, p.no_meja, p.status_pesanan, p.total_harga, p.tanggal_pesanan, pl.username
    FROM pesanan p
    LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
    ORDER BY p.id_pesanan DESC
    LIMIT 5
");
$recentOrders = $stmtRecent->fetchAll();

echo json_encode([
    'revToday' => $revToday,
    'pesananToday' => $pesananToday,
    'menuAktif' => $menuAktif,
    'timAktif' => $timAktif,
    'chartLabels' => $chartLabels,
    'chartRevenue' => $chartRevenue,
    'chartOrders' => $chartOrders,
    'recentOrders' => $recentOrders,
]);
