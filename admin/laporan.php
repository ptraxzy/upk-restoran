<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Laporan Penjualan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<style>
    /* Override shell for Light Mode specific to this page */
    .dashboard-main { background-color: #ffffff; color: #111111; }
    .dashboard-topbar-title { color: #111111 !important; }
    .dashboard-topbar-desc { color: #666666 !important; }
    .text-warning { color: #d4af37 !important; }
    .table { --bs-table-color: #111111; --bs-table-border-color: #eeeeee; }
    .table thead th { color: #888888; border-bottom: 1px solid #dddddd; }
    .table tbody td { border-bottom: 1px solid #eeeeee; color: #111111; }
</style>

<section class="d-flex flex-column gap-5">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 border-bottom pb-4" style="border-color: #eeeeee !important;">
        <div class="d-flex gap-4">
            <a class="text-dark text-uppercase small letter-spacing-1 fw-medium text-decoration-none pb-2 border-bottom border-dark border-2" href="#">HARI INI</a>
            <a class="text-secondary text-uppercase small letter-spacing-1 text-decoration-none pb-2" href="#">BULAN INI</a>
            <a class="text-secondary text-uppercase small letter-spacing-1 text-decoration-none pb-2" href="#">KUSTOM</a>
        </div>
        <div>
            <button class="btn btn-outline-dark rounded-0 small text-uppercase letter-spacing-1 fw-medium px-4 py-2" style="font-size: 10px;">Export Laporan</button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4 d-flex flex-column gap-4">
            <div class="p-4 border" style="border-color: #eeeeee;">
                <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">Total Pendapatan</p>
                <p class="font-display text-dark mb-0" style="font-size: 32px;">Rp 124.500.000</p>
                <p class="small text-success mt-2 mb-0">+12.5% vs bulan lalu</p>
            </div>
            <div class="p-4 border" style="border-color: #eeeeee;">
                <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">Total Transaksi</p>
                <p class="font-display text-dark mb-0" style="font-size: 32px;">1,432</p>
                <p class="small text-success mt-2 mb-0">+8.2% vs bulan lalu</p>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="p-4 border h-100" style="border-color: #eeeeee;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <p class="text-secondary small text-uppercase letter-spacing-1 m-0">Grafik Pendapatan</p>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-secondary"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                </div>
                <!-- Placeholder Grafik Line Emas -->
                <div class="w-100 d-flex align-items-end" style="height: 200px; position: relative;">
                    <svg viewBox="0 0 100 40" class="w-100 h-100" preserveAspectRatio="none">
                        <path d="M0 35 Q 20 5, 40 25 T 80 15 L 100 5" fill="none" stroke="#d4af37" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="20" cy="20" r="1.5" fill="#d4af37"/>
                        <circle cx="60" cy="25" r="1.5" fill="#d4af37"/>
                        <circle cx="80" cy="15" r="1.5" fill="#d4af37"/>
                    </svg>
                    <div class="d-flex justify-content-between w-100 position-absolute bottom-0 text-secondary" style="font-size: 10px;">
                        <span>W1</span><span>W2</span><span>W3</span><span>W4</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-lg-5">
            <h4 class="font-display text-dark mb-4" style="font-size: 24px;">Menu Terlaris</h4>
            <div class="d-flex flex-column gap-3">
                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom" style="border-color: #eeeeee !important;">
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&w=100&q=80" alt="Truffle Risotto" style="width: 48px; height: 48px; object-fit: cover;">
                        <div>
                            <p class="fw-medium text-dark m-0" style="font-size: 13px;">Truffle Risotto</p>
                            <p class="text-secondary small m-0">124 PORSI</p>
                        </div>
                    </div>
                    <span class="fw-medium text-dark small">Rp 48.2M</span>
                </div>
                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom" style="border-color: #eeeeee !important;">
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1558030006-450675393462?auto=format&fit=crop&w=100&q=80" alt="Wagyu A5 Striploin" style="width: 48px; height: 48px; object-fit: cover;">
                        <div>
                            <p class="fw-medium text-dark m-0" style="font-size: 13px;">Wagyu A5 Striploin</p>
                            <p class="text-secondary small m-0">98 PORSI</p>
                        </div>
                    </div>
                    <span class="fw-medium text-dark small">Rp 72.5M</span>
                </div>
                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom" style="border-color: #eeeeee !important;">
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?auto=format&fit=crop&w=100&q=80" alt="Smoked Old Fashioned" style="width: 48px; height: 48px; object-fit: cover;">
                        <div>
                            <p class="fw-medium text-dark m-0" style="font-size: 13px;">Smoked Old Fashioned</p>
                            <p class="text-secondary small m-0">210 GELAS</p>
                        </div>
                    </div>
                    <span class="fw-medium text-dark small">Rp 21.5M</span>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="font-display text-dark m-0" style="font-size: 24px;">Transaksi Terbaru</h4>
                <a href="#" class="text-dark small text-uppercase letter-spacing-1 fw-medium text-decoration-none border-bottom border-dark">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>ID PESANAN</th>
                            <th>WAKTU</th>
                            <th>MEJA</th>
                            <th>TOTAL</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-medium">#ORD-0821</td>
                            <td>20:45</td>
                            <td>Meja 12</td>
                            <td>Rp 4.250.000</td>
                            <td><span class="text-success small fw-medium letter-spacing-1">SELESAI</span></td>
                        </tr>
                        <tr>
                            <td class="fw-medium">#ORD-0820</td>
                            <td>20:30</td>
                            <td>VIP Room B</td>
                            <td>Rp 12.400.000</td>
                            <td><span class="text-success small fw-medium letter-spacing-1">SELESAI</span></td>
                        </tr>
                        <tr>
                            <td class="fw-medium">#ORD-0819</td>
                            <td>20:15</td>
                            <td>Meja 04</td>
                            <td>Rp 1.650.000</td>
                            <td><span class="text-success small fw-medium letter-spacing-1">SELESAI</span></td>
                        </tr>
                        <tr>
                            <td class="fw-medium">#ORD-0818</td>
                            <td>19:50</td>
                            <td>Bar 02</td>
                            <td>Rp 650.000</td>
                            <td><span class="text-success small fw-medium letter-spacing-1">SELESAI</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'brand' => 'NOCTRA',
    'badge' => 'Administration',
    'title' => 'Laporan Penjualan',
    'description' => 'Ringkasan performa dan pendapatan operasional.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
