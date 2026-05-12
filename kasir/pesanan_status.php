<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('kasir');

$title = 'Status Pesanan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
    <h3 class="h3 mb-1 text-warning">Update Status Pesanan</h3>
    <p class="text-muted small mb-4">Panel status untuk memindahkan order dari diproses ke selesai atau dibatalkan.</p>

    <div class="status-modal mt-4">
        <div>
            <p class="text-muted small text-uppercase mb-2">Order Terpilih</p>
            <h4 class="status-modal-title">#K-110 • Truffle Mushroom Risotto</h4>
            <p class="text-muted small mb-4">Naomi Hart • Table 06 • 19:42</p>
        </div>

        <form class="mt-4 d-flex flex-column gap-4" action="#" method="post">
            <div>
                <label class="form-label small text-muted text-uppercase mb-1" for="status">Status Pesanan</label>
                <select class="form-control bg-dark text-white border-secondary rounded-0" id="status" name="status">
                    <option>Diproses</option>
                    <option>Selesai</option>
                    <option>Dibatalkan</option>
                </select>
            </div>
            <div>
                <label class="form-label small text-muted text-uppercase mb-1" for="note">Catatan Shift</label>
                <textarea class="form-control bg-dark text-white border-secondary rounded-0" id="note" name="note" placeholder="Catatan singkat untuk kitchen atau kasir berikutnya."></textarea>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-warning rounded-0 text-uppercase fw-medium px-4 py-2" type="submit">Simpan Status</button>
                <a class="btn btn-outline-warning rounded-0 text-uppercase fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('kasir/pesanan.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
            </div>
        </form>
    </div>
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
