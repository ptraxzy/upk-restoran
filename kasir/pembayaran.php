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
    SELECT p.id_pesanan, p.no_meja, p.total_harga, p.status_pesanan, p.tanggal_pesanan,
           u.username, pb.metode AS metode_pembayaran, pb.status AS status_pembayaran
    FROM pesanan p
    LEFT JOIN user u ON p.id_user = u.id_user
    LEFT JOIN pembayaran pb ON p.id_pesanan = pb.id_pesanan
    WHERE p.status_pesanan = 'Menunggu Pembayaran'
    ORDER BY p.tanggal_pesanan DESC
");
$pendingOrders = $stmtPending->fetchAll();

$search = trim($_GET['search'] ?? '');
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 5;

$filteredOrders = [];
foreach ($pendingOrders as $order) {
    if ($search === '') {
        $filteredOrders[] = $order;
    } else {
        $s = strtolower($search);
        $uStr = strtolower((string)($order['username'] ?? 'Guest'));
        $idStr = strtolower((string)$order['id_pesanan']);
        if (strpos($uStr, $s) !== false || strpos($idStr, $s) !== false) {
            $filteredOrders[] = $order;
        }
    }
}

$totalRows = count($filteredOrders);
$totalPages = ceil($totalRows / $limit);
$paginatedOrders = array_slice($filteredOrders, ($page - 1) * $limit, $limit);

// Metrics
$stmtCountToday = $pdo->query("SELECT COUNT(*) FROM pembayaran WHERE DATE(tanggal_pembayaran) = CURDATE()");
$countToday = (int)$stmtCountToday->fetchColumn();

$stmtCountPending = $pdo->query("SELECT COUNT(*) FROM pesanan WHERE status_pesanan = 'Menunggu Pembayaran'");
$countPending = (int)$stmtCountPending->fetchColumn();

$stmtRevToday = $pdo->query("SELECT COALESCE(SUM(total_bayar), 0) FROM pembayaran WHERE status = 'Lunas' AND DATE(tanggal_pembayaran) = CURDATE()");
$revToday = (float)$stmtRevToday->fetchColumn();

ob_start();
?>
<!-- Ringkasan Kas at the top -->
<section class="row row-cols-1 row-cols-md-3 g-4 mb-4 animate-fade-in-up">
    <div class="col">
        <article class="card bg-dark text-white border-secondary p-4 rounded-0 h-100" style="background-color: rgba(20,18,14,0.45) !important;">
            <p class="text-secondary small mb-2 text-uppercase fw-semibold" style="letter-spacing: 0.08em; font-size: 10px; color: var(--text-secondary);">Transaksi Hari Ini</p>
            <p class="h2 text-warning mb-0 font-display fw-bold" style="font-size: 28px;"><?= $countToday; ?></p>
        </article>
    </div>
    <div class="col">
        <article class="card bg-dark text-white border-secondary p-4 rounded-0 h-100" style="background-color: rgba(20,18,14,0.45) !important;">
            <p class="text-secondary small mb-2 text-uppercase fw-semibold" style="letter-spacing: 0.08em; font-size: 10px; color: var(--text-secondary);">Belum Dibayar</p>
            <p class="h2 text-warning mb-0 font-display fw-bold" style="font-size: 28px;"><?= $countPending; ?></p>
        </article>
    </div>
    <div class="col">
        <article class="card bg-dark text-white border-secondary p-4 rounded-0 h-100" style="background-color: rgba(20,18,14,0.45) !important;">
            <p class="text-secondary small mb-2 text-uppercase fw-semibold" style="letter-spacing: 0.08em; font-size: 10px; color: var(--text-secondary);">Pendapatan Hari Ini</p>
            <p class="h2 text-gold mb-0 font-display fw-bold" style="font-size: 28px;"><?= rupiah($revToday); ?></p>
        </article>
    </div>
</section>

