<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('pelanggan');

$title = 'Profil Member';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

$userId = $_SESSION['id_user'] ?? 0;
$pdo = db();

$stmt = $pdo->prepare('SELECT * FROM user WHERE id_user = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();

ob_start();
?>
<section class="row g-5">
    <div class="col-lg-8 animate-fade-in-up">
        <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
            <div class="d-flex flex-column gap-2 flex-md-row align-items-md-end justify-content-md-between border-bottom border-soft pb-4 mb-4">
                <div>
                    <h2 class="font-display text-warning mb-1" style="font-size: 24px;">Profil Member</h2>
                    <p class="text-secondary small mb-0">Informasi personal akun member terdaftar di Lumière.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-warning rounded-0 fw-medium px-3 py-2" style="font-size: 12px;" href="<?= htmlspecialchars(base_url('pelanggan/profil_edit.php'), ENT_QUOTES, 'UTF-8'); ?>">Edit Data Diri</a>
                </div>
            </div>
            <div class="compact-list mt-4">
                <div class="compact-list-item d-flex justify-content-between border-bottom border-soft py-3" style="font-size: 13px;">
                    <span class="text-secondary">Nama Lengkap</span>
                    <span class="text-white fw-medium"><?= htmlspecialchars((string)($user['nama_user'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="compact-list-item d-flex justify-content-between border-bottom border-soft py-3" style="font-size: 13px;">
                    <span class="text-secondary">Username</span>
                    <span class="text-white fw-medium"><?= htmlspecialchars((string)($user['username'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="compact-list-item d-flex justify-content-between border-bottom border-soft py-3" style="font-size: 13px;">
                    <span class="text-secondary">Alamat Email</span>
                    <span class="text-white fw-medium"><?= htmlspecialchars((string)($user['email'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="compact-list-item d-flex justify-content-between border-bottom border-soft py-3" style="font-size: 13px;">
                    <span class="text-secondary">Level Akun</span>
                    <span class="text-white fw-medium"><?= htmlspecialchars(role_label($user['level'] ?? 'pelanggan'), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="compact-list-item d-flex justify-content-between border-bottom border-soft py-3" style="font-size: 13px;">
                    <span class="text-secondary">Status Keanggotaan</span>
                    <span class="badge bg-warning text-dark">Aktif</span>
                </div>
            </div>
        </article>
    </div>

    <aside class="col-lg-4 animate-fade-in-up" style="animation-delay: 0.2s;">
        <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
            <h3 class="h3 mb-4 text-warning font-display" style="font-size: 24px;">Hak Istimewa</h3>
            <div class="row g-3">
                <article class="p-3 border border-soft bg-black d-flex flex-column h-100 w-100 mb-2">
                    <p class="text-secondary small mb-2">Loyalty Tier</p>
                    <p class="h2 text-gold font-display mb-0" style="font-size: 32px;">Gold Member</p>
                </article>
                <article class="p-3 border border-soft bg-black d-flex flex-column h-100 w-100">
                    <p class="text-secondary small mb-2">Potongan Harga</p>
                    <p class="h2 text-gold font-display mb-0" style="font-size: 32px;">10% OFF</p>
                </article>
            </div>
        </article>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Art Culinaire",
    'eyebrow' => 'Member Profile',
    'title' => 'Ruang Personal Anda',
    'description' => 'Informasi akun, tingkat keanggotaan, dan preferensi eksklusif diatur secara terpusat.',
], $content);
require __DIR__ . '/../includes/footer.php';
