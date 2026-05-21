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

$countAdmin = (int) $pdo->query("SELECT COUNT(*) FROM user WHERE level = 'admin'")->fetchColumn();
$countKasir = (int) $pdo->query("SELECT COUNT(*) FROM user WHERE level = 'kasir'")->fetchColumn();
$timAktif = $countAdmin + $countKasir;

// Recent orders
$stmtRecent = $pdo->query("
    SELECT p.id_pesanan, p.no_meja, p.status_pesanan, p.total_harga, p.tanggal_pesanan, pl.username
    FROM pesanan p
    LEFT JOIN user pl ON p.id_user = pl.id_user
    ORDER BY p.tanggal_pesanan DESC
    LIMIT 5
");
$recentOrders = $stmtRecent->fetchAll();

ob_start();
?>
<section class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-5 animate-fade-in-up">
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

<section class="row g-5 animate-fade-in-up" style="animation-delay: 0.2s;">
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
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administrasi',
    'title' => 'Ringkasan Aktivitas',
    'description' => 'Pusat pantauan menu, pesanan, dan tim operasional restoran.',
    'nav_sections' => admin_nav_sections(),
], $content);

require __DIR__ . '/../includes/footer.php';
