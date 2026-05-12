<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('kasir');

$title = 'Status Pembayaran';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="row g-5">
    <div class="col-lg-8">
        <article class="section-panel h-100">
            <div class="panel-header d-flex flex-column gap-3 flex-md-row justify-content-md-between align-items-md-end">
                <div>
                    <h3 class="panel-title">Status Transaksi</h3>
                    <p class="panel-desc">Pantau status pembayaran untuk setiap pesanan yang sedang berjalan.</p>
                </div>
                <div class="d-flex flex-wrap gap-3">
                    <a class="btn btn-outline-warning" href="<?= htmlspecialchars(base_url('kasir/pembayaran.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Metode</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-white fw-medium">#K-110</td>
                            <td>Naomi Hart</td>
                            <td class="text-gold">Rp 875.000</td>
                            <td>QRIS</td>
                            <td><span class="badge bg-warning">Menunggu</span></td>
                        </tr>
                        <tr>
                            <td class="text-white fw-medium">#K-111</td>
                            <td>Luca Stone</td>
                            <td class="text-gold">Rp 1.450.000</td>
                            <td>QRIS</td>
                            <td><span class="badge bg-secondary">Lunas</span></td>
                        </tr>
                        <tr>
                            <td class="text-white fw-medium">#K-112</td>
                            <td>Clara Vance</td>
                            <td class="text-gold">Rp 420.000</td>
                            <td>Tunai</td>
                            <td><span class="badge bg-secondary">Lunas</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </article>
    </div>

    <aside class="col-lg-4">
        <article class="section-panel h-100">
            <h3 class="panel-title mb-4">Ringkasan</h3>
            <div class="row row-cols-2 g-4">
                <div class="col">
                    <article class="card p-4 h-100">
                        <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">Menunggu Bayar</p>
                        <p class="h2 text-gold font-display mb-0">1</p>
                    </article>
                </div>
                <div class="col">
                    <article class="card p-4 h-100">
                        <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">Sudah Lunas</p>
                        <p class="h2 text-gold font-display mb-0">2</p>
                    </article>
                </div>
            </div>
        </article>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Service Floor',
    'title' => 'Status Pembayaran',
    'description' => 'Pantau status pembayaran secara real-time untuk setiap transaksi.',
    'nav_sections' => staff_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
