<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('kasir');

$title = 'Pesanan Karyawan';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

ob_start();
?>
<section class="section-panel">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <h3 class="section-title">Pesanan Masuk</h3>
            <p class="section-subtitle">Daftar pesanan operasional untuk diproses, dikonfirmasi, atau diteruskan ke kitchen.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a class="cta-secondary" href="<?= htmlspecialchars(frontend_url('karyawan/pesanan/status.php'), ENT_QUOTES, 'UTF-8'); ?>">Update Status</a>
            <a class="cta-primary" href="<?= htmlspecialchars(frontend_url('karyawan/pesanan/create.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah Pesanan</a>
        </div>
    </div>

    <div class="order-tabs mt-6">
        <a class="order-tab order-tab-active" href="#">Semua</a>
        <a class="order-tab" href="#">Diproses</a>
        <a class="order-tab" href="#">Selesai</a>
        <a class="order-tab" href="#">Dibatalkan</a>
    </div>

    <table class="dark-table mt-6">
        <thead>
            <tr>
                <th>No. Pesanan</th>
                <th>Waktu</th>
                <th>Item</th>
                <th>Total</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>#K-110</td>
                <td>19:42</td>
                <td>Autumn Potage, Burrata Salad, Sparkling Water</td>
                <td>Rp 875.000</td>
                <td><span class="badge badge-gold">Diproses</span></td>
                <td><a class="action-link action-link-gold" href="<?= htmlspecialchars(frontend_url('karyawan/pesanan/status.php'), ENT_QUOTES, 'UTF-8'); ?>">Proses</a></td>
            </tr>
            <tr>
                <td>#K-111</td>
                <td>20:05</td>
                <td>A5 Wagyu Ribeye, Wine Pairing</td>
                <td>Rp 1.450.000</td>
                <td><span class="badge badge-muted">Siap Bayar</span></td>
                <td><a class="action-link" href="<?= htmlspecialchars(frontend_url('karyawan/pesanan/status.php'), ENT_QUOTES, 'UTF-8'); ?>">Selesai</a></td>
            </tr>
        </tbody>
    </table>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Service Floor',
    'title' => 'Incoming Orders',
    'description' => 'Pantau dan kelola antrian pesanan dengan cepat dan efisien.',
    'nav_sections' => staff_nav_sections(),
], $content);
require base_path('backend/includes/footer.php');
