<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Ubah Menu';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="row row-cols-1 row-cols-lg-2 g-4">
    <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <p class="text-muted small text-uppercase mb-1">Menu Revision</p>
        <h3 class="h3 mb-1 text-warning mt-2">Ubah Menu</h3>
        <p class="text-muted small mb-4">Edit item menu dengan preview foto, informasi harga, dan status yang lebih jelas.</p>

        <div class="mt-4 grid gap-6 lg:grid-cols-[220px_minmax(0,1fr)]">
            <img class="h-64 w-100 object-cover" src="https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=900&q=80" alt="Preview menu">
            <form class="d-flex flex-column gap-4" action="#" method="post">
                <div class="row row-cols-1 row-cols-md-2 g-3 mb-3">
                    <div>
                        <label class="form-label small text-muted text-uppercase mb-1">Nama Menu</label>
                        <input class="form-control bg-dark text-white border-secondary rounded-0" type="text" value="Truffle Beef Wellington">
                    </div>
                    <div>
                        <label class="form-label small text-muted text-uppercase mb-1">Harga</label>
                        <input class="form-control bg-dark text-white border-secondary rounded-0" type="text" value="315000">
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-md-2 g-3 mb-3">
                    <div>
                        <label class="form-label small text-muted text-uppercase mb-1">Kategori</label>
                        <select class="form-control bg-dark text-white border-secondary rounded-0">
                            <option selected>Signature</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label small text-muted text-uppercase mb-1">Status Menu</label>
                        <select class="form-control bg-dark text-white border-secondary rounded-0">
                            <option selected>Tersedia</option>
                            <option>Habis</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Deskripsi</label>
                    <textarea class="form-control bg-dark text-white border-secondary rounded-0">Tenderloin, mushroom duxelles, butter glaze, dan puff pastry dengan plating gelap premium.</textarea>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-warning rounded-0 text-uppercase fw-medium px-4 py-2" type="submit">Simpan Perubahan</button>
                    <a class="btn btn-outline-warning rounded-0 text-uppercase fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('admin/menu.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
                </div>
            </form>
        </div>
    </article>

    <aside class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <h3 class="h3 mb-1 text-warning">Catatan Editor</h3>
        <div class="list-stack mt-4">
            <div class="stack-item">
                <div>
                    <p class="fw-medium text-light">Visual pembeli</p>
                    <p class="mt-2 small text-secondary">Gunakan nama dan deskripsi yang lebih puitis untuk sisi member.</p>
                </div>
            </div>
            <div class="list-item">
                <div>
                    <p class="fw-medium text-light">Status stok</p>
                    <p class="mt-2 small text-secondary">Sinkronkan ketersediaan dengan kitchen pass sebelum prime time.</p>
                </div>
            </div>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administration',
    'title' => 'Ubah Menu',
    'description' => 'Perbarui detail menu yang sudah ada.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
