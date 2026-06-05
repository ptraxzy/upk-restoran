<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Laporan Penjualan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';
$pdo = db();

// Total Revenue & Transactions (all time)
$stmtStats = $pdo->query("
    SELECT
        COALESCE(SUM(total_bayar), 0) as total_revenue,
        COUNT(*) as total_count
    FROM pembayaran
    WHERE status = 'Lunas'
");
$stats = $stmtStats->fetch();

// Revenue today
$stmtToday = $pdo->query("SELECT COALESCE(SUM(total_bayar), 0) FROM pembayaran WHERE status = 'Lunas' AND DATE(tanggal_pembayaran) = CURDATE()");
$revToday = (float)$stmtToday->fetchColumn();

// Revenue this month
$stmtMonth = $pdo->query("SELECT COALESCE(SUM(total_bayar), 0) FROM pembayaran WHERE status = 'Lunas' AND MONTH(tanggal_pembayaran) = MONTH(CURDATE()) AND YEAR(tanggal_pembayaran) = YEAR(CURDATE())");
$revMonth = (float)$stmtMonth->fetchColumn();

// Average per transaction
$avgPerTrx = $stats['total_count'] > 0 ? (float)$stats['total_revenue'] / (float)$stats['total_count'] : 0;

// Daily revenue for chart (last 14 days)
$stmtDaily = $pdo->query("
    SELECT DATE(tanggal_pembayaran) as tgl, COALESCE(SUM(total_bayar), 0) as revenue
    FROM pembayaran
    WHERE status = 'Lunas' AND tanggal_pembayaran >= DATE_SUB(CURDATE(), INTERVAL 13 DAY)
    GROUP BY DATE(tanggal_pembayaran)
    ORDER BY tgl ASC
");
$dailyRaw = $stmtDaily->fetchAll();

// Build full 14-day array (fill missing days with 0)
$dailyLabels = [];
$dailyData = [];
$dailyMap = [];
foreach ($dailyRaw as $d) {
    $dailyMap[$d['tgl']] = (float)$d['revenue'];
}
for ($i = 13; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $label = date('d M', strtotime($date));
    $dailyLabels[] = $label;
    $dailyData[] = $dailyMap[$date] ?? 0;
}

// Best Selling (top 5)
$stmtBest = $pdo->query("
    SELECT m.nama_menu, m.gambar, SUM(dp.jumlah) as total_porsi, SUM(dp.jumlah * dp.harga_satuan) as total_revenue
    FROM detail_pesanan dp
    JOIN menu m ON dp.id_menu = m.id_menu
    JOIN pesanan p ON dp.id_pesanan = p.id_pesanan
    JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
    WHERE py.status = 'Lunas'
    GROUP BY m.id_menu
    ORDER BY total_porsi DESC
    LIMIT 5
");
$bestSelling = $stmtBest->fetchAll();

// Prepare chart data for best selling donut
$bestLabels = array_map(fn($i) => $i['nama_menu'], $bestSelling);
$bestData = array_map(fn($i) => (int)$i['total_porsi'], $bestSelling);

// Orders by status
$stmtStatus = $pdo->query("
    SELECT status_pesanan, COUNT(*) as cnt
    FROM pesanan
    GROUP BY status_pesanan
");
$statusData = $stmtStatus->fetchAll();

// Recent Transactions
$stmtRecent = $pdo->query("
    SELECT p.id_pesanan, p.no_meja, p.total_harga, p.status_pesanan, p.tanggal_pesanan,
           py.total_bayar, py.status as payment_status, py.metode,
           pl.username AS pelanggan,
           COALESCE(k_pesanan.nama_karyawan, k_pesanan.username, kc.nama_karyawan, kc.username) AS kasir, 'kasir' AS kasir_role
    FROM pesanan p
    LEFT JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
    LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
    LEFT JOIN karyawan kc ON py.id_karyawan = kc.id_karyawan
    LEFT JOIN karyawan k_pesanan ON p.id_karyawan = k_pesanan.id_karyawan
    ORDER BY p.id_pesanan DESC
    LIMIT 5
");
$recentTransactions = $stmtRecent->fetchAll();

ob_start();
?>
<?php // Rendering kartu metrik performa utama ?>
<section class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-5 animate-fade-in-up">
    <div class="col">
        <article class="card h-100 p-4">
            <p class="text-secondary small mb-2">Total Pendapatan</p>
            <p class="h2 font-display text-gold mb-0"><?= rupiah((float)$stats['total_revenue']); ?></p>
            <p class="metric-note">Akumulasi seluruh transaksi lunas.</p>
        </article>
    </div>
    <div class="col">
        <article class="card h-100 p-4">
            <p class="text-secondary small mb-2">Pendapatan Hari Ini</p>
            <p class="h2 font-display text-white mb-0"><?= rupiah($revToday); ?></p>
            <p class="metric-note">Transaksi lunas pada hari ini.</p>
        </article>
    </div>
    <div class="col">
        <article class="card h-100 p-4">
            <p class="text-secondary small mb-2">Pendapatan Bulan Ini</p>
            <p class="h2 font-display text-white mb-0"><?= rupiah($revMonth); ?></p>
            <p class="metric-note">Akumulasi bulan berjalan.</p>
        </article>
    </div>
    <div class="col">
        <article class="card h-100 p-4">
            <p class="text-secondary small mb-2">Rata-Rata / Transaksi</p>
            <p class="h2 font-display text-white mb-0"><?= rupiah($avgPerTrx); ?></p>
            <p class="metric-note">Dari <?= number_format((float)$stats['total_count'], 0, ',', '.'); ?> transaksi lunas.</p>
        </article>
    </div>
</section>

<?php // Layout visualisasi grafik tren pendapatan dan menu terlaris ?>
<section class="row g-5 mb-5 animate-fade-in-up" style="animation-delay: 0.15s;">
    <div class="col-lg-8">
        <article class="section-panel h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="font-display text-white m-0" style="font-size: 24px;">Tren Pendapatan</h3>
                    <p class="text-secondary small mb-0 mt-1">14 hari terakhir</p>
                </div>
                <button class="btn btn-outline-warning py-2 px-3" style="font-size: 12px;" onclick="window.print()">Cetak Laporan</button>
            </div>
            <style>
                .rev-chart-wrapper {
                    height: 280px;
                    position: relative;
                    width: 100%;
                }
                .best-chart-wrapper {
                    height: 220px;
                    position: relative;
                    width: 100%;
                }
                @media (max-width: 575.98px) {
                    .rev-chart-wrapper {
                        height: 200px;
                    }
                    .best-chart-wrapper {
                        height: 180px;
                    }
                }
            </style>
            <div class="rev-chart-wrapper">
                <canvas id="revenueChart"></canvas>
            </div>
        </article>
    </div>
    <div class="col-lg-4">
        <article class="section-panel h-100">
            <h3 class="font-display text-white mb-4" style="font-size: 24px;">Menu Terlaris</h3>
            <div class="best-chart-wrapper">
                <canvas id="bestSellingChart"></canvas>
            </div>
            <div class="mt-4 d-flex flex-column gap-2">
                <?php
                $donutColors = ['#C9A84C', '#E8D48B', '#8B7635', '#A89253', '#D4BE6A'];
                foreach ($bestSelling as $idx => $item):
                ?>
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: <?= $donutColors[$idx] ?? '#666'; ?>;"></span>
                        <span class="text-white" style="font-size: 12px;"><?= htmlspecialchars($item['nama_menu']); ?></span>
                    </div>
                    <span class="text-gold" style="font-size: 11px;"><?= $item['total_porsi']; ?> porsi</span>
                </div>
                <?php endforeach; ?>
                <?php if (empty($bestSelling)): ?>
                    <p class="text-muted small text-center">Belum ada data.</p>
                <?php endif; ?>
            </div>
        </article>
    </div>
</section>

<?php // Detail performa item spesifik dan rekapan transaksi terkini ?>
<section class="row g-5 animate-fade-in-up" style="animation-delay: 0.3s;">
    <div class="col-lg-5">
        <article class="section-panel">
            <h3 class="font-display text-white mb-4" style="font-size: 22px;">Detail Menu Terlaris</h3>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($bestSelling as $rank => $item): ?>
                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-soft">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border: 1px solid var(--border); background: rgba(201,168,76,0.08);">
                            <span class="text-gold fw-bold" style="font-size: 14px;"><?= $rank + 1; ?></span>
                        </div>
                        <?php if ($item['gambar']): ?>
                            <img src="<?= htmlspecialchars(menu_image($item['gambar'])); ?>" alt="<?= htmlspecialchars($item['nama_menu']); ?>" style="width: 44px; height: 44px; object-fit: cover; border: 1px solid var(--border);">
                        <?php else: ?>
                            <div class="bg-black border border-soft d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                <span class="text-muted" style="font-size: 12px;">N/A</span>
                            </div>
                        <?php endif; ?>
                        <div>
                            <p class="text-white m-0" style="font-size: 13px;"><?= htmlspecialchars($item['nama_menu']); ?></p>
                            <p class="text-muted small m-0" style="font-size: 12px;"><?= $item['total_porsi']; ?> PORSI TERJUAL</p>
                        </div>
                    </div>
                    <span class="text-gold small fw-medium"><?= rupiah((float)$item['total_revenue']); ?></span>
                </div>
                <?php endforeach; ?>
                <?php if (empty($bestSelling)): ?>
                    <p class="text-muted small text-center py-4">Belum ada data penjualan.</p>
                <?php endif; ?>
            </div>
        </article>
    </div>

    <div class="col-lg-7">
        <article class="section-panel">
            <h3 class="font-display text-white mb-4" style="font-size: 22px;">Transaksi Terbaru</h3>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Pelanggan</th>
                            <th>Meja</th>
                            <th>Waktu</th>
                            <th>Total</th>
                            <th>Metode</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentTransactions as $trx): ?>
                        <?php
                        $statusClass = match($trx['status_pesanan']) {
                            'Selesai' => 'bg-success text-white',
                            'Menunggu Pembayaran' => 'bg-danger text-white',
                            'Diproses', 'Sedang Disiapkan' => 'bg-warning text-dark',
                            'Siap Saji' => 'bg-info text-dark',
                            default => 'bg-secondary text-white',
                        };
                        ?>
                        <tr>
                            <td class="fw-medium text-gold">#LP-<?= $trx['id_pesanan']; ?></td>
                            <td class="text-white">
                                <?= htmlspecialchars((string)($trx['pelanggan'] ?? 'Guest'), ENT_QUOTES, 'UTF-8'); ?>
                                <?php
                                $kasirText = null;
                                $roleText = 'kasir';
                                if (!empty($trx['kasir'])) {
                                    $kasirText = $trx['kasir'];
                                    $roleText = $trx['kasir_role'];
                                } elseif (($trx['payment_status'] ?? '') === 'Lunas') {
                                    if (($trx['metode'] ?? '') === 'Tunai') {
                                        $kasirText = 'Admin';
                                        $roleText = 'admin';
                                    } elseif (($trx['metode'] ?? '') === 'QRIS') {
                                        $kasirText = 'Sistem (QRIS)';
                                        $roleText = 'system';
                                    }
                                }
                                if ($kasirText):
                                ?>
                                    <span class="text-secondary small d-block" style="font-size: 10px; margin-top: 2px;">
                                        Oleh: <?= htmlspecialchars($kasirText); ?> <span class="text-gold" style="font-size: 9px; text-transform: uppercase;">(<?= htmlspecialchars($roleText); ?>)</span>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-secondary"><?= htmlspecialchars((string)$trx['no_meja']); ?></td>
                            <td class="text-secondary" style="font-size: 11px;"><?= date('d M Y, H:i', strtotime($trx['tanggal_pesanan'])); ?></td>
                            <td class="text-white fw-medium"><?= rupiah((float)$trx['total_harga']); ?></td>
                            <td class="text-secondary" style="font-size: 11px;"><?= htmlspecialchars((string)($trx['metode'] ?? '-')); ?></td>
                            <td><span class="badge <?= $statusClass; ?>" style="font-size: 12px;"><?= htmlspecialchars($trx['status_pesanan']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentTransactions)): ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada transaksi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</section>

<?php // Injeksi pustaka eksternal Chart.js untuk perenderan grafik ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gold color palette
    const gold = '#C9A84C';
    const goldLight = 'rgba(201, 168, 76, 0.15)';
    const goldBorder = 'rgba(201, 168, 76, 0.2)';
    const textMuted = '#888';
    const isMobile = window.innerWidth < 576;

    Chart.defaults.color = textMuted;
    Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';
    Chart.defaults.font.family = "'DM Sans', sans-serif";

    // ─── Custom Plugins for Ultra-Premium Look ───

    // 1. Vertical line tracking cursor on hover (Line Chart)
    const hoverLinePlugin = {
        id: 'hoverLine',
        afterDraw: function(chart) {
            if (chart.tooltip?._active && chart.tooltip._active.length) {
                const activePoint = chart.tooltip._active[0];
                const ctx = chart.ctx;
                const x = activePoint.element.x;
                const topY = chart.chartArea.top;
                const bottomY = chart.chartArea.bottom;
                
                ctx.save();
                ctx.beginPath();
                ctx.moveTo(x, topY);
                ctx.lineTo(x, bottomY);
                ctx.lineWidth = 1;
                ctx.strokeStyle = 'rgba(201, 168, 76, 0.25)';
                ctx.setLineDash([4, 4]);
                ctx.stroke();
                ctx.restore();
            }
        }
    };

    // 2. Dynamic metrics inside cutout center (Doughnut Chart)
    const centerTextPlugin = {
        id: 'centerText',
        beforeDraw: function(chart) {
            if (chart.config.type !== 'doughnut') return;
            const { width, height, ctx } = chart;
            ctx.save();
            
            const chartArea = chart.chartArea;
            const centerX = chart.chartArea ? (chartArea.left + chartArea.right) / 2 : width / 2;
            const centerY = chart.chartArea ? (chartArea.top + chartArea.bottom) / 2 : height / 2;
            
            // Fetch hovered segment or show total
            const activeElements = chart.getActiveElements();
            let labelText = "Total Terjual";
            let valueText = "";
            
            if (activeElements && activeElements.length > 0) {
                const index = activeElements[0].index;
                const value = chart.data.datasets[0].data[index];
                const rawLabel = chart.data.labels[index];
                labelText = rawLabel.length > 14 ? rawLabel.substring(0, 11) + '...' : rawLabel;
                valueText = value.toString() + ' pcs';
            } else {
                const total = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                valueText = total.toString() + ' pcs';
            }
            
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            
            // Draw Sub-label (gray text)
            ctx.font = isMobile ? "600 8px 'DM Sans', sans-serif" : "600 9px 'DM Sans', sans-serif";
            ctx.fillStyle = '#888888';
            ctx.fillText(labelText.toUpperCase(), centerX, centerY - (isMobile ? 8 : 10));
            
            // Draw Main Metric value (white bold text)
            ctx.font = isMobile ? "bold 16px 'DM Sans', sans-serif" : "bold 22px 'DM Sans', sans-serif";
            ctx.fillStyle = '#ffffff';
            ctx.fillText(valueText, centerX, centerY + (isMobile ? 10 : 12));
            
            ctx.restore();
        }
    };

    // ─── Revenue Line Chart ───
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revenueCtx.createLinearGradient(0, 0, 0, 280);
    gradient.addColorStop(0, 'rgba(201, 168, 76, 0.25)');
    gradient.addColorStop(0.5, 'rgba(201, 168, 76, 0.08)');
    gradient.addColorStop(1, 'rgba(201, 168, 76, 0)');

    new Chart(revenueCtx, {     
        type: 'line',
        plugins: [hoverLinePlugin],
        data: {
            labels: <?= json_encode($dailyLabels); ?>,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: <?= json_encode($dailyData); ?>,
                borderColor: gold,
                backgroundColor: gradient,
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: gold,
                pointBorderColor: '#0a0a0a',
                pointBorderWidth: 2,
                pointRadius: 0, // Clean line without nodes by default
                pointHoverRadius: 6, // Node pops up beautifully on hover
                pointHoverBackgroundColor: gold,
                pointHoverBorderColor: '#ffffff',
                pointHoverBorderWidth: 2
            }]
        },
        options: {
            animation: {
                duration: 1200,
                easing: 'easeOutQuart',
                delay: (context) => {
                    let delay = 0;
                    if (context.type === 'data' && context.mode === 'default') {
                        delay = context.dataIndex * 80;
                    }
                    return delay;
                }
            },
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(10, 10, 10, 0.95)',
                    borderColor: 'rgba(201, 168, 76, 0.2)',
                    borderWidth: 1,
                    borderRadius: 8,
                    titleColor: gold,
                    titleFont: { family: "'DM Sans', sans-serif", weight: 'bold', size: 12 },
                    bodyFont: { family: "'DM Sans', sans-serif", size: 13 },
                    bodyColor: '#fff',
                    padding: 12,
                    usePointStyle: true,
                    boxPadding: 6,
                    callbacks: {
                        label: function(ctx) {
                            return ' Pendapatan: Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { 
                        font: { size: isMobile ? 8 : 10, weight: '500' }, 
                        color: '#888',
                        maxTicksLimit: isMobile ? 7 : 14
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(255,255,255,0.05)',
                        tickLength: 0
                    },
                    border: {
                        dash: [4, 4],
                        display: false
                    },
                    ticks: {
                        color: '#888',
                        font: { size: isMobile ? 8 : 10, weight: '500' },
                        callback: function(v) {
                            if (v >= 1000000) return (v / 1000000).toFixed(1) + ' jt';
                            if (v >= 1000) return (v / 1000).toFixed(0) + ' rb';
                            return v;
                        }
                    }
                }
            }
        }
    });

    // ─── Best Selling Doughnut Chart ───
    const bestCtx = document.getElementById('bestSellingChart').getContext('2d');
    new Chart(bestCtx, {
        type: 'doughnut',
        plugins: [centerTextPlugin],
        data: {
            labels: <?= json_encode($bestLabels); ?>,
            datasets: [{
                data: <?= json_encode($bestData); ?>,
                backgroundColor: ['#C9A84C', '#E8D48B', '#8B7635', '#A89253', '#D4BE6A'],
                borderColor: '#0f0f0f',
                borderWidth: 3,
                hoverBorderColor: gold,
                hoverOffset: 6,
                borderRadius: 6, // Smooth rounded edges on doughnut segments
                spacing: 4       // Subtle gaps between segments
            }]
        },
        options: {
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 1500,
                easing: 'easeOutQuart',
                delay: (context) => {
                    let delay = 0;
                    if (context.type === 'data' && context.mode === 'default') {
                        delay = context.dataIndex * 120;
                    }
                    return delay;
                }
            },
            responsive: true,
            maintainAspectRatio: false,
            cutout: '78%', // Thinner circle for modern minimalist aesthetic
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(10, 10, 10, 0.95)',
                    borderColor: 'rgba(201, 168, 76, 0.2)',
                    borderWidth: 1,
                    borderRadius: 8,
                    titleColor: gold,
                    titleFont: { family: "'DM Sans', sans-serif", weight: 'bold', size: 12 },
                    bodyFont: { family: "'DM Sans', sans-serif", size: 12 },
                    bodyColor: '#fff',
                    padding: 10,
                    callbacks: {
                        label: function(ctx) {
                            return ' ' + ctx.label + ': ' + ctx.parsed + ' porsi';
                        }
                    }
                }
            }
        }
    });
});
</script>
<?php
$content = ob_get_clean();
render_internal_shell([
    'brand' => 'Lumière',
    'badge' => 'Administration',
    'title' => 'Laporan Penjualan',
    'description' => 'Ringkasan performa dan pendapatan operasional.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
