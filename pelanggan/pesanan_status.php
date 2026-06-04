<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('pelanggan');

$title = 'Status Pesanan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

$userId = $_SESSION['id_user'] ?? 0;
$pdo = db();

$idParam = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idParam > 0) {
    $stmt = $pdo->prepare('
        SELECT p.*, pb.metode AS metode_pembayaran, pb.status AS status_pembayaran, pb.trx_id
        FROM pesanan p
        LEFT JOIN pembayaran pb ON p.id_pesanan = pb.id_pesanan
        WHERE p.id_pelanggan = ? AND p.id_pesanan = ?
        LIMIT 1
    ');
    $stmt->execute([$userId, $idParam]);
} else {
    // Fetch the latest active order
    $stmt = $pdo->prepare('
        SELECT p.*, pb.metode AS metode_pembayaran, pb.status AS status_pembayaran, pb.trx_id
        FROM pesanan p
        LEFT JOIN pembayaran pb ON p.id_pesanan = pb.id_pesanan
        WHERE p.id_pelanggan = ? AND p.status_pesanan NOT IN ("Selesai", "Dibatalkan")
        ORDER BY p.tanggal_pesanan DESC 
        LIMIT 1
    ');
    $stmt->execute([$userId]);
}
$order = $stmt->fetch();

$details = [];
if ($order) {
    $stmtDetails = $pdo->prepare('
        SELECT dp.*, m.nama_menu
        FROM detail_pesanan dp
        JOIN menu m ON dp.id_menu = m.id_menu
        WHERE dp.id_pesanan = ?
    ');
    $stmtDetails->execute([$order['id_pesanan']]);
    $details = $stmtDetails->fetchAll();
}

ob_start();
?>
<section class="row g-5">
    <div class="col-lg-8 animate-fade-in-up">
        <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
            <div class="d-flex flex-column gap-2 flex-md-row align-items-md-end justify-content-md-between border-bottom border-soft pb-4 mb-4">
                <div>
                    <h3 class="h3 mb-1 text-warning font-display" style="font-size: 24px;">Status Sajian Terkini</h3>
                    <p class="text-secondary small mb-0">Pantau proses persiapan hidangan Anda secara langsung dari meja dapur chef kami.</p>
                </div>
                <a class="btn btn-outline-warning rounded-0 fw-medium px-3 py-2 text-white" style="font-size: 12px;" href="<?= htmlspecialchars(base_url('pelanggan/pesanan.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
            </div>

            <?php if (!$order): ?>
                <div class="py-5 text-center">
                    <p class="text-secondary">Anda tidak memiliki pesanan aktif saat ini.</p>
                    <a class="btn btn-warning mt-3 rounded-0 fw-medium px-4 py-2" style="font-size: 11px;" href="<?= htmlspecialchars(base_url('pelanggan/menu.php'), ENT_QUOTES, 'UTF-8'); ?>">Lihat Menu</a>
                </div>
            <?php else: ?>
                <?php
                $status = $order['status_pesanan'];
                
                // Done steps
                $done1 = !in_array($status, ['Menunggu Pembayaran']);
                $done2 = !in_array($status, ['Menunggu Pembayaran', 'Diproses']);
                $done3 = !in_array($status, ['Menunggu Pembayaran', 'Diproses', 'Sedang Disiapkan']);
                $done4 = !in_array($status, ['Menunggu Pembayaran', 'Diproses', 'Sedang Disiapkan', 'Siap Saji']);
                $done5 = ($status === 'Selesai');

                // Active steps (currently highlighted)
                $active1 = ($status === 'Menunggu Pembayaran');
                $active2 = ($status === 'Diproses');
                $active3 = ($status === 'Sedang Disiapkan');
                $active4 = ($status === 'Siap Saji');
                $active5 = ($status === 'Selesai');
                ?>

                <div id="unpaid-warning-banner" class="mb-4 p-4 border border-warning animate-fade-in-up <?= ($status === 'Menunggu Pembayaran') ? '' : 'hidden'; ?>" style="background: rgba(201, 168, 76, 0.05); border-color: var(--gold) !important;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-gold" style="font-size: 24px;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        </div>
                        <div>
                            <h5 class="text-gold font-display mb-1" style="font-size: 16px; font-weight: 600;">Menunggu Pembayaran</h5>
                            <p class="text-secondary small mb-0">Pesanan Anda telah kami catat. Silakan selesaikan pembayaran untuk mengirimkan pesanan ke dapur dan memulai persiapan hidangan.</p>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mt-3">
                        <?php if (($order['metode_pembayaran'] ?? 'QRIS') === 'QRIS'): ?>
                            <a href="<?= base_url('pelanggan/keranjang_checkout.php?action=pay&trx=' . ($order['trx_id'] ?? '') . '&id_pesanan=' . $order['id_pesanan']); ?>" class="btn btn-warning py-2 px-4" style="font-size: 11px; font-weight: 600; border-radius: 0;">Selesaikan Pembayaran (QRIS)</a>
                        <?php else: ?>
                            <button class="btn btn-outline-warning py-2 px-4 text-white" style="font-size: 11px; font-weight: 600; border-radius: 0;" disabled>Silakan Lakukan Pembayaran Tunai di Kasir (Meja <?= htmlspecialchars((string)$order['no_meja'], ENT_QUOTES, 'UTF-8'); ?>)</button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mt-4 border border-soft bg-black p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-soft pb-3">
                        <div>
                            <p class="text-secondary small mb-1">ID Pesanan Anda</p>
                            <h4 class="font-display text-gold mb-0" style="font-size: 22px;">#LP-<?= $order['id_pesanan']; ?></h4>
                        </div>
                        <span id="order-status-badge" class="badge bg-warning text-dark px-3 py-2" style="font-size: 12px;"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>

                    <div class="d-flex flex-column gap-4 mt-2">
                        <!-- Step 1 -->
                        <div class="d-flex align-items-start gap-3">
                            <div id="step-circle-1" class="d-flex h-8 w-8 align-items-center justify-content-center <?= ($done1 || $active1) ? 'bg-warning text-dark fw-bold' : 'bg-secondary text-light'; ?> rounded-circle" style="font-size: 12px;">1</div>
                            <div>
                                <p id="step-title-1" class="fw-semibold text-light mb-1" style="font-size: 14px;">Konfirmasi Pembayaran</p>
                                <p id="step-detail-1" class="small text-secondary mb-0"><?= $active1 ? 'Silakan selesaikan pembayaran via ' . htmlspecialchars($order['metode_pembayaran'] ?? 'QRIS') . ' Anda.' : 'Pembayaran terkonfirmasi • Lunas via ' . htmlspecialchars($order['metode_pembayaran'] ?? 'QRIS'); ?></p>
                            </div>
                        </div>
                        <!-- Step 2 -->
                        <div class="d-flex align-items-start gap-3">
                            <div id="step-circle-2" class="d-flex h-8 w-8 align-items-center justify-content-center <?= ($done2 || $active2) ? 'bg-warning text-dark fw-bold' : 'bg-secondary text-light'; ?> rounded-circle" style="font-size: 12px;">2</div>
                            <div>
                                <p id="step-title-2" class="fw-semibold <?= ($done2 || $active2) ? 'text-light' : 'text-secondary'; ?> mb-1" style="font-size: 14px;">Pesanan Diterima</p>
                                <p id="step-detail-2" class="small text-secondary mb-0"><?= $active1 ? 'Menunggu pembayaran diselesaikan...' : 'Pesanan telah masuk ke antrean chef • Meja ' . htmlspecialchars((string)$order['no_meja'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>
                        <!-- Step 3 -->
                        <div class="d-flex align-items-start gap-3">
                            <div id="step-circle-3" class="d-flex h-8 w-8 align-items-center justify-content-center <?= ($done3 || $active3) ? 'bg-warning text-dark fw-bold' : 'bg-secondary text-light'; ?> rounded-circle" style="font-size: 12px;">3</div>
                            <div>
                                <p id="step-title-3" class="fw-semibold <?= ($done3 || $active3) ? 'text-light' : 'text-secondary'; ?> mb-1" style="font-size: 14px;">Persiapan Hidangan</p>
                                <p id="step-detail-3" class="small text-secondary mb-0"><?= ($done3 || $active3) ? 'Chef sedang mengolah dan meracik bahan masakan Anda.' : 'Menunggu antrean dapur chef...'; ?></p>
                            </div>
                        </div>
                        <!-- Step 4 -->
                        <div class="d-flex align-items-start gap-3">
                            <div id="step-circle-4" class="d-flex h-8 w-8 align-items-center justify-content-center <?= ($done4 || $active4) ? 'bg-warning text-dark fw-bold' : 'bg-secondary text-light'; ?> rounded-circle" style="font-size: 12px;">4</div>
                            <div>
                                <p id="step-title-4" class="fw-semibold <?= ($done4 || $active4) ? 'text-light' : 'text-secondary'; ?> mb-1" style="font-size: 14px;">Siap Disajikan</p>
                                <p id="step-detail-4" class="small text-secondary mb-0"><?= ($done4 || $active4) ? 'Hidangan matang sempurna dan siap disajikan oleh pelayan kami.' : 'Menunggu masakan matang...'; ?></p>
                            </div>
                        </div>
                        <!-- Step 5 -->
                        <div class="d-flex align-items-start gap-3">
                            <div id="step-circle-5" class="d-flex h-8 w-8 align-items-center justify-content-center <?= ($done5 || $active5) ? 'bg-warning text-dark fw-bold' : 'bg-secondary text-light'; ?> rounded-circle" style="font-size: 12px;">5</div>
                            <div>
                                <p id="step-title-5" class="fw-semibold <?= ($done5 || $active5) ? 'text-light' : 'text-secondary'; ?> mb-1" style="font-size: 14px;">Selesai</p>
                                <p id="step-detail-5" class="small text-secondary mb-0"><?= $active5 ? 'Sajian lengkap dinikmati di meja Anda.' : 'Sedang diantar ke meja...'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endif; ?>
        </article>
    </div>

    <aside class="col-lg-4 animate-fade-in-up" style="animation-delay: 0.2s;">
        <!-- Card 1: Informasi Sajian -->
        <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
            <h3 class="h3 mb-4 text-warning font-display" style="font-size: 24px;">Informasi Sajian</h3>
            <div class="row g-3">
                <article class="p-3 border border-soft bg-black d-flex flex-column h-100 w-100 mb-2">
                    <p class="text-secondary small mb-2">Estimasi Plating</p>
                    <p class="h2 text-gold font-display mb-0" style="font-size: 32px;"><?= $order ? '12 Menit' : '-'; ?></p>
                </article>
                <article class="p-3 border border-soft bg-black d-flex flex-column h-100 w-100">
                    <p class="text-secondary small mb-2">Nomor Meja Anda</p>
                    <p class="h2 text-gold font-display mb-0" style="font-size: 32px;"><?= $order ? htmlspecialchars((string)$order['no_meja'], ENT_QUOTES, 'UTF-8') : '-'; ?></p>
                </article>
            </div>
        </article>

        <!-- Card 2: Rincian Menu Yang Dipesan -->
        <?php if ($order): ?>
            <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
                <h3 class="h3 mb-4 text-warning font-display" style="font-size: 20px;">Rincian Menu</h3>
                <div class="compact-list bg-black p-3 border border-soft">
                    <?php foreach ($details as $detail): ?>
                        <div class="d-flex justify-content-between align-items-center border-bottom border-soft py-2 last:border-0" style="font-size: 13px;">
                            <span class="text-white"><?= htmlspecialchars($detail['nama_menu'], ENT_QUOTES, 'UTF-8'); ?> <span class="text-secondary small">x<?= $detail['jumlah']; ?></span></span>
                            <span class="text-gold fw-medium"><?= rupiah((float)$detail['harga_satuan'] * $detail['jumlah']); ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="d-flex justify-content-between align-items-center pt-3 mt-1 font-display text-gold" style="font-size: 16px;">
                        <span>Total Harga</span>
                        <span><?= rupiah((float)$order['total_harga']); ?></span>
                    </div>
                </div>
            </article>
        <?php endif; ?>
    </aside>
</section>

<?php if ($order): ?>
<script>
    let currentStatus = <?= json_encode($order['status_pesanan']); ?>;
    const orderId = <?= (int)$order['id_pesanan']; ?>;
    const metodePembayaran = <?= json_encode($order['metode_pembayaran'] ?? 'QRIS'); ?>;
    const noMeja = <?= json_encode((string)$order['no_meja']); ?>;
    
    function updateStatusUI(status) {
        // Update badge status di atas
        const badge = document.getElementById('order-status-badge');
        if (badge) badge.textContent = status;

        // Cek status step selesai (done) dan aktif
        const done1 = status !== 'Menunggu Pembayaran';
        const done2 = !['Menunggu Pembayaran', 'Diproses'].includes(status);
        const done3 = !['Menunggu Pembayaran', 'Diproses', 'Sedang Disiapkan'].includes(status);
        const done4 = !['Menunggu Pembayaran', 'Diproses', 'Sedang Disiapkan', 'Siap Saji'].includes(status);
        const done5 = (status === 'Selesai');

        const active1 = (status === 'Menunggu Pembayaran');
        const active2 = (status === 'Diproses');
        const active3 = (status === 'Sedang Disiapkan');
        const active4 = (status === 'Siap Saji');
        const active5 = (status === 'Selesai');

        // Struktur data teks deskripsi per langkah
        const steps = [
            {
                num: 1,
                done: done1,
                active: active1,
                activeDesc: 'Silakan selesaikan pembayaran via ' + metodePembayaran + ' Anda.',
                inactiveDesc: 'Pembayaran terkonfirmasi • Lunas via ' + metodePembayaran
            },
            {
                num: 2,
                done: done2,
                active: active2,
                activeDesc: 'Pesanan telah masuk ke antrean chef • Meja ' + noMeja,
                inactiveDesc: active1 ? 'Menunggu pembayaran diselesaikan...' : 'Pesanan telah masuk ke antrean chef • Meja ' + noMeja
            },
            {
                num: 3,
                done: done3,
                active: active3,
                activeDesc: 'Chef sedang mengolah dan meracik bahan masakan Anda.',
                inactiveDesc: (done3 || active3) ? 'Chef sedang mengolah dan meracik bahan masakan Anda.' : 'Menunggu antrean dapur chef...'
            },
            {
                num: 4,
                done: done4,
                active: active4,
                activeDesc: 'Hidangan matang sempurna dan siap disajikan oleh pelayan kami.',
                inactiveDesc: (done4 || active4) ? 'Hidangan matang sempurna dan siap disajikan oleh pelayan kami.' : 'Menunggu masakan matang...'
            },
            {
                num: 5,
                done: done5,
                active: active5,
                activeDesc: 'Sajian lengkap dinikmati di meja Anda.',
                inactiveDesc: active5 ? 'Sajian lengkap dinikmati di meja Anda.' : 'Sedang diantar ke meja...'
            }
        ];

        // Terapkan class CSS dan konten secara dinamis
        steps.forEach(step => {
            const circle = document.getElementById('step-circle-' + step.num);
            const title = document.getElementById('step-title-' + step.num);
            const detail = document.getElementById('step-detail-' + step.num);

            if (circle) {
                if (step.done || step.active) {
                    circle.className = 'd-flex h-8 w-8 align-items-center justify-content-center bg-warning text-dark fw-bold rounded-circle';
                } else {
                    circle.className = 'd-flex h-8 w-8 align-items-center justify-content-center bg-secondary text-light rounded-circle';
                }
            }

            if (title) {
                if (step.done || step.active) {
                    title.className = 'fw-semibold text-light mb-1';
                } else {
                    title.className = 'fw-semibold text-secondary mb-1';
                }
            }

            if (detail) {
                detail.textContent = step.active ? step.activeDesc : step.inactiveDesc;
            }
        });

        // Tampilkan/sembunyikan alert pembayaran belum lunas
        const warningBanner = document.getElementById('unpaid-warning-banner');
        if (warningBanner) {
            if (status === 'Menunggu Pembayaran') {
                warningBanner.classList.remove('hidden');
            } else {
                warningBanner.classList.add('hidden');
            }
        }
    }
    
    console.log('[Status Tracker] Memulai pemantauan pesanan #' + orderId + ' (Status saat ini: ' + currentStatus + ')');
    
    // Polling setiap 3 detik dengan cache buster
    setInterval(() => {
        fetch('ajax_pesanan_status.php?id=' + orderId + '&_=' + Date.now())
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    console.log('[Status Tracker] Status di database: ' + data.data.status_pesanan);
                    if (data.data.status_pesanan !== currentStatus) {
                        console.log('[Status Tracker] Perubahan terdeteksi: ' + currentStatus + ' -> ' + data.data.status_pesanan);
                        currentStatus = data.data.status_pesanan;
                        updateStatusUI(currentStatus);
                    }
                }
            })
            .catch(err => console.error('Error polling status:', err));
    }, 3000);
</script>
<?php endif; ?>

<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Art Culinaire",
    'eyebrow' => 'Order Tracking',
    'title' => 'Lacak Progres Sajian',
    'description' => 'Lihat progres pesanan secara real-time dari diterimanya pesanan hingga matang di dapur chef.',
], $content);
require __DIR__ . '/../includes/footer.php';
