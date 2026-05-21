<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/QrisCepat.php';

require_role('pelanggan');

use Backend\PaymentGateway\QrisCepat;

$pdo = db();
$isPaymentPhase = isset($_GET['action']) && $_GET['action'] === 'pay' && isset($_GET['id_pesanan']);
$userId = $_SESSION['id_user'] ?? 0;
$cart = [];

// Fetch active vouchers for the dropdown selection
$stmtVoucherList = $pdo->query("
    SELECT id_voucher, kode_voucher, nama_voucher, jenis_voucher, nilai_voucher 
    FROM voucher 
    WHERE status_voucher = 'Active' 
      AND tanggal_mulai <= CURRENT_DATE() 
      AND tanggal_berakhir >= CURRENT_DATE()
      AND deleted_at IS NULL
    ORDER BY nama_voucher ASC
");
$availableVouchers = $stmtVoucherList->fetchAll();

// Handle POST to apply custom voucher
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'apply_voucher') {
    $voucherCode = trim($_POST['voucher_code'] ?? '');
    if ($voucherCode !== '' && !isset($_POST['clear_voucher'])) {
        $stmtVoucher = $pdo->prepare("
            SELECT * FROM voucher 
            WHERE kode_voucher = ? 
              AND status_voucher = 'Active' 
              AND tanggal_mulai <= CURRENT_DATE() 
              AND tanggal_berakhir >= CURRENT_DATE()
              AND deleted_at IS NULL
        ");
        $stmtVoucher->execute([$voucherCode]);
        $voucher = $stmtVoucher->fetch();

        if ($voucher) {
            $_SESSION['active_voucher'] = $voucher['kode_voucher'];
            $_SESSION['active_voucher_type'] = $voucher['jenis_voucher'];
            $_SESSION['active_voucher_value'] = (float)$voucher['nilai_voucher'];
            set_flash('success', 'Voucher "' . htmlspecialchars($voucher['kode_voucher']) . '" berhasil diterapkan!');
        } else {
            unset($_SESSION['active_voucher']);
            unset($_SESSION['active_voucher_type']);
            unset($_SESSION['active_voucher_value']);
            unset($_SESSION['active_discount']);
            set_flash('error', 'Kode voucher tidak valid, tidak aktif, atau telah kedaluwarsa.');
        }
    } else {
        unset($_SESSION['active_voucher']);
        unset($_SESSION['active_voucher_type']);
        unset($_SESSION['active_voucher_value']);
        unset($_SESSION['active_discount']);
        set_flash('success', 'Voucher berhasil dihapus.');
    }
    redirect(base_url('pelanggan/keranjang_checkout.php'));
}

if (!$isPaymentPhase) {
    $stmtCart = $pdo->prepare(
        "SELECT k.qty, m.id_menu, m.nama_menu, m.harga FROM keranjang k JOIN menu m ON k.id_menu = m.id_menu WHERE k.id_user = ? ORDER BY k.id_keranjang DESC"
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
$metodePembayaran = 'QRIS';

if ($isPaymentPhase) {
    // Phase: Payment (Session cart already cleared)
    // Fetch data from database instead of session
    $id_pesanan = (int) $_GET['id_pesanan'];

    // 1. Order main info
    $stmtOrder = $pdo->prepare("SELECT * FROM pesanan WHERE id_pesanan = ? AND id_user = ?");
    $stmtOrder->execute([$id_pesanan, $_SESSION['id_user']]);
    $order = $stmtOrder->fetch();

    if (!$order) {
        set_flash('error', 'Pesanan tidak ditemukan.');
        redirect(base_url('pelanggan/dashboard.php'));
    }

    $total = (float) $order['total_harga'];
    $currentNoMeja = $order['no_meja'];

    // 1.5. Payment info (to check chosen method Cash vs QRIS)
    $stmtBayarInfo = $pdo->prepare("SELECT * FROM pembayaran WHERE id_pesanan = ?");
    $stmtBayarInfo->execute([$id_pesanan]);
    $pembayaran = $stmtBayarInfo->fetch();
    $metodePembayaran = $pembayaran ? $pembayaran['metode'] : 'QRIS';

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

    if (isset($_SESSION['active_voucher'])) {
        $vType = $_SESSION['active_voucher_type'] ?? 'Persentase';
        $vVal = $_SESSION['active_voucher_value'] ?? 0.0;
        if ($vType === 'Persentase') {
            $discount = $subtotal * ($vVal / 100);
        } else {
            $discount = min($vVal, $subtotal);
        }
        $_SESSION['active_discount'] = $discount;
    }

    $tax = ($subtotal - $discount) * 0.11;
    $total = ($subtotal - $discount) + $tax;
}

// Payment Gateway Logic (only for QRIS)
$paymentResult = null;
if ($isPaymentPhase && $metodePembayaran === 'QRIS') {
    $payment = new QrisCepat();
    // UNTUK UPK: Hardcode nominal ke 10.000 agar gampang dites presentasi,
    // tapi di tampilan (frontend) tetap muncul harga asli.
    $presentationAmount = 10000;
    $paymentResult = $payment->deposit($presentationAmount);
}

$title = 'Checkout';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

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

        <?php if ($isPaymentPhase): ?>
            <?php if ($metodePembayaran === 'Tunai'): ?>
                <div class="mb-5 p-4 border border-warning bg-black d-flex flex-column flex-md-row gap-4 align-items-center">
                    <div class="text-warning" style="font-size: 40px;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/><path d="M6 14h2m4 0h2"/></svg>
                    </div>
                    <div class="flex-grow-1">
                        <h3 class="font-display text-gold mb-2" style="font-size: 20px;">Menunggu Pembayaran di Kasir</h3>
                        <p class="small text-secondary mb-3" style="font-size: 11px;">Silakan datangi meja kasir dan sebutkan nomor meja Anda atau ID pesanan Anda untuk melakukan pembayaran tunai.</p>

                        <div class="p-3 border border-secondary" style="font-size: 12px; background: rgba(255,255,255,0.05);">
                            <div class="d-flex justify-content-between border-bottom border-soft pb-2 mb-2">
                                <span class="text-secondary">Nomor Meja</span>
                                <span class="text-white fw-bold">Meja <?= htmlspecialchars($currentNoMeja) ?></span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom border-soft pb-2 mb-2">
                                <span class="text-secondary">Total Bayar</span>
                                <span class="text-white fw-bold"><?= rupiah((float)$total) ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-secondary">Metode Pembayaran</span>
                                <span class="text-warning fw-medium">Tunai (Cash)</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php elseif ($metodePembayaran === 'Kartu Kredit'): ?>
                <div class="mb-5 p-4 border border-gold bg-black d-flex flex-column flex-md-row gap-4 align-items-center" style="background-color: rgba(20, 18, 14, 0.45); border-color: rgba(201, 168, 76, 0.3) !important;">
                    <div class="flex-shrink-0">
                        <!-- Luxury Credit Card Graphic -->
                        <div class="p-4 border border-gold border-opacity-30 rounded-3 position-relative overflow-hidden" style="width: 280px; height: 160px; background: linear-gradient(135deg, #151515 0%, #2a251b 50%, #101010 100%); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                            <div style="position: absolute; top: 0; right: 0; width: 120px; height: 120px; background: radial-gradient(circle, rgba(201, 168, 76, 0.08) 0%, transparent 70%); pointer-events: none;"></div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-gold font-display small" style="font-size: 9px; letter-spacing: 0.1em; color: var(--gold);">LUMIÈRE BLACK CARD</span>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C9A84C" stroke-width="1.5"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                            </div>
                            <div class="mb-3" style="margin-top: 15px;">
                                <span class="text-secondary d-block" style="font-size: 7px; letter-spacing: 0.05em; text-transform: uppercase;">Nomor Kartu</span>
                                <span class="text-white font-display" style="font-size: 14px; letter-spacing: 0.15em;">•••• •••• •••• 8888</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-end" style="margin-top: 5px;">
                                <div>
                                    <span class="text-secondary d-block" style="font-size: 7px; letter-spacing: 0.05em; text-transform: uppercase;">Pemegang Kartu</span>
                                    <span class="text-white small text-uppercase" style="font-size: 9px;"><?= htmlspecialchars($_SESSION['username'] ?? 'MEMBER SETIA') ?></span>
                                </div>
                                <div class="text-end">
                                    <span class="text-secondary d-block" style="font-size: 7px; letter-spacing: 0.05em; text-transform: uppercase;">Berlaku</span>
                                    <span class="text-white small" style="font-size: 9px;">12 / 29</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1 w-100">
                        <h3 class="font-display text-gold mb-2" style="font-size: 20px;">Pembayaran Kartu Kredit Premium</h3>
                        <p class="small text-secondary mb-3" style="font-size: 11px;">Silakan tekan tombol simulasi pembayaran di panel sebelah kanan untuk menyelesaikan transaksi menggunakan kartu kredit Anda secara instan.</p>

                        <div class="p-3 border border-secondary w-100" style="font-size: 12px; background: rgba(255,255,255,0.05);">
                            <div class="d-flex justify-content-between border-bottom border-soft pb-2 mb-2">
                                <span class="text-secondary">Nomor Meja</span>
                                <span class="text-white fw-bold">Meja <?= htmlspecialchars($currentNoMeja) ?></span>
                            </div>
                            <div class="d-flex justify-content-between border-bottom border-soft pb-2 mb-2">
                                <span class="text-secondary">Total Bayar</span>
                                <span class="text-white fw-bold"><?= rupiah((float)$total) ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-secondary">Status Pembayaran</span>
                                <span class="text-warning fw-medium blink">Menunggu Otorisasi...</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php elseif ($paymentResult): ?>
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
                            <p class="small text-secondary mb-3" style="font-size: 11px;">Gunakan aplikasi mobile banking or e-wallet (OVO, GoPay, Dana).</p>

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
        <?php endif; ?>

        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="checkout-info-card h-100">
                    <div class="checkout-info-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <div>
                        <p class="text-white small mb-0 fw-medium">Metode Pembayaran</p>
                        <p class="text-secondary" style="font-size: 12px;">
                            <?= htmlspecialchars($metodePembayaran === 'Tunai' ? 'Tunai (Bayar di Kasir)' : 'QRIS Otomatis') ?>
                        </p>
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
                        <p class="text-secondary" style="font-size: 12px;">Meja: <?= htmlspecialchars($currentNoMeja) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!$isPaymentPhase): ?>
        <div class="promo-section animate-fade-in-up">
            <h3 class="font-display text-white mb-3" style="font-size: 24px;">Voucher & Promo</h3>
            <p class="text-secondary small mb-4" style="font-size: 12px; color: var(--text-secondary);">Pilih salah satu penawaran eksklusif Lumière di bawah ini untuk digunakan pada pesanan Anda:</p>
            
            <div class="row g-3 mb-4">
                <!-- Option for "No Voucher" -->
                <div class="col-md-6">
                    <form method="POST" id="form-voucher-none">
                        <input type="hidden" name="action" value="apply_voucher">
                        <input type="hidden" name="voucher_code" value="">
                        <div class="p-3 voucher-card cursor-pointer d-flex align-items-center justify-content-center <?= !isset($_SESSION['active_voucher']) ? 'active-gold' : '' ?>" 
                             style="min-height: 95px; border-radius: 0; cursor: pointer;"
                             onclick="document.getElementById('form-voucher-none').submit();">
                            <span class="text-white small fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.1em; color: <?= !isset($_SESSION['active_voucher']) ? 'var(--gold) !important' : 'var(--text-secondary)' ?>;">Tanpa Voucher</span>
                        </div>
                    </form>
                </div>
                
                <?php foreach ($availableVouchers as $av): ?>
                    <?php 
                        $lblVal = $av['jenis_voucher'] === 'Persentase' ? $av['nilai_voucher'] . '%' : rupiah((float)$av['nilai_voucher']);
                        $isActive = (isset($_SESSION['active_voucher']) && $_SESSION['active_voucher'] === $av['kode_voucher']);
                        $formId = 'form-voucher-' . htmlspecialchars($av['kode_voucher']);
                    ?>
                    <div class="col-md-6">
                        <form method="POST" id="<?= $formId ?>">
                            <input type="hidden" name="action" value="apply_voucher">
                            <input type="hidden" name="voucher_code" value="<?= htmlspecialchars($av['kode_voucher']); ?>">
                            <div class="p-3 voucher-card cursor-pointer d-flex align-items-center justify-content-start <?= $isActive ? 'active-gold' : '' ?>"
                                 style="min-height: 95px; border-radius: 0; cursor: pointer;"
                                 onclick="document.getElementById('<?= $formId ?>').submit();">
                                
                                <?php if ($isActive): ?>
                                    <!-- Small gold badge in corner -->
                                    <span class="position-absolute top-0 end-0 bg-gold text-black px-2 py-0.5 small fw-bold" style="font-size: 8px; letter-spacing: 0.05em; border-radius: 0;">AKTIF</span>
                                <?php endif; ?>
                                
                                <div class="w-100 text-start">
                                    <h5 class="text-white mb-1" style="font-size: 13px; font-family: var(--font-sans);"><?= htmlspecialchars($av['nama_voucher']); ?></h5>
                                    <p class="text-secondary small mb-2" style="font-size: 10px; color: var(--text-secondary);"><?= htmlspecialchars($av['kode_voucher']); ?></p>
                                    <span class="h6 text-gold mb-0 d-block" style="font-size: 14px; font-family: var(--font-serif); font-weight: 600; color: var(--gold) !important;">Potongan: <?= $lblVal; ?></span>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (isset($_SESSION['active_voucher'])): ?>
                <?php
                $vTypeDisplay = ($_SESSION['active_voucher_type'] === 'Persentase') ? $_SESSION['active_voucher_value'] . '%' : 'Rp ' . number_format((float)$_SESSION['active_voucher_value'], 0, ',', '.');
                ?>
                <div class="mt-3 p-3 border border-success bg-success bg-opacity-10 text-success small d-flex align-items-center gap-2" style="border-radius: 0;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <span>Voucher <strong><?= htmlspecialchars($_SESSION['active_voucher']) ?></strong> aktif (Potongan <?= $vTypeDisplay ?>).</span>
                </div>
            <?php endif; ?>
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
            <span class="text-uppercase small text-secondary" style="font-size: 12px;">Total Tagihan</span>
            <span class="font-display text-gold" style="font-size: 32px;"><?= rupiah((float)$total); ?></span>
        </div>

        <?php if (!$isPaymentPhase): ?>
        <form action="<?= base_url('actions/pesanan/checkout.php'); ?>" method="post" id="checkout-payment-form">
            <input type="hidden" name="metode_pembayaran" id="selected_payment_method" value="QRIS">
            
            <div class="mb-4">
                <label class="text-secondary small mb-2 d-block text-uppercase fw-semibold" style="letter-spacing: 0.08em; font-size: 10px; color: var(--text-secondary);">Pilih Metode Pembayaran</label>
                <div class="d-flex flex-column gap-3">
                    
                    <!-- QRIS Card Option -->
                    <div class="p-3 border payment-card cursor-pointer d-flex align-items-center gap-3 active-gold" 
                         data-value="QRIS" 
                         style="cursor: pointer; transition: all 0.3s ease; border-radius: 0; background: rgba(0,0,0,0.45); border-color: var(--gold) !important;"
                         onclick="selectPayment('QRIS', this)">
                        <div class="payment-icon text-gold" style="font-size: 20px; color: var(--gold) !important;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><rect x="7" y="7" width="3" height="3"/><rect x="14" y="7" width="3" height="3"/><rect x="7" y="14" width="3" height="3"/><rect x="14" y="14" width="3" height="3"/></svg>
                        </div>
                        <div>
                            <h6 class="text-white m-0" style="font-size: 13px; font-weight: 500;">QRIS (Digital Otomatis)</h6>
                            <p class="text-secondary small m-0" style="font-size: 10px; color: var(--text-secondary);">Otorisasi instan via QrisCepat.</p>
                        </div>
                    </div>
                    
                    <!-- Tunai Card Option -->
                    <div class="p-3 border payment-card cursor-pointer d-flex align-items-center gap-3" 
                         data-value="Tunai" 
                         style="cursor: pointer; transition: all 0.3s ease; border-radius: 0; background: rgba(0,0,0,0.45); border-color: rgba(255,255,255,0.15) !important;"
                         onclick="selectPayment('Tunai', this)">
                        <div class="payment-icon text-gold" style="font-size: 20px; color: var(--gold) !important;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        </div>
                        <div>
                            <h6 class="text-white m-0" style="font-size: 13px; font-weight: 500;">Tunai / Cash</h6>
                            <p class="text-secondary small m-0" style="font-size: 10px; color: var(--text-secondary);">Bayar di meja kasir secara langsung.</p>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            <button type="submit" class="btn btn-warning w-100 py-3 mb-2" style="font-size: 12px; font-weight: 600; border-radius: 0;">Buat Pesanan & Bayar</button>
            <a href="<?= base_url('pelanggan/keranjang.php'); ?>" class="btn btn-outline-warning w-100 py-3" style="font-size: 12px; font-weight: 600; border-radius: 0;">Kembali</a>
        </form>
        <?php else: ?>
        <div class="mb-4 p-3 border border-warning bg-black text-center animate-fade-in-up" style="background-color: rgba(20,18,14,0.45) !important;">
            <h6 class="text-warning text-uppercase mb-2" style="font-size: 11px; letter-spacing: 0.1em; font-weight: 600;">Petunjuk Pembayaran</h6>
            <?php if ($metodePembayaran === 'Tunai'): ?>
                <p class="text-secondary small mb-0" style="font-size: 11px;">Silakan datang ke Kasir untuk melunasi pembayaran pesanan Anda.</p>
            <?php else: ?>
                <p class="text-secondary small mb-0" style="font-size: 11px;">Silakan selesaikan pembayaran QRIS Anda dengan memindai kode QR di atas.</p>
            <?php endif; ?>
        </div>
        <a href="<?= base_url('pelanggan/pesanan_status.php'); ?>" class="btn btn-warning w-100 py-3" style="font-size: 12px; font-weight: 600; border-radius: 0;">Pantau Sajian Anda</a>
        <a href="<?= base_url('pelanggan/dashboard.php'); ?>" class="btn btn-outline-warning w-100 py-3 mt-2" style="font-size: 12px; font-weight: 600; border-radius: 0;">Dashboard</a>
        <?php endif; ?>
    </aside>
</section>

<style>
@keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
.blink { animation: blink 2s infinite; }

.payment-card, .voucher-card {
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    background-color: rgba(20, 18, 14, 0.45) !important;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
    position: relative;
    overflow: hidden;
}
.payment-card::before, .voucher-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at top right, rgba(201, 168, 76, 0.04) 0%, transparent 70%);
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.4s ease;
}
.payment-card:hover::before, .voucher-card:hover::before {
    opacity: 1;
}
.payment-card:hover, .voucher-card:hover {
    border-color: rgba(201, 168, 76, 0.45) !important;
    background-color: rgba(20, 18, 14, 0.6) !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4) !important;
}
.payment-card.active-gold, .voucher-card.active-gold {
    border-color: var(--gold) !important;
    background-color: rgba(201, 168, 76, 0.06) !important;
    box-shadow: 0 8px 25px rgba(201, 168, 76, 0.1) !important;
}
.payment-card.active-gold::before, .voucher-card.active-gold::before {
    opacity: 1;
    background: radial-gradient(circle at top right, rgba(201, 168, 76, 0.08) 0%, transparent 60%);
}
</style>

<script>
function selectPayment(value, element) {
    document.getElementById('selected_payment_method').value = value;
    
    // Toggle active classes
    document.querySelectorAll('.payment-card').forEach(card => {
        card.classList.remove('active-gold');
        card.style.setProperty('border-color', 'rgba(255,255,255,0.15)', 'important');
    });
    
    element.classList.add('active-gold');
    element.style.setProperty('border-color', 'var(--gold)', 'important');
}
</script>

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
