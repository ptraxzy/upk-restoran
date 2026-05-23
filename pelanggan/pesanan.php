<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('pelanggan');

$title = 'Pesanan Saya';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

$userId = $_SESSION['id_user'] ?? 0;
$pdo = db();

// Fetch all customer orders with menu names aggregated
$stmt = $pdo->prepare('
    SELECT p.*, GROUP_CONCAT(CONCAT(m.nama_menu, " x", dp.jumlah) SEPARATOR ", ") AS items_summary
    FROM pesanan p
    LEFT JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
    LEFT JOIN menu m ON dp.id_menu = m.id_menu
    WHERE p.id_user = ?
    GROUP BY p.id_pesanan
    ORDER BY p.tanggal_pesanan DESC
');
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();

$activeOrders = [];
$historyOrders = [];
$totalSpent = 0.0;
$totalOrdersCount = 0;

foreach ($orders as $order) {
    if (in_array($order['status_pesanan'], ['Selesai', 'Dibatalkan'])) {
        $historyOrders[] = $order;
        if ($order['status_pesanan'] === 'Selesai') {
            $totalSpent += (float)$order['total_harga'];
            $totalOrdersCount++;
        }
    } else {
        $activeOrders[] = $order;
    }
}

ob_start();
?>
<section class="row g-5">
    <div class="col-lg-8 animate-fade-in-up">
        <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
            <div class="d-flex flex-column gap-2 flex-md-row align-items-md-end justify-content-md-between border-bottom border-soft pb-4 mb-4">
                <div>
                    <h2 class="font-display text-warning mb-1" style="font-size: 24px;">Jalur Perjalanan Hidangan</h2>
                    <p class="text-secondary small mb-0">Pantau pesanan aktif Anda dan lihat riwayat kuliner Anda di Lumière.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-outline-warning rounded-0 fw-medium px-3 py-2 text-white" style="font-size: 12px;" href="<?= htmlspecialchars(base_url('pelanggan/pesanan_status.php'), ENT_QUOTES, 'UTF-8'); ?>">Lacak Status</a>
                    <a class="btn btn-outline-warning rounded-0 fw-medium px-3 py-2 text-white" style="font-size: 12px;" href="<?= htmlspecialchars(base_url('pelanggan/pesanan_riwayat.php'), ENT_QUOTES, 'UTF-8'); ?>">Lihat Riwayat</a>
                </div>
            </div>

            <!-- Active Orders -->
            <h3 class="h5 text-white mb-3 mt-4" style="font-size: 12px;">Pesanan Aktif</h3>
            <div class="compact-list mb-4">
                <?php foreach ($activeOrders as $order): ?>
                    <div class="compact-list-item d-flex justify-content-between align-items-center border-bottom border-soft py-3">
                        <div>
                            <p class="fw-medium text-light mb-1">#LP-<?= $order['id_pesanan']; ?> • <?= htmlspecialchars((string)($order['items_summary'] ?: 'Menu Hidangan'), ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="small text-secondary mb-0">Order aktif • Meja <?= htmlspecialchars((string)$order['no_meja'], ENT_QUOTES, 'UTF-8'); ?> • <?= date('d M Y, H:i', strtotime($order['tanggal_pesanan'])); ?></p>
                        </div>
                        <span class="badge bg-warning text-dark"><?= htmlspecialchars($order['status_pesanan'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($activeOrders)): ?>
                    <p class="text-secondary py-3 small">Tidak ada pesanan aktif saat ini.</p>
                <?php endif; ?>
            </div>

            <!-- Last Orders -->
            <h3 class="h5 text-white mb-3 mt-4" style="font-size: 12px;">Pesanan Terakhir</h3>
            <div class="compact-list">
                <?php foreach (array_slice($historyOrders, 0, 3) as $order): ?>
                    <div class="compact-list-item d-flex justify-content-between align-items-center border-bottom border-soft py-3">
                        <div>
                            <p class="fw-medium text-secondary mb-1">#LP-<?= $order['id_pesanan']; ?> • <?= htmlspecialchars((string)($order['items_summary'] ?: 'Menu Hidangan'), ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="small text-muted mb-0"><?= date('d M Y', strtotime($order['tanggal_pesanan'])); ?> • <?= rupiah((float)$order['total_harga']); ?></p>
                        </div>
                        <span class="badge <?= $order['status_pesanan'] === 'Selesai' ? 'bg-secondary text-white' : 'bg-danger text-white'; ?>"><?= htmlspecialchars($order['status_pesanan'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($historyOrders)): ?>
                    <p class="text-secondary py-3 small">Belum ada riwayat pesanan.</p>
                <?php endif; ?>
            </div>
        </article>
    </div>

    <aside class="col-lg-4 animate-fade-in-up" style="animation-delay: 0.2s;">
        <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
            <h3 class="h3 mb-4 text-warning font-display" style="font-size: 24px;">Status Pesanan</h3>
            <div class="row g-3">
                <article class="p-3 border border-soft bg-black d-flex flex-column h-100 w-100 mb-2">
                    <p class="text-secondary small mb-2">Aktif Sekarang</p>
                    <p class="h2 text-gold font-display mb-0" style="font-size: 32px;"><?= str_pad((string)count($activeOrders), 2, '0', STR_PAD_LEFT); ?></p>
                </article>
                <article class="p-3 border border-soft bg-black d-flex flex-column h-100 w-100 mb-2">
                    <p class="text-secondary small mb-2">Total Kunjungan</p>
                    <p class="h2 text-gold font-display mb-0" style="font-size: 32px;"><?= str_pad((string)$totalOrdersCount, 2, '0', STR_PAD_LEFT); ?></p>
                </article>
                <article class="p-3 border border-soft bg-black d-flex flex-column h-100 w-100">
                    <p class="text-secondary small mb-2">Total Belanja</p>
                    <p class="h2 text-gold font-display mb-0" style="font-size: 32px;"><?= rupiah($totalSpent); ?></p>
                </article>
            </div>
        </article>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Art Culinaire",
    'eyebrow' => 'Order Journey',
    'title' => 'Riwayat Sajian Anda',
    'description' => 'Status aktif, riwayat kunjungan, dan pesanan kuliner Anda disusun secara ringkas dan presisi.',
], $content);
require __DIR__ . '/../includes/footer.php';
