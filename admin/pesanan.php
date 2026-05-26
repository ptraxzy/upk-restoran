<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('admin');

$title = 'Kelola Pesanan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

$pdo = db();

$stmt = $pdo->query('
    SELECT p.*, u.username, pb.metode AS metode_pembayaran, pb.status AS status_pembayaran
    FROM pesanan p
    LEFT JOIN user u ON p.id_user = u.id_user
    LEFT JOIN pembayaran pb ON p.id_pesanan = pb.id_pesanan
    ORDER BY p.tanggal_pesanan DESC
');
$orders = $stmt->fetchAll();

$search = trim($_GET['search'] ?? '');
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 5;

$filteredOrders = [];
foreach ($orders as $order) {
    if ($search === '') {
        $filteredOrders[] = $order;
    } else {
        $s = strtolower($search);
        $idStr = strtolower((string)$order['id_pesanan']);
        $userStr = strtolower((string)($order['username'] ?? 'Guest'));
        if (strpos($idStr, $s) !== false || strpos($userStr, $s) !== false) {
            $filteredOrders[] = $order;
        }
    }
}

$totalRows = count($filteredOrders);
$totalPages = ceil($totalRows / $limit);
$paginatedOrders = array_slice($filteredOrders, ($page - 1) * $limit, $limit);

ob_start();
?>
<section class="row g-5 animate-fade-in-up">
    <div class="col-12">
        <article class="section-panel h-100">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom border-soft pb-4 mb-4 gap-3">
                <div>
                    <h3 class="font-display text-white m-0" style="font-size: 24px;">Semua Pesanan Restoran</h3>
                    <p class="text-secondary small mb-0 mt-1">Pantau dan kelola seluruh transaksi serta status pesanan aktif.</p>
                </div>
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control bg-black text-white border-secondary rounded-0" placeholder="Cari No. Order / Pelanggan..." value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" style="min-width: 250px;">
                    <button type="submit" class="btn btn-warning rounded-0 px-4">Cari</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No. Order</th>
                            <th>Pelanggan</th>
                            <th>Meja</th>
                            <th>Waktu Order</th>
                            <th>Total Harga</th>
                            <th>Metode</th>
                            <th>Status Bayar</th>
                            <th>Status Pesanan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paginatedOrders as $order): ?>
                        <?php
                        $statusClass = 'badge ';
                        $status = $order['status_pesanan'];
                        if ($status === 'Menunggu Pembayaran') {
                            $statusClass .= 'bg-danger text-white';
                        } elseif ($status === 'Diproses' || $status === 'Sedang Disiapkan') {
                            $statusClass .= 'bg-warning text-dark';
                        } elseif ($status === 'Siap Saji') {
                            $statusClass .= 'bg-info text-dark';
                        } elseif ($status === 'Selesai') {
                            $statusClass .= 'bg-success text-white';
                        } else {
                            $statusClass .= 'bg-secondary text-white';
                        }

                        // Payment status class
                        $payStatusClass = 'badge ';
                        $payStatus = $order['status_pembayaran'] ?? 'Menunggu';
                        if ($payStatus === 'Lunas') {
                            $payStatusClass .= 'bg-success text-white';
                        } else {
                            $payStatusClass .= 'bg-danger text-white';
                        }
                        ?>
                        <tr>
                            <td class="fw-medium text-gold">#LP-<?= $order['id_pesanan']; ?></td>
                            <td class="text-white"><?= htmlspecialchars((string)($order['username'] ?? 'Guest'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-secondary">Meja <?= htmlspecialchars((string)$order['no_meja'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-secondary" style="font-size: 12px;"><?= date('d M Y, H:i', strtotime($order['tanggal_pesanan'])); ?></td>
                            <td class="text-white fw-medium"><?= rupiah((float)$order['total_harga']); ?></td>
                            <td class="text-secondary" style="font-size: 12px;"><?= htmlspecialchars((string)($order['metode_pembayaran'] ?? 'QRIS')); ?></td>
                            <td><span class="<?= $payStatusClass; ?>"><?= $payStatus === 'Lunas' ? 'Lunas' : 'Belum Bayar'; ?></span></td>
                            <td><span class="<?= $statusClass; ?>"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td class="text-end">
                                <?php if ($payStatus !== 'Lunas' && ($order['metode_pembayaran'] ?? 'QRIS') === 'Tunai'): ?>
                                    <a href="<?= base_url('actions/pesanan/confirm_cash.php?id_pesanan=' . $order['id_pesanan']); ?>" class="btn btn-sm btn-warning rounded-0 fw-semibold" style="font-size: 10px; padding: 4px 8px;">Konfirmasi Cash</a>
                                <?php elseif ($payStatus !== 'Lunas' && ($order['metode_pembayaran'] ?? 'QRIS') === 'QRIS'): ?>
                                    <a href="<?= base_url('actions/pesanan/simulate_pay.php?id_pesanan=' . $order['id_pesanan']); ?>" class="btn btn-sm btn-outline-success rounded-0 fw-semibold" style="font-size: 10px; padding: 4px 8px; border-color: #28a745 !important; color: #28a745 !important;">Simulasi QRIS</a>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($paginatedOrders)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">Belum ada pesanan terdaftar.</td>
                            </tr>
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
    'brand' => 'Lumière',
    'badge' => 'Administration',
    'title' => 'Manajemen Pesanan',
    'description' => 'Kelola dan pantau seluruh alur pesanan dari satu dasbor terpusat.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
