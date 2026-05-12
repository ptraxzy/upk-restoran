<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Ringkasan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-5">
    <div class="col">
        <article class="card p-4 h-100">
            <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">Total Pendapatan</p>
            <p class="h2 text-gold font-display mb-0">Rp 24,85jt</p>
            <p class="metric-note">Naik 12% dari minggu lalu dengan tingkat pesanan makan malam (dine-in) yang padat.</p>
        </article>
    </div>
    <div class="col">
        <article class="card p-4 h-100">
            <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">Pesanan Aktif</p>
            <p class="h2 text-gold font-display mb-0">142</p>
            <p class="metric-note">Pesanan layanan makan malam mendominasi arus masuk kitchen malam ini.</p>
        </article>
    </div>
    <div class="col">
        <article class="card p-4 h-100">
            <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">Menu Premium</p>
            <p class="h2 text-gold font-display mb-0">18</p>
            <p class="metric-note">Menu signature masih dipimpin Truffle Risotto dan Wagyu Selection.</p>
        </article>
    </div>
    <div class="col">
        <article class="card p-4 h-100">
            <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">Karyawan Aktif</p>
            <p class="h2 text-gold font-display mb-0">12</p>
            <p class="metric-note">Shift malam terisi penuh untuk service, kasir, dan plating counter.</p>
        </article>
    </div>
</section>

<section class="row g-5">
    <div class="col-lg-8 d-flex flex-column gap-5">
        <article class="section-panel">
            <div class="panel-header d-flex flex-column gap-3 flex-md-row justify-content-md-between align-items-md-end">
                <div>
                    <h3 class="panel-title">Ringkasan Operasional</h3>
                    <p class="panel-desc">Pantau aktivitas restoran secara real-time, dari tingkat hunian meja hingga status pesanan malam ini.</p>
                </div>
                <div class="d-flex flex-wrap gap-3">
                    <a class="btn btn-outline-warning" href="<?= htmlspecialchars(base_url('admin/laporan.php'), ENT_QUOTES, 'UTF-8'); ?>">Lihat Laporan</a>
                    <a class="btn btn-warning" href="<?= htmlspecialchars(base_url('admin/menu_tambah.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah Menu</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
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
                            <td><span class="badge bg-warning">Padat</span></td>
                            <td>Tingkat hunian meja penuh, arus pesanan tinggi.</td>
                        </tr>
                        <tr>
                            <td>Kitchen Pass</td>
                            <td>Plating Premium</td>
                            <td><span class="badge bg-secondary">Stabil</span></td>
                            <td>Signature tasting menu berjalan sesuai target waktu.</td>
                        </tr>
                        <tr>
                            <td>Kasir</td>
                            <td>Pembayaran</td>
                            <td><span class="badge bg-success">Aktif</span></td>
                            <td>Lonjakan transaksi terjadi pada paket wine pairing.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </article>

        <article class="section-panel">
            <h3 class="panel-title mb-4">Shortcut Manajemen</h3>
            <div class="mini-card-grid">
                <a class="role-card text-decoration-none" href="<?= htmlspecialchars(base_url('admin/menu.php'), ENT_QUOTES, 'UTF-8'); ?>">
                    <p class="text-secondary small text-uppercase letter-spacing-2 mb-1">Catalog</p>
                    <h4 class="font-display text-white mb-0" style="font-size: 28px;">Menu</h4>
                    <p class="menu-card-copy">Kelola daftar menu, kategori, dan pembaruan item signature.</p>
                </a>
                <a class="role-card text-decoration-none" href="<?= htmlspecialchars(base_url('admin/pesanan/index.php'), ENT_QUOTES, 'UTF-8'); ?>">
                    <p class="text-secondary small text-uppercase letter-spacing-2 mb-1">Orders</p>
                    <h4 class="font-display text-white mb-0" style="font-size: 28px;">Pesanan</h4>
                    <p class="menu-card-copy">Lihat alur pesanan, status, dan tekanan operasional malam ini.</p>
                </a>
            </div>
        </article>
    </div>

    <aside class="col-lg-4 d-flex flex-column gap-5">
        <article class="chart-panel">
            <div class="d-flex align-items-end justify-content-between position-relative z-1">
                <div>
                    <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">Total Pendapatan</p>
                    <p class="h3 text-gold font-display mb-0">Rp 24.850.000</p>
                </div>
                <span class="badge bg-warning">+12%</span>
            </div>
            <!-- Decorative Background for Chart Panel -->
            <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 120px; background: linear-gradient(to top, rgba(212, 175, 55, 0.1), transparent); z-index: 0;"></div>
            <div style="position: absolute; bottom: -20px; right: -20px; width: 150px; height: 150px; border-radius: 50%; background: radial-gradient(circle, rgba(212, 175, 55, 0.15) 0%, transparent 70%); z-index: 0;"></div>
        </article>

        <article class="section-panel">
            <h3 class="panel-title mb-4">Agenda Admin</h3>
            <div class="compact-list">
                <div class="compact-list-item">
                    <div>
                        <p class="fw-medium text-white mb-1">Audit stok premium cuts</p>
                        <p class="small text-secondary mb-0">Pastikan wagyu, foie gras, dan scallop tersedia untuk weekend service.</p>
                    </div>
                    <span class="badge bg-secondary ms-3">20:00</span>
                </div>
                <div class="compact-list-item">
                    <div>
                        <p class="fw-medium text-white mb-1">Review laporan harian</p>
                        <p class="small text-secondary mb-0">Bandingkan average ticket dengan tasting menu bulan lalu.</p>
                    </div>
                    <span class="badge bg-secondary ms-3">21:30</span>
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

require __DIR__ . '/../includes/footer.php';
