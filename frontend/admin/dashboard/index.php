<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('admin');

$title = 'Dashboard Admin';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

ob_start();
?>
<section class="metric-grid">
    <article class="metric-card">
        <p class="metric-label">Total Pendapatan</p>
        <p class="metric-value">Rp24,85jt</p>
        <p class="metric-note">Naik 12% dari minggu lalu dengan reservasi dinner yang lebih padat.</p>
    </article>
    <article class="metric-card">
        <p class="metric-label">Pesanan Aktif</p>
        <p class="metric-value">142</p>
        <p class="metric-note">Layanan makan malam mendominasi arus pesanan malam ini.</p>
    </article>
    <article class="metric-card">
        <p class="metric-label">Menu Premium</p>
        <p class="metric-value">18</p>
        <p class="metric-note">Menu signature masih dipimpin Truffle Risotto dan Wagyu Selection.</p>
    </article>
    <article class="metric-card">
        <p class="metric-label">Karyawan Aktif</p>
        <p class="metric-value">12</p>
        <p class="metric-note">Shift malam terisi penuh untuk service, kasir, dan plating counter.</p>
    </article>
</section>

<section class="content-grid mt-6">
    <div class="space-y-6">
        <article class="section-panel">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <h3 class="section-title">Ringkasan Operasional</h3>
                    <p class="section-subtitle">Pantau aktivitas restoran secara real-time, dari reservasi meja hingga status pesanan malam ini.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a class="cta-secondary" href="<?= htmlspecialchars(frontend_url('admin/laporan/index.php'), ENT_QUOTES, 'UTF-8'); ?>">Lihat Laporan</a>
                    <a class="cta-primary" href="<?= htmlspecialchars(frontend_url('admin/menu/create.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah Menu</a>
                </div>
            </div>

            <table class="dark-table mt-6">
                <thead>
                    <tr>
                        <th>Area</th>
                        <th>Fokus</th>
                        <th>Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Main Dining</td>
                        <td>Service Dinner</td>
                        <td><span class="badge badge-gold">Padat</span></td>
                        <td>Reservasi setelah pukul 19:00 masih dominan.</td>
                    </tr>
                    <tr>
                        <td>Kitchen Pass</td>
                        <td>Plating Premium</td>
                        <td><span class="badge badge-muted">Stabil</span></td>
                        <td>Signature tasting menu berjalan sesuai target waktu.</td>
                    </tr>
                    <tr>
                        <td>Kasir</td>
                        <td>Pembayaran</td>
                        <td><span class="badge badge-gold">Aktif</span></td>
                        <td>Lonjakan transaksi terjadi pada paket wine pairing.</td>
                    </tr>
                </tbody>
            </table>
        </article>

        <article class="section-panel">
            <h3 class="section-title">Shortcut Manajemen</h3>
            <div class="mini-card-grid mt-6">
                <a class="role-card min-h-0" href="<?= htmlspecialchars(frontend_url('admin/menu/index.php'), ENT_QUOTES, 'UTF-8'); ?>">
                    <div>
                        <p class="eyebrow">Catalog</p>
                        <h4 class="mt-3 font-display text-2xl text-stone-50">Menu</h4>
                        <p class="menu-card-copy">Kelola daftar menu, kategori, dan pembaruan item signature.</p>
                    </div>
                </a>
                <a class="role-card min-h-0" href="<?= htmlspecialchars(frontend_url('admin/pesanan/index.php'), ENT_QUOTES, 'UTF-8'); ?>">
                    <div>
                        <p class="eyebrow">Orders</p>
                        <h4 class="mt-3 font-display text-2xl text-stone-50">Pesanan</h4>
                        <p class="menu-card-copy">Lihat alur pesanan, status, dan tekanan operasional malam ini.</p>
                    </div>
                </a>
            </div>
        </article>
    </div>

    <aside class="space-y-6">
        <article class="chart-panel">
            <div class="flex items-end justify-between">
                <div>
                    <p class="metric-label">Total Pendapatan</p>
                    <p class="metric-value">Rp 24.850.000</p>
                </div>
                <span class="badge badge-gold">+12%</span>
            </div>
            <div class="mt-6 h-56 bg-[radial-gradient(circle_at_right,rgba(200,169,107,0.3),transparent_20%),linear-gradient(180deg,rgba(255,255,255,0.05),rgba(255,255,255,0.01))]"></div>
        </article>

        <article class="section-panel">
            <h3 class="section-title">Agenda Admin</h3>
            <div class="compact-list mt-5">
                <div class="compact-list-item">
                    <div>
                        <p class="font-medium text-stone-100">Audit stok premium cuts</p>
                        <p class="mt-2 text-sm text-stone-500">Pastikan wagyu, foie gras, dan scallop tersedia untuk weekend service.</p>
                    </div>
                    <span class="badge badge-muted">20:00</span>
                </div>
                <div class="compact-list-item">
                    <div>
                        <p class="font-medium text-stone-100">Review laporan harian</p>
                        <p class="mt-2 text-sm text-stone-500">Bandingkan average ticket dengan tasting menu bulan lalu.</p>
                    </div>
                    <span class="badge badge-muted">21:30</span>
                </div>
            </div>
        </article>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administration',
    'title' => 'Ringkasan',
    'description' => 'Pusat pemantauan menu, pesanan, tim, dan performa keseluruhan restoran Anda.',
    'nav_sections' => admin_nav_sections(),
], $content);

require base_path('backend/includes/footer.php');
