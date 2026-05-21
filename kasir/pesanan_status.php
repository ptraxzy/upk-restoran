<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('kasir');

$title = 'Status Pesanan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';
$pdo = db();

// Handle POST form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pesanan = (int)($_POST['id_pesanan'] ?? 0);
    $status = $_POST['status'] ?? '';
    $note = trim($_POST['note'] ?? '');
    $valid_statuses = ['Menunggu Pembayaran', 'Diproses', 'Sedang Disiapkan', 'Siap Saji', 'Selesai', 'Dibatalkan'];

    if ($id_pesanan > 0 && in_array($status, $valid_statuses, true)) {
        try {
            $stmt = $pdo->prepare("UPDATE pesanan SET status_pesanan = ? WHERE id_pesanan = ?");
            $stmt->execute([$status, $id_pesanan]);
            set_flash('success', "Status pesanan #LP-$id_pesanan berhasil diubah menjadi $status.");
        } catch (Exception $e) {
            set_flash('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    } else {
        set_flash('error', 'Permintaan tidak valid.');
    }
    redirect(base_url('kasir/pesanan_status.php'));
}

// Get selected order (if via ?id=)
$selectedId = (int)($_GET['id'] ?? 0);
$selected = null;
$selectedDetails = [];
if ($selectedId > 0) {
    $stmt = $pdo->prepare("SELECT p.*, u.username FROM pesanan p LEFT JOIN user u ON p.id_user = u.id_user WHERE p.id_pesanan = ?");
    $stmt->execute([$selectedId]);
    $selected = $stmt->fetch();

    if ($selected) {
        $stmtD = $pdo->prepare("SELECT dp.jumlah, m.nama_menu FROM detail_pesanan dp JOIN menu m ON dp.id_menu = m.id_menu WHERE dp.id_pesanan = ?");
        $stmtD->execute([$selectedId]);
        $selectedDetails = $stmtD->fetchAll();
    }
}

// List active orders for selection
$stmtActive = $pdo->query("
    SELECT p.id_pesanan, p.no_meja, p.status_pesanan, u.username
    FROM pesanan p
    LEFT JOIN user u ON p.id_user = u.id_user
    WHERE p.status_pesanan IN ('Diproses', 'Sedang Disiapkan', 'Siap Saji', 'Menunggu Pembayaran')
    ORDER BY p.tanggal_pesanan ASC
");
$activeOrders = $stmtActive->fetchAll();

ob_start();
?>
<section class="row g-5">
    <div class="col-lg-7">
        <article class="section-panel h-100">
            <h3 class="panel-title mb-2">Update Status Pesanan</h3>
            <p class="text-secondary small mb-4">Panel status untuk memindahkan order dari diproses ke selesai atau dibatalkan.</p>

            <?php if ($selected): ?>
            <div class="p-4 border border-secondary mb-4" style="background: rgba(197,160,89,0.05);">
                <p class="text-muted small mb-2">Order Terpilih</p>
                <h4 class="font-display text-white mb-1" style="font-size: 22px;">#LP-<?= $selected['id_pesanan']; ?> • Meja <?= htmlspecialchars($selected['no_meja'], ENT_QUOTES, 'UTF-8'); ?></h4>
                <p class="text-muted small mb-3"><?= htmlspecialchars($selected['username'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?> • <?= date('H:i', strtotime($selected['tanggal_pesanan'])); ?></p>
                <?php foreach ($selectedDetails as $d): ?>
                    <span class="text-gold" style="font-size: 12px;"><?= $d['jumlah']; ?>x <?= htmlspecialchars($d['nama_menu'], ENT_QUOTES, 'UTF-8'); ?></span><br>
                <?php endforeach; ?>

                <form class="mt-4 d-flex flex-column gap-4" action="<?= htmlspecialchars(base_url('kasir/pesanan_status.php'), ENT_QUOTES, 'UTF-8'); ?>" method="POST">
                    <input type="hidden" name="id_pesanan" value="<?= $selected['id_pesanan']; ?>">
                    <div>
                        <label class="form-label small text-muted mb-1" for="status">Status Pesanan</label>
                        <select class="form-control bg-dark text-white border-secondary rounded-0" id="status" name="status">
                            <?php
                            $statuses = ['Diproses', 'Sedang Disiapkan', 'Siap Saji', 'Selesai', 'Dibatalkan'];
                            foreach ($statuses as $s):
                            ?>
                            <option value="<?= $s; ?>" <?= $selected['status_pesanan'] === $s ? 'selected' : ''; ?>><?= $s; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label small text-muted mb-1" for="note">Catatan Shift</label>
                        <textarea class="form-control bg-dark text-white border-secondary rounded-0" id="note" name="note" placeholder="Catatan singkat untuk kitchen atau kasir berikutnya."></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-warning rounded-0 fw-medium px-4 py-2" type="submit">Simpan Status</button>
                        <a class="btn btn-outline-warning rounded-0 fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('kasir/pesanan.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <div class="py-5 text-center">
                <p class="text-muted mb-3">Pilih pesanan dari daftar untuk mengubah statusnya.</p>
            </div>
            <?php endif; ?>
        </article>
    </div>

    <aside class="col-lg-5">
        <article class="section-panel h-100">
            <h3 class="panel-title mb-4">Pesanan Aktif</h3>
            <div class="compact-list">
                <?php foreach ($activeOrders as $o): ?>
                <div class="compact-list-item">
                    <div>
                        <p class="fw-medium text-white mb-1">#LP-<?= $o['id_pesanan']; ?> • Meja <?= htmlspecialchars($o['no_meja'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="small text-secondary mb-0"><?= htmlspecialchars($o['username'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?> • <?= htmlspecialchars($o['status_pesanan'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <a class="text-gold small text-decoration-none border-bottom border-gold pb-1" href="<?= htmlspecialchars(base_url('kasir/pesanan_status.php?id=' . $o['id_pesanan']), ENT_QUOTES, 'UTF-8'); ?>">Update</a>
                </div>
                <?php endforeach; ?>
                <?php if (empty($activeOrders)): ?>
                    <p class="text-muted small text-center py-4">Tidak ada pesanan aktif.</p>
                <?php endif; ?>
            </div>
        </article>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Service Floor',
    'title' => 'Update Status',
    'description' => 'Kontrol cepat untuk menjaga flow pesanan tetap terlihat dan sinkron dengan dapur.',
    'nav_sections' => staff_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
