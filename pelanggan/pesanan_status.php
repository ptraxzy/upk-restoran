<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('pelanggan');

$title = 'Status Pesanan';
$userId = $_SESSION['id_user'] ?? 0;
$pdo = db();

// Fetch the latest active order
$stmt = $pdo->prepare('
    SELECT * 
    FROM pesanan 
    WHERE id_user = ? AND status_pesanan NOT IN ("Selesai", "Dibatalkan")
    ORDER BY tanggal_pesanan DESC 
    LIMIT 1
');
$stmt->execute([$userId]);
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
                // Steps mapping
                $step1 = true; // Diterima is always true if order exists
                $step2 = in_array($status, ['Diproses', 'Sedang Disiapkan', 'Siap Saji', 'Selesai']);
                $step3 = in_array($status, ['Siap Saji', 'Selesai']);
                $step4 = ($status === 'Selesai');
                ?>
                <div class="mt-4 border border-soft bg-black p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-soft pb-3">
                        <div>
                            <p class="text-secondary small mb-1">ID Pesanan Anda</p>
                            <h4 class="font-display text-gold mb-0" style="font-size: 22px;">#LP-<?= $order['id_pesanan']; ?></h4>
                        </div>
                        <span class="badge bg-warning text-dark px-3 py-2" style="font-size: 12px;"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>

                    <div class="d-flex flex-column gap-4 mt-2">
                        <!-- Step 1 -->
                        <div class="d-flex align-items-start gap-3">
                            <div class="d-flex h-8 w-8 align-items-center justify-content-center bg-warning text-dark fw-bold rounded-circle" style="font-size: 12px;">1</div>
                            <div>
                                <p class="fw-semibold text-light mb-1" style="font-size: 14px;">Pesanan Diterima</p>
                                <p class="small text-secondary mb-0">Pesanan telah masuk ke sistem kami • Meja <?= htmlspecialchars((string)$order['no_meja'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>
                        <!-- Step 2 -->
                        <div class="d-flex align-items-start gap-3">
                            <div class="d-flex h-8 w-8 align-items-center justify-content-center <?= $step2 ? 'bg-warning text-dark fw-bold' : 'bg-secondary text-light'; ?> rounded-circle" style="font-size: 12px;">2</div>
                            <div>
                                <p class="fw-semibold <?= $step2 ? 'text-light' : 'text-secondary'; ?> mb-1" style="font-size: 14px;">Persiapan Hidangan</p>
                                <p class="small text-secondary mb-0"><?= $step2 ? 'Chef sedang mengolah dan meracik bahan masakan Anda.' : 'Menunggu antrean dapur chef...'; ?></p>
                            </div>
                        </div>
                        <!-- Step 3 -->
                        <div class="d-flex align-items-start gap-3">
                            <div class="d-flex h-8 w-8 align-items-center justify-content-center <?= $step3 ? 'bg-warning text-dark fw-bold' : 'bg-secondary text-light'; ?> rounded-circle" style="font-size: 12px;">3</div>
                            <div>
                                <p class="fw-semibold <?= $step3 ? 'text-light' : 'text-secondary'; ?> mb-1" style="font-size: 14px;">Siap Disajikan</p>
                                <p class="small text-secondary mb-0"><?= $step3 ? 'Hidangan matang sempurna dan siap disajikan oleh pelayan kami.' : 'Menunggu masakan matang...'; ?></p>
                            </div>
                        </div>
                        <!-- Step 4 -->
                        <div class="d-flex align-items-start gap-3">
                            <div class="d-flex h-8 w-8 align-items-center justify-content-center <?= $step4 ? 'bg-warning text-dark fw-bold' : 'bg-secondary text-light'; ?> rounded-circle" style="font-size: 12px;">4</div>
                            <div>
                                <p class="fw-semibold <?= $step4 ? 'text-light' : 'text-secondary'; ?> mb-1" style="font-size: 14px;">Selesai</p>
                                <p class="small text-secondary mb-0"><?= $step4 ? 'Sajian lengkap dinikmati di meja Anda.' : 'Sedang diantar ke meja...'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h4 class="small fw-semibold text-warning mb-3" style="font-size: 12px;">Rincian Menu Yang Dipesan</h4>
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
                </div>
            <?php endif; ?>
        </article>
    </div>

    <aside class="col-lg-4 animate-fade-in-up" style="animation-delay: 0.2s;">
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
    </aside>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Art Culinaire",
    'eyebrow' => 'Order Tracking',
    'title' => 'Lacak Progres Sajian',
    'description' => 'Lihat progres pesanan secara real-time dari diterimanya pesanan hingga matang di dapur chef.',
], $content);
require __DIR__ . '/../includes/footer.php';
