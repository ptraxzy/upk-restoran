<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('pelanggan');

$title = 'Profil Member';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="editorial-grid">
    <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <div class="d-flex flex-column gap-2 flex-md-row align-items-md-end justify-content-md-between">
            <h2 class="h3 mb-1 text-warning">Profil Member</h2>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-warning rounded-0 text-uppercase fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('pelanggan/profil_alamat.php'), ENT_QUOTES, 'UTF-8'); ?>">Kelola Alamat</a>
                <a class="btn btn-warning rounded-0 text-uppercase fw-medium px-4 py-2" href="<?= htmlspecialchars(base_url('pelanggan/profil_edit.php'), ENT_QUOTES, 'UTF-8'); ?>">Edit Data Diri</a>
            </div>
        </div>
        <div class="compact-list mt-4">
            <div class="compact-list-item"><span>Username</span><span><?= htmlspecialchars($_SESSION['user_name'] ?? 'Member', ENT_QUOTES, 'UTF-8'); ?></span></div>
            <div class="compact-list-item"><span>Level</span><span><?= htmlspecialchars(role_label($_SESSION['user_role'] ?? 'pelanggan'), ENT_QUOTES, 'UTF-8'); ?></span></div>
            <div class="compact-list-item"><span>Alamat</span><span>Jakarta Selatan</span></div>
            <div class="compact-list-item"><span>Status Akun</span><span class="badge badge bg-warning text-dark">Aktif</span></div>
        </div>
    </article>

    <aside class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <h3 class="h3 mb-1 text-warning">Preferensi</h3>
        <div class="row g-3 mt-4">
            <article class="order-stat">
                <p class="text-muted small text-uppercase mb-2">Favorit</p>
                <p class="h2 text-warning mb-0 !text-[2rem]">06</p>
            </article>
            <article class="order-stat">
                <p class="text-muted small text-uppercase mb-2">Loyalty Tier</p>
                <p class="h2 text-warning mb-0 !text-[2rem]">Gold</p>
            </article>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Essence",
    'text-muted small text-uppercase mb-1' => 'Member Profile',
    'title' => 'Profil akun dan preferensi yang lebih ringkas.',
    'description' => 'Informasi dasar member disusun sebagai panel yang bersih dan siap dikembangkan.',
    'actions' => [
        ['label' => 'Riwayat Pesanan', 'href' => base_url('pelanggan/pesanan.php')],
        ['label' => 'Menu', 'href' => base_url('pelanggan/menu.php'), 'variant' => 'secondary'],
    ],
], $content);
require __DIR__ . '/../includes/footer.php';
