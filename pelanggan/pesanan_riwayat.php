<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('pelanggan');

$title = 'Riwayat Pesanan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

$userId = $_SESSION['id_user'] ?? 0;
$pdo = db();

// Fetch customer's past orders (Selesai or Dibatalkan)
$stmt = $pdo->prepare('
    SELECT p.*, GROUP_CONCAT(CONCAT(m.nama_menu, " x", dp.jumlah) SEPARATOR ", ") AS items_summary
    FROM pesanan p
    LEFT JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
    LEFT JOIN menu m ON dp.id_menu = m.id_menu
    WHERE p.id_user = ? AND p.status_pesanan IN ("Selesai", "Dibatalkan")
    GROUP BY p.id_pesanan
    ORDER BY p.tanggal_pesanan DESC
');
$stmt->execute([$userId]);
$historyOrders = $stmt->fetchAll();

// Statistics calculation
$totalSpent = 0.0;
$totalHistoryCount = 0;
foreach ($historyOrders as $order) {
    if ($order['status_pesanan'] === 'Selesai') {
        $totalSpent += (float)$order['total_harga'];
        $totalHistoryCount++;
    }
}

// Find customer's favorite menu item
$stmtFav = $pdo->prepare('
    SELECT m.nama_menu, SUM(dp.jumlah) AS total_qty
    FROM detail_pesanan dp
    JOIN pesanan p ON dp.id_pesanan = p.id_pesanan
    JOIN menu m ON dp.id_menu = m.id_menu
    WHERE p.id_user = ? AND p.status_pesanan = "Selesai"
    GROUP BY dp.id_menu, m.nama_menu
    ORDER BY total_qty DESC
    LIMIT 1
');
$stmtFav->execute([$userId]);
$favRow = $stmtFav->fetch();
$favoriteMenu = $favRow ? $favRow['nama_menu'] : 'Belum Ada';

ob_start();
?>
<section class="row g-5">
    <div class="col-lg-8 animate-fade-in-up">
        <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
            <div class="d-flex flex-column gap-2 flex-md-row align-items-md-end justify-content-md-between border-bottom border-soft pb-4 mb-4">
                <div>
                    <h3 class="h3 mb-1 text-warning font-display" style="font-size: 24px;">Arsip Sajian Kuliner</h3>
                    <p class="text-secondary small mb-0">Review seluruh riwayat transaksi hidangan mewah yang telah selesai dinikmati.</p>
                </div>
                <a class="btn btn-outline-warning rounded-0 fw-medium px-3 py-2 text-white" style="font-size: 12px;" href="<?= htmlspecialchars(base_url('pelanggan/pesanan.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
            </div>

            <div class="compact-list mt-4">
                <?php foreach ($historyOrders as $order): ?>
                    <div class="compact-list-item d-flex justify-content-between align-items-center border-bottom border-soft py-3">
                        <div>
                            <p class="fw-medium text-light mb-1">#LP-<?= $order['id_pesanan']; ?> • <?= htmlspecialchars((string)($order['items_summary'] ?: 'Menu Hidangan'), ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="small text-secondary mb-0"><?= date('d M Y, H:i', strtotime($order['tanggal_pesanan'])); ?> • <?= rupiah((float)$order['total_harga']); ?></p>
                        </div>
                        <span class="badge <?= $order['status_pesanan'] === 'Selesai' ? 'bg-secondary text-white' : 'bg-danger text-white'; ?> px-3 py-2" style="font-size: 12px;"><?= htmlspecialchars($order['status_pesanan'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($historyOrders)): ?>
                    <div class="py-5 text-center">
                        <p class="text-secondary small">Belum ada riwayat transaksi pesanan kuliner.</p>
                    </div>
                <?php endif; ?>
            </div>
        </article>
    </div>

    <aside class="col-lg-4 animate-fade-in-up" style="animation-delay: 0.2s;">
        <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
            <h3 class="h3 mb-4 text-warning font-display" style="font-size: 24px;">Statistik Kuliner</h3>
            <div class="row g-3">
                <article class="p-3 border border-soft bg-black d-flex flex-column h-100 w-100 mb-2">
                    <p class="text-secondary small mb-2">Total Kunjungan</p>
                    <p class="h2 text-gold font-display mb-0" style="font-size: 32px;"><?= str_pad((string)$totalHistoryCount, 2, '0', STR_PAD_LEFT); ?></p>
                </article>
                <article class="p-3 border border-soft bg-black d-flex flex-column h-100 w-100 mb-2">
                    <p class="text-secondary small mb-2">Total Akumulasi</p>
                    <p class="h2 text-gold font-display mb-0" style="font-size: 32px;"><?= rupiah($totalSpent); ?></p>
                </article>
                <article class="p-3 border border-soft bg-black d-flex flex-column h-100 w-100">
                    <p class="text-secondary small mb-2">Sajian Favorit</p>
                    <p class="h2 text-gold font-display mb-0 text-truncate" style="font-size: 20px; line-height: 1.6;"><?= htmlspecialchars($favoriteMenu, ENT_QUOTES, 'UTF-8'); ?></p>
                </article>
            </div>
        </article>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Art Culinaire",
    'eyebrow' => 'Order History',
    'title' => 'Arsip Kunjungan Kuliner',
    'description' => 'Lihat semua riwayat hidangan Anda, total pengeluaran, dan makanan favorit Anda.',
], $content);
require __DIR__ . '/../includes/footer.php';
