<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('pelanggan');

$title = 'Kelola Alamat';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="editorial-grid">
    <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <div class="d-flex flex-column gap-2 flex-md-row align-items-md-end justify-content-md-between">
            <div>
                <h3 class="h3 mb-1 text-warning">Alamat Saya</h3>
                <p class="text-muted small mb-4">Kelola alamat untuk pengiriman atau informasi pribadi.</p>
            </div>
            <a class="btn btn-outline-warning rounded-0 fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('pelanggan/profil.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali ke Profil</a>
        </div>

        <div class="compact-list mt-4">
            <div class="compact-list-item">
                <div>
                    <p class="fw-medium text-light">Rumah</p>
                    <p class="mt-2 small text-muted">Jl. Mawar No. 12, Jakarta Selatan, 12345</p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge badge bg-warning text-dark">Utama</span>
                    <a class="action-link" href="#">Edit</a>
                </div>
            </div>
            <div class="compact-list-item">
                <div>
                    <p class="fw-medium text-light">Kantor</p>
                    <p class="mt-2 small text-muted">Jl. Sudirman Kav. 45, Jakarta Pusat, 10210</p>
                </div>
                <div class="d-flex gap-2">
                    <a class="action-link" href="#">Edit</a>
                    <a class="action-link" href="#">Hapus</a>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <h4 class="small fw-medium text-secondary">Tambah Alamat Baru</h4>
            <form class="mt-4 d-flex flex-column gap-4" action="#" method="post">
                <div class="row row-cols-1 row-cols-md-2 g-3 mb-3">
                    <div>
                        <label class="form-label small text-muted mb-1">Label Alamat</label>
                        <input class="form-control bg-dark text-white border-secondary rounded-0" type="text" name="label" placeholder="Contoh: Rumah, Kantor">
                    </div>
                    <div>
                        <label class="form-label small text-muted mb-1">Kota</label>
                        <input class="form-control bg-dark text-white border-secondary rounded-0" type="text" name="kota" placeholder="Jakarta Selatan">
                    </div>
                </div>
                <div>
                    <label class="form-label small text-muted mb-1">Alamat Lengkap</label>
                    <textarea class="form-control bg-dark text-white border-secondary rounded-0" name="alamat" placeholder="Jl. Nama Jalan No. XX, RT/RW, Kelurahan, Kecamatan"></textarea>
                </div>
                <div>
                    <label class="form-label small text-muted mb-1">Kode Pos</label>
                    <input class="form-control bg-dark text-white border-secondary rounded-0" type="text" name="kode_pos" placeholder="12345" style="max-width: 200px;">
                </div>
                <button class="btn btn-warning rounded-0 fw-medium px-4 py-2" type="submit">Simpan Alamat</button>
            </form>
        </div>
    </article>

    <aside class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <h3 class="h3 mb-1 text-warning">Info</h3>
        <div class="row g-3 mt-4">
            <article class="order-stat">
                <p class="text-muted small mb-2">Total Alamat</p>
                <p class="h2 text-warning mb-0 !text-[2rem]">2</p>
            </article>
            <article class="order-stat">
                <p class="text-muted small mb-2">Alamat Utama</p>
                <p class="h2 text-warning mb-0 !text-[2rem]">Rumah</p>
            </article>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Essence",
    'text-muted small mb-1' => 'Address Book',
    'title' => 'Kelola alamat pengiriman dan informasi lokasi.',
    'description' => 'Tambah, edit, atau hapus alamat yang tersimpan di akun Anda.',
    'actions' => [
        ['label' => 'Edit Profil', 'href' => base_url('pelanggan/profil_edit.php')],
        ['label' => 'Kembali ke Profil', 'href' => base_url('pelanggan/profil.php'), 'variant' => 'secondary'],
    ],
], $content);
require __DIR__ . '/../includes/footer.php';
