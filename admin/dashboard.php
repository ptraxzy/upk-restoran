<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Ikhtisar';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-5 animate-fade-in-up">
    <div class="col">
        <article class="card h-100 p-4">
            <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">Pemasukan Hari Ini</p>
            <p class="h2 text-white font-display mb-0">Rp 24,85jt</p>
            <p class="metric-note">Ada kenaikan 12% dibanding pekan lalu karena ramainya kunjungan makan malam.</p>
        </article>
    </div>
    <div class="col">
        <article class="card h-100 p-4">
            <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">Meja Terisi</p>
            <p class="h2 text-white font-display mb-0">142</p>
            <p class="metric-note">Alur pesanan dari meja pelanggan sedang meningkat saat ini.</p>
        </article>
    </div>
    <div class="col">
        <article class="card h-100 p-4">
            <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">Menu Andalan</p>
            <p class="h2 text-white font-display mb-0">18</p>
            <p class="metric-note">Hidangan seperti Truffle Risotto masih menjadi pilihan utama para tamu.</p>
        </article>
    </div>
    <div class="col">
        <article class="card h-100 p-4">
            <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">Tim Bertugas</p>
            <p class="h2 text-white font-display mb-0">12</p>
            <p class="metric-note">Seluruh staf di bagian pelayanan dan dapur siap melayani pesanan.</p>
        </article>
    </div>
</section>

<section class="row g-5 animate-fade-in-up" style="animation-delay: 0.2s;">
    <div class="col-lg-8">
        <article class="section-panel h-100">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom border-soft pb-4 mb-4 gap-3">
                <div>
                    <h3 class="font-display text-white m-0" style="font-size: 24px;">Status Restoran</h3>
                    <p class="text-secondary small mb-0 mt-1">Pantauan aktivitas terkini di seluruh area operasional.</p>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-warning py-2 px-3" style="font-size: 10px;" href="<?= htmlspecialchars(base_url('admin/laporan.php'), ENT_QUOTES, 'UTF-8'); ?>">Buka Laporan</a>
                    <a class="btn btn-warning py-2 px-3" style="font-size: 10px;" href="<?= htmlspecialchars(base_url('admin/menu_tambah.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah Menu</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Posisi</th>
                            <th>Fokus Kerja</th>
                            <th>Kondisi</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Ruang Utama</td>
                            <td>Pelayanan Tamu</td>
                            <td><span class="badge bg-warning text-dark">Ramai</span></td>
                            <td>Hampir seluruh meja terisi, pesanan berjalan cepat.</td>
                        </tr>
                        <tr>
                            <td>Dapur</td>
                            <td>Pengolahan Menu</td>
                            <td><span class="badge bg-secondary">Lancar</span></td>
                            <td>Proses masak berjalan tepat waktu sesuai standar dapur.</td>
                        </tr>
                        <tr>
                            <td>Kasir</td>
                            <td>Administrasi</td>
                            <td><span class="badge bg-success">Aktif</span></td>
                            <td>Proses transaksi berjalan tanpa kendala berarti malam ini.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </article>
    </div>

    <aside class="col-lg-4">
        <article class="section-panel h-100">
            <h3 class="font-display text-white mb-4" style="font-size: 24px;">Catatan Hari Ini</h3>
            <div class="d-flex flex-column gap-4">
                <div class="pb-3 border-bottom border-soft">
                    <p class="fw-medium text-white mb-1" style="font-size: 14px;">Periksa stok daging pilihan</p>
                    <p class="small text-secondary mb-0">Pastikan stok Wagyu dan Scallop cukup untuk akhir pekan.</p>
                    <span class="badge bg-secondary mt-2">Pukul 20:00</span>
                </div>
                <div class="pb-3 border-bottom border-soft">
                    <p class="fw-medium text-white mb-1" style="font-size: 14px;">Evaluasi pendapatan harian</p>
                    <p class="small text-secondary mb-0">Bandingkan total rata-rata pesanan dengan bulan sebelumnya.</p>
                    <span class="badge bg-secondary mt-2">Pukul 21:30</span>
                </div>
            </div>
        </article>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administrasi',
    'title' => 'Ringkasan Aktivitas',
    'description' => 'Pusat pantauan menu, pesanan, dan tim operasional restoran.',
    'nav_sections' => admin_nav_sections(),
], $content);

require __DIR__ . '/../includes/footer.php';
