<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('pelanggan');

$title = 'Dashboard Member';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="row g-5 mb-5">
    <div class="col-lg-6 d-flex flex-column justify-content-center">
        <h1 class="font-display text-white mb-4" style="font-size: 56px; line-height: 1.1;">Selamat Malam.</h1>
        <p class="text-secondary mb-4" style="font-size: 16px; line-height: 1.6; max-width: 480px;">Jelajahi penawaran makan kami, tinjau riwayat kuliner Anda, dan lakukan pemesanan secara langsung dari meja Anda.</p>
        <div class="d-flex align-items-center gap-4 mt-2">
            <div class="d-flex align-items-center gap-2">
                <span class="text-secondary small text-uppercase letter-spacing-1">Sesi Pelanggan:</span>
                <span class="text-white small fw-medium text-uppercase letter-spacing-1"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Alexandre Dubois', ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="d-flex align-items-center gap-2 border-start border-secondary ps-4">
                <span class="text-secondary small text-uppercase letter-spacing-1">Lokasi Meja:</span>
                <span class="text-gold small fw-medium text-uppercase letter-spacing-1"><?= htmlspecialchars($_SESSION['meja_aktif'] ?? '01', ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <article class="position-relative overflow-hidden border border-secondary" style="height: 400px;">
            <img src="https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=1200&q=80" alt="The Autumn Forage" class="w-100 h-100 object-cover" style="filter: brightness(0.7);">
            <div class="position-absolute bottom-0 start-0 w-100 p-5 bg-gradient-to-t from-black to-transparent">
                <p class="text-gold small text-uppercase letter-spacing-2 mb-2">Penawaran Eksklusif</p>
                <h3 class="font-display text-white mb-2" style="font-size: 32px;">The Autumn Forage</h3>
                <p class="text-light small mb-4" style="max-width: 360px;">Eksplorasi rasa musim gugur yang menyingkap kehangatan bumbu. Menampilkan jamur liar, daging rusa asap, dan reduksi akar manis.</p>
                <a class="btn btn-warning rounded-0 text-uppercase fw-medium px-4 py-2" href="<?= htmlspecialchars(base_url('pelanggan/menu.php'), ENT_QUOTES, 'UTF-8'); ?>">Pesan Sekarang</a>
            </div>
        </article>
    </div>
</section>

<section class="mb-5 pb-5 border-bottom border-soft">
    <div class="d-flex align-items-end justify-content-between mb-4">
        <h3 class="font-display text-white m-0" style="font-size: 28px;">Dikurasi Untuk Anda</h3>
        <a class="text-gold small text-uppercase letter-spacing-1 fw-medium text-decoration-none border-bottom border-gold pb-1" href="<?= htmlspecialchars(base_url('pelanggan/menu.php'), ENT_QUOTES, 'UTF-8'); ?>">Lihat Menu Lengkap</a>
    </div>
    
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <div class="col">
            <article class="h-100 d-flex flex-column position-relative">
                <img src="https://images.unsplash.com/photo-1558030006-450675393462?auto=format&fit=crop&w=800&q=80" alt="A5 Wagyu Striploin" class="w-100 object-cover mb-3 border border-secondary" style="height: 240px;">
                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    <h4 class="font-display text-white m-0" style="font-size: 20px;">A5 Wagyu Striploin</h4>
                    <span class="text-gold small fw-medium whitespace-nowrap">Rp 1.100.000</span>
                </div>
                <p class="text-secondary small m-0" style="line-height: 1.6;">Truffle mash dingin hitam, saus daun bawang bakar, emulsi sumsum tulang asap.</p>
            </article>
        </div>
        <div class="col">
            <article class="h-100 d-flex flex-column position-relative">
                <img src="https://images.unsplash.com/photo-1511690656952-34342bb7c2f2?auto=format&fit=crop&w=800&q=80" alt="Hokkaido Scallop" class="w-100 object-cover mb-3 border border-secondary" style="height: 240px;">
                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    <h4 class="font-display text-white m-0" style="font-size: 20px;">Hokkaido Scallop</h4>
                    <span class="text-gold small fw-medium whitespace-nowrap">Rp 420.000</span>
                </div>
                <p class="text-secondary small m-0" style="line-height: 1.6;">Yuzu plum hijau fermentasi, lobak es, busa kedelai putih, jeruk mirin.</p>
            </article>
        </div>
        <div class="col">
            <article class="h-100 d-flex flex-column position-relative">
                <img src="https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?auto=format&fit=crop&w=800&q=80" alt="Dark Matter" class="w-100 object-cover mb-3 border border-secondary" style="height: 240px;">
                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    <h4 class="font-display text-white m-0" style="font-size: 20px;">Dark Matter</h4>
                    <span class="text-gold small fw-medium whitespace-nowrap">Rp 250.000</span>
                </div>
                <p class="text-secondary small m-0" style="line-height: 1.6;">Kakao eksklusif single-origin, praline wijen hitam, balsamic dust.</p>
            </article>
        </div>
    </div>
</section>

<section>
    <h3 class="font-display text-white mb-4" style="font-size: 28px;">Pesanan Terakhir</h3>
    <div class="d-flex flex-column gap-4">
        <div class="d-flex align-items-center justify-content-between pb-4 border-bottom border-soft">
            <div>
                <p class="text-secondary small text-uppercase letter-spacing-1 mb-1">24 Okt 2023 • Meja 4</p>
                <h4 class="font-display text-white mb-1" style="font-size: 22px;">The Elements Tasting Menu</h4>
                <p class="text-secondary small m-0">Termasuk pasangan anggur pilihan.</p>
            </div>
            <a class="btn btn-outline-secondary text-white" href="<?= htmlspecialchars(base_url('pelanggan/pesanan.php'), ENT_QUOTES, 'UTF-8'); ?>">Lihat Struk</a>
        </div>
        <div class="d-flex align-items-center justify-content-between pb-4 border-bottom border-soft">
            <div>
                <p class="text-secondary small text-uppercase letter-spacing-1 mb-1">10 Sep 2023 • Meja VIP</p>
                <h4 class="font-display text-white mb-1" style="font-size: 22px;">Pilihan A La Carte</h4>
                <p class="text-secondary small m-0">Menampilkan Dry Aged Duck Crown.</p>
            </div>
            <a class="btn btn-outline-secondary text-white" href="<?= htmlspecialchars(base_url('pelanggan/pesanan.php'), ENT_QUOTES, 'UTF-8'); ?>">Lihat Struk</a>
        </div>
    </div>
</section>

<style>
.bg-gradient-to-t {
    background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0) 100%);
}
</style>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'ESSENCE",
    'eyebrow' => 'Customer Dashboard',
    'title' => 'Selamat malam, nikmati kurasi menu dan ringkasan pesanan Anda.',
    'description' => '',
    'hide_hero' => true,
    'actions' => [],
], $content);
require __DIR__ . '/../includes/footer.php';
