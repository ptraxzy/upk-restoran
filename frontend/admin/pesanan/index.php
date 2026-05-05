<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('admin');

$title = 'Manajemen Pesanan';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

ob_start();
?>
<section class="content-grid">
    <article class="section-panel">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h3 class="section-title">Daftar Pesanan</h3>
                <p class="section-subtitle">Ringkasan pesanan aktif, status, dan nilai transaksi per meja atau delivery.</p>
            </div>
            <a class="cta-secondary" href="<?= htmlspecialchars(frontend_url('karyawan/pesanan/status.php'), ENT_QUOTES, 'UTF-8'); ?>">Lihat Status Kasir</a>
        </div>

        <table class="dark-table mt-6">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Tamu</th>
                    <th>Menu</th>
                    <th>Status</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#A-204</td>
                    <td>Elix Thorne</td>
                    <td>Truffle Risotto</td>
                    <td><span class="badge badge-gold">Diproses</span></td>
                    <td>Rp195.000</td>
                </tr>
                <tr>
                    <td>#A-205</td>
                    <td>Clara Vance</td>
                    <td>Wagyu Selection</td>
                    <td><span class="badge badge-muted">Selesai</span></td>
                    <td>Rp420.000</td>
                </tr>
                <tr>
                    <td>#A-206</td>
                    <td>Lucas Mercer</td>
                    <td>Autumn Potage</td>
                    <td><span class="badge badge-muted">Dibatalkan</span></td>
                    <td>Rp98.000</td>
                </tr>
            </tbody>
        </table>
    </article>

    <aside class="section-panel">
        <h3 class="section-title">Pesanan Andalan</h3>
        <div class="list-stack mt-5">
            <div class="stack-item">
                <div>
                    <p class="font-medium text-stone-100">Truffle Mushroom Risotto</p>
                    <p class="mt-2 text-sm text-stone-400">7 pesanan aktif pada service malam ini.</p>
                </div>
                <span class="badge badge-gold">Hot</span>
            </div>
            <div class="stack-item">
                <div>
                    <p class="font-medium text-stone-100">A5 Wagyu Ribeye</p>
                    <p class="mt-2 text-sm text-stone-400">Mendorong tiket rata-rata tertinggi.</p>
                </div>
                <span class="badge badge-muted">Premium</span>
            </div>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administration',
    'title' => 'Manajemen Pesanan',
    'description' => 'Pusat kendali pesanan untuk melihat arus service dan keputusan operasional yang perlu diambil cepat.',
    'nav_sections' => admin_nav_sections(),
], $content);
require base_path('backend/includes/footer.php');
