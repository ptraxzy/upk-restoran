<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('pelanggan');

$title = 'Profil Saya';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

$userId = $_SESSION['id_user'] ?? 0;
$pdo = db();

$stmt = $pdo->prepare('SELECT * FROM pelanggan WHERE id_pelanggan = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();

ob_start();
?>
<section class="row justify-content-center">
    <div class="col-lg-6 col-md-8 animate-fade-in-up">
        <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
            <div class="d-flex flex-column gap-2 flex-md-row align-items-md-center justify-content-md-between border-bottom border-soft pb-4 mb-4">
                <div>
                    <h2 class="font-display text-warning mb-1" style="font-size: 24px;">Profil Saya</h2>
                    <p class="text-secondary small mb-0">Informasi akun pelanggan terdaftar.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-warning rounded-0 fw-medium px-3 py-2" style="font-size: 12px;" href="<?= htmlspecialchars(base_url('pelanggan/profil_edit.php'), ENT_QUOTES, 'UTF-8'); ?>">Edit Profil</a>
                </div>
            </div>
            <div class="compact-list mt-4">
                <div class="compact-list-item d-flex justify-content-between border-bottom border-soft py-3" style="font-size: 13px;">
                    <span class="text-secondary">Nama Lengkap</span>
                    <span class="text-white fw-medium"><?= htmlspecialchars((string)($user['nama_pelanggan'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></span>
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
                    <span class="text-white fw-medium">Pelanggan</span>
                </div>
                <div class="compact-list-item d-flex justify-content-between border-bottom border-soft py-3" style="font-size: 13px;">
                    <span class="text-secondary">Status Akun</span>
                    <span class="badge bg-success text-white">Aktif</span>
                </div>
            </div>
        </article>
    </div>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => 'Lumière',
    'eyebrow' => 'Akun',
    'title' => 'Profil Saya',
    'description' => 'Kelola informasi data diri dan akun Anda.',
], $content);
require __DIR__ . '/../includes/footer.php';
