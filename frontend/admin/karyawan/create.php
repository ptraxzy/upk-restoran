<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('admin');

$title = 'Tambah Karyawan';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

ob_start();
?>
<section class="content-grid">
    <article class="section-panel">
        <p class="eyebrow">Team Onboarding</p>
        <h3 class="section-title mt-3">Tambah Karyawan</h3>
        <p class="section-subtitle">Daftarkan karyawan baru dan atur role mereka.</p>

        <form class="mt-6 space-y-5" action="<?= htmlspecialchars(backend_url('actions/karyawan/store.php'), ENT_QUOTES, 'UTF-8'); ?>" method="post">
            <div class="form-grid">
                <div>
                    <label class="field-label">Nama Lengkap</label>
                    <input class="field-input" type="text" placeholder="Nama karyawan">
                </div>
                <div>
                    <label class="field-label">Posisi</label>
                    <select class="field-input">
                        <option>Kasir</option>
                        <option>Service</option>
                        <option>Kitchen Support</option>
                    </select>
                </div>
            </div>
            <div class="form-grid">
                <div>
                    <label class="field-label">Email</label>
                    <input class="field-input" type="email" placeholder="email@restoran.com">
                </div>
                <div>
                    <label class="field-label">Jadwal</label>
                    <input class="field-input" type="text" placeholder="14:00 - 22:00">
                </div>
            </div>
            <div>
                <label class="field-label">Catatan Peran</label>
                <textarea class="textarea-input" placeholder="Catatan tanggung jawab, shift, atau kebutuhan onboarding."></textarea>
            </div>
            <div class="flex flex-wrap gap-3">
                <button class="cta-primary" type="submit">Simpan Karyawan</button>
                <a class="cta-secondary" href="<?= htmlspecialchars(frontend_url('admin/karyawan/index.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
            </div>
        </form>
    </article>

    <aside class="hero-card">
        <p class="eyebrow">Peran & Penugasan</p>
        <p class="mt-4 text-sm leading-7 text-stone-300">Tentukan jadwal operasional dan fungsi meja lebih dahulu agar struktur staf tetap seimbang.</p>
        <div class="mt-6 mini-card-grid !grid-cols-1">
            <div class="metric-card">
                <p class="metric-label">Shift Tersedia</p>
                <p class="metric-note">Siang, Malam, Flexi</p>
            </div>
            <div class="metric-card">
                <p class="metric-label">Prioritas</p>
                <p class="metric-note">Hospitality dan ritme layanan</p>
            </div>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administration',
    'title' => 'Tambah Karyawan',
    'description' => 'Panel input staf baru dengan penekanan pada role, jadwal, dan susunan operasional.',
    'nav_sections' => admin_nav_sections(),
], $content);
require base_path('backend/includes/footer.php');
