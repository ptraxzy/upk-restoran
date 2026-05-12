<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Daftar Karyawan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="row g-5">
    <div class="col-lg-8">
        <article class="section-panel h-100">
            <div class="panel-header d-flex flex-column gap-3 flex-md-row justify-content-md-between align-items-md-end">
                <div>
                    <h3 class="panel-title">Daftar Karyawan</h3>
                    <p class="panel-desc">Kelola data tim dan akses staf dengan cepat.</p>
                </div>
                <div class="d-flex flex-wrap gap-3">
                    <a class="btn btn-outline-warning" href="<?= htmlspecialchars(base_url('admin/karyawan_edit.php'), ENT_QUOTES, 'UTF-8'); ?>">Edit Karyawan</a>
                    <a class="btn btn-warning" href="<?= htmlspecialchars(base_url('admin/karyawan_tambah.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah Karyawan</a>
                </div>
            </div>

            <div class="list-stack mt-4">
                <div class="stack-item">
                    <div>
                        <p class="fw-medium text-white mb-1">Elisa Monroe</p>
                        <p class="small text-secondary mb-0">Kasir • Shift malam • 14:00 - 22:00</p>
                    </div>
                    <span class="badge bg-warning">Aktif</span>
                </div>
                <div class="stack-item">
                    <div>
                        <p class="fw-medium text-white mb-1">Daniel Reeves</p>
                        <p class="small text-secondary mb-0">Service floor • Fine dining assistance</p>
                    </div>
                    <span class="badge bg-secondary">On Duty</span>
                </div>
                <div class="stack-item">
                    <div>
                        <p class="fw-medium text-white mb-1">Sophia Verne</p>
                        <p class="small text-secondary mb-0">Kitchen support • Prep station</p>
                    </div>
                    <span class="badge bg-secondary">Standby</span>
                </div>
            </div>
        </article>
    </div>

    <aside class="col-lg-4">
        <article class="section-panel h-100">
            <h3 class="panel-title mb-4">Komposisi Tim</h3>
            <div class="row row-cols-2 g-4">
                <div class="col">
                    <article class="card p-4 h-100">
                        <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">Kasir</p>
                        <p class="h2 text-gold font-display mb-0">4</p>
                    </article>
                </div>
                <div class="col">
                    <article class="card p-4 h-100">
                        <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">Service</p>
                        <p class="h2 text-gold font-display mb-0">5</p>
                    </article>
                </div>
                <div class="col">
                    <article class="card p-4 h-100">
                        <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">Kitchen</p>
                        <p class="h2 text-gold font-display mb-0">3</p>
                    </article>
                </div>
                <div class="col">
                    <article class="card p-4 h-100">
                        <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">Total</p>
                        <p class="h2 text-gold font-display mb-0">12</p>
                    </article>
                </div>
            </div>
        </article>
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
require __DIR__ . '/../includes/footer.php';
