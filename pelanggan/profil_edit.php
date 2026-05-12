<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('pelanggan');

$title = 'Edit Profil';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="editorial-grid">
    <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <p class="text-muted small text-uppercase mb-1">Edit Data Diri</p>
        <h3 class="h3 mb-1 text-warning mt-2">Edit Profil</h3>
        <p class="text-muted small mb-4">Perbarui informasi profil dan data diri Anda.</p>

        <form class="mt-4 d-flex flex-column gap-4" action="#" method="post">
            <div class="row row-cols-1 row-cols-md-2 g-3 mb-3">
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Username</label>
                    <input class="form-control bg-dark text-white border-secondary rounded-0" type="text" name="username" value="<?= htmlspecialchars($_SESSION['user_name'] ?? 'Member', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Email</label>
                    <input class="form-control bg-dark text-white border-secondary rounded-0" type="email" name="email" value="member@email.com" placeholder="Email Anda">
                </div>
            </div>
            <div class="row row-cols-1 row-cols-md-2 g-3 mb-3">
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Nomor Telepon</label>
                    <input class="form-control bg-dark text-white border-secondary rounded-0" type="text" name="telepon" value="+62 812 345 6789" placeholder="Nomor telepon">
                </div>
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Password Baru</label>
                    <input class="form-control bg-dark text-white border-secondary rounded-0" type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-warning rounded-0 text-uppercase fw-medium px-4 py-2" type="submit">Simpan Perubahan</button>
                <a class="btn btn-outline-warning rounded-0 text-uppercase fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('pelanggan/profil.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
            </div>
        </form>
    </article>

    <aside class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <h3 class="h3 mb-1 text-warning">Info Akun</h3>
        <div class="compact-list mt-4">
            <div class="compact-list-item"><span>Level</span><span><?= htmlspecialchars(role_label($_SESSION['user_role'] ?? 'pelanggan'), ENT_QUOTES, 'UTF-8'); ?></span></div>
            <div class="compact-list-item"><span>Status</span><span class="badge badge bg-warning text-dark">Aktif</span></div>
            <div class="compact-list-item"><span>Bergabung</span><span>Januari 2026</span></div>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Essence",
    'text-muted small text-uppercase mb-1' => 'Edit Profile',
    'title' => 'Perbarui data diri Anda.',
    'description' => 'Ubah informasi profil, kontak, dan keamanan akun.',
    'actions' => [
        ['label' => 'Kelola Alamat', 'href' => base_url('pelanggan/profil_alamat.php')],
        ['label' => 'Kembali ke Profil', 'href' => base_url('pelanggan/profil.php'), 'variant' => 'secondary'],
    ],
], $content);
require __DIR__ . '/../includes/footer.php';
