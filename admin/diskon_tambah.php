<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Tambah Voucher Diskon';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="row g-4 justify-content-center">
    <div class="col-lg-8 animate-fade-in-up">
        <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
            <div class="border-bottom border-soft pb-3 mb-4">
                <p class="text-gold small mb-1" style="letter-spacing: 0.06em; font-weight: 600;">MANAJEMEN DISKON</p>
                <h3 class="h3 text-white mb-0 mt-1 font-display" style="font-size: 24px;">Tambah Voucher Baru</h3>
                <p class="text-secondary small mb-0 mt-1">Buat kode promo baru untuk menarik lebih banyak pelanggan.</p>
            </div>

            <!-- Error Notifications -->
            <?php if ($msg = get_flash('error')): ?>
                <div class="alert alert-danger bg-opacity-10 bg-danger border-danger text-danger rounded-0 mb-4" style="font-size: 13px;">
                    <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form class="d-flex flex-column gap-3" method="POST" action="<?= htmlspecialchars(base_url('actions/diskon/store.php'), ENT_QUOTES, 'UTF-8'); ?>">
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.04em;">KODE VOUCHER</label>
                        <input class="form-control bg-black text-white border-secondary rounded-0" name="kode_voucher" type="text" placeholder="Contoh: GOLD_PREMIUM_2024" required style="font-size: 13px; padding: 10px; border-color: rgba(255, 255, 255, 0.08) !important;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.04em;">NAMA KAMPANYE / DESKRIPSI</label>
                        <input class="form-control bg-black text-white border-secondary rounded-0" name="nama_voucher" type="text" placeholder="Contoh: Annual Executive Pass Discount" required style="font-size: 13px; padding: 10px; border-color: rgba(255, 255, 255, 0.08) !important;">
                    </div>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.04em;">JENIS POTONGAN</label>
                        <select class="form-select bg-black text-white border-secondary rounded-0" name="jenis_voucher" required style="font-size: 13px; padding: 10px; border-color: rgba(255, 255, 255, 0.08) !important; cursor: pointer;">
                            <option value="Persentase">Persentase (%)</option>
                            <option value="Nominal">Nominal Rupiah (Rp)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.04em;">NILAI POTONGAN</label>
                        <input class="form-control bg-black text-white border-secondary rounded-0" name="nilai_voucher" type="number" min="0" placeholder="Contoh: 25 (untuk 25%) atau 50000 (untuk Rp 50.000)" required style="font-size: 13px; padding: 10px; border-color: rgba(255, 255, 255, 0.08) !important;">
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.04em;">TANGGAL MULAI</label>
                        <input class="form-control bg-black text-white border-secondary rounded-0" name="tanggal_mulai" type="date" value="<?= date('Y-m-d') ?>" required style="font-size: 13px; padding: 10px; border-color: rgba(255, 255, 255, 0.08) !important;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.04em;">TANGGAL BERAKHIR</label>
                        <input class="form-control bg-black text-white border-secondary rounded-0" name="tanggal_berakhir" type="date" value="<?= date('Y-m-d', strtotime('+3 months')) ?>" required style="font-size: 13px; padding: 10px; border-color: rgba(255, 255, 255, 0.08) !important;">
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.04em;">STATUS AWAL</label>
                        <select class="form-select bg-black text-white border-secondary rounded-0" name="status_voucher" required style="font-size: 13px; padding: 10px; border-color: rgba(255, 255, 255, 0.08) !important; cursor: pointer;">
                            <option value="Active" selected>Active</option>
                            <option value="Scheduled">Scheduled</option>
                            <option value="Expired">Expired</option>
                        </select>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-3 mt-4 border-top border-soft pt-4">
                    <button class="btn btn-warning rounded-0 fw-bold px-4 py-2" type="submit" style="font-size: 12px; letter-spacing: 0.04em;">SIMPAN VOUCHER</button>
                    <a href="<?= htmlspecialchars(base_url('admin/diskon.php'), ENT_QUOTES, 'UTF-8'); ?>" class="small text-secondary hover:text-stone-300 text-decoration-none">Batal</a>
                </div>
            </form>
        </article>
    </div>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administration',
    'title' => 'Tambah Voucher',
    'description' => 'Isi detail kode promo dan aturan pakainya.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
