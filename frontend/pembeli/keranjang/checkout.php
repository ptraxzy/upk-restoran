<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('pelanggan');

$title = 'Checkout';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

ob_start();
?>
<section class="editorial-grid">
    <article class="cart-panel">
        <p class="eyebrow">Voucher & Promo</p>
        <h2 class="section-title mt-3">Pemesanan Anda</h2>
        <div class="mt-6 grid gap-5 md:grid-cols-[1fr_220px]">
            <div class="space-y-5">
                <div class="voucher-card">
                    <div>
                        <p class="font-medium text-stone-100">Tasting Menu</p>
                        <p class="mt-2 text-sm text-stone-500">3-Course Chef's Selection</p>
                    </div>
                    <span class="badge badge-muted">2 PAX</span>
                </div>
                <div class="voucher-card">
                    <div>
                        <p class="font-medium text-stone-100">Jadwal</p>
                        <p class="mt-2 text-sm text-stone-500">Jumat, 24 April 2026<br>19.00 WIB</p>
                    </div>
                </div>
            </div>
            <div class="summary-panel">
                <p class="font-medium text-stone-100">Rincian Biaya</p>
                <div class="mt-4">
                    <div class="summary-row"><span>Tenderloin Menu Pairing</span><span>Rp 1.250.000</span></div>
                    <div class="summary-row"><span>Premium Wine Pairing</span><span>Rp 1.100.000</span></div>
                    <div class="summary-row"><span>Subtotal</span><span>Rp 2.350.000</span></div>
                    <div class="summary-row"><span>15% Diskon Member</span><span>-Rp 225.000</span></div>
                    <div class="summary-row"><span>Pajak & Layanan</span><span>Rp 42.750</span></div>
                </div>
                <div class="mt-5 flex items-center justify-between">
                    <span>Total</span>
                    <span class="font-display text-xl text-brass">Rp 1.772.750</span>
                </div>
            </div>
        </div>

        <div class="mt-8 space-y-4">
            <div class="voucher-card">
                <div>
                    <p class="font-medium text-stone-100">Diskon 15% Wine Pairing</p>
                    <p class="mt-2 text-sm text-stone-500">Berlaku sampai 30 April 2026</p>
                </div>
                <button class="cta-secondary" type="button">Pakai</button>
            </div>
            <div class="voucher-card">
                <div>
                    <p class="font-medium text-stone-100">Complimentary Dessert</p>
                    <p class="mt-2 text-sm text-stone-500">Bonus untuk member loyalitas premium</p>
                </div>
                <button class="cta-secondary" type="button">Gunakan</button>
            </div>
        </div>
    </article>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Art Culinaire",
    'eyebrow' => 'Member Voucher',
    'title' => 'Gunakan Voucher',
    'description' => 'Konfirmasi akhir pesanan dan detail pembayaran Anda.',
    'actions' => [
        ['label' => 'Kembali ke Keranjang', 'href' => frontend_url('pembeli/keranjang/index.php'), 'variant' => 'secondary'],
    ],
], $content);
require base_path('backend/includes/footer.php');
