<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('kasir');

$title = 'Menu Karyawan';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

$items = [
    ['name' => 'Truffle Mushroom Risotto', 'copy' => 'Creme, earthy, dan favorit layanan dinner.', 'price' => 'Rp195.000', 'image' => 'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&w=1200&q=80'],
    ['name' => 'A5 Wagyu Ribeye', 'copy' => 'Premium cut dengan plating kontras dan jus kaya.', 'price' => 'Rp420.000', 'image' => 'https://images.unsplash.com/photo-1558030006-450675393462?auto=format&fit=crop&w=1200&q=80'],
];

ob_start();
?>
<section class="section-panel">
    <h3 class="section-title">Daftar Menu Service</h3>
    <p class="section-subtitle">Panel cepat untuk membantu kasir memahami harga, signature item, dan rekomendasi upsell.</p>
    <div class="menu-grid mt-6">
        <?php foreach ($items as $item): ?>
            <article class="menu-card">
                <img class="menu-card-image" src="<?= htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>">
                <div class="menu-card-body">
                    <h4 class="menu-card-title"><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></h4>
                    <p class="menu-card-copy"><?= htmlspecialchars($item['copy'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="menu-card-footer">
                        <span class="font-display text-2xl text-stone-50"><?= htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="badge badge-gold">Recommended</span>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Service Floor',
    'title' => 'Menu',
    'description' => 'Daftar menu untuk membantu penjelasan item ke tamu dan mempercepat proses input pesanan.',
    'nav_sections' => staff_nav_sections(),
], $content);
require base_path('backend/includes/footer.php');
