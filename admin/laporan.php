<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Laporan Penjualan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';

// Total Revenue & Transactions
$stmtStats = db()->query("
    SELECT
        COALESCE(SUM(total_bayar), 0) as total_revenue,
        COUNT(*) as total_count
    FROM pembayaran
    WHERE status = 'Lunas'
");
$stats = $stmtStats->fetch();

// Best Selling
$stmtBest = db()->query("
    SELECT m.nama_menu, m.gambar, SUM(dp.jumlah) as total_porsi, SUM(dp.jumlah * dp.harga_satuan) as total_revenue
    FROM detail_pesanan dp
    JOIN menu m ON dp.id_menu = m.id_menu
    JOIN pesanan p ON dp.id_pesanan = p.id_pesanan
    JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
    WHERE py.status = 'Lunas'
    GROUP BY m.id_menu
    ORDER BY total_porsi DESC
    LIMIT 3
");
$bestSelling = $stmtBest->fetchAll();

// Recent Transactions
$stmtRecent = db()->query("
    SELECT p.*, py.total_bayar, py.status as payment_status
    FROM pesanan p
    LEFT JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
    ORDER BY p.tanggal_pesanan DESC
    LIMIT 5
");
$recentTransactions = $stmtRecent->fetchAll();

ob_start();
?>
<section class="row g-5">
    <div class="col-lg-4 d-flex flex-column gap-4">
        <article class="card h-100">
            <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">Total Pendapatan</p>
            <p class="h2 font-display mb-0">Rp <?= number_format((float)$stats['total_revenue'], 0, ',', '.'); ?></p>
            <p class="metric-note">Akumulasi seluruh transaksi lunas.</p>
        </article>
        <article class="card h-100">
            <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">Total Transaksi</p>
            <p class="h2 font-display mb-0"><?= number_format((float)$stats['total_count'], 0, ',', '.'); ?></p>
            <p class="metric-note">Jumlah pesanan yang telah diselesaikan.</p>
        </article>
    </div>

    <div class="col-lg-8">
        <article class="section-panel h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="font-display text-white m-0" style="font-size: 24px;">Tren Penjualan</h3>
                <button class="btn btn-outline-warning py-2 px-3" style="font-size: 9px;" onclick="window.print()">Cetak Laporan</button>
            </div>
            <div class="w-100 d-flex align-items-end" style="height: 240px; position: relative; padding-bottom: 30px;">
                <svg viewBox="0 0 100 40" class="w-100 h-100" preserveAspectRatio="none">
                    <path d="M0 35 Q 20 5, 40 25 T 80 15 L 100 10" fill="none" stroke="#C9A84C" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <div class="d-flex justify-content-between w-100 position-absolute bottom-0 text-muted" style="font-size: 10px;">
                    <span>SEN</span><span>SEL</span><span>RAB</span><span>KAM</span><span>JUM</span><span>SAB</span><span>MIN</span>
                </div>
            </div>
        </article>
    </div>
</section>

<section class="row g-5 mt-2">
    <div class="col-lg-5">
        <article class="section-panel">
            <h3 class="font-display text-white mb-4" style="font-size: 22px;">Menu Terlaris</h3>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($bestSelling as $item): ?>
                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-soft">
                    <div class="d-flex align-items-center gap-3">
                        <?php if ($item['gambar']): ?>
                            <img src="<?= htmlspecialchars($item['gambar']); ?>" alt="<?= htmlspecialchars($item['nama_menu']); ?>" style="width: 44px; height: 44px; object-fit: cover; border: 1px solid var(--border);">
                        <?php else: ?>
                            <div class="bg-black border border-soft d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                <span class="text-muted" style="font-size: 9px;">N/A</span>
                            </div>
                        <?php endif; ?>
                        <div>
                            <p class="text-white m-0" style="font-size: 13px;"><?= htmlspecialchars($item['nama_menu']); ?></p>
                            <p class="text-muted small m-0 text-uppercase" style="font-size: 9px;"><?= $item['total_porsi']; ?> PORSI</p>
                        </div>
                    </div>
                    <span class="text-gold small fw-medium">Rp <?= number_format((float)$item['total_revenue'], 0, ',', '.'); ?></span>
                </div>
                <?php endforeach; ?>
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
                            <th>Meja</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentTransactions as $trx): ?>
                        <tr>
                            <td class="fw-medium text-white">#<?= str_pad((string)$trx['id_pesanan'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td>Meja <?= htmlspecialchars($trx['no_meja']); ?></td>
                            <td class="text-white">Rp <?= number_format((float)$trx['total_harga'], 0, ',', '.'); ?></td>
                            <td>
                                <span class="text-<?= $trx['status_pesanan'] === 'Selesai' ? 'success' : 'warning'; ?> small text-uppercase fw-bold" style="font-size: 9px;">
                                    <?= htmlspecialchars($trx['status_pesanan']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'brand' => 'NOCTRA',
    'badge' => 'Administration',
    'title' => 'Laporan Penjualan',
    'description' => 'Ringkasan performa dan pendapatan operasional.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
