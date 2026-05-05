<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('admin');

$title = 'Laporan';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

ob_start();
?>
<section class="content-grid">
    <article class="section-panel">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h3 class="section-title">Laporan Penjualan</h3>
                <p class="section-subtitle">Ringkasan profit dan performa operasional.</p>
            </div>
            <div class="flex gap-2">
                <span class="badge badge-muted">Hari Ini</span>
                <span class="badge badge-muted">Bulanan</span>
                <span class="badge badge-gold">Kustom</span>
            </div>
        </div>
        <div class="mt-8 grid gap-5 lg:grid-cols-[220px_minmax(0,1fr)]">
            <div class="chart-panel">
                <p class="metric-label">Total Pendapatan</p>
                <p class="metric-value">Rp 24.150.000</p>
                <p class="mt-3 text-sm text-emerald-500">+12% dibanding bulan lalu</p>
                <div class="mt-8 border-t border-white/8 pt-4">
                    <p class="metric-label">Total Transaksi</p>
                    <p class="mt-3 font-display text-2xl text-stone-50">1.024</p>
                    <p class="mt-3 text-sm text-emerald-500">+5.2% vs minggu lalu</p>
                </div>
            </div>
            <div class="chart-panel">
                <div class="flex items-center justify-between">
                    <p class="metric-label">Grafik Pendapatan</p>
                    <span class="badge badge-gold">Live</span>
                </div>
                <div class="mt-6 h-64 bg-[radial-gradient(circle_at_right,rgba(200,169,107,0.35),transparent_20%),linear-gradient(180deg,rgba(255,255,255,0.06),rgba(255,255,255,0.01))]"></div>
            </div>
        </div>
    </article>

    <aside class="section-panel">
        <h3 class="section-title">Menu Terlaris</h3>
        <div class="compact-list mt-5">
            <div class="compact-list-item">
                <span>Truffle Risotto</span>
                <span class="badge badge-muted">320 porsi</span>
            </div>
            <div class="compact-list-item">
                <span>Wagyu A5 Striploin</span>
                <span class="badge badge-muted">275 porsi</span>
            </div>
            <div class="compact-list-item">
                <span>Smoked Chirashi</span>
                <span class="badge badge-muted">210 gelas</span>
            </div>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administration',
    'title' => 'Laporan Penjualan',
    'description' => 'Ringkasan komprehensif metrik penjualan, tren performa, dan laporan pendapatan.',
    'nav_sections' => admin_nav_sections(),
], $content);
require base_path('backend/includes/footer.php');
