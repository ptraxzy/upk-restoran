<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Ubah Menu';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';

$id = $_GET['id'] ?? null;
$menu = null;

if ($id) {
    $stmt = db()->prepare("SELECT * FROM menu WHERE id_menu = ?");
    $stmt->execute([$id]);
    $menu = $stmt->fetch();
}

if (!$menu) {
    set_flash('error', 'Menu tidak ditemukan.');
    redirect(base_url('admin/menu.php'));
}

$stmtKategori = db()->query("SELECT * FROM kategori");
$kategoris = $stmtKategori->fetchAll();

ob_start();
?>
<section class="row row-cols-1 row-cols-lg-2 g-4">
    <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <p class="text-muted small text-uppercase mb-1">Menu Revision</p>
        <h3 class="h3 mb-1 text-warning mt-2">Ubah Menu</h3>
        <p class="text-muted small mb-4">Edit item menu dengan preview foto, informasi harga, dan status yang lebih jelas.</p>

        <div class="mt-4">
            <form class="d-flex flex-column gap-4" action="<?= htmlspecialchars(base_url('actions/menu/update.php'), ENT_QUOTES, 'UTF-8'); ?>" method="post">
                <input type="hidden" name="id_menu" value="<?= htmlspecialchars((string)$menu['id_menu']); ?>">

                <div class="mb-3">
                    <label class="form-label small text-muted text-uppercase mb-1">URL Gambar</label>
                    <input class="form-control bg-dark text-white border-secondary rounded-0" type="text" name="gambar" value="<?= htmlspecialchars($menu['gambar'] ?? ''); ?>">
                </div>

                <div class="row row-cols-1 row-cols-md-2 g-3 mb-3">
                    <div>
                        <label class="form-label small text-muted text-uppercase mb-1">Nama Menu</label>
                        <input class="form-control bg-dark text-white border-secondary rounded-0" type="text" name="nama_menu" value="<?= htmlspecialchars($menu['nama_menu']); ?>" required>
                    </div>
                    <div>
                        <label class="form-label small text-muted text-uppercase mb-1">Harga</label>
                        <input class="form-control bg-dark text-white border-secondary rounded-0" type="number" name="harga" value="<?= htmlspecialchars((string)$menu['harga']); ?>" required>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-md-2 g-3 mb-3">
                    <div>
                        <label class="form-label small text-muted text-uppercase mb-1">Kategori</label>
                        <select class="form-control bg-dark text-white border-secondary rounded-0" name="id_kategori">
                            <?php foreach ($kategoris as $kat): ?>
                                <option value="<?= $kat['id_kategori']; ?>" <?= $kat['id_kategori'] == $menu['id_kategori'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($kat['nama_kategori']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label small text-muted text-uppercase mb-1">Status Menu</label>
                        <select class="form-control bg-dark text-white border-secondary rounded-0" name="status">
                            <option value="Tersedia" <?= $menu['status'] === 'Tersedia' ? 'selected' : ''; ?>>Tersedia</option>
                            <option value="Habis" <?= $menu['status'] === 'Habis' ? 'selected' : ''; ?>>Habis</option>
                        </select>
                    </div>
                </div>
                <div class="row row-cols-1 row-cols-md-2 g-3 mb-3">
                    <div>
                        <label class="form-label small text-muted text-uppercase mb-1">Jumlah Porsi</label>
                        <input class="form-control bg-dark text-white border-secondary rounded-0" type="number" name="porsi" value="<?= htmlspecialchars((string)$menu['porsi']); ?>" required>
                    </div>
                </div>
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Deskripsi</label>
                    <textarea class="form-control bg-dark text-white border-secondary rounded-0" name="deskripsi" rows="3"><?= htmlspecialchars($menu['deskripsi'] ?? ''); ?></textarea>
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
