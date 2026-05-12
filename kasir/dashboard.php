<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('kasir');

$title = 'Dashboard Karyawan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="row row-cols-1 row-cols-md-3 g-3 mb-4">
    <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
        <p class="text-muted small text-uppercase mb-2">Pesanan Aktif</p>
        <p class="h2 text-warning mb-0">24</p>
        <p class="metric-note">Arus service malam didominasi tasting menu dan premium pairings.</p>
    </article>
    <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
        <p class="text-muted small text-uppercase mb-2">Pesanan Selesai</p>
        <p class="h2 text-warning mb-0">12</p>
        <p class="metric-note">Terselesaikan dalam shift malam tanpa eskalasi kitchen.</p>
    </article>
    <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
        <p class="text-muted small text-uppercase mb-2">Total Shift</p>
        <p class="h2 text-warning mb-0">8j</p>
        <p class="metric-note">Service berjalan dalam window operasional malam hingga pukul 22:00.</p>
    </article>
    <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
        <p class="text-muted small text-uppercase mb-2">Status Meja</p>
        <p class="h2 text-warning mb-0">08</p>
        <p class="metric-note">Meja aktif masih menunggu update plating dan pembayaran.</p>
    </article>
</section>

<section class="row row-cols-1 row-cols-lg-2 g-4 mt-4">
    <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <div class="d-flex flex-column gap-2 flex-md-row align-items-md-end justify-content-md-between">
            <div>
                <h3 class="h3 mb-1 text-warning">Daftar Pesanan Masuk</h3>
                <p class="text-muted small mb-4">Daftar pesanan aktif saat ini.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-warning rounded-0 text-uppercase fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('kasir/pesanan_status.php'), ENT_QUOTES, 'UTF-8'); ?>">Update Status</a>
                <a class="btn btn-warning rounded-0 text-uppercase fw-medium px-4 py-2" href="<?= htmlspecialchars(base_url('karyawan/pesanan/create.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah Pesanan</a>
            </div>
        </div>

        <table class="table table-dark table-hover table-bordered border-secondary mt-4 mb-0 mt-4">
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
                    <td><span class="badge badge bg-warning text-dark">Diproses</span></td>
                </tr>
                <tr>
                    <td>#K-111</td>
                    <td>Luca Stone</td>
                    <td>A5 Wagyu Ribeye</td>
                    <td>VIP-2</td>
                    <td><span class="badge badge bg-secondary text-light">Siap Bayar</span></td>
                </tr>
            </tbody>
        </table>
    </article>

    <aside class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <h3 class="h3 mb-1 text-warning">Layanan Aktif</h3>
        <div class="compact-list mt-4">
            <div class="compact-list-item">
                <div>
                    <p class="fw-medium text-light">Meja 04</p>
                    <p class="mt-2 small text-muted">Black Truffle Risotto • Wagyu Ribeye A5</p>
                </div>
                <span class="badge badge bg-warning text-dark">3 Item</span>
            </div>
            <div class="compact-list-item">
                <div>
                    <p class="fw-medium text-light">Meja 12</p>
                    <p class="mt-2 small text-muted">Oyster Hot Caesar • Smoked Scallops Pairing</p>
                </div>
                <span class="badge badge bg-secondary text-light">4 Item</span>
            </div>
            <div class="compact-list-item">
                <div>
                    <p class="fw-medium text-light">Meja 08</p>
                    <p class="mt-2 small text-muted">Complimentary Dessert • Espresso</p>
                </div>
                <span class="badge badge bg-secondary text-light">2 Item</span>
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
require __DIR__ . '/../includes/footer.php';
