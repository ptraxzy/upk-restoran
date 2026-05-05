<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('pelanggan');

$title = 'Menu Member';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

$items = [
    ['name' => 'Poached Halibut', 'copy' => 'Mild dan buttery dengan herbal oil yang bersih.', 'price' => 'Rp230.000', 'image' => 'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?auto=format&fit=crop&w=1200&q=80'],
    ['name' => 'A5 Wagyu Ribeye', 'copy' => 'Melted marbling dengan plating gelap berkarakter.', 'price' => 'Rp420.000', 'image' => 'https://images.unsplash.com/photo-1558030006-450675393462?auto=format&fit=crop&w=1200&q=80'],
    ['name' => 'Dark Matter', 'copy' => 'Dessert cokelat gelap dengan citrus dust dan finish halus.', 'price' => 'Rp82.000', 'image' => 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?auto=format&fit=crop&w=1200&q=80'],
];

ob_start();
?>
<section class="space-y-5">
    <article class="public-hero">
        <div class="public-hero-body">
            <div>
                <p class="eyebrow">Principal Course</p>
                <h2 class="mt-3 font-display text-[3rem] leading-none text-stone-50">Simfoni Musim Gugur</h2>
                <p class="mt-4 max-w-md text-sm leading-7 text-stone-300">Pengalaman hidangan kuratorial untuk meja restoran, menonjolkan keseimbangan rasa hangat dan gelap.</p>
            </div>
        </div>
        <div class="public-hero-media" style="background-image:url('https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=1400&q=80');"></div>
    </article>

    <div class="menu-showcase">
        <?php foreach ($items as $item): ?>
            <article class="product-card">
                <img class="product-card-image" src="<?= htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>">
                <div>
                    <h2 class="product-card-title"><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p class="product-card-copy mt-3"><?= htmlspecialchars($item['copy'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="price-inline"><?= htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <a class="nav-link" href="<?= htmlspecialchars(frontend_url('pembeli/menu/detail.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => 'Lumiere',
    'eyebrow' => 'Customer Menu',
    'title' => 'Menu & Ordering',
    'description' => 'Jelajahi sajian unggulan kami. Setiap menu dikurasi khusus untuk menghadirkan pengalaman bersantap yang tak terlupakan.',
    'actions' => [
        ['label' => 'Lihat Keranjang', 'href' => frontend_url('pembeli/keranjang/index.php')],
        ['label' => 'Riwayat', 'href' => frontend_url('pembeli/pesanan/index.php'), 'variant' => 'secondary'],
    ],
], $content);
require base_path('backend/includes/footer.php');
