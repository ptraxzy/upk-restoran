<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('kasir');

$title = 'Layanan Aktif';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

// Fetch active orders from database
$pdo = db();
$stmt = $pdo->query("
    SELECT p.id_pesanan, p.no_meja, p.status_pesanan, p.tanggal_pesanan,
           TIMESTAMPDIFF(MINUTE, p.tanggal_pesanan, NOW()) as menit_menunggu
    FROM pesanan p
    WHERE p.status_pesanan IN ('Diproses', 'Sedang Disiapkan', 'Siap Saji', 'Menunggu Pembayaran')
    ORDER BY p.tanggal_pesanan ASC
");
$pesananList = $stmt->fetchAll();

// Fetch details for each order
$pesananDetails = [];
if (count($pesananList) > 0) {
    $ids = array_column($pesananList, 'id_pesanan');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmtDetail = $pdo->prepare("
        SELECT dp.id_pesanan, dp.jumlah, m.nama_menu
        FROM detail_pesanan dp
        JOIN menu m ON dp.id_menu = m.id_menu
        WHERE dp.id_pesanan IN ($placeholders)
    ");
    $stmtDetail->execute($ids);
    $details = $stmtDetail->fetchAll();
    
    foreach ($details as $detail) {
        $pesananDetails[$detail['id_pesanan']][] = $detail;
    }
}

// Calculate counts
$countSemua = count($pesananList);
$countDisiapkan = count(array_filter($pesananList, fn($p) => $p['status_pesanan'] === 'Sedang Disiapkan' || $p['status_pesanan'] === 'Diproses'));
$countSiap = count(array_filter($pesananList, fn($p) => $p['status_pesanan'] === 'Siap Saji'));

ob_start();
?>
<section style="background: transparent; border: none; padding: 0;">
    <div class="d-flex flex-column gap-3 flex-md-row justify-content-md-between align-items-md-start mb-5">
        <div style="max-width: 600px;">
            <h2 class="font-display text-white mb-2" style="font-size: 36px;">Layanan Aktif</h2>
            <p class="text-secondary small" style="line-height: 1.6;">Memantau <?= $countSemua ?> meja aktif. Harap perhatikan urutan waktu pemesanan.</p>
        </div>
    </div>

    <div class="d-flex gap-4 mb-5 border-bottom border-soft overflow-auto" style="scrollbar-width: none;">
        <a class="text-gold text-uppercase small letter-spacing-1 fw-medium text-decoration-none pb-3 border-bottom border-gold border-2 whitespace-nowrap flex-shrink-0" href="#">SEMUA AKTIF (<?= $countSemua ?>)</a>
        <a class="text-secondary hover-gold text-uppercase small letter-spacing-1 text-decoration-none pb-3 whitespace-nowrap flex-shrink-0" href="#">SEDANG DISIAPKAN (<?= $countDisiapkan ?>)</a>
        <a class="text-secondary hover-gold text-uppercase small letter-spacing-1 text-decoration-none pb-3 whitespace-nowrap flex-shrink-0" href="#">SIAP SAJI (<?= $countSiap ?>)</a>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4">
        <?php if ($countSemua === 0): ?>
            <div class="col-12 w-100 py-5 text-center">
                <p class="text-muted">Tidak ada pesanan aktif saat ini.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($pesananList as $p): ?>
            <?php
            $statusClass = 'text-warning';
            $statusText = $p['status_pesanan'];
            $borderOp = '';
            
            if ($p['status_pesanan'] === 'Menunggu Pembayaran') {
                $statusClass = 'text-danger';
                $statusText = 'Belum Bayar';
            } elseif ($p['status_pesanan'] === 'Siap Saji') {
                $statusClass = 'text-success';
                $borderOp = 'opacity: 0.9; border-color: var(--gold);';
            }
            ?>
            <div class="col">
                <article class="p-3 border border-secondary h-100 d-flex flex-column bg-card" style="<?= $borderOp ?>">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-soft">
                        <div class="d-flex align-items-center gap-2">
                            <span class="font-display text-white" style="font-size: 24px; line-height: 1;"><?= htmlspecialchars($p['no_meja'], ENT_QUOTES, 'UTF-8') ?></span>
                            <div>
                                <p class="text-secondary small text-uppercase letter-spacing-1 m-0" style="font-size: 9px;">MEJA</p>
                                <p class="<?= $statusClass ?> m-0" style="font-size: 10px; font-weight: 500;"><?= $statusText ?> • <?= $p['menit_menunggu'] ?>m</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex flex-column gap-1 mb-3 flex-grow-1">
                        <?php if (isset($pesananDetails[$p['id_pesanan']])): ?>
                            <?php foreach ($pesananDetails[$p['id_pesanan']] as $detail): ?>
                            <div class="d-flex gap-2">
                                <span class="text-gold fw-medium" style="font-size: 11px;"><?= $detail['jumlah'] ?>x</span>
                                <span class="text-white" style="font-size: 11px;"><?= htmlspecialchars($detail['nama_menu'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted small">Tidak ada detail menu.</p>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex gap-2 mt-auto">
                        <?php if ($p['status_pesanan'] === 'Menunggu Pembayaran'): ?>
                            <button class="btn btn-outline-danger w-100 py-2" style="font-size: 10px; padding: 8px !important;" disabled>MENUNGGU BAYAR</button>
                        <?php elseif ($p['status_pesanan'] === 'Diproses'): ?>
                            <button class="btn btn-warning w-100 py-2" style="font-size: 10px; padding: 8px !important;" onclick="window.location.href='<?= base_url('actions/pesanan/update_status.php?id='.$p['id_pesanan'].'&status=Sedang Disiapkan') ?>'">MENYIAPKAN</button>
                        <?php elseif ($p['status_pesanan'] === 'Sedang Disiapkan'): ?>
                            <button class="btn btn-warning w-100 py-2" style="font-size: 10px; padding: 8px !important;" onclick="window.location.href='<?= base_url('actions/pesanan/update_status.php?id='.$p['id_pesanan'].'&status=Siap Saji') ?>'">SIAP SAJI</button>
                        <?php elseif ($p['status_pesanan'] === 'Siap Saji'): ?>
                            <button class="btn btn-outline-secondary w-100 py-2 text-white border-secondary" style="font-size: 10px; padding: 8px !important;" onclick="window.location.href='<?= base_url('actions/pesanan/update_status.php?id='.$p['id_pesanan'].'&status=Selesai') ?>'">SELESAI</button>
                        <?php endif; ?>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'brand' => 'NOCTRA',
    'badge' => 'Service Floor',
    'title' => 'Layanan Aktif',
    'description' => 'Memantau meja aktif dan pesanan real-time.',
    'nav_sections' => staff_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
