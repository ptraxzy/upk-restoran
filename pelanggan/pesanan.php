<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('pelanggan');

$title = 'Pesanan Member';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="editorial-grid">
    <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <div class="d-flex flex-column gap-2 flex-md-row align-items-md-end justify-content-md-between">
            <div>
                <h2 class="h3 mb-1 text-warning">Daftar Pesanan</h2>
                <p class="text-muted small mb-4">Perjalanan order aktif dan pesanan terakhir disusun lebih ringkas agar mudah dipantau.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-warning rounded-0 text-uppercase fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('pelanggan/pesanan_status.php'), ENT_QUOTES, 'UTF-8'); ?>">Lihat Status Pesanan</a>
                <a class="btn btn-outline-warning rounded-0 text-uppercase fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('pelanggan/pesanan_riwayat.php'), ENT_QUOTES, 'UTF-8'); ?>">Riwayat Pesanan</a>
            </div>
        </div>
        <div class="compact-list mt-4">
            <div class="compact-list-item">
                <div>
                    <p class="fw-medium text-light">#M-901 • Truffle Linguine</p>
                    <p class="mt-2 small text-muted">Order aktif • Diproses dapur • 19:25</p>
                </div>
                <span class="badge badge bg-warning text-dark">Diproses</span>
            </div>
            <div class="compact-list-item">
                <div>
                    <p class="fw-medium text-light">#M-887 • A5 Wagyu Ribeye</p>
                    <p class="mt-2 small text-muted">Pesanan selesai • 02 Mei 2026</p>
                </div>
                <span class="badge badge bg-secondary text-light">Selesai</span>
            </div>
        </div>
    </article>

    <aside class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <h3 class="h3 mb-1 text-warning">Pesanan Aktif</h3>
        <div class="row g-3 mt-4">
            <article class="order-stat">
                <p class="text-muted small text-uppercase mb-2">Aktif Sekarang</p>
                <p class="h2 text-warning mb-0 !text-[2rem]">01</p>
            </article>
            <article class="order-stat">
                <p class="text-muted small text-uppercase mb-2">Estimasi Plating</p>
                <p class="h2 text-warning mb-0 !text-[2rem]">8m</p>
            </article>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Essence",
    'text-muted small text-uppercase mb-1' => 'Order Journey',
    'title' => 'Riwayat pesanan yang lebih jelas dan lebih tenang dibaca.',
    'description' => 'Status aktif, order terakhir, dan jalur tindakan utama disusun dalam pola yang lebih bersih.',
    'actions' => [
        ['label' => 'Buka Keranjang', 'href' => base_url('pelanggan/keranjang.php')],
        ['label' => 'Menu', 'href' => base_url('pelanggan/menu.php'), 'variant' => 'secondary'],
    ],
], $content);
require __DIR__ . '/../includes/footer.php';
