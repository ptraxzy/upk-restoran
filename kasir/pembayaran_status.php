<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('kasir');

$title = 'Status Pembayaran';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';
$pdo = db();

// Fetch all payments with order and user info
$stmt = $pdo->query("
    SELECT py.id_pembayaran, py.total_bayar, py.metode, py.status, py.trx_id,
           p.id_pesanan, p.no_meja,
           u.username
    FROM pembayaran py
    JOIN pesanan p ON py.id_pesanan = p.id_pesanan
    LEFT JOIN pelanggan u ON p.id_pelanggan = u.id_pelanggan
    ORDER BY py.tanggal_pembayaran DESC
");
$payments = $stmt->fetchAll();

$search = trim($_GET['search'] ?? '');
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 5;

$filteredPayments = [];
foreach ($payments as $pay) {
    if ($search === '') {
        $filteredPayments[] = $pay;
    } else {
        $s = strtolower($search);
        $uStr = strtolower((string)($pay['username'] ?? 'Guest'));
        $idStr = strtolower((string)$pay['id_pesanan']);
        if (strpos($uStr, $s) !== false || strpos($idStr, $s) !== false) {
            $filteredPayments[] = $pay;
        }
    }
}

$totalRows = count($filteredPayments);
$totalPages = ceil($totalRows / $limit);
$paginatedPayments = array_slice($filteredPayments, ($page - 1) * $limit, $limit);

$countMenunggu = count(array_filter($payments, fn($p) => $p['status'] === 'Menunggu'));
$countLunas = count(array_filter($payments, fn($p) => $p['status'] === 'Lunas'));

ob_start();
?>
<section class="row g-5">
    <div class="col-lg-8">
        <article class="section-panel h-100">
            <div class="panel-header d-flex flex-column gap-3 flex-md-row justify-content-md-between align-items-md-end mb-4">
                <div>
                    <h3 class="panel-title">Status Transaksi</h3>
                    <p class="panel-desc mb-0 mt-1">Pantau status pembayaran untuk setiap pesanan yang sedang berjalan.</p>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <form method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control bg-black text-white border-secondary rounded-0" placeholder="Cari pesanan..." value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-outline-warning rounded-0">Cari</button>
                    </form>
                    <a class="btn btn-outline-warning rounded-0 text-nowrap" href="<?= htmlspecialchars(base_url('kasir/pembayaran.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Metode</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paginatedPayments as $pay): ?>
                        <tr>
                            <td class="text-white fw-medium">#LP-<?= $pay['id_pesanan']; ?></td>
                            <td><?= htmlspecialchars($pay['username'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-gold"><?= rupiah((float)$pay['total_bayar']); ?></td>
                            <td><?= htmlspecialchars($pay['metode'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php
                                $badgeClass = match($pay['status']) {
                                    'Menunggu' => 'bg-warning text-dark',
                                    'Lunas' => 'bg-success',
                                    'Gagal' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
                                ?>
                                <span class="badge <?= $badgeClass; ?>"><?= htmlspecialchars($pay['status'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($paginatedPayments)): ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi pembayaran.</td></tr>
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

    <aside class="col-lg-4">
        <article class="section-panel h-100">
            <h3 class="panel-title mb-4">Ringkasan</h3>
            <div class="row row-cols-2 g-4">
                <div class="col">
                    <article class="card p-4 h-100">
                        <p class="text-secondary small mb-2">Menunggu Bayar</p>
                        <p class="h2 text-gold font-display mb-0"><?= $countMenunggu; ?></p>
                    </article>
                </div>
                <div class="col">
                    <article class="card p-4 h-100">
                        <p class="text-secondary small mb-2">Sudah Lunas</p>
                        <p class="h2 text-gold font-display mb-0"><?= $countLunas; ?></p>
                    </article>
                </div>
            </div>
        </article>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Service Floor',
    'title' => 'Status Pembayaran',
    'description' => 'Pantau status pembayaran secara real-time untuk setiap transaksi.',
    'nav_sections' => staff_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
