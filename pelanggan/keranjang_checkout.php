<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/QrisCepat.php';

require_role('pelanggan');

use Backend\PaymentGateway\QrisCepat;

$title = 'Checkout';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

$pdo = db();
$isPaymentPhase = isset($_GET['action']) && $_GET['action'] === 'pay' && isset($_GET['id_pesanan']);
$userId = $_SESSION['user_id'] ?? 0;
$cart = [];

if (!$isPaymentPhase) {
    $stmtCart = $pdo->prepare(
        "SELECT k.qty, m.nama_menu, m.harga FROM keranjang k JOIN menu m ON k.id_menu = m.id_menu WHERE k.user_id = ? ORDER BY k.id_keranjang DESC"
    );
    $stmtCart->execute([$userId]);
    $cart = $stmtCart->fetchAll();
}

// Security check: if not paying and cart empty, back to cart
if (empty($cart) && !$isPaymentPhase) {
    set_flash('error', 'Keranjang Anda kosong.');
    redirect(base_url('pelanggan/keranjang.php'));
}

$subtotal = 0;
$discount = 0;
$tax = 0;
$total = 0;
$orderItems = [];
$currentNoMeja = $_SESSION['meja_aktif'] ?? '01';

if ($isPaymentPhase) {
    // Phase: Payment (Session cart already cleared)
    // Fetch data from database instead of session
    $pdo = db();
    $id_pesanan = (int) $_GET['id_pesanan'];

    // 1. Order main info
    $stmtOrder = $pdo->prepare("SELECT * FROM pesanan WHERE id_pesanan = ? AND id_user = ?");
    $stmtOrder->execute([$id_pesanan, $_SESSION['user_id']]);
    $order = $stmtOrder->fetch();

    if (!$order) {
        set_flash('error', 'Pesanan tidak ditemukan.');
        redirect(base_url('pelanggan/dashboard.php'));
    }

    $total = (float) $order['total_harga'];
    $currentNoMeja = $order['no_meja'];

    // 2. Order items
    $stmtItems = $pdo->prepare("
        SELECT dp.*, m.nama_menu
        FROM detail_pesanan dp
        JOIN menu m ON dp.id_menu = m.id_menu
        WHERE dp.id_pesanan = ?
    ");
    $stmtItems->execute([$id_pesanan]);
    $orderItems = $stmtItems->fetchAll();

    // Calculate subtotal for display
    foreach ($orderItems as $item) {
        $subtotal += ((float)$item['harga_satuan'] * (int)$item['jumlah']);
    }
    $tax = $subtotal * 0.11; // Approx
} else {
    // Phase: Review before checkout (Cart data fetched dari DB)
    foreach ($cart as $item) {
        $subtotal += ((float)$item['harga'] * (int)$item['qty']);
        $orderItems[] = [
            'nama_menu' => $item['nama_menu'],
            'jumlah' => $item['qty'],
            'harga_satuan' => $item['harga']
        ];
    }

    if (isset($_SESSION['active_voucher']) && $_SESSION['active_voucher'] === 'DISC15WINE') {
        $discount = $subtotal * 0.15;
    }

    $tax = ($subtotal - $discount) * 0.11;
    $total = ($subtotal - $discount) + $tax;
}

// Payment Gateway Logic
$paymentResult = null;
if ($isPaymentPhase) {
    $payment = new QrisCepat();
    // UNTUK UPK: Hardcode nominal ke 10.000 agar gampang dites presentasi,
    // tapi di tampilan (frontend) tetap muncul harga asli.
    $presentationAmount = 10000;
    $paymentResult = $payment->deposit($presentationAmount);
}

ob_start();
?>
<style>
    .cart-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 40px;
        margin-top: 20px;
    }
    @media (min-width: 992px) {
        .cart-layout { grid-template-columns: 1.5fr 1fr; }
    }
    .summary-panel {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        padding: 32px;
        height: fit-content;
    }
    .checkout-info-card {
        border: 1px solid var(--border-soft);
        padding: 20px;
        background: rgba(255,255,255,0.02);
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .checkout-info-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border-soft);
        color: var(--gold);
    }
