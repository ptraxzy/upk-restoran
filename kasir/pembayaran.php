<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('kasir');

$title = 'Pembayaran';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';
$pdo = db();

// Fetch orders awaiting payment
$stmtPending = $pdo->query("
    SELECT p.id_pesanan, p.no_meja, p.total_harga, p.status_pesanan,
           u.username
    FROM pesanan p
    LEFT JOIN user u ON p.id_user = u.id_user
    WHERE p.status_pesanan = 'Menunggu Pembayaran'
    ORDER BY p.tanggal_pesanan DESC
    LIMIT 10
");
$pendingOrders = $stmtPending->fetchAll();

// Metrics
$stmtCountToday = $pdo->query("SELECT COUNT(*) FROM pembayaran WHERE DATE(tanggal_pembayaran) = CURDATE()");
$countToday = (int)$stmtCountToday->fetchColumn();

$stmtCountPending = $pdo->query("SELECT COUNT(*) FROM pesanan WHERE status_pesanan = 'Menunggu Pembayaran'");
$countPending = (int)$stmtCountPending->fetchColumn();

$stmtRevToday = $pdo->query("SELECT COALESCE(SUM(total_bayar), 0) FROM pembayaran WHERE status = 'Lunas' AND DATE(tanggal_pembayaran) = CURDATE()");
$revToday = (float)$stmtRevToday->fetchColumn();

ob_start();
?>
<section class="row row-cols-1 row-cols-lg-2 g-4">
    <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <div class="d-flex flex-column gap-2 flex-md-row align-items-md-end justify-content-md-between">
            <div>
                <h3 class="h3 mb-1 text-warning">Pesanan Menunggu Pembayaran</h3>
                <p class="text-muted small mb-4">Daftar pesanan yang belum diselesaikan pembayarannya.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-warning rounded-0 fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('kasir/pembayaran_riwayat.php'), ENT_QUOTES, 'UTF-8'); ?>">Riwayat Transaksi</a>
                <a class="btn btn-warning rounded-0 fw-medium px-4 py-2" href="<?= htmlspecialchars(base_url('kasir/pembayaran_cetak.php'), ENT_QUOTES, 'UTF-8'); ?>">Cetak Struk</a>
            </div>
        </div>

        <table class="table table-dark table-hover table-bordered border-secondary mt-4 mb-0">
            <thead>
                <tr>
                    <th>No. Order</th>
                    <th>Pelanggan</th>
                    <th>Meja</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingOrders as $order): ?>
                <tr>
                    <td class="text-gold fw-medium">#LP-<?= $order['id_pesanan']; ?></td>
                    <td><?= htmlspecialchars($order['username'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($order['no_meja'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td class="text-white"><?= rupiah((float)$order['total_harga']); ?></td>
                    <td>
                        <a href="<?= htmlspecialchars(base_url('kasir/pembayaran_cetak.php?id=' . $order['id_pesanan']), ENT_QUOTES, 'UTF-8'); ?>" class="text-gold small text-decoration-none">Bayar & Cetak</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($pendingOrders)): ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada pesanan yang menunggu pembayaran.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </article>

    <aside class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <h3 class="h3 mb-1 text-warning">Ringkasan Kas</h3>
        <div class="row row-cols-1 row-cols-md-3 g-3 mb-4 mt-4">
            <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
                <p class="text-muted small mb-2">Transaksi Hari Ini</p>
                <p class="h2 text-warning mb-0"><?= $countToday; ?></p>
            </article>
            <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
                <p class="text-muted small mb-2">Belum Dibayar</p>
                <p class="h2 text-warning mb-0"><?= $countPending; ?></p>
            </article>
            <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
                <p class="text-muted small mb-2">Pendapatan Hari Ini</p>
                <p class="h2 text-warning mb-0"><?= rupiah($revToday); ?></p>
            </article>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Service Floor',
    'title' => 'Proses Bayar',
    'description' => 'Modul kasir untuk memproses pembayaran dan mencetak struk.',
    'nav_sections' => staff_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
