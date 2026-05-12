<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('kasir');

$title = 'Riwayat Transaksi';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="row row-cols-1 row-cols-lg-2 g-4">
    <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <div class="d-flex flex-column gap-2 flex-md-row align-items-md-end justify-content-md-between">
            <div>
                <h3 class="h3 mb-1 text-warning">Riwayat Transaksi</h3>
                <p class="text-muted small mb-4">Daftar seluruh transaksi pembayaran yang telah tercatat.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-warning rounded-0 text-uppercase fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('kasir/pembayaran.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
            </div>
        </div>

        <div class="nav-link text-white rounded-0s mt-4">
            <a class="nav-link active bg-warning text-dark rounded-0" href="#">Semua</a>
            <a class="nav-link text-white rounded-0" href="#">Hari Ini</a>
            <a class="nav-link text-white rounded-0" href="#">Minggu Ini</a>
            <a class="nav-link text-white rounded-0" href="#">Bulan Ini</a>
        </div>

        <table class="table table-dark table-hover table-bordered border-secondary mt-4 mb-0 mt-4">
            <thead>
                <tr>
                    <th>No. Struk</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#RCP-260512</td>
                    <td>12 Mei 2026</td>
                    <td>Naomi Hart</td>
                    <td>Rp 875.000</td>
                    <td>QRIS</td>
                    <td><span class="badge badge bg-secondary text-light">Lunas</span></td>
                </tr>
                <tr>
                    <td>#RCP-260511</td>
                    <td>11 Mei 2026</td>
                    <td>Luca Stone</td>
                    <td>Rp 1.450.000</td>
                    <td>QRIS</td>
                    <td><span class="badge badge bg-secondary text-light">Lunas</span></td>
                </tr>
                <tr>
                    <td>#RCP-260510</td>
                    <td>10 Mei 2026</td>
                    <td>Clara Vance</td>
                    <td>Rp 420.000</td>
                    <td>Tunai</td>
                    <td><span class="badge badge bg-secondary text-light">Lunas</span></td>
                </tr>
                <tr>
                    <td>#RCP-260509</td>
                    <td>09 Mei 2026</td>
                    <td>Elix Thorne</td>
                    <td>Rp 195.000</td>
                    <td>QRIS</td>
                    <td><span class="badge badge bg-secondary text-light">Lunas</span></td>
                </tr>
            </tbody>
        </table>
    </article>

    <aside class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <h3 class="h3 mb-1 text-warning">Ringkasan Kas</h3>
        <div class="row row-cols-1 row-cols-md-3 g-3 mb-4 mt-4">
            <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
                <p class="text-muted small text-uppercase mb-2">Total Transaksi Hari Ini</p>
                <p class="h2 text-warning mb-0 !text-3xl">8</p>
            </article>
            <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
                <p class="text-muted small text-uppercase mb-2">Pendapatan Hari Ini</p>
                <p class="h2 text-warning mb-0 !text-3xl">Rp 4.2jt</p>
            </article>
            <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
                <p class="text-muted small text-uppercase mb-2">Metode Terbanyak</p>
                <p class="h2 text-warning mb-0 !text-3xl">QRIS</p>
            </article>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Service Floor',
    'title' => 'Riwayat Transaksi',
    'description' => 'Riwayat lengkap seluruh transaksi pembayaran yang tercatat di sistem.',
    'nav_sections' => staff_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
