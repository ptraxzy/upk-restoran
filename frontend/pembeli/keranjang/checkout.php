<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_once base_path('backend/payment gateway/QrisCepat.php');

require_role('pelanggan');

use Backend\PaymentGateway\QrisCepat;

$title = 'Checkout';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

$paymentResult = null;
if (isset($_GET['action']) && $_GET['action'] === 'pay') {
    $payment = new QrisCepat();
    // Gunakan nominal total Rp 1.772.750 untuk contoh
    $paymentResult = $payment->deposit(11500);
}

$voucherMessage = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'apply_voucher') {
    $voucherCode = $_POST['voucher_code'] ?? 'Promo';
    $voucherMessage = "Voucher '{$voucherCode}' berhasil diterapkan!";
}

ob_start();
?>
<section class="editorial-grid">
    <article class="cart-panel">
        <p class="eyebrow">Checkout</p>
        <h2 class="section-title mt-3">Pemesanan Anda</h2>
        
        <?php if ($paymentResult): ?>
            <div class="mt-6 p-6 border border-brass/30 bg-stone-900 rounded-sm flex flex-col md:flex-row gap-8 items-center md:items-start">
                <?php if (isset($paymentResult['status']) && $paymentResult['status'] === 'success' && isset($paymentResult['data'])): ?>
                    <?php 
                        $qrisPayload = urlencode($paymentResult['data']['qris']);
                        $qrImageUrl = "https://quickchart.io/qr?text={$qrisPayload}&size=300&margin=2";
                    ?>
                    <div class="flex-shrink-0 bg-white p-4 rounded-md">
                        <img src="<?= $qrImageUrl ?>" alt="QRIS Code" class="w-48 h-48 md:w-64 md:h-64 object-contain">
                    </div>
                    <div class="flex-1 w-full space-y-4">
                        <div>
                            <h3 class="text-xl font-display text-brass mb-1">Pindai untuk Membayar</h3>
                            <p class="text-sm text-stone-400">Silakan buka aplikasi mobile banking atau e-wallet Anda dan pindai QR code ini.</p>
                        </div>
                        
                        <div class="bg-stone-950 p-4 rounded-sm space-y-3">
                            <div class="flex justify-between border-b border-stone-800 pb-2">
                                <span class="text-stone-500 text-sm">Nominal Pembayaran</span>
                                <span class="text-stone-200 font-medium">Rp <?= number_format((float)$paymentResult['data']['amount'], 0, ',', '.') ?></span>
                            </div>
                            <div class="flex justify-between border-b border-stone-800 pb-2">
                                <span class="text-stone-500 text-sm">ID Transaksi</span>
                                <span class="text-stone-300 font-mono text-xs break-all text-right ml-4"><?= htmlspecialchars($paymentResult['data']['trx_id']) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-stone-500 text-sm">Status</span>
                                <span class="text-amber-400 text-sm font-medium animate-pulse">Menunggu Pembayaran...</span>
                            </div>
                        </div>
                        <p class="text-xs text-stone-500 italic mt-2">Peringatan: Jangan tutup halaman ini sebelum pembayaran Anda terkonfirmasi.</p>
                    </div>
                <?php else: ?>
                    <div class="w-full bg-red-950/30 border border-red-500/30 p-4 rounded-sm text-red-400">
                        <p class="font-medium mb-1">Gagal Menghasilkan QRIS</p>
                        <p class="text-sm"><?= htmlspecialchars($paymentResult['message'] ?? 'Terjadi kesalahan sistem, silakan coba lagi.') ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($voucherMessage): ?>
            <div class="mt-6 p-4 border border-green-500/30 bg-green-950/30 rounded-sm text-green-400 text-sm">
                <?= htmlspecialchars($voucherMessage) ?>
            </div>
        <?php endif; ?>

        <div class="mt-6 grid gap-8 md:grid-cols-[1fr_340px]">
            <div class="space-y-5">
                <div class="voucher-card">
                    <div>
                        <p class="font-medium text-stone-100">Tasting Menu</p>
                        <p class="mt-2 text-sm text-stone-500">3-Course Chef's Selection</p>
                    </div>
                    <span class="badge badge-muted">2 PAX</span>
                </div>
                <div class="voucher-card">
                    <div>
                        <p class="font-medium text-stone-100">Jadwal</p>
                        <p class="mt-2 text-sm text-stone-500">Jumat, 24 April 2026<br>19.00 WIB</p>
                    </div>
                </div>
            </div>
            <div class="summary-panel">
                <p class="font-medium text-stone-100">Rincian Biaya</p>
                <div class="mt-4 space-y-3">
                    <div class="summary-row flex justify-between"><span>Tenderloin Menu Pairing</span><span>Rp 1.250.000</span></div>
                    <div class="summary-row flex justify-between"><span>Premium Wine Pairing</span><span>Rp 1.100.000</span></div>
                    <div class="summary-row flex justify-between"><span>Subtotal</span><span>Rp 2.350.000</span></div>
                    <div class="summary-row flex justify-between"><span>15% Diskon Member</span><span>-Rp 225.000</span></div>
                    <div class="summary-row flex justify-between"><span>Pajak & Layanan</span><span>Rp 42.750</span></div>
                </div>
                <div class="mt-6 pt-4 border-t border-stone-800 flex items-center justify-between">
                    <span class="font-medium">Total</span>
                    <span class="font-display text-xl text-brass">Rp 1.772.750</span>
                </div>
            </div>
        </div>

        <div class="mt-10 space-y-4">
            <h3 class="text-lg font-display text-stone-100">Voucher & Promo Tersedia</h3>
            
            <form method="POST" class="flex gap-3 mb-6">
                <input type="hidden" name="action" value="apply_voucher">
                <input type="text" name="voucher_code" placeholder="Masukkan kode voucher" class="form-input flex-1" required>
                <button type="submit" class="cta-primary whitespace-nowrap">Terapkan</button>
            </form>

            <form method="POST" class="voucher-card">
                <input type="hidden" name="action" value="apply_voucher">
                <input type="hidden" name="voucher_code" value="DISC15WINE">
                <div>
                    <p class="font-medium text-stone-100">Diskon 15% Wine Pairing</p>
                    <p class="mt-2 text-sm text-stone-500">Berlaku sampai 30 April 2026</p>
                </div>
                <button class="cta-secondary" type="submit">Pakai</button>
            </form>
            
            <form method="POST" class="voucher-card">
                <input type="hidden" name="action" value="apply_voucher">
                <input type="hidden" name="voucher_code" value="COMPDESSERT">
                <div>
                    <p class="font-medium text-stone-100">Complimentary Dessert</p>
                    <p class="mt-2 text-sm te   xt-stone-500">Bonus untuk member loyalitas premium</p>
                </div>
                <button class="cta-secondary" type="submit">Gunakan</button>
            </form>
        </div>
    </article>
</section>
<?php
$content = ob_get_clean();

$actions = [
    ['label' => 'Kembali ke Keranjang', 'href' => frontend_url('pembeli/keranjang/index.php'), 'variant' => 'secondary']
];

// Jika belum membayar, tampilkan tombol bayar
if (!$paymentResult) {
    $actions[] = ['label' => 'Bayar dengan QRIS', 'href' => '?action=pay', 'variant' => 'primary'];
}

render_public_shell([
    'brand' => "L'Art Culinaire",
    'eyebrow' => 'Checkout',
    'title' => 'Konfirmasi Pembayaran',
    'description' => 'Selesaikan pembayaran Anda menggunakan metode QRIS untuk pesanan ini.',
    'actions' => $actions,
], $content);
require base_path('backend/includes/footer.php');
