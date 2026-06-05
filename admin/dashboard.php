<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Ikhtisar';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';
$pdo = db();

// Dynamic metrics
$stmtRev = $pdo->query("SELECT COALESCE(SUM(total_bayar), 0) FROM pembayaran WHERE status = 'Lunas' AND DATE(tanggal_pembayaran) = CURDATE()");
$revToday = (float)$stmtRev->fetchColumn();

$stmtPesanan = $pdo->query("SELECT COUNT(*) FROM pesanan WHERE DATE(tanggal_pesanan) = CURDATE()");
$pesananToday = (int)$stmtPesanan->fetchColumn();

$stmtMenu = $pdo->query("SELECT COUNT(*) FROM menu WHERE status = 'Tersedia'");
$menuAktif = (int)$stmtMenu->fetchColumn();

$countAdmin = (int) $pdo->query("SELECT COUNT(*) FROM admin")->fetchColumn();
$countKasir = (int) $pdo->query("SELECT COUNT(*) FROM karyawan")->fetchColumn();
$timAktif = $countAdmin + $countKasir;

// Recent orders
$stmtRecent = $pdo->query("
    SELECT p.id_pesanan, p.no_meja, p.status_pesanan, p.total_harga, p.tanggal_pesanan, pl.username
    FROM pesanan p
    LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
    ORDER BY p.id_pesanan DESC
    LIMIT 5
");
$recentOrders = $stmtRecent->fetchAll();

// Get past 7 days of daily revenue and order volume
$last7Days = [];
$dayTranslations = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
];

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

// Query daily revenue for the past 7 days (only completed payments / 'Lunas')
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

// Query daily order counts for the past 7 days (only completed payments / 'Lunas')
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

$chartLabelsJSON = json_encode($chartLabels);
$chartRevenueJSON = json_encode($chartRevenue);
$chartOrdersJSON = json_encode($chartOrders);

ob_start();
?>
<section class="mb-5 animate-fade-in-up">
    <article class="card p-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center border-bottom border-soft pb-4 mb-4 gap-3">
            <div>
                <h4 class="font-display text-white m-0" style="font-size: 20px;">Analisis Transaksi Mingguan</h4>
                <p class="text-secondary small mb-0 mt-1">Perbandingan pemasukan harian dan volume pesanan selama 7 hari terakhir.</p>
            </div>
            <div>
                <span class="badge bg-warning text-dark px-3 py-2 fw-medium" style="font-size: 11px; border-radius: 2px;">7 HARI TERAKHIR</span>
            </div>
        </div>
        <style>
            .chart-wrapper {
                height: 320px;
                position: relative;
                width: 100%;
            }
            @media (max-width: 575.98px) {
                .chart-wrapper {
                    height: 250px;
                }
            }
        </style>
        <div class="chart-wrapper">
            <canvas id="weeklyChart"></canvas>
        </div>
    </article>
</section>

<section class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-5 animate-fade-in-up" style="animation-delay: 0.1s;">
    <div class="col">
        <article class="card h-100 p-4">
            <p class="text-secondary small mb-2">Pemasukan Hari Ini</p>
            <p class="h2 text-white font-display mb-0"><?= rupiah($revToday); ?></p>
            <p class="metric-note">Akumulasi pembayaran lunas pada hari ini.</p>
        </article>
    </div>
    <div class="col">
        <article class="card h-100 p-4">
            <p class="text-secondary small mb-2">Pesanan Hari Ini</p>
            <p class="h2 text-white font-display mb-0"><?= $pesananToday; ?></p>
            <p class="metric-note">Jumlah pesanan yang masuk hari ini.</p>
        </article>
    </div>
    <div class="col">
        <article class="card h-100 p-4">
            <p class="text-secondary small mb-2">Menu Tersedia</p>
            <p class="h2 text-white font-display mb-0"><?= $menuAktif; ?></p>
            <p class="metric-note">Hidangan yang aktif dan dapat dipesan pelanggan.</p>
        </article>
    </div>
    <div class="col">
        <article class="card h-100 p-4">
            <p class="text-secondary small mb-2">Tim Bertugas</p>
            <p class="h2 text-white font-display mb-0"><?= $timAktif; ?></p>
            <p class="metric-note">Total staf admin dan kasir yang terdaftar.</p>
        </article>
    </div>
