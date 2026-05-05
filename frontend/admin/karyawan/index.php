<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('admin');

$title = 'Daftar Karyawan';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

ob_start();
?>
<section class="content-grid">
    <article class="section-panel">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h3 class="section-title">Daftar Karyawan</h3>
                <p class="section-subtitle">Kelola data tim dan akses staf dengan cepat.</p>
            </div>
            <a class="cta-primary" href="<?= htmlspecialchars(frontend_url('admin/karyawan/create.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah Karyawan</a>
        </div>

        <div class="list-stack mt-6">
            <div class="stack-item">
                <div>
                    <p class="font-medium text-stone-100">Elisa Monroe</p>
                    <p class="mt-2 text-sm text-stone-400">Kasir • Shift malam • 14:00 - 22:00</p>
                </div>
                <span class="badge badge-gold">Aktif</span>
            </div>
            <div class="list-item">
                <div>
                    <p class="font-medium text-stone-100">Daniel Reeves</p>
                    <p class="mt-2 text-sm text-stone-400">Service floor • Fine dining assistance</p>
                </div>
                <span class="badge badge-muted">On Duty</span>
            </div>
            <div class="list-item">
                <div>
                    <p class="font-medium text-stone-100">Sophia Verne</p>
                    <p class="mt-2 text-sm text-stone-400">Kitchen support • Prep station</p>
                </div>
                <span class="badge badge-muted">Standby</span>
            </div>
        </div>
    </article>

    <aside class="section-panel">
        <h3 class="section-title">Komposisi Tim</h3>
        <div class="metric-grid mt-5 !grid-cols-2">
            <article class="metric-card">
                <p class="metric-label">Kasir</p>
                <p class="metric-value !text-3xl">4</p>
            </article>
            <article class="metric-card">
                <p class="metric-label">Service</p>
                <p class="metric-value !text-3xl">5</p>
            </article>
            <article class="metric-card">
                <p class="metric-label">Kitchen</p>
                <p class="metric-value !text-3xl">3</p>
            </article>
            <article class="metric-card">
                <p class="metric-label">Total</p>
                <p class="metric-value !text-3xl">12</p>
            </article>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administration',
    'title' => 'Manajemen Karyawan',
    'description' => 'Kelola tim operasional restoran dari satu panel yang padat tapi tetap elegan.',
    'nav_sections' => admin_nav_sections(),
], $content);
require base_path('backend/includes/footer.php');
