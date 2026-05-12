<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('pelanggan');

$title = 'Status Pesanan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="editorial-grid">
    <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <div class="d-flex flex-column gap-2 flex-md-row align-items-md-end justify-content-md-between">
            <div>
                <h3 class="h3 mb-1 text-warning">Status Pesanan</h3>
                <p class="text-muted small mb-4">Pantau status pesanan Anda secara real-time.</p>
            </div>
            <a class="btn btn-outline-warning rounded-0 text-uppercase fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('pelanggan/pesanan.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
        </div>

        <div class="mt-4 border border-secondary bg-white/[0.03] p-6">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-muted small text-uppercase mb-1">Pesanan Aktif</p>
                    <h4 class="mt-2 font-display text-2xl text-white">#M-901</h4>
                </div>
                <span class="badge badge bg-warning text-dark">Diproses</span>
            </div>

            <div class="mt-4 d-flex flex-column gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex h-8 w-8 align-items-center justify-content-center bg-brass small fw-medium text-stone-950">1</div>
                    <div>
                        <p class="small fw-medium text-light">Pesanan Diterima</p>
                        <p class="small text-muted">12 Mei 2026 • 19:25</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex h-8 w-8 align-items-center justify-content-center bg-brass small fw-medium text-stone-950">2</div>
                    <div>
                        <p class="small fw-medium text-light">Sedang Diproses Dapur</p>
                        <p class="small text-muted">12 Mei 2026 • 19:28</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex h-8 w-8 align-items-center justify-content-center bg-stone-800 small fw-medium text-muted">3</div>
                    <div>
                        <p class="small fw-medium text-muted">Siap Disajikan</p>
                        <p class="small text-stone-600">Menunggu...</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex h-8 w-8 align-items-center justify-content-center bg-stone-800 small fw-medium text-muted">4</div>
                    <div>
                        <p class="small fw-medium text-muted">Selesai</p>
                        <p class="small text-stone-600">Menunggu...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <h4 class="small fw-medium text-secondary">Detail Pesanan</h4>
            <div class="compact-list mt-2">
                <div class="compact-list-item">
                    <span>Truffle Linguine x1</span>
                    <span>Rp 300.000</span>
                </div>
                <div class="compact-list-item">
                    <span>Sparkling Water x1</span>
                    <span>Rp 45.000</span>
                </div>
            </div>
        </div>
    </article>

    <aside class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <h3 class="h3 mb-1 text-warning">Estimasi</h3>
        <div class="row g-3 mt-4">
            <article class="order-stat">
                <p class="text-muted small text-uppercase mb-2">Estimasi Waktu</p>
                <p class="h2 text-warning mb-0 !text-[2rem]">~12m</p>
            </article>
            <article class="order-stat">
                <p class="text-muted small text-uppercase mb-2">Meja</p>
                <p class="h2 text-warning mb-0 !text-[2rem]">06</p>
            </article>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Essence",
    'text-muted small text-uppercase mb-1' => 'Order Tracking',
    'title' => 'Pantau status pesanan Anda secara langsung.',
    'description' => 'Lihat progres pesanan dari diterima hingga selesai disajikan.',
    'actions' => [
        ['label' => 'Riwayat Pesanan', 'href' => base_url('pelanggan/pesanan_riwayat.php')],
        ['label' => 'Menu', 'href' => base_url('pelanggan/menu.php'), 'variant' => 'secondary'],
    ],
], $content);
require __DIR__ . '/../includes/footer.php';
