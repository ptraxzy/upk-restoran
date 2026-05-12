<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('pelanggan');

$title = 'Indeks Kuliner';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

$items = [
    ['name' => 'Wagyu Ribeye A5', 'copy' => 'Potongan marbling sempurna dari Miyazaki, disajikan dengan jus reduksi dan truffle butter.', 'price' => 'Rp 420.000', 'image' => 'https://images.unsplash.com/photo-1558030006-450675393462?auto=format&fit=crop&w=1200&q=80'],
    ['name' => 'Poached Halibut', 'copy' => 'Mild dan buttery dengan herbal oil yang bersih dan saus veloute.', 'price' => 'Rp 230.000', 'image' => 'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?auto=format&fit=crop&w=1200&q=80'],
    ['name' => 'Dark Matter', 'copy' => 'Dessert cokelat gelap dengan citrus dust dan finish halus.', 'price' => 'Rp 82.000', 'image' => 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?auto=format&fit=crop&w=1200&q=80'],
    ['name' => 'Truffle Risotto', 'copy' => 'Beras carnaroli dimasak perlahan dengan parmesan dan truffle hitam.', 'price' => 'Rp 195.000', 'image' => 'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&w=1200&q=80'],
];

ob_start();
?>
<section>
    <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom border-secondary">
        <div>
            <p class="text-gold small text-uppercase letter-spacing-2 mb-1">Edisi Terbatas</p>
            <h2 class="font-display text-white mb-0">Indeks Kuliner</h2>
        </div>
        <div class="d-flex gap-3">
            <select class="form-select bg-black text-white border-secondary rounded-0 shadow-none w-auto" style="min-width: 150px; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">
                <option>Semua Kategori</option>
                <option>Appetizer</option>
                <option>Main Course</option>
                <option>Dessert</option>
            </select>
        </div>
    </div>

    <div class="menu-showcase">
        <?php foreach ($items as $item): ?>
            <article class="product-card">
                <img class="product-card-image" src="<?= htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>">
                <div>
                    <h3 class="product-card-title"><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="product-card-copy mt-2"><?= htmlspecialchars($item['copy'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="mt-4 d-flex align-items-center justify-content-between">
                        <span class="price-inline"><?= htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <a class="text-gold text-decoration-none text-uppercase small letter-spacing-1 fw-medium" href="<?= htmlspecialchars(base_url('pelanggan/menu_detail.php'), ENT_QUOTES, 'UTF-8'); ?>">Lihat Detail</a>
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