</section>

<section class="row g-5 animate-fade-in-up" style="animation-delay: 0.25s;">
    <div class="col-lg-8">
        <article class="section-panel h-100">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom border-soft pb-4 mb-4 gap-3">
                <div>
                    <h3 class="font-display text-white m-0" style="font-size: 24px;">Pesanan Terbaru</h3>
                    <p class="text-secondary small mb-0 mt-1">Transaksi terkini yang masuk ke sistem.</p>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-warning py-2 px-3" style="font-size: 12px;" href="<?= htmlspecialchars(base_url('admin/laporan.php'), ENT_QUOTES, 'UTF-8'); ?>">Buka Laporan</a>
                    <a class="btn btn-warning py-2 px-3" style="font-size: 12px;" href="<?= htmlspecialchars(base_url('admin/menu_tambah.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah Menu</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Pelanggan</th>
                            <th>Meja</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                        <?php
                        $statusClass = match($order['status_pesanan']) {
                            'Menunggu Pembayaran' => 'bg-danger text-white',
                            'Diproses', 'Sedang Disiapkan' => 'bg-warning text-dark',
                            'Siap Saji' => 'bg-info text-dark',
                            'Selesai' => 'bg-success text-white',
                            default => 'bg-secondary text-white',
                        };
                        ?>
                        <tr>
                            <td class="fw-medium text-gold">#LP-<?= $order['id_pesanan']; ?></td>
                            <td class="text-white"><?= htmlspecialchars($order['username'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-secondary"><?= htmlspecialchars($order['no_meja'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-white fw-medium"><?= rupiah((float)$order['total_harga']); ?></td>
                            <td><span class="badge <?= $statusClass; ?>"><?= htmlspecialchars($order['status_pesanan'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentOrders)): ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada pesanan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>
    </div>

    <aside class="col-lg-4">
        <article class="section-panel h-100">
            <h3 class="font-display text-white mb-4" style="font-size: 24px;">Aksi Cepat</h3>
            <div class="d-flex flex-column gap-3">
                <a class="btn btn-warning w-100 py-3 fw-medium" style="font-size: 13px;" href="<?= htmlspecialchars(base_url('admin/menu_tambah.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah Menu Baru</a>
                <a class="btn btn-outline-warning w-100 py-3 fw-medium text-white" style="font-size: 13px;" href="<?= htmlspecialchars(base_url('admin/karyawan_tambah.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah Karyawan</a>
                <a class="btn btn-outline-warning w-100 py-3 fw-medium text-white" style="font-size: 13px;" href="<?= htmlspecialchars(base_url('admin/laporan.php'), ENT_QUOTES, 'UTF-8'); ?>">Lihat Laporan</a>
                <a class="btn btn-outline-warning w-100 py-3 fw-medium text-white" style="font-size: 13px;" href="<?= htmlspecialchars(base_url('admin/pesanan.php'), ENT_QUOTES, 'UTF-8'); ?>">Kelola Pesanan</a>
            </div>
        </article>
    </aside>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('weeklyChart').getContext('2d');
    
    // Gradient for the gold bar chart
    const goldGradient = ctx.createLinearGradient(0, 0, 0, 300);
    goldGradient.addColorStop(0, 'rgba(201, 168, 76, 0.85)');
    goldGradient.addColorStop(1, 'rgba(201, 168, 76, 0.15)');

    const isMobile = window.innerWidth < 576;
    const chartLabels = <?= $chartLabelsJSON; ?>;
    const chartRevenue = <?= $chartRevenueJSON; ?>;
    const chartOrders = <?= $chartOrdersJSON; ?>;

    const displayLabels = chartLabels.map(label => {
        if (!isMobile) return label;
        const parts = label.split(' ');
        return parts[0].substring(0, 3);
    });

    const maxRevVal = Math.max(...chartRevenue);
    const yMax = maxRevVal > 0 ? Math.ceil(maxRevVal / 500000) * 500000 : 1000000;

    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: displayLabels,
            datasets: [
                {
                    label: 'Pemasukan (Rp)',
                    data: new Array(chartLabels.length).fill(0),
                    backgroundColor: goldGradient,
                    borderColor: '#C9A84C',
                    borderWidth: 1.5,
                    yAxisID: 'y',
                    barThickness: 28,
                    maxBarThickness: 35,
                    order: 2
                },
                {
                    label: 'Tren Pemasukan',
                    data: new Array(chartLabels.length).fill(0),
                    type: 'line',
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    pointBackgroundColor: '#C9A84C',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.35,
                    fill: false,
                    yAxisID: 'y',
                    order: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 15,
                    top: 15,
                    bottom: 5
                }
            },
            animation: {
                duration: 1200,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: '#aaaaaa',
                        font: {
                            family: "'DM Sans', sans-serif",
                            size: isMobile ? 10 : 12
                        },
                        boxWidth: isMobile ? 10 : 15,
                        padding: isMobile ? 8 : 15
                    }
                },
                tooltip: {
                    backgroundColor: '#111111',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: 'rgba(201, 168, 76, 0.25)',
                    borderWidth: 1,
                    cornerRadius: 4,
                    padding: 12,
                    titleFont: {
                        family: "'DM Sans', sans-serif",
                        weight: 'bold'
                    },
                    bodyFont: {
                        family: "'DM Sans', sans-serif"
                    },
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) {
                                return 'Pemasukan: ' + new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR',
                                    minimumFractionDigits: 0
                                }).format(context.raw);
                            } else {
                                const originalOrderCount = chartOrders[context.dataIndex];
                                return 'Volume Pesanan: ' + originalOrderCount + ' pesanan';
                            }
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.03)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#aaaaaa',
                        font: {
                            family: "'DM Sans', sans-serif",
                            size: isMobile ? 9 : 11
                        }
                    }
                },
                y: {
                    type: 'linear',
                    position: 'left',
                    min: 0,
                    max: yMax,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#C9A84C',
                        font: {
                            family: "'DM Sans', sans-serif",
                            size: isMobile ? 9 : 11
                        },
                        callback: function(value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                            }
                            return 'Rp ' + value;
                        }
                    },
                    title: {
                        display: !isMobile,
                        text: 'Pemasukan (Rupiah)',
                        color: '#C9A84C',
                        font: {
                            family: "'DM Sans', sans-serif",
                            size: 12,
                            weight: 'medium'
                        }
                    }
                }
            }
        }
    });

    // Fully responsive window resize handling to update layout dynamically
    window.addEventListener('resize', () => {
        const isMobileNow = window.innerWidth < 576;
        
        // Update labels
        chart.data.labels = chartLabels.map(label => {
            if (!isMobileNow) return label;
            const parts = label.split(' ');
            return parts[0].substring(0, 3);
        });
        
        // Update scales settings
        chart.options.scales.y.title.display = !isMobileNow;
        
        // Update fonts
        chart.options.plugins.legend.labels.font.size = isMobileNow ? 10 : 12;
        chart.options.plugins.legend.labels.boxWidth = isMobileNow ? 10 : 15;
        chart.options.plugins.legend.labels.padding = isMobileNow ? 8 : 15;
        chart.options.scales.x.ticks.font.size = isMobileNow ? 9 : 11;
        chart.options.scales.y.ticks.font.size = isMobileNow ? 9 : 11;
        
        chart.update('none');
    });

    // Smooth, simultaneous growth animation on load
    setTimeout(() => {
        chart.data.datasets[0].data = chartRevenue;
        chart.data.datasets[1].data = chartRevenue;
        chart.update();
    }, 150);
});
</script>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administrasi',
    'title' => 'Ringkasan Aktivitas',
    'description' => 'Pusat pantauan menu, pesanan, dan tim operasional restoran.',
    'nav_sections' => admin_nav_sections(),
], $content);

require __DIR__ . '/../includes/footer.php';
