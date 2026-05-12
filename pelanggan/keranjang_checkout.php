<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_once base_path('backend/payment gateway/QrisCepat.php');

require_role('pelanggan');

use Backend\PaymentGateway\QrisCepat;

$title = 'Checkout';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart) && (!isset($_GET['action']) || $_GET['action'] !== 'pay')) {
    set_flash('error', 'Keranjang Anda kosong.');
    redirect(base_url('pelanggan/keranjang.php'));
}

$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += ($item['harga'] * $item['jumlah']);
}

$discount = 0;
$voucherMessage = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'apply_voucher') {
    $voucherCode = $_POST['voucher_code'] ?? '';
    if ($voucherCode === 'DISC15WINE') {
        $discount = $subtotal * 0.15;
        $voucherMessage = "Voucher '{$voucherCode}' diterapkan! Diskon 15%.";
        $_SESSION['active_voucher'] = $voucherCode;
        $_SESSION['active_discount'] = $discount;
    } else {
        set_flash('error', 'Kode voucher tidak valid atau tidak dapat digunakan.');
    }
} else {
    // Retain previous active voucher
    if (isset($_SESSION['active_voucher']) && $_SESSION['active_voucher'] === 'DISC15WINE') {
        $discount = $subtotal * 0.15;
        $_SESSION['active_discount'] = $discount;
    }
}

$tax = ($subtotal - $discount) * 0.11;
$total = ($subtotal - $discount) + $tax;

$paymentResult = null;
$finalTotal = $total;
if (isset($_GET['action']) && $_GET['action'] === 'pay' && isset($_GET['trx'], $_GET['id_pesanan'])) {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT total_bayar FROM pembayaran WHERE id_pesanan = ? AND trx_id = ?");
    $stmt->execute([$_GET['id_pesanan'], $_GET['trx']]);
    $bayar = $stmt->fetch();
    
    if ($bayar) {
        $finalTotal = (float) $bayar['total_bayar'];
        $payment = new QrisCepat();
        $paymentResult = $payment->deposit((int) $finalTotal);
    }
}

