<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('kasir');

$title = 'Dashboard Karyawan';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

ob_start();
?>
<section class="metric-grid">
    <article class="metric-card">
        <p class="metric-label">Pesanan Aktif</p>
        <p class="metric-value">24</p>
        <p class="metric-note">Arus service malam didominasi tasting menu dan premium pairings.</p>
    </article>
    <article class="metric-card">
        <p class="metric-label">Pesanan Selesai</p>
        <p class="metric-value">12</p>
        <p class="metric-note">Terselesaikan dalam shift malam tanpa eskalasi kitchen.</p>
    </article>
    <article class="metric-card">
        <p class="metric-label">Total Shift</p>
        <p class="metric-value">8j</p>
        <p class="metric-note">Service berjalan dalam window operasional malam hingga pukul 22:00.</p>
    </article>
    <article class="metric-card">
        <p class="metric-label">Status Meja</p>
        <p class="metric-value">08</p>
        <p class="metric-note">Meja aktif masih menunggu update plating dan pembayaran.</p>
    </article>
</section>

<section class="content-grid mt-6">
    <article class="section-panel">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h3 class="section-title">Daftar Pesanan Masuk</h3>
                <p class="section-subtitle">Daftar pesanan aktif saat ini.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a class="cta-secondary" href="<?= htmlspecialchars(frontend_url('karyawan/pesanan/status.php'), ENT_QUOTES, 'UTF-8'); ?>">Update Status</a>
                <a class="cta-primary" href="<?= htmlspecialchars(frontend_url('karyawan/pesanan/create.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah Pesanan</a>
            </div>
        </div>

        <table class="dark-table mt-6">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Tamu</th>
                    <th>Menu</th>
                    <th>Meja</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#K-110</td>
                    <td>Naomi Hart</td>
                    <td>Autumn Potage</td>
                    <td>06</td>
                    <td><span class="badge badge-gold">Diproses</span></td>
                </tr>
                <tr>
                    <td>#K-111</td>
                    <td>Luca Stone</td>
                    <td>A5 Wagyu Ribeye</td>
                    <td>VIP-2</td>
                    <td><span class="badge badge-muted">Siap Bayar</span></td>
                </tr>
            </tbody>
        </table>
    </article>

    <aside class="section-panel">
        <h3 class="section-title">Layanan Aktif</h3>
        <div class="compact-list mt-5">
            <div class="compact-list-item">
                <div>
                    <p class="font-medium text-stone-100">Meja 04</p>
                    <p class="mt-2 text-sm text-stone-500">Black Truffle Risotto • Wagyu Ribeye A5</p>
                </div>
                <span class="badge badge-gold">3 Item</span>
            </div>
            <div class="compact-list-item">
                <div>
                    <p class="font-medium text-stone-100">Meja 12</p>
                    <p class="mt-2 text-sm text-stone-500">Oyster Hot Caesar • Smoked Scallops Pairing</p>
                </div>
                <span class="badge badge-muted">4 Item</span>
            </div>
            <div class="compact-list-item">
                <div>
                    <p class="font-medium text-stone-100">Meja 08</p>
                    <p class="mt-2 text-sm text-stone-500">Complimentary Dessert • Espresso</p>
                </div>
                <span class="badge badge-muted">2 Item</span>
            </div>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Service Floor',
    'title' => 'Selamat malam, ' . ($_SESSION['user_name'] ?? 'Kasir') . '.',
    'description' => 'SHIFT HARI INI',
    'nav_sections' => staff_nav_sections(),
], $content);
require base_path('backend/includes/footer.php');
