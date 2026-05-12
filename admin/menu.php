<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Indeks Kuliner';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

$menuItems = [
    [
        'name' => 'Wagyu Ribeye A5', 
        'copy' => 'A5 Japanese Wagyu, black garlic butter, smoked sea salt.', 
        'price' => 'Rp 420.000', 
        'status' => 'Tersedia', 
        'porsi' => '18',
        'image' => 'https://images.unsplash.com/photo-1558030006-450675393462?auto=format&fit=crop&w=1200&q=80'
    ],
    [
        'name' => 'Pan-Seared Duck', 
        'copy' => 'Dry-aged duck breast, cherry reduction, parsnip puree.', 
        'price' => 'Rp 280.000', 
        'status' => 'Tersedia', 
        'porsi' => '22',
        'image' => 'https://images.unsplash.com/photo-1625943555419-56a2cb596640?auto=format&fit=crop&w=1200&q=80'
    ],
    [
        'name' => 'Black Truffle Risotto', 
        'copy' => 'Acquerello rice, wild mushrooms, shaved black truffle.', 
        'price' => 'Rp 195.000', 
        'status' => 'Tersedia', 
        'porsi' => '12',
        'image' => 'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&w=1200&q=80'
    ],
    [
        'name' => 'Poached Halibut', 
        'copy' => 'Wild-caught halibut, saffron emulsion, sea bean salad, sustainably sourced caviar. A delicate balance of oceanic salinity and rich aromatics.', 
        'price' => 'Rp 280.000', 
        'status' => 'Hampir Habis', 
        'porsi' => '4',
        'image' => 'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?auto=format&fit=crop&w=1200&q=80'
    ],
    [
        'name' => 'Heritage Lamb', 
        'copy' => 'Pistachio crust, charred baby leeks, rosemary lamb jus.', 
        'price' => 'Rp -', 
        'status' => 'Tersedia', 
        'porsi' => '10',
        'image' => ''
    ],
];

ob_start();
?>
<section class="section-panel" style="background: transparent; border: none; padding: 0;">
    <div class="d-flex flex-column gap-3 flex-md-row justify-content-md-between align-items-md-start mb-5">
        <div style="max-width: 600px;">
            <h2 class="font-display text-white mb-2" style="font-size: 36px;">Indeks Kuliner</h2>
            <p class="text-secondary small" style="line-height: 1.6;">Kurasi dan kelola portofolio hidangan aktif. Sesuaikan ketersediaan, harga, dan presentasi visual.</p>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
            <a class="btn btn-warning" href="<?= htmlspecialchars(base_url('admin/menu_tambah.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah Menu Baru</a>
        </div>
    </div>

    <div class="d-flex gap-4 mb-5 border-bottom border-soft overflow-auto" style="scrollbar-width: none;">
        <a class="text-gold text-uppercase small letter-spacing-1 fw-medium text-decoration-none pb-3 border-bottom border-gold border-2 whitespace-nowrap flex-shrink-0" href="#">Hidangan Utama</a>
        <a class="text-secondary hover-gold text-uppercase small letter-spacing-1 text-decoration-none pb-3 whitespace-nowrap flex-shrink-0" href="#">Hidangan Pembuka</a>
        <a class="text-secondary hover-gold text-uppercase small letter-spacing-1 text-decoration-none pb-3 whitespace-nowrap flex-shrink-0" href="#">Pencuci Mulut</a>
        <a class="text-secondary hover-gold text-uppercase small letter-spacing-1 text-decoration-none pb-3 whitespace-nowrap flex-shrink-0" href="#">Minuman</a>
        <a class="text-secondary hover-gold text-uppercase small letter-spacing-1 text-decoration-none pb-3 whitespace-nowrap flex-shrink-0" href="#">Diarsipkan</a>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
        <?php foreach ($menuItems as $item): ?>
            <div class="col">
                <article class="h-100 d-flex flex-column position-relative" style="transition: transform 0.3s ease;">
                    <?php if ($item['image']): ?>
                        <img src="<?= htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>" class="w-100 object-cover mb-3" style="height: 240px; border: 1px solid var(--border);">
                    <?php else: ?>
                        <div class="w-100 mb-3 d-flex align-items-center justify-content-center bg-black" style="height: 240px; border: 1px solid var(--border);">
                            <span class="text-secondary small text-uppercase letter-spacing-2">No Image</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-2">
                        <h4 class="font-display text-white m-0" style="font-size: 24px; line-height: 1.2;"><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></h4>
                        <span class="text-gold small fw-medium" style="white-space: nowrap;"><?= htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    
                    <p class="text-secondary small mb-4 flex-grow-1" style="line-height: 1.6;"><?= htmlspecialchars($item['copy'], ENT_QUOTES, 'UTF-8'); ?></p>
                    
                    <div class="d-flex align-items-center gap-2 pt-3 border-top border-soft mt-auto">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background-color: <?= $item['status'] === 'Tersedia' ? 'var(--gold)' : '#dc3545'; ?>;"></span>
                        <span class="text-secondary" style="font-size: 11px;"><?= htmlspecialchars($item['status'], ENT_QUOTES, 'UTF-8'); ?> • <?= htmlspecialchars($item['porsi'], ENT_QUOTES, 'UTF-8'); ?> Porsi</span>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'brand' => 'NOCTRA',
    'badge' => 'Menu Management',
    'title' => 'Indeks Kuliner',
    'description' => 'Katalog menu restoran, kategori, dan ketersediaan.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
