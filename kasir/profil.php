<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('kasir');

$title = 'Profil Karyawan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="row row-cols-1 row-cols-lg-2 g-4">
    <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <p class="text-muted small text-uppercase mb-1">Personal Profile</p>
        <h3 class="h3 mb-1 text-warning mt-2">Profil Karyawan</h3>

        <div class="profile-grid mt-4">
            <div class="profile-avatar"><?= htmlspecialchars(strtoupper(substr($_SESSION['user_name'] ?? 'K', 0, 1)), ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="row row-cols-1 row-cols-md-2 g-3 mb-3">
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Nama</label>
                    <input class="form-control bg-dark text-white border-secondary rounded-0 profile-field" type="text" value="<?= htmlspecialchars($_SESSION['user_name'] ?? 'Kasir', ENT_QUOTES, 'UTF-8'); ?>" readonly>
                </div>
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Level</label>
                    <input class="form-control bg-dark text-white border-secondary rounded-0 profile-field" type="text" value="<?= htmlspecialchars(role_label($_SESSION['user_role'] ?? 'kasir'), ENT_QUOTES, 'UTF-8'); ?>" readonly>
                </div>
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Shift</label>
                    <input class="form-control bg-dark text-white border-secondary rounded-0 profile-field" type="text" value="14:00 - 22:00" readonly>
                </div>
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Status</label>
                    <input class="form-control bg-dark text-white border-secondary rounded-0 profile-field" type="text" value="Aktif" readonly>
                </div>
            </div>
        </div>
    </article>

    <aside class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <h3 class="h3 mb-1 text-warning">Akses</h3>
        <div class="row g-3 mt-4">
            <article class="order-stat">
                <p class="text-muted small text-uppercase mb-2">Stasiun</p>
                <p class="h2 text-warning mb-0 !text-[2rem]">Front</p>
            </article>
            <article class="order-stat">
                <p class="text-muted small text-uppercase mb-2">Status Shift</p>
                <p class="h2 text-warning mb-0 !text-[2rem]">On</p>
            </article>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Service Floor',
    'title' => 'Profil',
    'description' => 'Ringkasan data diri, stasiun kerja, dan informasi shift untuk karyawan aktif.',
    'nav_sections' => staff_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
