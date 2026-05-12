<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('kasir');

$title = 'Pembayaran';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="row row-cols-1 row-cols-lg-2 g-4">
    <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <div class="d-flex flex-column gap-2 flex-md-row align-items-md-end justify-content-md-between">
            <div>
                <h3 class="h3 mb-1 text-warning">Manajemen Pembayaran</h3>
                <p class="text-muted small mb-4">Penyelesaian transaksi untuk pesanan pelanggan.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-warning rounded-0 text-uppercase fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('kasir/pembayaran_riwayat.php'), ENT_QUOTES, 'UTF-8'); ?>">Lihat Riwayat Transaksi</a>
                <a class="btn btn-outline-warning rounded-0 text-uppercase fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('kasir/pembayaran_status.php'), ENT_QUOTES, 'UTF-8'); ?>">Lihat Status Pembayaran</a>
                <a class="btn btn-warning rounded-0 text-uppercase fw-medium px-4 py-2" href="<?= htmlspecialchars(base_url('kasir/pembayaran_cetak.php'), ENT_QUOTES, 'UTF-8'); ?>">Cetak Struk</a>
            </div>
        </div>

        <div class="mx-auto mt-4 max-w-[320px] border border-secondary bg-white/[0.03] p-6 text-center">
            <p class="font-display text-xl text-brass">L'ART CULINAIRE</p>
            <p class="mt-2 small text-muted">Jl. Saffron No. 45, Jakarta Selatan</p>
            <div class="mt-4 d-grid gap-2 small text-secondary">
                <div class="d-flex justify-content-between"><span>Tanggal</span><span>24/04/2026 20:05</span></div>
                <div class="d-flex justify-content-between"><span>Waiter</span><span>ID: W-24</span></div>
                <div class="d-flex justify-content-between"><span>Meja</span><span>08 VIP</span></div>
                <div class="d-flex justify-content-between"><span>No. Struk</span><span>#RCP-240724</span></div>
            </div>
            <div class="mt-4 d-grid gap-3 border-t border-b border-secondary py-4 text-left small">
                <div class="d-flex justify-content-between"><span>Truffle Risotto</span><span>Rp 850.000</span></div>
                <div class="d-flex justify-content-between"><span>Wagyu A5 Striploin</span><span>Rp 1.200.000</span></div>
                <div class="d-flex justify-content-between"><span>Crafted Mixology</span><span>Rp 520.000</span></div>
            </div>
            <div class="mt-4 d-grid gap-2 small text-secondary">
                <div class="d-flex justify-content-between"><span>Subtotal</span><span>Rp 2.570.000</span></div>
                <div class="d-flex justify-content-between"><span>Tax 10%</span><span>Rp 256.000</span></div>
                <div class="d-flex justify-content-between"><span>Service 5%</span><span>Rp 128.500</span></div>
            </div>
            <div class="mt-4 d-flex justify-content-between border-t border-secondary pt-4 font-display text-lg text-brass">
                <span>Total</span><span>Rp 3.016.000</span>
            </div>
        </div>
    </article>

    <aside class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <h3 class="h3 mb-1 text-warning">Ringkasan Kas</h3>
        <div class="row row-cols-1 row-cols-md-3 g-3 mb-4 mt-4">
            <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
                <p class="text-muted small text-uppercase mb-2">Transaksi Hari Ini</p>
                <p class="h2 text-warning mb-0 !text-3xl">31</p>
            </article>
            <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
                <p class="text-muted small text-uppercase mb-2">Belum Dibayar</p>
                <p class="h2 text-warning mb-0 !text-3xl">6</p>
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
require __DIR__ . '/../includes/footer.php';