</style>
<section class="cart-layout">
    <article class="cart-panel">
        <div class="mb-4 pb-3 border-bottom border-secondary">
            <h2 class="font-display text-white mb-0" style="font-size: 32px;"><?= $isPaymentPhase ? 'Status Pembayaran' : 'Penyelesaian Pesanan' ?></h2>
        </div>

        <?php if ($isPaymentPhase && $paymentResult): ?>
            <div class="mb-5 p-4 border border-gold bg-black d-flex flex-column flex-md-row gap-4 align-items-center">
                <?php if (isset($paymentResult['status']) && $paymentResult['status'] === 'success' && isset($paymentResult['data'])): ?>
                    <?php
                        $qrisPayload = urlencode($paymentResult['data']['qris']);
                        $qrImageUrl = "https://quickchart.io/qr?text={$qrisPayload}&size=300&margin=2";
                    ?>
                    <div class="bg-white p-3 shadow-lg">
                        <img src="<?= $qrImageUrl ?>" alt="QRIS Code" style="width: 140px; height: 140px; object-fit: contain;">
                    </div>
                    <div class="flex-grow-1">
                        <h3 class="font-display text-gold mb-2" style="font-size: 20px;">Pindai untuk Membayar</h3>
                        <p class="small text-secondary mb-3" style="font-size: 11px;">Gunakan aplikasi mobile banking atau e-wallet (OVO, GoPay, Dana).</p>

                        <div class="p-3 border border-secondary" style="font-size: 12px; background: rgba(255,255,255,0.05);">
                            <div class="d-flex justify-content-between border-bottom border-soft pb-2 mb-2">
                                <span class="text-secondary">Total Bayar</span>
                                <span class="text-white fw-bold"><?= rupiah((float)$total) ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-secondary">Status</span>
                                <span class="text-warning fw-medium blink">Menunggu Pembayaran...</span>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="w-100 p-4 border border-danger text-danger bg-danger bg-opacity-10">
                        <p class="fw-medium mb-1">Gagal Menghasilkan QRIS</p>
                        <p class="small m-0">Kesalahan: <?= htmlspecialchars($paymentResult['message'] ?? 'API tidak merespon') ?></p>
                        <p class="small mt-2">Jangan khawatir, Anda tetap bisa melanjutkan dengan simulasi bayar di bawah.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="checkout-info-card h-100">
                    <div class="checkout-info-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <div>
                        <p class="text-white small mb-0 fw-medium">Metode Pembayaran</p>
                        <p class="text-secondary" style="font-size: 10px;">QRIS Otomatis / Virtual Account</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="checkout-info-card h-100">
                    <div class="checkout-info-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                    </div>
                    <div>
                        <p class="text-white small mb-0 fw-medium">Lokasi Meja</p>
                        <p class="text-secondary" style="font-size: 10px;">Meja: <?= htmlspecialchars($currentNoMeja) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!$isPaymentPhase): ?>
        <div class="promo-section">
            <h3 class="font-display text-white mb-3" style="font-size: 24px;">Voucher & Promo</h3>
            <div class="voucher-list">
                <form method="POST" class="border border-secondary p-3 d-flex align-items-center justify-content-between bg-card">
                    <input type="hidden" name="action" value="apply_voucher">
                    <input type="hidden" name="voucher_code" value="DISC15WINE">
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-gold"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="15" r="3"/><circle cx="15" cy="9" r="3"/><line x1="21" y1="3" x2="3" y2="21"/></svg></div>
                        <div>
                            <p class="text-white small mb-0 fw-medium">Diskon 15% (DISC15WINE)</p>
                            <p class="text-secondary" style="font-size: 10px;">Gunakan voucher ini untuk potongan harga eksklusif.</p>
                        </div>
                    </div>
                    <button class="btn btn-warning py-2 px-3" type="submit" style="font-size: 10px;">Gunakan</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </article>

    <aside class="summary-panel">
        <h3 class="font-display text-white mb-4" style="font-size: 24px;">Rincian Biaya</h3>

        <div class="d-flex flex-column gap-3 mb-4">
            <?php foreach ($orderItems as $item): ?>
            <div class="d-flex justify-content-between">
                <span class="text-secondary small"><?= htmlspecialchars($item['nama_menu']); ?> (x<?= $item['jumlah'] ?>)</span>
                <span class="text-white small"><?= rupiah((float)$item['harga_satuan'] * (int)$item['jumlah']); ?></span>
            </div>
            <?php endforeach; ?>

            <div class="d-flex justify-content-between mt-2 pt-2 border-top border-soft">
                <span class="text-secondary small">Subtotal</span>
                <span class="text-white small"><?= rupiah((float)$subtotal); ?></span>
            </div>

            <?php if ($discount > 0): ?>
            <div class="d-flex justify-content-between">
                <span class="text-success small">Diskon Voucher</span>
                <span class="text-success small">-<?= rupiah((float)$discount); ?></span>
            </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between">
                <span class="text-secondary small">Pajak (11%)</span>
                <span class="text-white small"><?= rupiah((float)$tax); ?></span>
            </div>
        </div>

        <div class="pt-3 border-top border-secondary d-flex align-items-center justify-content-between mb-4">
            <span class="text-uppercase small letter-spacing-1 text-secondary" style="font-size: 10px;">Total Tagihan</span>
            <span class="font-display text-gold" style="font-size: 32px;"><?= rupiah((float)$total); ?></span>
        </div>

        <?php if (!$isPaymentPhase): ?>
        <form action="<?= base_url('actions/pesanan/checkout.php'); ?>" method="post">
            <button type="submit" class="btn btn-warning w-100 py-3" style="font-size: 12px;">Buat Pesanan & Bayar</button>
            <a href="<?= base_url('pelanggan/keranjang.php'); ?>" class="btn btn-outline-warning w-100 py-3 mt-2" style="font-size: 12px;">Kembali</a>
        </form>
        <?php else: ?>
        <a href="<?= base_url('actions/pesanan/simulate_pay.php?id_pesanan=' . $_GET['id_pesanan']); ?>" class="btn btn-warning w-100 py-3" style="font-size: 12px;">Bayar Sekarang (Simulasi)</a>
        <a href="<?= base_url('pelanggan/dashboard.php'); ?>" class="btn btn-outline-warning w-100 py-3 mt-2" style="font-size: 12px;">Dashboard</a>
        <?php endif; ?>
    </aside>
</section>

<style>
@keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
.blink { animation: blink 2s infinite; }
</style>

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
