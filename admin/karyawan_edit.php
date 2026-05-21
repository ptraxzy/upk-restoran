<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Edit Karyawan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';

$id = $_GET['id'] ?? null;
$karyawan = null;

if ($id) {
    $stmt = db()->prepare("SELECT id_user, username, level, status FROM user WHERE id_user = ? AND level IN ('admin', 'kasir')");
    $stmt->execute([$id]);
    $karyawan = $stmt->fetch();
}

if (!$karyawan) {
    set_flash('error', 'Karyawan tidak ditemukan.');
    redirect(base_url('admin/karyawan.php'));
}

ob_start();
?>
<div class="mb-5 animate-fade-in-up">
    <a href="<?= htmlspecialchars(base_url('admin/karyawan.php'), ENT_QUOTES, 'UTF-8'); ?>" class="premium-back-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="align-middle me-1"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Kembali Ke Tim
    </a>
</div>

<section class="premium-card-glass animate-fade-in-up" style="max-width: 800px; margin: 0 auto; animation-delay: 0.1s;">
    <form action="<?= htmlspecialchars(base_url('actions/karyawan/update.php'), ENT_QUOTES, 'UTF-8'); ?>" method="post">
        <input type="hidden" name="id_user" value="<?= htmlspecialchars((string)$karyawan['id_user']); ?>">
        
        <!-- Section 1: Kredensial Login -->
        <div class="row g-4 mb-5 pb-4" style="border-bottom: 1px solid rgba(197, 160, 89, 0.1);">
            <div class="col-md-4">
                <h4 class="text-white mb-2" style="font-size: 16px; font-family: var(--font-display); letter-spacing: 1px;">Kredensial Login</h4>
                <p class="text-muted small" style="line-height: 1.5;">Informasi autentikasi dasar untuk akses masuk ke sistem admin / kasir.</p>
            </div>
            <div class="col-md-8 d-flex flex-column gap-4">
                <div>
                    <label class="form-label text-secondary small mb-2">Nama Pengguna (Username)</label>
                    <input class="form-control premium-input-standard" type="text" name="username" value="<?= htmlspecialchars($karyawan['username']); ?>" required autocomplete="off">
                </div>
                <div>
                    <label class="form-label text-secondary small mb-2">Kata Sandi Baru (Opsional)</label>
                    <input class="form-control premium-input-standard" type="password" name="password" placeholder="Kosongkan jika kata sandi tidak ingin diubah" autocomplete="new-password">
                </div>
            </div>
        </div>

        <!-- Section 2: Hak Akses & Peran -->
        <div class="row g-4 mb-5 pb-4" style="border-bottom: 1px solid rgba(197, 160, 89, 0.1);">
            <div class="col-md-4">
                <h4 class="text-white mb-2" style="font-size: 16px; font-family: var(--font-display); letter-spacing: 1px;">Peran & Otoritas</h4>
                <p class="text-muted small" style="line-height: 1.5;">Tentukan level otoritas sistem untuk karyawan ini berdasarkan tanggung jawabnya.</p>
            </div>
            <div class="col-md-8">
                <label class="form-label text-secondary small mb-3">Pilih Tingkat Akses</label>
                <div class="premium-role-container">
                    <label class="premium-role-label">
                        <input type="radio" name="level" value="kasir" class="premium-role-input" <?= $karyawan['level'] === 'kasir' ? 'checked' : ''; ?>>
                        <div class="premium-role-card">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mb-3 mx-auto d-block"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M12 4v16M2 12h20"/></svg>
                            <span class="text-white d-block small fw-medium mb-1">Kasir</span>
                            <span class="text-muted d-block" style="font-size: 11px; line-height: 1.3;">Akses kasir utama, transaksi pesanan, & pembayaran.</span>
                        </div>
                    </label>
                    
                    <label class="premium-role-label">
                        <input type="radio" name="level" value="admin" class="premium-role-input" <?= $karyawan['level'] === 'admin' ? 'checked' : ''; ?>>
                        <div class="premium-role-card">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="mb-3 mx-auto d-block"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <span class="text-white d-block small fw-medium mb-1">Administrator</span>
                            <span class="text-muted d-block" style="font-size: 11px; line-height: 1.3;">Akses penuh ke semua panel manajemen, laporan keuangan, & konfigurasi.</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Section 3: Status Keanggotaan -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <h4 class="text-white mb-2" style="font-size: 16px; font-family: var(--font-display); letter-spacing: 1px;">Status Kerja</h4>
                <p class="text-muted small" style="line-height: 1.5;">Aktifkan atau nonaktifkan akses karyawan ini (misal saat pensiun, cuti panjang, atau resign).</p>
            </div>
            <div class="col-md-8">
                <label class="form-label text-secondary small mb-2">Status Akun Karyawan</label>
                <select name="status" class="form-select premium-input-standard" style="background-color: var(--bg-card); color: var(--text-primary); border: 1px solid var(--border);">
                    <option value="Aktif" <?= ($karyawan['status'] ?? 'Aktif') === 'Aktif' ? 'selected' : ''; ?>>Aktif (Akses Diizinkan)</option>
                    <option value="Nonaktif" <?= ($karyawan['status'] ?? 'Aktif') === 'Nonaktif' ? 'selected' : ''; ?>>Nonaktif / Resign (Akses Diblokir)</option>
                </select>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-3 pt-4 border-top" style="border-top-color: rgba(197, 160, 89, 0.1) !important;">
            <a class="btn premium-btn-outline" href="<?= htmlspecialchars(base_url('admin/karyawan.php'), ENT_QUOTES, 'UTF-8'); ?>">Batal</a>
            <button class="btn premium-btn-gold" type="submit">Simpan Perubahan</button>
        </div>
    </form>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'brand' => 'Lumière',
    'badge' => 'Administration',
    'title' => 'Edit Data Karyawan',
    'description' => 'Perbarui informasi dan pengaturan karyawan yang sudah terdaftar.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
