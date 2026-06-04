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
    SELECT py.id_pembayaran, py.total_bayar, py.metode, py.status, py.trx_id, py.tanggal_pembayaran, 'kasir' AS kasir_role,
           p.id_pesanan, p.no_meja,
           u.username AS nama_pelanggan,
           COALESCE(uc.nama_karyawan, uc.username) AS nama_kasir
    FROM pembayaran py
    JOIN pesanan p ON py.id_pesanan = p.id_pesanan
    LEFT JOIN pelanggan u ON p.id_pelanggan = u.id_pelanggan
    LEFT JOIN karyawan uc ON py.id_karyawan = uc.id_karyawan
    ORDER BY py.tanggal_pembayaran DESC
");
$riwayat = $stmt->fetchAll();

$search = trim($_GET['search'] ?? '');
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 5;

$filteredRiwayat = [];
foreach ($riwayat as $trx) {
    if ($search === '') {
        $filteredRiwayat[] = $trx;
    } else {
        $s = strtolower($search);
        $uStr = strtolower((string)($trx['nama_pelanggan'] ?? 'Guest'));
        $idStr = strtolower((string)($trx['trx_id'] ?? $trx['id_pesanan']));
        if (strpos($uStr, $s) !== false || strpos($idStr, $s) !== false) {
            $filteredRiwayat[] = $trx;
        }
    }
}

$totalRows = count($filteredRiwayat);
$totalPages = ceil($totalRows / $limit);
$paginatedRiwayat = array_slice($filteredRiwayat, ($page - 1) * $limit, $limit);

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
<section class="row g-4">
    <div class="col-lg-8">
        <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0 h-100">
            <div class="d-flex flex-column gap-3 mb-4">
                <div>
                    <a class="text-gold text-decoration-none small d-inline-flex align-items-center gap-2 mb-3 hover-gold" href="<?= htmlspecialchars(base_url('kasir/pembayaran.php'), ENT_QUOTES, 'UTF-8'); ?>" style="font-size: 12px; letter-spacing: 0.04em;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                        KEMBALI KE PEMBAYARAN
                    </a>
                </div>
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom border-soft pb-4 gap-3">
                    <div>
                        <h3 class="h3 mb-1 text-warning font-display">Riwayat Transaksi</h3>
                        <p class="text-secondary small mb-0">Daftar seluruh transaksi pembayaran yang telah tercatat.</p>
                    </div>
                    <form method="GET" class="d-flex gap-2 m-0">
                        <input type="text" name="search" class="form-control bg-black text-white border-secondary rounded-0" placeholder="Cari..." value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" style="max-width: 180px;">
                        <button type="submit" class="btn btn-outline-warning rounded-0">Cari</button>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-hover table-bordered border-secondary mb-0">
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
                        <?php foreach ($paginatedRiwayat as $trx): ?>
                        <tr>
                            <td class="text-gold"><?= htmlspecialchars($trx['trx_id'] ?? '#LP-' . $trx['id_pesanan'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-nowrap"><?= date('d M Y, H:i', strtotime($trx['tanggal_pembayaran'])); ?></td>
                            <td><?= htmlspecialchars($trx['nama_pelanggan'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-white text-nowrap"><?= rupiah((float)$trx['total_bayar']); ?></td>
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
                        <?php if (empty($paginatedRiwayat)): ?>
                            <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat transaksi yang ditemukan.</td></tr>
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

    <div class="col-lg-4">
        <aside class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0 h-100">
            <h3 class="h3 mb-1 text-warning">Ringkasan Kas</h3>
            <div class="d-flex flex-column gap-3 mb-4 mt-4">
                <article class="card bg-dark text-white border-secondary p-3 rounded-0">
                    <p class="text-muted small mb-2">Total Transaksi Hari Ini</p>
                    <p class="h2 text-warning mb-0"><?= $countToday; ?></p>
                </article>
                <article class="card bg-dark text-white border-secondary p-3 rounded-0">
                    <p class="text-muted small mb-2">Pendapatan Hari Ini</p>
                    <p class="h2 text-warning mb-0"><?= rupiah($revToday); ?></p>
                </article>
                <article class="card bg-dark text-white border-secondary p-3 rounded-0">
                    <p class="text-muted small mb-2">Metode Terbanyak</p>
                    <p class="h2 text-warning mb-0"><?= htmlspecialchars($topMethod['metode'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
                </article>
            </div>
        </aside>
    </div>
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
