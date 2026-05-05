<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('kasir');

$title = 'Pembayaran';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

ob_start();
?>
<section class="content-grid">
    <article class="section-panel">
        <h3 class="section-title">Cetak Struk</h3>
        <p class="section-subtitle">Penyelesaian transaksi untuk pesanan pelanggan.</p>

        <div class="mx-auto mt-6 max-w-[320px] border border-white/10 bg-white/[0.03] p-6 text-center">
            <p class="font-display text-xl text-brass">L'ART CULINAIRE</p>
            <p class="mt-2 text-xs text-stone-500">Jl. Saffron No. 45, Jakarta Selatan</p>
            <div class="mt-6 space-y-2 text-xs text-stone-400">
                <div class="flex justify-between"><span>Tanggal</span><span>24/04/2026 20:05</span></div>
                <div class="flex justify-between"><span>Waiter</span><span>ID: W-24</span></div>
                <div class="flex justify-between"><span>Meja</span><span>08 VIP</span></div>
                <div class="flex justify-between"><span>No. Struk</span><span>#RCP-240724</span></div>
            </div>
            <div class="mt-6 space-y-3 border-t border-b border-white/8 py-4 text-left text-sm">
                <div class="flex justify-between"><span>Truffle Risotto</span><span>Rp 850.000</span></div>
                <div class="flex justify-between"><span>Wagyu A5 Striploin</span><span>Rp 1.200.000</span></div>
                <div class="flex justify-between"><span>Crafted Mixology</span><span>Rp 520.000</span></div>
            </div>
            <div class="mt-4 space-y-2 text-xs text-stone-400">
                <div class="flex justify-between"><span>Subtotal</span><span>Rp 2.570.000</span></div>
                <div class="flex justify-between"><span>Tax 10%</span><span>Rp 256.000</span></div>
                <div class="flex justify-between"><span>Service 5%</span><span>Rp 128.500</span></div>
            </div>
            <div class="mt-4 flex justify-between border-t border-white/8 pt-4 font-display text-lg text-brass">
                <span>Total</span><span>Rp 3.016.000</span>
            </div>
        </div>
    </article>

    <aside class="section-panel">
        <h3 class="section-title">Ringkasan Kas</h3>
        <div class="metric-grid mt-5 !grid-cols-1">
            <article class="metric-card">
                <p class="metric-label">Transaksi Hari Ini</p>
                <p class="metric-value !text-3xl">31</p>
            </article>
            <article class="metric-card">
                <p class="metric-label">Belum Dibayar</p>
                <p class="metric-value !text-3xl">6</p>
            </article>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Service Floor',
    'title' => 'Cetak Struk',
    'description' => 'Modul kasir untuk memproses pembayaran dan mencetak struk.',
    'nav_sections' => staff_nav_sections(),
], $content);
require base_path('backend/includes/footer.php');
