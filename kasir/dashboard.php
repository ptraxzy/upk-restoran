<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('kasir');

$title = 'Dashboard Karyawan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';
$pdo = db();

// Dynamic metrics
$stmtAktif = $pdo->query("SELECT COUNT(*) FROM pesanan WHERE status_pesanan IN ('Diproses','Sedang Disiapkan','Siap Saji','Menunggu Pembayaran')");
$countAktif = (int)$stmtAktif->fetchColumn();

$stmtSelesai = $pdo->query("SELECT COUNT(*) FROM pesanan WHERE status_pesanan = 'Selesai'");
$countSelesai = (int)$stmtSelesai->fetchColumn();

$stmtBelumBayar = $pdo->query("SELECT COUNT(*) FROM pesanan WHERE status_pesanan = 'Menunggu Pembayaran'");
$countBelumBayar = (int)$stmtBelumBayar->fetchColumn();

// Recent active orders (limit 5)
$stmtRecent = $pdo->query("
    SELECT p.id_pesanan, p.no_meja, p.status_pesanan, p.tanggal_pesanan,
           u.username
    FROM pesanan p
     LEFT JOIN pelanggan u ON p.id_pelanggan = u.id_pelanggan
     WHERE p.status_pesanan IN ('Diproses','Sedang Disiapkan','Siap Saji','Menunggu Pembayaran')
     ORDER BY p.tanggal_pesanan DESC
     LIMIT 5
");
$recentOrders = $stmtRecent->fetchAll();

// Get details for recent orders
$recentDetails = [];
if (count($recentOrders) > 0) {
    $ids = array_column($recentOrders, 'id_pesanan');
    $ph = implode(',', array_fill(0, count($ids), '?'));
    $stmtD = $pdo->prepare("SELECT dp.id_pesanan, m.nama_menu, dp.jumlah FROM detail_pesanan dp JOIN menu m ON dp.id_menu = m.id_menu WHERE dp.id_pesanan IN ($ph)");
    $stmtD->execute($ids);
    foreach ($stmtD->fetchAll() as $d) {
        $recentDetails[$d['id_pesanan']][] = $d;
    }
}

ob_start();
?>
<section class="row row-cols-1 row-cols-md-3 g-3 mb-4">
    <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
        <p class="text-muted small mb-2">Pesanan Aktif</p>
        <p class="h2 text-warning mb-0"><?= $countAktif; ?></p>
        <p class="metric-note">Jumlah pesanan yang sedang dalam proses layanan.</p>
    </article>
    <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
        <p class="text-muted small mb-2">Pesanan Selesai</p>
        <p class="h2 text-warning mb-0"><?= $countSelesai; ?></p>
        <p class="metric-note">Total pesanan yang sudah diselesaikan.</p>
    </article>
    <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
        <p class="text-muted small mb-2">Belum Dibayar</p>
        <p class="h2 text-warning mb-0"><?= $countBelumBayar; ?></p>
        <p class="metric-note">Pesanan yang menunggu proses pembayaran.</p>
    </article>
</section>

<section class="row g-4 mt-4">
    <div class="col-lg-8">
        <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0 h-100">
            <div class="d-flex flex-column gap-2 flex-md-row align-items-md-end justify-content-md-between">
                <div>
                    <h3 class="h3 mb-1 text-warning">Daftar Pesanan Masuk</h3>
                    <p class="text-muted small mb-4">Pesanan aktif terbaru dari pelanggan.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-outline-warning rounded-0 fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('kasir/pesanan.php'), ENT_QUOTES, 'UTF-8'); ?>">Lihat Semua</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover table-bordered border-secondary mt-4 mb-0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Tamu</th>
                            <th>Menu</th>
                            <th>Meja</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                        <?php
                            $menuList = [];
                            if (isset($recentDetails[$order['id_pesanan']])) {
                                foreach ($recentDetails[$order['id_pesanan']] as $d) {
                                    $menuList[] = htmlspecialchars($d['nama_menu'], ENT_QUOTES, 'UTF-8');
                                }
                            }
                            $statusClass = match($order['status_pesanan']) {
                                'Menunggu Pembayaran' => 'bg-danger text-white',
                                'Diproses', 'Sedang Disiapkan' => 'bg-warning text-dark',
                                'Siap Saji' => 'bg-info text-dark',
                                default => 'bg-secondary text-light',
                            };
                        ?>
                        <tr>
                            <td>#LP-<?= $order['id_pesanan']; ?></td>
                            <td><?= htmlspecialchars($order['username'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= implode(', ', $menuList) ?: '-'; ?></td>
                            <td><?= htmlspecialchars($order['no_meja'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="badge <?= $statusClass; ?>"><?= htmlspecialchars($order['status_pesanan'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentOrders)): ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada pesanan aktif saat ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>
    </div>

    <div class="col-lg-4">
        <aside class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0 h-100">
            <h3 class="h3 mb-1 text-warning">Aksi Cepat</h3>
            <div class="d-flex flex-column gap-3 mt-4">
                <a class="btn btn-warning rounded-0 fw-medium py-3" href="<?= htmlspecialchars(base_url('kasir/pesanan.php'), ENT_QUOTES, 'UTF-8'); ?>">Kelola Pesanan</a>
                <a class="btn btn-outline-warning rounded-0 fw-medium py-3 text-white" href="<?= htmlspecialchars(base_url('kasir/pembayaran.php'), ENT_QUOTES, 'UTF-8'); ?>">Proses Pembayaran</a>
                <a class="btn btn-outline-warning rounded-0 fw-medium py-3 text-white" href="<?= htmlspecialchars(base_url('kasir/menu.php'), ENT_QUOTES, 'UTF-8'); ?>">Lihat Menu</a>
            </div>
        </aside>
    </div>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Service Floor',
    'title' => 'Selamat datang, ' . ($_SESSION['user_name'] ?? 'Kasir') . '.',
    'description' => 'SHIFT HARI INI',
    'nav_sections' => staff_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
