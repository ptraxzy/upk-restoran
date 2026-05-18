<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('pelanggan');

$title = 'Indeks Kuliner';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

$selectedCategory = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Ambil semua kategori
$stmtKat = db()->query("SELECT * FROM kategori ORDER BY id_kategori ASC");
$categories = $stmtKat->fetchAll();

// Ambil menu berdasarkan kategori jika dipilih
if ($selectedCategory > 0) {
    $stmt = db()->prepare("SELECT * FROM menu WHERE status = 'Tersedia' AND id_kategori = ? ORDER BY id_menu DESC");
    $stmt->execute([$selectedCategory]);
} else {
    $stmt = db()->query("SELECT * FROM menu WHERE status = 'Tersedia' ORDER BY id_menu DESC");
}
$items = $stmt->fetchAll();

ob_start();
?>
<style>
    .category-nav {
        display: flex;
        gap: 32px;
        overflow-x: auto;
        scrollbar-width: none;
        padding-bottom: 8px;
    }
    .category-nav::-webkit-scrollbar { display: none; }
    .category-link {
        font-size: 11px;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 2px;
        font-weight: 500;
        text-decoration: none;
        white-space: nowrap;
        padding-bottom: 12px;
        border-bottom: 2px solid transparent;
        transition: all 0.3s ease;
    }
    .category-link:hover, .category-link.active {
        color: var(--gold);
        border-bottom-color: var(--gold);
    }

    .menu-showcase {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 32px;
        margin-top: 40px;
    }
    @media (min-width: 768px) { .menu-showcase { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width: 1200px) { .menu-showcase { grid-template-columns: repeat(4, 1fr); } }

    .product-card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .product-card-image {
        width: 100%;
        height: 180px !important;
        object-fit: cover !important;
        margin-bottom: 16px !important;
        border: 1px solid var(--border);
    }
    .product-card-title {
        font-size: 20px !important;
        margin-bottom: 8px !important;
    }
    .product-card-copy {
        font-size: 12px !important;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 3em;
        line-height: 1.5 !important;
        color: var(--text-secondary);
        margin-bottom: 20px !important;
    }
    .product-card-footer {
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 15px;
        border-top: 1px solid var(--border-soft);
    }
    .product-card-footer form {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        width: 100%;
        flex-wrap: wrap;
    }
    .product-card-footer button {
        min-width: 92px;
        font-size: 10px;
        padding: 8px 10px;
    }
    .product-card-footer .detail-link {
        font-size: 10px;
    }
</style>
<section>
    <div class="mb-5 pb-4 border-bottom border-secondary">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <p class="text-gold small text-uppercase letter-spacing-2 mb-1">Edisi Terbatas</p>
                <h2 class="font-display text-white mb-0" style="font-size: 42px;">Indeks Kuliner</h2>
            </div>
        </div>

        <nav class="category-nav">
            <a href="<?= base_url('pelanggan/menu.php'); ?>" class="category-link <?= $selectedCategory === 0 ? 'active' : ''; ?>">Semua</a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= base_url('pelanggan/menu.php?category=' . $cat['id_kategori']); ?>"
                   class="category-link <?= $selectedCategory === (int)$cat['id_kategori'] ? 'active' : ''; ?>">
                    <?= htmlspecialchars($cat['nama_kategori']); ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <div class="menu-showcase">
        <?php if (empty($items)): ?>
            <div class="col-12 py-5 text-center">
                <p class="text-muted">Tidak ada hidangan yang tersedia untuk kategori ini.</p>
            </div>
        <?php endif; ?>
        <?php foreach ($items as $item): ?>
            <article class="product-card">
                <?php if ($item['gambar']): ?>
                    <img class="product-card-image" src="<?= htmlspecialchars($item['gambar'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item['nama_menu'], ENT_QUOTES, 'UTF-8'); ?>">
                <?php else: ?>
                    <div class="product-card-image bg-black d-flex align-items-center justify-content-center">
                        <span class="text-muted small text-uppercase letter-spacing-2" style="font-size: 10px;">No Image</span>
                    </div>
                <?php endif; ?>
                <div class="d-flex flex-column flex-grow-1">
                    <h3 class="product-card-title"><?= htmlspecialchars($item['nama_menu'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="product-card-copy"><?= htmlspecialchars($item['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="product-card-footer">
                        <form method="post" action="<?= htmlspecialchars(base_url('actions/tambah_keranjang.php'), ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="id_menu" value="<?= htmlspecialchars((string)$item['id_menu'], ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="qty" value="1">
                            <span class="price-inline">Rp <?= number_format((float)$item['harga'], 0, ',', '.'); ?></span>
                            <button type="submit" class="btn btn-warning">Tambah</button>
                        </form>
                        <a class="text-gold text-decoration-none text-uppercase small letter-spacing-1 fw-medium detail-link" href="<?= htmlspecialchars(base_url('pelanggan/menu_detail.php?id=' . $item['id_menu']), ENT_QUOTES, 'UTF-8'); ?>">Detail</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Art Culinaire",
    'eyebrow' => 'Menu Eksklusif',
    'title' => 'Simfoni Musim Gugur',
    'description' => 'Pengalaman hidangan kuratorial untuk meja restoran, menonjolkan keseimbangan rasa hangat dan gelap.',
], $content);
require __DIR__ . '/../includes/footer.php';
