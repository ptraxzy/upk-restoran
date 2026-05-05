<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('pelanggan');

$title = 'Pesanan Member';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

ob_start();
?>
<section class="editorial-grid">
    <article class="section-panel">
        <h2 class="section-title">Riwayat dan Status Pesanan</h2>
        <p class="section-subtitle">Perjalanan order aktif dan pesanan terakhir disusun lebih ringkas agar mudah dipantau.</p>
        <div class="compact-list mt-6">
            <div class="compact-list-item">
                <div>
                    <p class="font-medium text-stone-100">#M-901 • Truffle Linguine</p>
                    <p class="mt-2 text-sm text-stone-500">Order aktif • Diproses dapur • 19:25</p>
                </div>
                <span class="badge badge-gold">Diproses</span>
            </div>
            <div class="compact-list-item">
                <div>
                    <p class="font-medium text-stone-100">#M-887 • A5 Wagyu Ribeye</p>
                    <p class="mt-2 text-sm text-stone-500">Pesanan selesai • 02 Mei 2026</p>
                </div>
                <span class="badge badge-muted">Selesai</span>
            </div>
        </div>
    </article>

    <aside class="section-panel">
        <h3 class="section-title">Pesanan Aktif</h3>
        <div class="order-rail mt-5 !grid-cols-1">
            <article class="order-stat">
                <p class="metric-label">Aktif Sekarang</p>
                <p class="metric-value !text-[2rem]">01</p>
            </article>
            <article class="order-stat">
                <p class="metric-label">Estimasi Plating</p>
                <p class="metric-value !text-[2rem]">8m</p>
            </article>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Essence",
    'eyebrow' => 'Order Journey',
    'title' => 'Riwayat pesanan yang lebih jelas dan lebih tenang dibaca.',
    'description' => 'Status aktif, order terakhir, dan jalur tindakan utama disusun dalam pola yang lebih bersih.',
    'actions' => [
        ['label' => 'Buka Keranjang', 'href' => frontend_url('pembeli/keranjang/index.php')],
        ['label' => 'Menu', 'href' => frontend_url('pembeli/menu/index.php'), 'variant' => 'secondary'],
    ],
], $content);
require base_path('backend/includes/footer.php');
