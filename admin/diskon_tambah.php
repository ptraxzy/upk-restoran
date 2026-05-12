<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Tambah Voucher Diskon';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="row row-cols-1 row-cols-lg-2 g-4">
    <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <p class="text-muted small text-uppercase mb-1">Manajemen Diskon</p>
        <h3 class="h3 mb-1 text-warning mt-2">Tambah Voucher</h3>
        <p class="text-muted small mb-4">Buat kode promo baru untuk menarik lebih banyak pelanggan.</p>

        <form class="mt-4 d-flex flex-column gap-4" method="POST" action="<?= htmlspecialchars(base_url('admin/diskon.php'), ENT_QUOTES, 'UTF-8'); ?>">
            <div class="row row-cols-1 row-cols-md-2 g-3 mb-3">
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Kode Voucher</label>
                    <input class="form-control bg-dark text-white border-secondary rounded-0 text-uppercase" type="text" placeholder="Contoh: PROMO-WAGYU" required>
                </div>
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Jenis Potongan</label>
                    <select class="form-control bg-dark text-white border-secondary rounded-0">
                        <option value="persen">Persentase (%)</option>
                        <option value="nominal">Nominal Rupiah (Rp)</option>
                    </select>
                </div>
            </div>
            
            <div class="row row-cols-1 row-cols-md-2 g-3 mb-3">
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Nilai Potongan</label>
                    <input class="form-control bg-dark text-white border-secondary rounded-0" type="number" placeholder="Contoh: 15 (untuk persen) atau 50000 (untuk nominal)" required>
                </div>
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Status</label>
                    <select class="form-control bg-dark text-white border-secondary rounded-0">
                        <option value="aktif" selected>Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="form-label small text-muted text-uppercase mb-1">Deskripsi & Syarat</label>
                <textarea class="form-control bg-dark text-white border-secondary rounded-0" placeholder="Jelaskan syarat dan ketentuan penggunaan voucher..."></textarea>
            </div>
            
            <div class="d-flex align-items-center gap-2 mt-4">
                <button class="btn btn-warning rounded-0 text-uppercase fw-medium px-4 py-2" type="submit">Simpan Voucher</button>
                <a href="<?= htmlspecialchars(base_url('admin/diskon.php'), ENT_QUOTES, 'UTF-8'); ?>" class="small text-uppercase text-muted hover:text-stone-300">Batal</a>
            </div>
        </form>
    </article>
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