ob_start();
?>
<section class="cart-layout">
    <article class="cart-panel">
        <div class="mb-5">
            <h2 class="font-display text-white mb-2" style="font-size: 32px;">Pemesanan Anda</h2>
            <p class="text-secondary">Langkah akhir sebelum kami siapkan hidangan terbaik untuk meja Anda.</p>
        </div>
        
        <?php if ($paymentResult): ?>
            <div class="mb-5 p-4 border border-gold bg-black d-flex flex-column flex-md-row gap-4 align-items-center">
                <?php if (isset($paymentResult['status']) && $paymentResult['status'] === 'success' && isset($paymentResult['data'])): ?>
                    <?php 
                        $qrisPayload = urlencode($paymentResult['data']['qris']);
                        $qrImageUrl = "https://quickchart.io/qr?text={$qrisPayload}&size=300&margin=2";
                    ?>
                    <div class="bg-white p-3">
                        <img src="<?= $qrImageUrl ?>" alt="QRIS Code" style="width: 160px; height: 160px; object-fit: contain;">
                    </div>
                    <div class="flex-grow-1">
                        <h3 class="font-display text-gold mb-2" style="font-size: 24px;">Pindai untuk Membayar</h3>
                        <p class="small text-secondary mb-3">Silakan buka aplikasi mobile banking atau e-wallet Anda dan pindai QR code ini.</p>
                        
                        <div class="bg-dark p-3 border border-secondary">
                            <div class="d-flex justify-content-between border-bottom border-soft pb-2 mb-2">
                                <span class="text-muted small">Nominal Pembayaran</span>
                                <span class="text-white fw-medium"><?= htmlspecialchars(rupiah($finalTotal), ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom border-soft pb-2 mb-2">
                                <span class="text-muted small">ID Transaksi</span>
                                <span class="text-white small" style="word-break: break-all; text-align: right; margin-left: 16px;"><?= htmlspecialchars($_GET['trx']) ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Status</span>
                                <span class="text-warning small fw-medium">Menunggu Pembayaran...</span>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="w-100 p-4 border border-danger text-danger">
                        <p class="fw-medium mb-1">Gagal Menghasilkan QRIS</p>
                        <p class="small m-0"><?= htmlspecialchars($paymentResult['message'] ?? 'Terjadi kesalahan sistem, silakan coba lagi.') ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($voucherMessage): ?>
            <div class="alert alert-success rounded-0 small p-3 mb-5 border-0 bg-success bg-opacity-10 text-success text-center text-uppercase letter-spacing-1">
                <?= htmlspecialchars($voucherMessage) ?>
            </div>
        <?php endif; ?>

        <div class="checkout-cards-grid mb-5">
            <div class="checkout-info-card">
                <div class="checkout-info-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/><path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/></svg>
                </div>
                <div>
                    <p class="text-white fw-medium mb-1">Pesanan Anda</p>
                    <?php if ($paymentResult): ?>
                        <p class="text-secondary small mb-0">Pesanan telah diproses.</p>
                    <?php else: ?>
                        <p class="text-secondary small mb-0"><?= count($cart) ?> Item Dipilih</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="checkout-info-card">
                <div class="checkout-info-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/><path d="m3 9 2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9"/><path d="M12 3v6"/></svg>
                </div>
                <div>
                    <p class="text-white fw-medium mb-1">Informasi Meja</p>
                    <p class="text-secondary small mb-0">Dine-In<br>Meja <?= htmlspecialchars($_SESSION['meja_aktif'] ?? '01', ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
        </div>

        <?php if (!$paymentResult): ?>
        <div class="promo-section">
            <h3 class="font-display text-white mb-2" style="font-size: 24px;">Voucher & Promo</h3>
            <p class="text-muted small text-uppercase letter-spacing-2 mb-3">Kode Promo</p>
            
            <form method="POST" class="promo-input-group">
                <input type="hidden" name="action" value="apply_voucher">
                <input type="text" name="voucher_code" placeholder="Masukkan kode promo" class="form-control" required>
                <button type="submit" class="btn btn-outline-warning">Terapkan</button>
            </form>

            <p class="text-muted small text-uppercase letter-spacing-2 mb-3">Voucher Tersedia</p>
            <div class="voucher-list">
                <form method="POST" class="voucher-card <?= (isset($_SESSION['active_voucher']) && $_SESSION['active_voucher'] === 'DISC15WINE') ? 'active' : '' ?>">
                    <input type="hidden" name="action" value="apply_voucher">
                    <input type="hidden" name="voucher_code" value="DISC15WINE">
                    <div class="voucher-card-content">
                        <div class="voucher-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="15" r="3"/><circle cx="15" cy="9" r="3"/><line x1="21" y1="3" x2="3" y2="21"/></svg>
                        </div>
                        <div>
                            <p class="text-white fw-medium mb-1">Diskon 15%</p>
                            <p class="text-secondary small mb-0">Berlaku untuk semua menu premium.<br>Dapat digunakan hari ini.</p>
                        </div>
                    </div>
                    <button class="btn btn-warning" type="submit">Gunakan</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </article>

    <aside class="summary-panel">
        <h3 class="font-display text-white mb-4">Rincian Biaya</h3>
        
        <div>
            <?php if (!$paymentResult): ?>
                <?php foreach ($cart as $item): ?>
                <div class="summary-row"><span><?= htmlspecialchars($item['nama_menu'], ENT_QUOTES, 'UTF-8'); ?> (x<?= $item['jumlah'] ?>)</span><span><?= htmlspecialchars(rupiah($item['harga'] * $item['jumlah']), ENT_QUOTES, 'UTF-8'); ?></span></div>
                <?php endforeach; ?>
                <div class="summary-row border-bottom border-soft pb-3 mb-3"><span>Subtotal</span><span><?= htmlspecialchars(rupiah($subtotal), ENT_QUOTES, 'UTF-8'); ?></span></div>
                
                <?php if ($discount > 0): ?>
                <div class="summary-row"><span>Diskon</span><span class="text-gold">-<?= htmlspecialchars(rupiah($discount), ENT_QUOTES, 'UTF-8'); ?></span></div>
                <?php endif; ?>
                <div class="summary-row"><span>Pajak (11%)</span><span><?= htmlspecialchars(rupiah($tax), ENT_QUOTES, 'UTF-8'); ?></span></div>
            <?php else: ?>
                <div class="summary-row mb-3"><span>Pesanan (No. <?= htmlspecialchars($_GET['id_pesanan']) ?>)</span><span>Selesai diproses</span></div>
                <div class="summary-row border-bottom border-soft pb-3 mb-3"><span>Pembayaran QRIS</span><span class="text-warning">Menunggu Pembayaran</span></div>
            <?php endif; ?>
        </div>
        
        <div class="mt-4 pt-4 border-top border-secondary d-flex flex-column gap-1">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <span class="text-uppercase small letter-spacing-1 text-secondary">Total Akhir</span>
                <span class="font-display text-gold" style="font-size: 28px;"><?= htmlspecialchars(rupiah($finalTotal), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            
            <?php if (!$paymentResult): ?>
            <form action="<?= htmlspecialchars(base_url('actions/pesanan/checkout.php'), ENT_QUOTES, 'UTF-8'); ?>" method="post">
                <button type="submit" class="btn btn-warning w-100">Buat Pesanan & Bayar</button>
            </form>
            <?php else: ?>
            <a href="<?= htmlspecialchars(base_url('actions/pesanan/simulate_pay.php?id_pesanan=' . $_GET['id_pesanan']), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-warning w-100 text-white">Simulasi Bayar Lunas (Testing)</a>
            <a href="<?= htmlspecialchars(base_url('pelanggan/dashboard.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary w-100 mt-2 bg-dark border-secondary text-white text-uppercase letter-spacing-1 fw-medium py-2 rounded-0">Tutup ke Dashboard</a>
            <?php endif; ?>
            <p class="text-center text-secondary mt-3" style="font-size: 10px;">Pembayaran diproses dengan aman</p>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();

render_public_shell([
    'brand' => "L'Art Culinaire",
    'eyebrow' => 'Checkout',
    'title' => 'Konfirmasi Pembayaran',
    'description' => 'Selesaikan pembayaran Anda.',
    'actions' => [],
], $content);
require __DIR__ . '/../includes/footer.php';
