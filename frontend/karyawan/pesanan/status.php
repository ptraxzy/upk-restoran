<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('kasir');

$title = 'Status Pesanan';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

ob_start();
?>
<section class="section-panel">
    <h3 class="section-title">Update Status Pesanan</h3>
    <p class="section-subtitle">Panel status untuk memindahkan order dari diproses ke selesai atau dibatalkan.</p>

    <div class="status-modal mt-6">
        <div>
            <p class="metric-label">Order Terpilih</p>
            <h4 class="status-modal-title">#K-110 • Truffle Mushroom Risotto</h4>
            <p class="section-subtitle">Naomi Hart • Table 06 • 19:42</p>
        </div>

        <form class="mt-8 space-y-5" action="#" method="post">
            <div>
                <label class="field-label" for="status">Status Pesanan</label>
                <select class="field-input" id="status" name="status">
                    <option>Diproses</option>
                    <option>Selesai</option>
                    <option>Dibatalkan</option>
                </select>
            </div>
            <div>
                <label class="field-label" for="note">Catatan Shift</label>
                <textarea class="textarea-input" id="note" name="note" placeholder="Catatan singkat untuk kitchen atau kasir berikutnya."></textarea>
            </div>
            <div class="flex gap-2">
                <button class="cta-primary" type="submit">Simpan Status</button>
                <a class="cta-secondary" href="<?= htmlspecialchars(frontend_url('karyawan/pesanan/index.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
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
require base_path('backend/includes/footer.php');
