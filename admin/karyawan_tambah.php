<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Tambah Karyawan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="section-panel" style="max-width: 800px; margin: 0 auto; border: none; padding: 40px 0;">
    <div class="text-center mb-5 pb-4 border-bottom border-soft">
        <h2 class="font-display text-gold mb-3" style="font-size: 42px;">Tambah Karyawan</h2>
        <p class="text-secondary small mx-auto" style="max-width: 400px; line-height: 1.6;">Masukkan identitas dasar karyawan untuk keperluan administrasi dan komunikasi inti. Semua bidang wajib diisi kecuali dinyatakan lain.</p>
    </div>

    <form action="<?= htmlspecialchars(base_url('actions/karyawan/store.php'), ENT_QUOTES, 'UTF-8'); ?>" method="post">
        
        <div class="row g-5 mb-5 pb-5 border-bottom border-soft">
            <div class="col-md-4">
                <h4 class="text-white mb-2" style="font-size: 16px;">Kredensial Login</h4>
                <p class="text-muted small">Informasi yang digunakan karyawan untuk masuk ke sistem.</p>
            </div>
            <div class="col-md-8">
                <div class="mb-4">
                    <label class="form-label">Username</label>
                    <input class="form-control" type="text" name="username" placeholder="contoh: kasir.elisa" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input class="form-control" type="password" name="password" placeholder="Minimal 6 karakter" required>
                </div>
            </div>
        </div>

        <div class="row g-5 mb-5 pb-5 border-bottom border-soft">
            <div class="col-md-4">
                <h4 class="text-white mb-2" style="font-size: 16px;">Hak Akses & Peran</h4>
                <p class="text-muted small">Tentukan tingkat akses sistem untuk karyawan ini.</p>
            </div>
            <div class="col-md-8">
                <label class="form-label mb-3">Tingkat Akses (Level)</label>
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <label class="role-select flex-grow-1 text-center border border-secondary p-3 cursor-pointer transition-all" style="cursor: pointer;">
                        <input type="radio" name="level" value="admin" class="d-none">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mb-2 text-secondary mx-auto"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        <span class="d-block small text-uppercase letter-spacing-1 text-secondary fw-medium">Admin</span>
                    </label>
                    <label class="role-select flex-grow-1 text-center border border-gold bg-gold-dim p-3 cursor-pointer transition-all" style="cursor: pointer; background: rgba(212,175,55,0.1);">
                        <input type="radio" name="level" value="kasir" class="d-none" checked>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.5" class="mb-2 mx-auto"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                        <span class="d-block small text-uppercase letter-spacing-1 text-gold fw-medium">Kasir</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 pt-5 border-top border-soft">
            <a class="btn btn-outline-warning text-white" href="<?= htmlspecialchars(base_url('admin/karyawan.php'), ENT_QUOTES, 'UTF-8'); ?>">Batal</a>
            <button class="btn btn-warning" type="submit">Simpan Karyawan</button>
        </div>
    </form>
</section>

<style>
.role-select:hover { border-color: var(--gold) !important; }
.form-check-input:checked { background-color: var(--gold) !important; border-color: var(--gold) !important; }
</style>
<?php
$content = ob_get_clean();
render_internal_shell([
    'brand' => 'NOCTRA',
    'badge' => 'Administration',
    'title' => 'Tambah Karyawan',
    'description' => 'Isi formulir pendaftaran staf.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
