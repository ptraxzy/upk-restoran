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

// Fetch customer's past orders (Selesai or Dibatalkan) along with rating and ulasan
$stmt = $pdo->prepare('
    SELECT p.*, GROUP_CONCAT(CONCAT(m.nama_menu, " x", dp.jumlah) SEPARATOR ", ") AS items_summary,
           MAX(ul.rating) AS rating, MAX(ul.komentar) AS komentar, MAX(py.total_bayar) AS total_bayar
    FROM pesanan p
    LEFT JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
    LEFT JOIN menu m ON dp.id_menu = m.id_menu
    LEFT JOIN ulasan ul ON p.id_pesanan = ul.id_pesanan
    LEFT JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
    WHERE p.id_pelanggan = ? AND p.status_pesanan IN ("Selesai", "Dibatalkan")
    GROUP BY p.id_pesanan
    ORDER BY p.id_pesanan DESC
');
$stmt->execute([$userId]);
$historyOrders = $stmt->fetchAll();

// Statistics calculation
$totalSpent = 0.0;
$totalHistoryCount = 0;
foreach ($historyOrders as $order) {
    if ($order['status_pesanan'] === 'Selesai') {
        $totalSpent += (float)($order['total_bayar'] ?? $order['total_harga']);
        $totalHistoryCount++;
    }
}

// Find customer's favorite menu item
$stmtFav = $pdo->prepare('
    SELECT m.nama_menu, SUM(dp.jumlah) AS total_qty
    FROM detail_pesanan dp
    JOIN pesanan p ON dp.id_pesanan = p.id_pesanan
    JOIN menu m ON dp.id_menu = m.id_menu
    WHERE p.id_pelanggan = ? AND p.status_pesanan = "Selesai"
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
                    <div class="compact-list-item border-bottom border-soft py-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <p class="fw-medium text-light mb-1">#LP-<?= $order['id_pesanan']; ?> • <?= htmlspecialchars((string)($order['items_summary'] ?: 'Menu Hidangan'), ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="small text-secondary mb-0"><?= date('d M Y, H:i', strtotime($order['tanggal_pesanan'])); ?> • <?= rupiah((float)$order['total_harga']); ?></p>
                            </div>
                            <span class="badge <?= $order['status_pesanan'] === 'Selesai' ? 'bg-secondary text-white' : 'bg-danger text-white'; ?> px-3 py-2" style="font-size: 12px;"><?= htmlspecialchars($order['status_pesanan'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>

                        <!-- Ulasan/Feedback Section -->
                        <?php if ($order['status_pesanan'] === 'Selesai'): ?>
                            <div class="p-3 bg-black border border-soft mt-2 animate-fade-in-up" style="border-radius: 0;">
                                <?php if ($order['rating'] !== null): ?>
                                    <!-- Jika sudah memberikan ulasan -->
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <div class="text-warning small" style="font-size: 12px; letter-spacing: 0.1em;">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?= $i <= (int)$order['rating'] ? '★' : '☆'; ?>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="text-secondary small" style="font-size: 10px; font-weight: 500;">Ulasan Anda</span>
                                    </div>
                                    <p class="small text-light mb-0" style="font-size: 12.5px; font-style: italic;">"<?= htmlspecialchars($order['komentar'] ?: '-', ENT_QUOTES, 'UTF-8'); ?>"</p>
                                <?php else: ?>
                                    <!-- Jika belum memberikan ulasan -->
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-secondary small" style="font-size: 11px;">Bagaimana hidangannya? Berikan feedback Anda!</span>
                                        <button class="btn btn-warning rounded-0 fw-semibold px-3 py-1" style="font-size: 10px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#modalUlasan<?= $order['id_pesanan']; ?>">Beri Ulasan</button>
                                    </div>

                                    <!-- Modal Form Ulasan -->
                                    <div class="modal fade" id="modalUlasan<?= $order['id_pesanan']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content bg-dark text-white border-secondary rounded-0">
                                                <form action="<?= base_url('actions/pesanan/ulasan_store.php'); ?>" method="POST">
                                                    <input type="hidden" name="id_pesanan" value="<?= $order['id_pesanan']; ?>">
                                                    <div class="modal-header border-secondary">
                                                        <h5 class="modal-title font-display text-warning" style="font-size: 18px;">Ulasan Hidangan #LP-<?= $order['id_pesanan']; ?></h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <div class="mb-3">
                                                            <label class="form-label text-secondary small text-uppercase fw-bold mb-2" style="font-size: 10px; letter-spacing: 0.05em;">Rating Bintang</label>
                                                            <div class="d-flex gap-2 justify-content-start rating-stars-input" style="font-size: 26px; cursor: pointer; user-select: none;">
                                                                <input type="hidden" name="rating" id="rating-input-<?= $order['id_pesanan']; ?>" value="5">
                                                                <?php for ($star = 1; $star <= 5; $star++): ?>
                                                                    <span class="text-warning star-btn" data-star="<?= $star; ?>" data-order="<?= $order['id_pesanan']; ?>" onclick="setStar(this)">★</span>
                                                                <?php endfor; ?>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="form-label text-secondary small text-uppercase fw-bold mb-2" style="font-size: 10px; letter-spacing: 0.05em;">Komentar Anda</label>
                                                            <textarea class="form-control bg-black text-white border-secondary rounded-0" name="komentar" rows="3" placeholder="Ceritakan cita rasa hidangan dan pelayanan kami..." required style="font-size: 13px;"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-secondary">
                                                        <button type="button" class="btn btn-outline-secondary rounded-0 text-white" data-bs-dismiss="modal" style="font-size: 11px;">Batal</button>
                                                        <button type="submit" class="btn btn-warning rounded-0 fw-bold" style="font-size: 11px;">Kirim Ulasan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
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
                    <p class="h2 text-gold font-display mb-0" style="font-size: 32px;"><?= $totalHistoryCount; ?></p>
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
<script>
function setStar(element) {
    const starVal = element.getAttribute('data-star');
    const orderId = element.getAttribute('data-order');
    document.getElementById('rating-input-' + orderId).value = starVal;
    
    const parent = element.parentElement;
    const stars = parent.querySelectorAll('.star-btn');
    stars.forEach(s => {
        const currentStarVal = s.getAttribute('data-star');
        if (parseInt(currentStarVal) <= parseInt(starVal)) {
            s.textContent = '★';
        } else {
            s.textContent = '☆';
        }
    });
}
</script>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Art Culinaire",
    'eyebrow' => 'Order History',
    'title' => 'Arsip Kunjungan Kuliner',
    'description' => 'Lihat semua riwayat hidangan Anda, total pengeluaran, dan makanan favorit Anda.',
], $content);
require __DIR__ . '/../includes/footer.php';
