<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('pelanggan');

$title = 'Detail Menu';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

ob_start();
?>
<section class="detail-layout">
    <div class="detail-media" style="background-image:url('https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&w=1600&q=80');"></div>
    <article class="detail-body">
        <p class="eyebrow">Menu Utama • Pasta Plate</p>
        <h2 class="mt-3 font-display text-[3.1rem] leading-none text-stone-50">Truffle Mushroom Risotto</h2>
        <p class="mt-3 text-sm text-stone-500">Rp 195.000</p>
        <p class="detail-copy">Sebuah interpretasi kuliner klasik dari kitchen artisan. Beras carnaroli premium yang dimasak perlahan hingga mencapai tekstur al dente yang menyatu, melapisi parmesan hangat dan mushroom glaze pekat. Diakhiri dengan irisan truffle hitam, veal jus lembut, dan sentuhan akhir buttery yang tenang namun tegas.</p>

        <div class="mt-7 flex flex-wrap gap-2">
            <span class="badge badge-muted">Mushroom & Umami</span>
            <span class="badge badge-muted">Rich Butter</span>
            <span class="badge badge-muted">Autumn Selection</span>
        </div>

        <div class="detail-actions items-center justify-between">
            <div class="qty-stepper">
                <button type="button">-</button>
                <span>1</span>
                <button type="button">+</button>
            </div>
            <div class="flex flex-wrap gap-3">
                <a class="cta-secondary" href="<?= htmlspecialchars(frontend_url('pembeli/menu/index.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
                <a class="cta-primary" href="<?= htmlspecialchars(frontend_url('pembeli/keranjang/index.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah ke Keranjang</a>
            </div>
        </div>
    </article>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => 'Lumiere',
    'eyebrow' => 'Customer Detail',
    'title' => 'Detail Menu',
    'description' => 'Detail sajian eksklusif dari chef kami, dibuat dengan bahan-bahan terbaik.',
    'actions' => [
        ['label' => 'Lihat Menu', 'href' => frontend_url('pembeli/menu/index.php'), 'variant' => 'secondary'],
    ],
], $content);
require base_path('backend/includes/footer.php');
