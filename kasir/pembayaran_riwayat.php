<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('kasir');

$title = 'Riwayat Transaksi';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';
$pdo = db();

$stmt = $pdo->query("
    SELECT py.id_pembayaran, py.total_bayar, py.metode, py.status, py.trx_id, py.tanggal_pembayaran, uc.level AS kasir_role,
           p.id_pesanan, p.no_meja,
           u.username AS nama_pelanggan,
           uc.username AS nama_kasir
    FROM pembayaran py
    JOIN pesanan p ON py.id_pesanan = p.id_pesanan
    LEFT JOIN user u ON p.id_user = u.id_user
    LEFT JOIN user uc ON py.id_user = uc.id_user
    ORDER BY py.tanggal_pembayaran DESC
    LIMIT 30
");
$riwayat = $stmt->fetchAll();

// Metrics
$stmtCountToday = $pdo->query("SELECT COUNT(*) FROM pembayaran WHERE DATE(tanggal_pembayaran) = CURDATE()");
$countToday = (int)$stmtCountToday->fetchColumn();

$stmtRevToday = $pdo->query("SELECT COALESCE(SUM(total_bayar), 0) FROM pembayaran WHERE status = 'Lunas' AND DATE(tanggal_pembayaran) = CURDATE()");
$revToday = (float)$stmtRevToday->fetchColumn();

// Most used method
$stmtMethod = $pdo->query("SELECT metode, COUNT(*) as cnt FROM pembayaran WHERE status = 'Lunas' GROUP BY metode ORDER BY cnt DESC LIMIT 1");
$topMethod = $stmtMethod->fetch();

ob_start();
?>
<section class="row row-cols-1 row-cols-lg-2 g-4">
    <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <div class="d-flex flex-column gap-2 flex-md-row align-items-md-end justify-content-md-between">
            <div>
                <h3 class="h3 mb-1 text-warning">Riwayat Transaksi</h3>
                <p class="text-muted small mb-4">Daftar seluruh transaksi pembayaran yang telah tercatat.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-warning rounded-0 fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('kasir/pembayaran.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
            </div>
        </div>

        <table class="table table-dark table-hover table-bordered border-secondary mt-4 mb-0">
            <thead>
                <tr>
                    <th>No. Struk</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Diproses Oleh</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($riwayat as $trx): ?>
                <tr>
                    <td class="text-gold"><?= htmlspecialchars($trx['trx_id'] ?? '#LP-' . $trx['id_pesanan'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= date('d M Y, H:i', strtotime($trx['tanggal_pembayaran'])); ?></td>
                    <td><?= htmlspecialchars($trx['nama_pelanggan'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td class="text-white"><?= rupiah((float)$trx['total_bayar']); ?></td>
                    <td><?= htmlspecialchars($trx['metode'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <?= htmlspecialchars($trx['nama_kasir'] ?? 'Pelanggan (Self)', ENT_QUOTES, 'UTF-8'); ?>
                        <?php if (isset($trx['kasir_role']) && $trx['kasir_role'] !== 'pelanggan'): ?>
                            <span class="text-warning small d-block" style="font-size: 9px; text-transform: uppercase;">(<?= htmlspecialchars($trx['kasir_role'], ENT_QUOTES, 'UTF-8'); ?>)</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $badgeClass = match($trx['status']) {
                            'Lunas' => 'bg-success',
                            'Menunggu' => 'bg-warning text-dark',
                            'Gagal' => 'bg-danger',
                            default => 'bg-secondary',
                        };
                        ?>
                        <span class="badge <?= $badgeClass; ?>"><?= htmlspecialchars($trx['status'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($riwayat)): ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat transaksi.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </article>

    <aside class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <h3 class="h3 mb-1 text-warning">Ringkasan Kas</h3>
        <div class="row row-cols-1 row-cols-md-3 g-3 mb-4 mt-4">
            <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
                <p class="text-muted small mb-2">Total Transaksi Hari Ini</p>
                <p class="h2 text-warning mb-0"><?= $countToday; ?></p>
            </article>
            <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
                <p class="text-muted small mb-2">Pendapatan Hari Ini</p>
                <p class="h2 text-warning mb-0"><?= rupiah($revToday); ?></p>
            </article>
            <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
                <p class="text-muted small mb-2">Metode Terbanyak</p>
                <p class="h2 text-warning mb-0"><?= htmlspecialchars($topMethod['metode'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
            </article>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Service Floor',
    'title' => 'Riwayat Transaksi',
    'description' => 'Riwayat lengkap seluruh transaksi pembayaran yang tercatat di sistem.',
    'nav_sections' => staff_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
