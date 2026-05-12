<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('pelanggan');

$title = 'Riwayat Pesanan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="editorial-grid">
    <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <div class="d-flex flex-column gap-2 flex-md-row align-items-md-end justify-content-md-between">
            <div>
                <h3 class="h3 mb-1 text-warning">Riwayat Pesanan</h3>
                <p class="text-muted small mb-4">Semua pesanan yang pernah Anda buat ditampilkan di sini.</p>
            </div>
            <a class="btn btn-outline-warning rounded-0 text-uppercase fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('pelanggan/pesanan.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
        </div>

        <div class="compact-list mt-4">
            <div class="compact-list-item">
                <div>
                    <p class="fw-medium text-light">#M-901 • Truffle Linguine</p>
                    <p class="mt-2 small text-muted">12 Mei 2026 • Rp 345.000</p>
                </div>
                <span class="badge badge bg-warning text-dark">Diproses</span>
            </div>
            <div class="compact-list-item">
                <div>
                    <p class="fw-medium text-light">#M-887 • A5 Wagyu Ribeye</p>
                    <p class="mt-2 small text-muted">02 Mei 2026 • Rp 420.000</p>
                </div>
                <span class="badge badge bg-secondary text-light">Selesai</span>
            </div>
            <div class="compact-list-item">
                <div>
                    <p class="fw-medium text-light">#M-875 • Tasting Menu 3-Course</p>
                    <p class="mt-2 small text-muted">24 April 2026 • Rp 1.772.750</p>
                </div>
                <span class="badge badge bg-secondary text-light">Selesai</span>
            </div>
            <div class="compact-list-item">
                <div>
                    <p class="fw-medium text-light">#M-861 • Hokkaido Scallop</p>
                    <p class="mt-2 small text-muted">17 April 2026 • Rp 250.000</p>
                </div>
                <span class="badge badge bg-secondary text-light">Selesai</span>
            </div>
        </div>
    </article>

    <aside class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <h3 class="h3 mb-1 text-warning">Statistik</h3>
        <div class="row g-3 mt-4">
            <article class="order-stat">
                <p class="text-muted small text-uppercase mb-2">Total Pesanan</p>
                <p class="h2 text-warning mb-0 !text-[2rem]">12</p>
            </article>
            <article class="order-stat">
                <p class="text-muted small text-uppercase mb-2">Total Belanja</p>
                <p class="h2 text-warning mb-0 !text-[2rem]">Rp 4.8jt</p>
            </article>
            <article class="order-stat">
                <p class="text-muted small text-uppercase mb-2">Menu Favorit</p>
                <p class="h2 text-warning mb-0 !text-[2rem]">Wagyu</p>
            </article>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Essence",
    'text-muted small text-uppercase mb-1' => 'Order History',
    'title' => 'Riwayat semua pesanan Anda dalam satu tampilan.',
    'description' => 'Lacak semua pesanan sebelumnya, dari yang aktif hingga selesai.',
    'actions' => [
        ['label' => 'Status Pesanan', 'href' => base_url('pelanggan/pesanan_status.php')],
        ['label' => 'Menu', 'href' => base_url('pelanggan/menu.php'), 'variant' => 'secondary'],
    ],
], $content);
require __DIR__ . '/../includes/footer.php';
