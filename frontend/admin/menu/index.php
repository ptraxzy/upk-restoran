<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('admin');

$title = 'Manajemen Menu';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

$menuItems = [
    ['name' => 'Truffle Beef Wellington', 'copy' => 'Beef tenderloin dengan mushroom duxelles dan puff pastry keemasan.', 'price' => 'Rp315.000', 'status' => 'Tersedia', 'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=1200&q=80'],
    ['name' => 'Pan-Seared Foie Gras', 'copy' => 'Foie gras lembut dengan citrus glaze dan brioche toast.', 'price' => 'Rp280.000', 'status' => 'Tersedia', 'image' => 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?auto=format&fit=crop&w=1200&q=80'],
    ['name' => 'Artisan Burrata Salad', 'copy' => 'Burrata creamy dengan roasted tomato dan basil oil.', 'price' => 'Rp145.000', 'status' => 'Hampir Habis', 'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=1200&q=80'],
];

ob_start();
?>
<section class="section-panel">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <h3 class="section-title">Indeks Kuliner</h3>
            <p class="section-subtitle">Daftar menu premium dengan visual informatif.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a class="cta-secondary" href="<?= htmlspecialchars(frontend_url('admin/menu/edit.php'), ENT_QUOTES, 'UTF-8'); ?>">Ubah Menu</a>
            <a class="cta-primary" href="<?= htmlspecialchars(frontend_url('admin/menu/create.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah Menu</a>
        </div>
    </div>

    <div class="menu-grid mt-6">
        <?php foreach ($menuItems as $item): ?>
            <article class="menu-card">
                <img class="menu-card-image" src="<?= htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>">
                <div class="menu-card-body">
                    <p class="eyebrow">Signature Plate</p>
                    <h4 class="menu-card-title mt-3"><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></h4>
                    <p class="menu-card-copy"><?= htmlspecialchars($item['copy'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="menu-card-footer">
                        <span class="font-display text-2xl text-stone-50"><?= htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="badge <?= $item['status'] === 'Tersedia' ? 'badge-gold' : 'badge-muted'; ?>"><?= htmlspecialchars($item['status'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administration',
    'title' => 'Indeks Kuliner',
    'description' => 'Katalog menu restoran, kategori, dan ketersediaan.',
    'nav_sections' => admin_nav_sections(),
], $content);
require base_path('backend/includes/footer.php');
