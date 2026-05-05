<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('pelanggan');

$title = 'Dashboard Member';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

ob_start();
?>
<section class="editorial-grid">
    <div class="space-y-5">
        <article class="public-hero">
            <div class="public-hero-body">
                <div>
                    <p class="eyebrow">Welcome Back</p>
                    <h2 class="mt-3 font-display text-[3rem] leading-none text-stone-50">Selamat Malam.</h2>
                    <p class="mt-4 max-w-md text-sm leading-7 text-stone-300">Meja Anda sudah siap, jelajahi penawaran musiman kami, nikmati favorit kitchen artisan, atau atur pesanan dengan ritme yang lebih tenang.</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a class="cta-primary" href="<?= htmlspecialchars(frontend_url('pembeli/menu/index.php'), ENT_QUOTES, 'UTF-8'); ?>">Pesan Menu</a>
                    <a class="cta-secondary" href="<?= htmlspecialchars(frontend_url('pembeli/pesanan/index.php'), ENT_QUOTES, 'UTF-8'); ?>">Riwayat</a>
                </div>
            </div>
            <div class="public-hero-media" style="background-image:url('https://images.unsplash.com/photo-1546039907-7fa05f864c02?auto=format&fit=crop&w=1400&q=80');"></div>
        </article>

        <article class="section-panel">
            <div class="flex items-end justify-between gap-4">
                <h3 class="section-title">Dikurasi Untuk Anda</h3>
                <a class="nav-link" href="<?= htmlspecialchars(frontend_url('pembeli/menu/index.php'), ENT_QUOTES, 'UTF-8'); ?>">Lihat Menu Lengkap</a>
            </div>
            <div class="menu-showcase mt-6">
                <article class="product-card">
                    <img class="product-card-image" src="https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=900&q=80" alt="Wagyu striploin">
                    <div>
                        <h4 class="product-card-title">A5 Wagyu Striploin</h4>
                        <p class="product-card-copy mt-3">Truffle butter glaze, charcoal finishing, dan tekstur daging yang halus.</p>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="price-inline">Rp520.000</span>
                            <a class="nav-link" href="<?= htmlspecialchars(frontend_url('pembeli/menu/detail.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah</a>
                        </div>
                    </div>
                </article>
                <article class="product-card">
                    <img class="product-card-image" src="https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?auto=format&fit=crop&w=900&q=80" alt="Matcha mille crepe">
                    <div>
                        <h4 class="product-card-title">Matcha Mille Crepe</h4>
                        <p class="product-card-copy mt-3">Lapisan lembut dengan bitter-sweet green tea finish yang ringan.</p>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="price-inline">Rp95.000</span>
                            <a class="nav-link" href="<?= htmlspecialchars(frontend_url('pembeli/menu/detail.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah</a>
                        </div>
                    </div>
                </article>
                <article class="product-card">
                    <img class="product-card-image" src="https://images.unsplash.com/photo-1511690656952-34342bb7c2f2?auto=format&fit=crop&w=900&q=80" alt="Hokkaido scallop">
                    <div>
                        <h4 class="product-card-title">Hokkaido Scallop</h4>
                        <p class="product-card-copy mt-3">Plating kontras lembut dengan citrus oil dan sea salt mineral.</p>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="price-inline">Rp250.000</span>
                            <a class="nav-link" href="<?= htmlspecialchars(frontend_url('pembeli/menu/detail.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah</a>
                        </div>
                    </div>
                </article>
            </div>
        </article>
    </div>

    <aside class="space-y-5">
        <article class="section-panel">
            <p class="eyebrow">Member Summary</p>
            <div class="order-rail mt-6">
                <article class="order-stat">
                    <p class="metric-label">Voucher Tersedia</p>
                    <p class="metric-value !text-[28px]">01</p>
                </article>
                <article class="order-stat">
                    <p class="metric-label">Pesanan Aktif</p>
                    <p class="metric-value !text-[28px]">02</p>
                </article>
                <article class="order-stat">
                    <p class="metric-label">Poin Member</p>
                    <p class="metric-value !text-[28px]">420</p>
                </article>
            </div>
        </article>

        <article class="section-panel">
            <h3 class="section-title">Pertemuan Terakhir</h3>
            <div class="compact-list mt-5">
                <div class="compact-list-item">
                    <div>
                        <p class="font-medium text-stone-100">The Essence Tasting Menu</p>
                        <p class="mt-2 text-sm text-stone-500">24 April 2026 • 2 tamu</p>
                    </div>
                    <a class="nav-link" href="<?= htmlspecialchars(frontend_url('pembeli/pesanan/index.php'), ENT_QUOTES, 'UTF-8'); ?>">Ulang</a>
                </div>
                <div class="compact-list-item">
                    <div>
                        <p class="font-medium text-stone-100">Private Dinner Pairing</p>
                        <p class="mt-2 text-sm text-stone-500">17 April 2026 • 4 tamu</p>
                    </div>
                    <a class="nav-link" href="<?= htmlspecialchars(frontend_url('pembeli/pesanan/index.php'), ENT_QUOTES, 'UTF-8'); ?>">Detail</a>
                </div>
            </div>
        </article>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Essence",
    'eyebrow' => 'Customer Dashboard',
    'title' => 'Selamat malam, nikmati kurasi menu dan ringkasan pesanan Anda.',
    'description' => 'Selamat datang di area keanggotaan eksklusif Anda. Nikmati kemudahan reservasi dan pesanan.',
    'actions' => [
        ['label' => 'Buka Menu', 'href' => frontend_url('pembeli/menu/index.php')],
        ['label' => 'Keranjang', 'href' => frontend_url('pembeli/keranjang/index.php'), 'variant' => 'secondary'],
    ],
], $content);
require base_path('backend/includes/footer.php');
