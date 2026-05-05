<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('pelanggan');

$title = 'Profil Member';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

ob_start();
?>
<section class="editorial-grid">
    <article class="section-panel">
        <h2 class="section-title">Profil Member</h2>
        <div class="compact-list mt-6">
            <div class="compact-list-item"><span>Username</span><span><?= htmlspecialchars($_SESSION['user_name'] ?? 'Member', ENT_QUOTES, 'UTF-8'); ?></span></div>
            <div class="compact-list-item"><span>Level</span><span><?= htmlspecialchars(role_label($_SESSION['user_role'] ?? 'pelanggan'), ENT_QUOTES, 'UTF-8'); ?></span></div>
            <div class="compact-list-item"><span>Alamat</span><span>Jakarta Selatan</span></div>
            <div class="compact-list-item"><span>Status Akun</span><span class="badge badge-gold">Aktif</span></div>
        </div>
    </article>

    <aside class="section-panel">
        <h3 class="section-title">Preferensi</h3>
        <div class="order-rail mt-5 !grid-cols-1">
            <article class="order-stat">
                <p class="metric-label">Favorit</p>
                <p class="metric-value !text-[2rem]">06</p>
            </article>
            <article class="order-stat">
                <p class="metric-label">Loyalty Tier</p>
                <p class="metric-value !text-[2rem]">Gold</p>
            </article>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Essence",
    'eyebrow' => 'Member Profile',
    'title' => 'Profil akun dan preferensi yang lebih ringkas.',
    'description' => 'Informasi dasar member disusun sebagai panel yang bersih dan siap dikembangkan.',
    'actions' => [
        ['label' => 'Riwayat Pesanan', 'href' => frontend_url('pembeli/pesanan/index.php')],
        ['label' => 'Menu', 'href' => frontend_url('pembeli/menu/index.php'), 'variant' => 'secondary'],
    ],
], $content);
require base_path('backend/includes/footer.php');