<!-- Table of Pending Payments in full width -->
<section class="row animate-fade-in-up" style="animation-delay: 0.1s;">
    <div class="col-12">
        <article class="card bg-dark text-white border-secondary p-4 rounded-0">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom border-soft pb-4 mb-4 gap-3">
                <div>
                    <h3 class="h3 mb-1 text-warning font-display">Pesanan Menunggu Pembayaran</h3>
                    <p class="text-secondary small mb-0">Daftar pesanan aktif yang belum diselesaikan pembayarannya.</p>
                </div>
                <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center">
                    <form method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control bg-black text-white border-secondary rounded-0" placeholder="Cari pesanan..." value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-outline-warning rounded-0">Cari</button>
                    </form>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-outline-warning rounded-0 fw-medium px-4 py-2 text-white" style="font-size: 12px;" href="<?= htmlspecialchars(base_url('kasir/pembayaran_riwayat.php'), ENT_QUOTES, 'UTF-8'); ?>">Riwayat Transaksi</a>
                        <a class="btn btn-warning rounded-0 fw-medium px-4 py-2" style="font-size: 12px; font-weight: 600;" href="<?= htmlspecialchars(base_url('kasir/pembayaran_cetak.php'), ENT_QUOTES, 'UTF-8'); ?>">Cetak Struk</a>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mt-2 mb-0">
                    <thead>
                        <tr style="border-bottom: 2px solid rgba(255,255,255,0.08);">
                            <th>No. Order</th>
                            <th>Pelanggan</th>
                            <th>Meja</th>
                            <th>Waktu Order</th>
                            <th>Metode</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paginatedOrders as $order): ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td class="text-gold fw-medium">#LP-<?= $order['id_pesanan']; ?></td>
                            <td class="text-white"><?= htmlspecialchars($order['username'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-secondary">Meja <?= htmlspecialchars($order['no_meja'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td style="font-size: 12px;" class="text-secondary"><?= date('d M Y, H:i', strtotime($order['tanggal_pesanan'])); ?></td>
                            <td style="font-size: 12px;" class="text-secondary"><span class="badge bg-secondary text-white"><?= htmlspecialchars((string)($order['metode_pembayaran'] ?? 'QRIS')); ?></span></td>
                            <td class="text-white fw-semibold"><?= rupiah((float)$order['total_harga']); ?></td>
                            <td><span class="badge bg-danger text-white" style="font-size: 10px; padding: 4px 8px;">Belum Dibayar</span></td>
                            <td class="text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="<?= htmlspecialchars(base_url('kasir/pembayaran_cetak.php?id=' . $order['id_pesanan']), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-outline-warning rounded-0 fw-semibold" style="font-size: 10px; padding: 6px 12px;">Bayar & Cetak</a>
                                    <?php if (($order['metode_pembayaran'] ?? 'QRIS') === 'Tunai'): ?>
                                        <a href="<?= base_url('actions/pesanan/confirm_cash.php?id_pesanan=' . $order['id_pesanan']); ?>" class="btn btn-sm btn-warning rounded-0 fw-semibold text-dark" style="font-size: 10px; padding: 6px 12px;">Konfirmasi Cash</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($paginatedOrders)): ?>
                            <tr><td colspan="8" class="text-center py-5 text-muted">Tidak ada pesanan yang ditemukan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination pagination-sm justify-content-center border-0 gap-2 m-0">
                        <li class="page-item <?= $page <= 1 ? 'disabled opacity-50 pe-none' : ''; ?>">
                            <a class="page-link rounded-0 bg-black text-white border-secondary" href="?page=<?= max(1, $page - 1); ?><?= $search ? '&search=' . urlencode($search) : ''; ?>" aria-label="Previous">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" style="transform: scaleX(-1);"><path fill="currentColor" d="M5 13.5h3v-3H5zm5 0h3v-3h-3zM17 9l-1 1l2 2l-2 2l1 1l3-3z"></path></svg>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : ''; ?>">
                                <a class="page-link rounded-0 <?= $i === $page ? 'bg-warning text-dark border-warning' : 'bg-black text-white border-secondary'; ?>" href="?page=<?= $i; ?><?= $search ? '&search=' . urlencode($search) : ''; ?>"><?= $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $totalPages ? 'disabled opacity-50 pe-none' : ''; ?>">
                            <a class="page-link rounded-0 bg-black text-white border-secondary" href="?page=<?= min($totalPages, $page + 1); ?><?= $search ? '&search=' . urlencode($search) : ''; ?>" aria-label="Next">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M5 13.5h3v-3H5zm5 0h3v-3h-3zM17 9l-1 1l2 2l-2 2l1 1l3-3z"></path></svg>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </article>
    </div>
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
