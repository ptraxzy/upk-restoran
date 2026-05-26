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

$search = trim($_GET['search'] ?? '');
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 5;

$filteredReviews = [];
foreach ($reviews as $rev) {
    if ($search === '') {
        $filteredReviews[] = $rev;
    } else {
        $s = strtolower($search);
        $uStr = strtolower((string)$rev['username']);
        $kStr = strtolower((string)$rev['komentar']);
        if (strpos($uStr, $s) !== false || strpos($kStr, $s) !== false) {
            $filteredReviews[] = $rev;
        }
    }
}

$totalRows = count($filteredReviews);
$totalPages = ceil($totalRows / $limit);
$paginatedReviews = array_slice($filteredReviews, ($page - 1) * $limit, $limit);

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
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <h3 class="font-display text-warning mb-0" style="font-size: 22px;">Semua Ulasan Kuliner</h3>
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control bg-black text-white border-secondary rounded-0" placeholder="Cari nama atau ulasan..." value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="btn btn-warning rounded-0 px-3">Cari</button>
                </form>
            </div>
            
            <?php if (empty($paginatedReviews)): ?>
                <div class="py-5 text-center">
                    <p class="text-secondary">Belum ada pelanggan yang mengirimkan ulasan.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($paginatedReviews as $rev): ?>
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

            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-5">
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
    'badge' => 'Administrasi',
    'title' => 'Ulasan & Kepuasan Pelanggan',
    'description' => 'Tinjau tingkat kepuasan hidangan dan pelayanan langsung dari pelanggan.',
    'nav_sections' => admin_nav_sections(),
], $content);

require __DIR__ . '/../includes/footer.php';
