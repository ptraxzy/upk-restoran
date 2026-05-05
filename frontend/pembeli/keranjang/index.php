<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('pelanggan');

$title = 'Keranjang';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

ob_start();
?>
<section class="cart-layout">
    <article class="cart-panel">
        <p class="eyebrow">Keranjang</p>
        <h2 class="section-title mt-3">Keranjang Anda</h2>
        <div class="mt-6">
            <div class="cart-item">
                <img class="cart-item-image" src="https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=400&q=80" alt="Wagyu A5 Tenderloin">
                <div class="flex-1">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="cart-item-title">Wagyu A5 Tenderloin</p>
                            <p class="mt-2 text-sm text-stone-500">Tingkat kematangan Medium Rare</p>
                        </div>
                        <span class="price-inline">Rp 1.250.000</span>
                    </div>
                    <div class="mt-3 qty-stepper">
                        <button type="button">-</button>
                        <span>1</span>
                        <button type="button">+</button>
                    </div>
                </div>
            </div>

            <div class="cart-item">
                <img class="cart-item-image" src="https://images.unsplash.com/photo-1625943555419-56a2cb596640?auto=format&fit=crop&w=400&q=80" alt="Truffle Linguine">
                <div class="flex-1">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="cart-item-title">Truffle Linguine</p>
                            <p class="mt-2 text-sm text-stone-500">Fresh shaved truffle</p>
                        </div>
                        <span class="price-inline">Rp 300.000</span>
                    </div>
                    <div class="mt-3 qty-stepper">
                        <button type="button">-</button>
                        <span>1</span>
                        <button type="button">+</button>
                    </div>
                </div>
            </div>
        </div>
    </article>

    <aside class="summary-panel">
        <p class="eyebrow">Rincian Biaya</p>
        <div class="mt-5">
            <div class="summary-row"><span>Truffle Infused Pairing</span><span>Rp 125.000</span></div>
            <div class="summary-row"><span>Premium Wine Pairing</span><span>Rp 1.100.000</span></div>
            <div class="summary-row"><span>Subtotal</span><span>Rp 1.550.000</span></div>
            <div class="summary-row"><span>Biaya Admin 15%</span><span>Rp 225.000</span></div>
            <div class="summary-row"><span>Pajak & Layanan</span><span>Rp 42.750</span></div>
        </div>
        <div class="mt-5 flex items-center justify-between">
            <span class="text-sm text-stone-500">Total</span>
            <span class="font-display text-[2rem] text-brass">Rp 1.772.750</span>
        </div>
        <div class="mt-6 flex flex-col gap-3">
            <a class="cta-primary w-full" href="<?= htmlspecialchars(frontend_url('pembeli/keranjang/checkout.php'), ENT_QUOTES, 'UTF-8'); ?>">Lanjut ke Pembayaran</a>
            <a class="cta-secondary w-full" href="<?= htmlspecialchars(frontend_url('pembeli/menu/index.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali Belanja</a>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Art Culinaire",
    'eyebrow' => 'Cart & Payment',
    'title' => 'Keranjang & Pembayaran',
    'description' => 'Review pesanan Anda sebelum melanjutkan ke pembayaran.',
    'actions' => [
        ['label' => 'Checkout', 'href' => frontend_url('pembeli/keranjang/checkout.php')],
        ['label' => 'Lihat Menu', 'href' => frontend_url('pembeli/menu/index.php'), 'variant' => 'secondary'],
    ],
], $content);
require base_path('backend/includes/footer.php');
