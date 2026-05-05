<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('kasir');

$title = 'Profil Karyawan';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

ob_start();
?>
<section class="content-grid">
    <article class="section-panel">
        <p class="eyebrow">Personal Profile</p>
        <h3 class="section-title mt-3">Profil Karyawan</h3>

        <div class="profile-grid mt-6">
            <div class="profile-avatar"><?= htmlspecialchars(strtoupper(substr($_SESSION['user_name'] ?? 'K', 0, 1)), ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="form-grid">
                <div>
                    <label class="field-label">Nama</label>
                    <input class="field-input profile-field" type="text" value="<?= htmlspecialchars($_SESSION['user_name'] ?? 'Kasir', ENT_QUOTES, 'UTF-8'); ?>" readonly>
                </div>
                <div>
                    <label class="field-label">Level</label>
                    <input class="field-input profile-field" type="text" value="<?= htmlspecialchars(role_label($_SESSION['user_role'] ?? 'kasir'), ENT_QUOTES, 'UTF-8'); ?>" readonly>
                </div>
                <div>
                    <label class="field-label">Shift</label>
                    <input class="field-input profile-field" type="text" value="14:00 - 22:00" readonly>
                </div>
                <div>
                    <label class="field-label">Status</label>
                    <input class="field-input profile-field" type="text" value="Aktif" readonly>
                </div>
            </div>
        </div>
    </article>

    <aside class="section-panel">
        <h3 class="section-title">Akses</h3>
        <div class="order-rail mt-5 !grid-cols-1">
            <article class="order-stat">
                <p class="metric-label">Stasiun</p>
                <p class="metric-value !text-[2rem]">Front</p>
            </article>
            <article class="order-stat">
                <p class="metric-label">Status Shift</p>
                <p class="metric-value !text-[2rem]">On</p>
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
require base_path('backend/includes/footer.php');
