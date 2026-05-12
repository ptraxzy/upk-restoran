<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Edit Karyawan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="row row-cols-1 row-cols-lg-2 g-4">
    <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <p class="text-muted small text-uppercase mb-1">Edit Data</p>
        <h3 class="h3 mb-1 text-warning mt-2">Edit Data Karyawan</h3>
        <p class="text-muted small mb-4">Perbarui informasi karyawan yang sudah terdaftar.</p>

        <form class="mt-4 d-flex flex-column gap-4" action="<?= htmlspecialchars(base_url('actions/karyawan/update.php'), ENT_QUOTES, 'UTF-8'); ?>" method="post">
            <div class="row row-cols-1 row-cols-md-2 g-3 mb-3">
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Nama Lengkap</label>
                    <input class="form-control bg-dark text-white border-secondary rounded-0" type="text" name="nama" value="Elisa Monroe" placeholder="Nama karyawan">
                </div>
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Posisi</label>
                    <select class="form-control bg-dark text-white border-secondary rounded-0" name="posisi">
                        <option selected>Kasir</option>
                        <option>Service</option>
                        <option>Kitchen Support</option>
                    </select>
                </div>
            </div>
            <div class="row row-cols-1 row-cols-md-2 g-3 mb-3">
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Email</label>
                    <input class="form-control bg-dark text-white border-secondary rounded-0" type="email" name="email" value="elisa@restoran.com" placeholder="email@restoran.com">
                </div>
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Jadwal</label>
                    <input class="form-control bg-dark text-white border-secondary rounded-0" type="text" name="jadwal" value="14:00 - 22:00" placeholder="14:00 - 22:00">
                </div>
            </div>
            <div>
                <label class="form-label small text-muted text-uppercase mb-1">Catatan Peran</label>
                <textarea class="form-control bg-dark text-white border-secondary rounded-0" name="catatan" placeholder="Catatan tanggung jawab, shift, atau kebutuhan onboarding.">Kasir utama shift malam, bertanggung jawab atas transaksi pembayaran.</textarea>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-warning rounded-0 text-uppercase fw-medium px-4 py-2" type="submit">Simpan Perubahan</button>
                <a class="btn btn-outline-warning rounded-0 text-uppercase fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('admin/karyawan.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
            </div>
        </form>
    </article>

    <aside class="hero-card">
        <p class="text-muted small text-uppercase mb-1">Info Karyawan</p>
        <p class="mt-4 small leading-7 text-stone-300">Pastikan data yang diubah sudah benar sebelum menyimpan. Perubahan posisi atau jadwal akan mempengaruhi akses sistem.</p>
        <div class="mt-4 mini-card-grid">
            <div class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
                <p class="text-muted small text-uppercase mb-2">Status</p>
                <p class="metric-note">Aktif</p>
            </div>
            <div class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
                <p class="text-muted small text-uppercase mb-2">Bergabung Sejak</p>
                <p class="metric-note">15 Januari 2026</p>
            </div>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administration',
    'title' => 'Edit Data Karyawan',
    'description' => 'Perbarui informasi dan pengaturan karyawan yang sudah terdaftar.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
