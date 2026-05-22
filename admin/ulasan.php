<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Ulasan Pelanggan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';
$pdo = db();

// Handle deletion (moderation) of reviews
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id_ulasan = (int)($_POST['id_ulasan'] ?? 0);
    if ($id_ulasan > 0) {
        try {
            $stmtDel = $pdo->prepare("DELETE FROM ulasan WHERE id_ulasan = ?");
            $stmtDel->execute([$id_ulasan]);
            set_flash('success', 'Ulasan berhasil dihapus.');
        } catch (Exception $e) {
            set_flash('error', 'Gagal menghapus ulasan: ' . $e->getMessage());
        }
    }
    redirect(base_url('admin/ulasan.php'));
}

// Fetch all reviews with user and order details
$stmt = $pdo->query("
    SELECT ul.*, u.username, p.no_meja, p.total_harga, p.tanggal_pesanan
    FROM ulasan ul
    JOIN user u ON ul.id_user = u.id_user
    JOIN pesanan p ON ul.id_pesanan = p.id_pesanan
    ORDER BY ul.tanggal_ulasan DESC
");
$reviews = $stmt->fetchAll();

// Calculate review statistics
$totalReviews = count($reviews);
$averageRating = 0.0;
if ($totalReviews > 0) {
    $totalStars = array_sum(array_column($reviews, 'rating'));
    $averageRating = round($totalStars / $totalReviews, 1);
}

ob_start();
?>
<section class="row g-4 animate-fade-in-up">
    <!-- Statistik Panel -->
    <div class="col-12 mb-2">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <article class="card p-4">
                    <p class="text-secondary small mb-2">Total Feedback</p>
                    <p class="h2 text-white font-display mb-0"><?= str_pad((string)$totalReviews, 2, '0', STR_PAD_LEFT); ?></p>
                    <p class="metric-note">Total ulasan yang dikirim oleh pelanggan.</p>
                </article>
            </div>
            <div class="col">
                <article class="card p-4">
                    <p class="text-secondary small mb-2">Rata-Rata Rating</p>
                    <p class="h2 text-gold font-display mb-0">⭐ <?= $totalReviews > 0 ? $averageRating : '0.0'; ?> <span class="text-secondary small" style="font-size: 14px;">/ 5.0</span></p>
                    <p class="metric-note">Akumulasi nilai kepuasan kuliner pelanggan.</p>
                </article>
            </div>
            <div class="col">
                <article class="card p-4">
                    <p class="text-secondary small mb-2">Tingkat Kepuasan</p>
                    <p class="h2 text-white font-display mb-0">
                        <?php
                        if ($totalReviews === 0) echo '0%';
                        else {
                            $satisfied = count(array_filter($reviews, fn($r) => $r['rating'] >= 4));
                            echo round(($satisfied / $totalReviews) * 100) . '%';
                        }
                        ?>
                    </p>
                    <p class="metric-note">Persentase ulasan dengan rating bintang 4 atau 5.</p>
                </article>
            </div>
        </div>
    </div>

    <!-- Feed Ulasan -->
    <div class="col-12">
        <article class="section-panel p-4">
            <h3 class="font-display text-warning mb-4" style="font-size: 22px;">Semua Ulasan Kuliner</h3>
            
            <?php if (empty($reviews)): ?>
                <div class="py-5 text-center">
                    <p class="text-secondary">Belum ada pelanggan yang mengirimkan ulasan.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($reviews as $rev): ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="p-4 border border-soft bg-black d-flex flex-column justify-content-between h-100">
                                <div>
                                    <!-- Header Ulasan -->
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <p class="fw-bold text-white mb-0" style="font-size: 14px;"><?= htmlspecialchars($rev['username'], ENT_QUOTES, 'UTF-8'); ?></p>
                                            <p class="text-secondary mb-0" style="font-size: 11px;">Meja <?= htmlspecialchars($rev['no_meja'], ENT_QUOTES, 'UTF-8'); ?> • #LP-<?= $rev['id_pesanan']; ?></p>
                                        </div>
                                        <div class="text-warning small" style="font-size: 11px; letter-spacing: 0.05em;">
                                            <?php for ($star = 1; $star <= 5; $star++): ?>
                                                <?= $star <= (int)$rev['rating'] ? '★' : '☆'; ?>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Isi Ulasan -->
                                    <p class="text-light mb-4" style="font-size: 13px; font-style: italic; line-height: 1.6;">
                                        "<?= htmlspecialchars($rev['komentar'] ?: 'Tidak ada ulasan tertulis.', ENT_QUOTES, 'UTF-8'); ?>"
                                    </p>
                                </div>
                                
                                <!-- Footer Card -->
                                <div class="d-flex justify-content-between align-items-center pt-3 border-top border-soft">
                                    <span class="text-secondary" style="font-size: 10px;"><?= date('d M Y, H:i', strtotime($rev['tanggal_ulasan'])); ?></span>
                                    
                                    <form action="" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ulasan ini?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id_ulasan" value="<?= $rev['id_ulasan']; ?>">
                                        <button type="submit" class="btn btn-outline-danger border-0 p-0 text-danger" style="font-size: 11px; font-weight: 500;">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>
    </div>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administrasi',
    'title' => 'Ulasan & Kepuasan Pelanggan',
    'description' => 'Tinjau tingkat kepuasan hidangan dan pelayanan langsung dari pelanggan.',
    'nav_sections' => admin_nav_sections(),
], $content);

require __DIR__ . '/../includes/footer.php';
