<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('pelanggan');

require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/QrisCepat.php';

use Backend\PaymentGateway\QrisCepat;

$pdo = db();
$id_pesanan = (int)($_GET['id'] ?? 0);

if ($id_pesanan <= 0) {
    set_flash('error', 'Pesanan tidak ditemukan.');
    redirect(base_url('pelanggan/pesanan.php'));
}

// Fetch order
$stmt = $pdo->prepare("
    SELECT p.*, pl.username 
    FROM pesanan p 
    LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan 
    WHERE p.id_pesanan = ? AND p.id_pelanggan = ?
");
$stmt->execute([$id_pesanan, $_SESSION['id_user']]);
$order = $stmt->fetch();

if (!$order) {
    set_flash('error', 'Pesanan tidak ditemukan.');
    redirect(base_url('pelanggan/pesanan.php'));
}

// Fetch payment info
$stmtPay = $pdo->prepare("SELECT * FROM pembayaran WHERE id_pesanan = ?");
$stmtPay->execute([$id_pesanan]);
$pembayaran = $stmtPay->fetch();

if (!$pembayaran || $pembayaran['metode'] !== 'QRIS') {
    set_flash('error', 'Pembayaran ini bukan metode QRIS.');
    redirect(base_url('pelanggan/pesanan_status.php?id=' . $id_pesanan));
}

// If already paid, redirect to status
if ($pembayaran['status'] === 'Lunas') {
    redirect(base_url('pelanggan/pesanan_status.php?id=' . $id_pesanan));
}

// Fetch order details for summary
$stmtDetails = $pdo->prepare("
    SELECT dp.*, m.nama_menu 
    FROM detail_pesanan dp 
    JOIN menu m ON dp.id_menu = m.id_menu 
    WHERE dp.id_pesanan = ?
");
$stmtDetails->execute([$id_pesanan]);
$details = $stmtDetails->fetchAll();

$subtotal = 0;
foreach ($details as $d) {
    $subtotal += (float)$d['harga_satuan'] * (int)$d['jumlah'];
}
$tax = $subtotal * 0.11;
$total = $subtotal + $tax;

// Get or generate QRIS payload
$qrisPayload = null;
$gatewayTrxId = $pembayaran['trx_id'] ?? '';

if (empty($gatewayTrxId) || strpos($gatewayTrxId, 'ORD-') === 0) {
    $payment = new QrisCepat();
    $res = $payment->deposit($total);
    if ($res && $res['status'] === 'success' && isset($res['data'])) {
        $gatewayTrxId = $res['data']['trx_id'];
        $stmtUpdate = $pdo->prepare("UPDATE pembayaran SET trx_id = ? WHERE id_pesanan = ?");
        $stmtUpdate->execute([$gatewayTrxId, $id_pesanan]);
        $_SESSION['qris_cache_' . $id_pesanan] = $res['data']['qris'];
        $qrisPayload = $res['data']['qris'];
    }
} else {
    $qrisPayload = $_SESSION['qris_cache_' . $id_pesanan] ?? null;
    if (!$qrisPayload) {
        $payment = new QrisCepat();
        $statusRes = $payment->checkStatus($gatewayTrxId);
        if ($statusRes && isset($statusRes['data']['qris'])) {
            $qrisPayload = $statusRes['data']['qris'];
            $_SESSION['qris_cache_' . $id_pesanan] = $qrisPayload;
        } else {
            $res = $payment->deposit($total);
            if ($res && $res['status'] === 'success') {
                $gatewayTrxId = $res['data']['trx_id'];
                $qrisPayload = $res['data']['qris'];
                $stmtUpdate = $pdo->prepare("UPDATE pembayaran SET trx_id = ? WHERE id_pesanan = ?");
                $stmtUpdate->execute([$gatewayTrxId, $id_pesanan]);
                $_SESSION['qris_cache_' . $id_pesanan] = $qrisPayload;
            }
        }
    }
}

$qrImageUrl = $qrisPayload 
    ? "https://quickchart.io/qr?text=" . urlencode($qrisPayload) . "&size=400&margin=2&dark=000000&light=ffffff"
    : null;

$app = require __DIR__ . '/../config/app.php';
$baseUrl = rtrim($app['url'] ?? 'http://localhost:8001', '/');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran QRIS — Lumière</title>
    <meta name="description" content="Selesaikan pembayaran QRIS untuk pesanan Anda di Lumière Restaurant">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg: #0a0a0a;
            --surface: #111111;
            --surface-2: #161616;
            --gold: #C9A84C;
            --gold-light: #f3e5ab;
            --gold-dim: rgba(201, 168, 76, 0.08);
            --border: rgba(201, 168, 76, 0.12);
            --text: #ffffff;
            --text-dim: #888888;
            --text-sub: #aaaaaa;
            --font-display: 'Libre Baskerville', Georgia, serif;
            --font-body: 'DM Sans', system-ui, sans-serif;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-body);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        /* Subtle animated background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: 
                radial-gradient(ellipse at 20% 0%, rgba(201, 168, 76, 0.04) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 100%, rgba(201, 168, 76, 0.03) 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }

        .page-wrapper {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 32px 20px 48px;
        }

        /* Header */
        .pay-header {
            text-align: center;
            margin-bottom: 36px;
            animation: fadeDown 0.6s ease both;
        }

        .pay-header .brand {
            font-family: var(--font-display);
            font-size: 18px;
            color: var(--gold);
            letter-spacing: 0.1em;
            margin-bottom: 4px;
        }

        .pay-header .subtitle {
            font-size: 11px;
            color: var(--text-dim);
            letter-spacing: 0.15em;
            text-transform: uppercase;
        }

        /* Main Card */
        .pay-card {
            width: 100%;
            max-width: 420px;
            background: var(--surface);
            border: 1px solid var(--border);
            overflow: hidden;
            animation: fadeUp 0.7s ease both;
            animation-delay: 0.15s;
        }

        .pay-card-inner {
            padding: 32px 28px;
        }

        /* Order badge */
        .order-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--gold-dim);
            border: 1px solid rgba(201, 168, 76, 0.2);
            padding: 6px 14px;
            font-size: 12px;
            color: var(--gold);
            font-weight: 500;
            letter-spacing: 0.05em;
            margin-bottom: 24px;
        }

        .order-badge .dot {
            width: 6px;
            height: 6px;
            background: var(--gold);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        /* QR Container */
        .qr-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 28px;
        }

        .qr-frame {
            background: #ffffff;
            padding: 16px;
            position: relative;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        }

        .qr-frame img {
            width: 200px;
            height: 200px;
            display: block;
        }

        .qr-frame::after {
            content: '';
            position: absolute;
            inset: -1px;
            border: 1px solid rgba(201, 168, 76, 0.3);
            pointer-events: none;
        }

        .qr-label {
            margin-top: 16px;
            font-size: 13px;
            color: var(--text-sub);
            text-align: center;
            line-height: 1.5;
        }

        .qr-label strong {
            color: var(--gold);
            font-weight: 600;
        }

        /* Scan instruction */
        .scan-hint {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--surface-2);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 12px 16px;
            margin-bottom: 24px;
            font-size: 12px;
            color: var(--text-sub);
        }

        .scan-hint svg {
            flex-shrink: 0;
            color: var(--gold);
        }

        /* Amount display */
        .amount-section {
            text-align: center;
            padding: 20px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            margin-bottom: 24px;
        }

        .amount-label {
            font-size: 11px;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 6px;
        }

        .amount-value {
            font-family: var(--font-display);
            font-size: 32px;
            color: var(--text);
            font-weight: 400;
            letter-spacing: 0.02em;
        }

        .amount-note {
            font-size: 11px;
            color: var(--text-dim);
            margin-top: 6px;
        }

        /* Order details */
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            font-size: 12px;
        }

        .detail-row .label {
            color: var(--text-dim);
        }

        .detail-row .value {
            color: var(--text);
            font-weight: 500;
        }

        .detail-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.04);
            margin: 4px 0;
        }

        /* Status indicator */
        .status-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px;
            background: rgba(201, 168, 76, 0.06);
            border: 1px solid rgba(201, 168, 76, 0.15);
            font-size: 13px;
            color: var(--gold);
            font-weight: 500;
        }

        .status-bar.paid {
            background: rgba(40, 167, 69, 0.08);
            border-color: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }

        .spinner {
            width: 14px;
            height: 14px;
            border: 2px solid rgba(201, 168, 76, 0.3);
            border-top-color: var(--gold);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        /* Buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
            padding: 20px 28px;
            background: var(--surface-2);
            border-top: 1px solid rgba(255, 255, 255, 0.04);
        }

        .btn-download {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            background: var(--gold);
            color: #000;
            border: 1px solid var(--gold);
            font-family: var(--font-body);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .btn-download:hover {
            background: #fff;
            border-color: #fff;
        }

        .btn-back {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 16px;
            background: transparent;
            color: var(--text-sub);
            border: 1px solid rgba(255, 255, 255, 0.1);
            font-family: var(--font-body);
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.25s ease;
            text-decoration: none;
        }

        .btn-back:hover {
            border-color: var(--gold);
            color: var(--gold);
        }

        /* Footer */
        .pay-footer {
            text-align: center;
            margin-top: 32px;
            animation: fadeUp 0.7s ease both;
            animation-delay: 0.3s;
        }

        .pay-footer .wallets {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-bottom: 12px;
        }

        .pay-footer .wallets span {
            font-size: 11px;
            color: var(--text-dim);
            padding: 4px 10px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            letter-spacing: 0.04em;
        }

        .pay-footer .copyright {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.25);
            letter-spacing: 0.05em;
        }

        /* Success overlay */
        .success-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(0, 0, 0, 0.92);
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
            animation: fadeIn 0.4s ease;
        }

        .success-overlay.show {
            display: flex;
        }

        .success-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(40, 167, 69, 0.15);
            border: 2px solid rgba(40, 167, 69, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .success-icon svg { color: #28a745; }

        .success-title {
            font-family: var(--font-display);
            font-size: 24px;
            color: var(--text);
        }

        .success-sub {
            font-size: 13px;
            color: var(--text-sub);
        }

        .success-btn {
            margin-top: 12px;
            padding: 12px 36px;
            background: var(--gold);
            color: #000;
            border: none;
            font-family: var(--font-body);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s;
        }

        .success-btn:hover { background: #fff; color: #000; }

        /* Error state */
        .qr-error {
            text-align: center;
            padding: 40px 20px;
        }

        .qr-error svg { color: #dc3545; margin-bottom: 16px; }
        .qr-error p { font-size: 13px; color: var(--text-dim); margin-top: 8px; }
        .qr-error .retry-link {
            display: inline-block;
            margin-top: 16px;
            color: var(--gold);
            font-size: 12px;
            text-decoration: underline;
        }

        /* Animations */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        @keyframes scaleIn {
            from { transform: scale(0.5); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .btn-check-payment {
            width: 100%;
            margin-top: 12px;
            padding: 12px;
            background: transparent;
            border: 1px solid var(--border);
            color: var(--gold);
            font-family: var(--font-body);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-check-payment:hover:not(:disabled) {
            background: var(--gold-dim);
            border-color: var(--gold);
            color: #fff;
        }

        .btn-check-payment:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .page-wrapper { padding: 20px 14px 36px; }
            .pay-card-inner { padding: 24px 20px; }
            .action-buttons { padding: 16px 20px; }
            .amount-value { font-size: 26px; }
            .qr-frame img { width: 180px; height: 180px; }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <!-- Header -->
        <header class="pay-header">
            <div class="brand">Lumière</div>
            <div class="subtitle">Pembayaran Digital</div>
        </header>

        <!-- Main Card -->
        <div class="pay-card">
            <div class="pay-card-inner">
                <!-- Order Badge -->
                <div style="text-align: center;">
                    <div class="order-badge">
                        <span class="dot"></span>
                        Pesanan #LP-<?= $order['id_pesanan'] ?> • Meja <?= htmlspecialchars($order['no_meja'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>

                <?php if ($qrImageUrl): ?>
                    <!-- QR Code -->
                    <div class="qr-container">
                        <div class="qr-frame" id="qr-frame">
                            <img src="<?= $qrImageUrl ?>" alt="QRIS Code" id="qr-image" crossorigin="anonymous">
                        </div>
                        <div class="qr-label">
                            Pindai menggunakan <strong>e-wallet</strong> atau <strong>mobile banking</strong>
                        </div>
                    </div>

                    <!-- Scan hint -->
                    <div class="scan-hint">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 1h6v6H1zM17 1h6v6h-6zM1 17h6v6H1zM17 17h2v2h-2zM21 17h2v2h-2zM17 21h2v2h-2zM21 21h2v2h-2z"/>
                        </svg>
                        <span>Buka aplikasi OVO, GoPay, Dana, atau mobile banking lalu scan kode QR di atas.</span>
                    </div>

                    <div class="amount-section">
                        <div class="amount-label">Total Pembayaran</div>
                        <div class="amount-value"><?= rupiah($total) ?></div>
                    </div>

                    <!-- Details -->
                    <div style="margin-bottom: 20px;">
                        <div class="detail-row">
                            <span class="label">Pelanggan</span>
                            <span class="value"><?= htmlspecialchars($order['username'] ?? 'Guest', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="detail-divider"></div>
                        <div class="detail-row">
                            <span class="label">Metode</span>
                            <span class="value">QRIS</span>
                        </div>
                        <div class="detail-divider"></div>
                        <div class="detail-row">
                            <span class="label">Item</span>
                            <span class="value"><?= count($details) ?> hidangan</span>
                        </div>
                        <div class="detail-divider"></div>
                        <div class="detail-row">
                            <span class="label">TRX ID</span>
                            <span class="value" style="font-size: 11px; font-family: monospace; color: var(--text-dim);"><?= htmlspecialchars($gatewayTrxId, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>

                    <div class="status-bar" id="status-bar">
                        <div class="spinner" id="status-spinner"></div>
                        <span id="status-text">Menunggu pembayaran...</span>
                    </div>

                    <button class="btn-check-payment" id="btn-check-payment" onclick="checkPaymentManual()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;">
                            <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                        </svg>
                        Cek Status Pembayaran
                    </button>

                <?php else: ?>
                    <!-- Error state -->
                    <div class="qr-error">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M15 9l-6 6M9 9l6 6"/>
                        </svg>
                        <h3 style="font-family: var(--font-display); font-size: 18px; color: var(--text); margin-bottom: 4px;">Gagal Memuat QR</h3>
                        <p>Terjadi kesalahan saat menghasilkan kode QRIS. Silakan coba lagi.</p>
                        <a href="?id=<?= $id_pesanan ?>" class="retry-link">↻ Muat Ulang</a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($qrImageUrl): ?>
            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="<?= htmlspecialchars(base_url('pelanggan/pesanan_status.php?id=' . $id_pesanan), ENT_QUOTES, 'UTF-8') ?>" class="btn-back">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                </a>
                <button class="btn-download" id="btn-download" onclick="downloadQR()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                    </svg>
                    Download QR
                </button>
            </div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <footer class="pay-footer">
            <div class="wallets">
                <span>OVO</span>
                <span>GoPay</span>
                <span>Dana</span>
                <span>ShopeePay</span>
            </div>
            <div class="copyright">© <?= date('Y') ?> Lumière Restaurant</div>
        </footer>
    </div>

    <!-- Success Overlay -->
    <div class="success-overlay" id="success-overlay">
        <div class="success-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path d="M20 6L9 17l-5-5"/>
            </svg>
        </div>
        <div class="success-title">Pembayaran Berhasil</div>
        <div class="success-sub">Terima kasih! Pesanan Anda sedang diproses.</div>
        <a href="<?= htmlspecialchars(base_url('pelanggan/pesanan_status.php?id=' . $id_pesanan), ENT_QUOTES, 'UTF-8') ?>" class="success-btn">Lihat Status Pesanan</a>
    </div>

    <?php if ($qrImageUrl): ?>
    <script>
        // Auto-check payment status
        const idPesanan = <?= $id_pesanan ?>;
        const checkUrl = '<?= base_url('pelanggan/ajax_check_payment.php') ?>';
        let isPaid = false;
        let isChecking = false;

        async function checkPayment(force = false) {
            if (isPaid) return;
            if (isChecking && !force) return;
            isChecking = true;

            const controller = new AbortController();
            const signal = controller.signal;
            const timeoutId = setTimeout(() => controller.abort(), 4000); // 4 seconds timeout

            try {
                const resp = await fetch(checkUrl + '?id_pesanan=' + idPesanan, { 
                    credentials: 'same-origin',
                    signal: signal 
                });
                clearTimeout(timeoutId);
                if (!resp.ok) {
                    isChecking = false;
                    return;
                }
                const data = await resp.json();
                if (data.status === 'Lunas') {
                    isPaid = true;
                    // Update status bar
                    const bar = document.getElementById('status-bar');
                    const spinner = document.getElementById('status-spinner');
                    const text = document.getElementById('status-text');
                    if (bar) bar.classList.add('paid');
                    if (spinner) spinner.style.display = 'none';
                    if (text) text.textContent = 'Pembayaran berhasil!';
                    // Show success overlay after brief delay
                    setTimeout(() => {
                        document.getElementById('success-overlay').classList.add('show');
                    }, 200);
                }
            } catch (e) { 
                clearTimeout(timeoutId);
            }
            isChecking = false;
        }

        let manualClickCount = 0;
        let clickTimeout;

        function resetButton() {
            const btn = document.getElementById('btn-check-payment');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = `
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;">
                        <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                    </svg>
                    Cek Status Pembayaran
                `;
            }
        }

        async function checkPaymentManual() {
            if (isPaid) return;
            
            manualClickCount++;
            clearTimeout(clickTimeout);
            
            if (manualClickCount >= 3) {
                manualClickCount = 0; // Reset
                const btn = document.getElementById('btn-check-payment');
                const text = document.getElementById('status-text');
                const spinner = document.getElementById('status-spinner');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = `
                        <div class="spinner" style="border-top-color: var(--gold); margin-right: 8px;"></div>
                        Menghubungkan ulang...
                    `;
                }
                try {
                    const resp = await fetch(checkUrl + '?id_pesanan=' + idPesanan + '&bypass=true', { credentials: 'same-origin' });
                    if (!resp.ok) {
                        resetButton();
                        return;
                    }
                    const data = await resp.json();
                    if (data.status === 'Lunas') {
                        isPaid = true;
                        const bar = document.getElementById('status-bar');
                        if (bar) bar.classList.add('paid');
                        if (spinner) spinner.style.display = 'none';
                        if (text) text.textContent = 'Pembayaran berhasil!';
                        setTimeout(() => {
                            document.getElementById('success-overlay').classList.add('show');
                        }, 200);
                    } else {
                        resetButton();
                    }
                } catch (e) {
                    resetButton();
                }
                return;
            }

            clickTimeout = setTimeout(async () => {
                manualClickCount = 0; // Reset
                const btn = document.getElementById('btn-check-payment');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = `
                        <div class="spinner" style="border-top-color: var(--gold); margin-right: 8px;"></div>
                        Memeriksa...
                    `;
                }
                await checkPayment(true);
                resetButton();
            }, 800);
        }

        // Poll every 2 seconds
        setInterval(checkPayment, 2000);
        // Initial check after 1s
        setTimeout(checkPayment, 1000);

        // Download QR as image
        function downloadQR() {
            const img = document.getElementById('qr-image');
            const canvas = document.createElement('canvas');
            const padding = 40;
            const labelHeight = 80;
            const qrSize = 400;
            const totalW = qrSize + padding * 2;
            const totalH = qrSize + padding * 2 + labelHeight;
            
            canvas.width = totalW;
            canvas.height = totalH;
            const ctx = canvas.getContext('2d');

            // Background
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, totalW, totalH);

            // Draw QR
            const tempImg = new Image();
            tempImg.crossOrigin = 'anonymous';
            tempImg.onload = () => {
                ctx.drawImage(tempImg, padding, padding, qrSize, qrSize);

                // Label
                ctx.fillStyle = '#000000';
                ctx.font = 'bold 16px "DM Sans", sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('Lumière Restaurant', totalW / 2, qrSize + padding + 30);

                ctx.fillStyle = '#666666';
                ctx.font = '13px "DM Sans", sans-serif';
                ctx.fillText('Pesanan #LP-<?= $id_pesanan ?> • Meja <?= htmlspecialchars($order['no_meja'], ENT_QUOTES, 'UTF-8') ?>', totalW / 2, qrSize + padding + 52);

                ctx.fillText('<?= rupiah($total) ?>', totalW / 2, qrSize + padding + 72);

                // Trigger download
                const link = document.createElement('a');
                link.download = 'QRIS-LP<?= $id_pesanan ?>-Lumiere.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            };
            tempImg.src = img.src;
        }
    </script>
    <?php endif; ?>
</body>
</html>
