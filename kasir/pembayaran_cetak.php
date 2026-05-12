<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('kasir');

$title = 'Cetak Struk';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="row g-5">
    <div class="col-lg-8">
        <article class="section-panel h-100 d-flex flex-column align-items-center">
            <div class="w-100 d-flex justify-content-between align-items-center mb-5 pb-3 border-bottom border-soft">
                <h3 class="panel-title m-0">Pratinjau Struk</h3>
                <a class="btn btn-outline-warning" href="<?= htmlspecialchars(base_url('kasir/pembayaran.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
            </div>

            <!-- Receipt Container -->
            <div class="receipt-container border border-secondary p-5" style="width: 100%; max-width: 400px; background-color: #161616;">
                <div class="text-center mb-4">
                    <h2 class="font-display text-gold mb-1" style="font-size: 28px; letter-spacing: 4px;">L'ART CULINAIRE</h2>
                    <p class="text-secondary small m-0" style="line-height: 1.4;">Jl. Sudirman No. 45, Jakarta Selatan<br>Telp: (021) 555-0123</p>
                </div>
                
                <div class="d-flex flex-column gap-2 mb-4 text-secondary" style="font-size: 11px;">
                    <div class="d-flex justify-content-between"><span>TANGGAL:</span><span class="text-white">24 Okt 2023, 20:15</span></div>
                    <div class="d-flex justify-content-between"><span>KASIR:</span><span class="text-white">Andi R.</span></div>
                    <div class="d-flex justify-content-between"><span>NO. MEJA:</span><span class="text-white">04 (VIP)</span></div>
                    <div class="d-flex justify-content-between"><span>NO. PESANAN:</span><span class="text-white">#ORD-0821A</span></div>
                </div>

                <div class="border-top border-bottom border-soft py-3 mb-4 d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between text-white small">
                        <span>Truffle Risotto<br><span class="text-secondary" style="font-size: 10px;">1x @ 500.000</span></span>
                        <span>Rp 500.000</span>
                    </div>
                    <div class="d-flex justify-content-between text-white small">
                        <span>Wagyu A5 Striploin<br><span class="text-secondary" style="font-size: 10px;">1x @ 1.200.000</span></span>
                        <span>Rp 1.200.000</span>
                    </div>
                    <div class="d-flex justify-content-between text-white small">
                        <span>Chateau Margaux (Glass)<br><span class="text-secondary" style="font-size: 10px;">1x @ 900.000</span></span>
                        <span>Rp 900.000</span>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2 mb-4 text-secondary" style="font-size: 11px;">
                    <div class="d-flex justify-content-between"><span>Subtotal</span><span class="text-white">Rp 2.600.000</span></div>
                    <div class="d-flex justify-content-between"><span>Pajak (11%)</span><span class="text-white">Rp 286.000</span></div>
                    <div class="d-flex justify-content-between"><span>Layanan (5%)</span><span class="text-white">Rp 130.000</span></div>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top border-soft mb-4">
                    <span class="text-gold fw-medium letter-spacing-1">TOTAL</span>
                    <span class="font-display text-white" style="font-size: 24px;">Rp 3.016.000</span>
                </div>

                <div class="d-flex flex-column gap-1 text-secondary" style="font-size: 11px;">
                    <div class="d-flex justify-content-between"><span>Metode Pembayaran</span><span class="text-white">KARTU KREDIT</span></div>
                    <div class="d-flex justify-content-between"><span>Dibayar</span><span class="text-white">Rp 3.016.000</span></div>
                    <div class="d-flex justify-content-between"><span>Kembalian</span><span class="text-white">Rp 0</span></div>
                </div>

                <div class="text-center mt-5">
                    <p class="text-secondary" style="font-size: 10px;">Terima kasih atas kunjungan Anda.</p>
                    <div class="mt-3 d-flex justify-content-center">
                        <!-- Pseudo Barcode -->
                        <div class="d-flex align-items-end" style="height: 30px; gap: 2px;">
                            <div class="bg-secondary" style="width: 2px; height: 100%;"></div>
                            <div class="bg-secondary" style="width: 4px; height: 100%;"></div>
                            <div class="bg-secondary" style="width: 1px; height: 100%;"></div>
                            <div class="bg-secondary" style="width: 3px; height: 100%;"></div>
                            <div class="bg-secondary" style="width: 2px; height: 100%;"></div>
                            <div class="bg-secondary" style="width: 1px; height: 100%;"></div>
                            <div class="bg-secondary" style="width: 5px; height: 100%;"></div>
                            <div class="bg-secondary" style="width: 2px; height: 100%;"></div>
                            <div class="bg-secondary" style="width: 1px; height: 100%;"></div>
                            <div class="bg-secondary" style="width: 3px; height: 100%;"></div>
                            <div class="bg-secondary" style="width: 2px; height: 100%;"></div>
                            <div class="bg-secondary" style="width: 4px; height: 100%;"></div>
                            <div class="bg-secondary" style="width: 2px; height: 100%;"></div>
                            <div class="bg-secondary" style="width: 1px; height: 100%;"></div>
                            <div class="bg-secondary" style="width: 3px; height: 100%;"></div>
                            <div class="bg-secondary" style="width: 2px; height: 100%;"></div>
                            <div class="bg-secondary" style="width: 1px; height: 100%;"></div>
                            <div class="bg-secondary" style="width: 5px; height: 100%;"></div>
                            <div class="bg-secondary" style="width: 2px; height: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 w-100 d-flex justify-content-center">
                <button class="btn btn-warning" onclick="window.print()" style="min-width: 200px;">CETAK STRUK</button>
            </div>
        </article>
    </div>

    <aside class="col-lg-4">
        <article class="section-panel h-100">
            <h3 class="panel-title mb-4">Riwayat Terakhir</h3>
            <div class="compact-list">
                <div class="compact-list-item">
                    <div>
                        <p class="fw-medium text-white mb-1">#ORD-0820 • Meja 12</p>
                        <p class="small text-secondary mb-0">Rp 12.400.000 • Kartu Kredit</p>
                    </div>
                    <a class="text-gold small text-uppercase letter-spacing-1 text-decoration-none border-bottom border-gold pb-1" href="#">Lihat</a>
                </div>
                <div class="compact-list-item">
                    <div>
                        <p class="fw-medium text-white mb-1">#ORD-0819 • Meja 04</p>
                        <p class="small text-secondary mb-0">Rp 1.650.000 • QRIS</p>
                    </div>
                    <a class="text-gold small text-uppercase letter-spacing-1 text-decoration-none border-bottom border-gold pb-1" href="#">Lihat</a>
                </div>
            </div>
        </article>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'brand' => 'NOCTRA',
    'badge' => 'Service Floor',
    'title' => 'Cetak Struk',
    'description' => 'Cetak struk pembayaran untuk pesanan yang sudah lunas.',
    'nav_sections' => staff_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
