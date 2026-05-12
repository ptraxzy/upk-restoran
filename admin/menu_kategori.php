<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../../backend/auth/check.php';
require_role('admin');

$title = 'Kategori Menu';
$assetBase = '../../../assets';
require __DIR__ . '/../includes/header.php';

$categories = [
    ['name' => 'Makanan Utama', 'count' => 8, 'status' => 'Aktif'],
    ['name' => 'Appetizer', 'count' => 5, 'status' => 'Aktif'],
    ['name' => 'Dessert', 'count' => 4, 'status' => 'Aktif'],
    ['name' => 'Minuman', 'count' => 6, 'status' => 'Aktif'],
    ['name' => 'Paket Spesial', 'count' => 2, 'status' => 'Nonaktif'],
];

ob_start();
?>
<section class="row row-cols-1 row-cols-lg-2 g-4">
    <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <div class="d-flex flex-column gap-2 flex-md-row align-items-md-end justify-content-md-between">
            <div>
                <h3 class="h3 mb-1 text-warning">Kategori Menu</h3>
                <p class="text-muted small mb-4">Kelola kategori untuk mengelompokkan menu restoran.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-warning rounded-0 text-uppercase fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('admin/menu.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali ke Menu</a>
            </div>
        </div>

        <div class="mt-4 d-flex flex-column gap-3">
            <form class="d-flex gap-2" action="#" method="post">
                <input class="form-control bg-dark text-white border-secondary rounded-0 d-flex-grow-1" type="text" name="nama_kategori" placeholder="Nama kategori baru">
                <button class="btn btn-warning rounded-0 text-uppercase fw-medium px-4 py-2" type="submit">Tambah Kategori</button>
            </form>
        </div>

        <table class="table table-dark table-hover table-bordered border-secondary mt-4 mb-0 mt-4">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Jumlah Menu</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $i => $cat): ?>
                    <tr>
                        <td><?= $i + 1; ?></td>
                        <td><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= $cat['count']; ?> item</td>
                        <td><span class="badge <?= $cat['status'] === 'Aktif' ? 'badge bg-warning text-dark' : 'badge bg-secondary text-light'; ?>"><?= htmlspecialchars($cat['status'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td>
                            <div class="d-flex gap-2">
                                <a class="action-link action-link-gold" href="#">Edit</a>
                                <a class="action-link" href="#">Hapus</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </article>

    <aside class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <h3 class="h3 mb-1 text-warning">Ringkasan Kategori</h3>
        <div class="row row-cols-1 row-cols-md-3 g-3 mb-4 mt-4">
            <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
                <p class="text-muted small text-uppercase mb-2">Total Kategori</p>
                <p class="h2 text-warning mb-0 !text-3xl"><?= count($categories); ?></p>
            </article>
            <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
                <p class="text-muted small text-uppercase mb-2">Kategori Aktif</p>
                <p class="h2 text-warning mb-0 !text-3xl"><?= count(array_filter($categories, fn($c) => $c['status'] === 'Aktif')); ?></p>
            </article>
            <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
                <p class="text-muted small text-uppercase mb-2">Total Menu</p>
                <p class="h2 text-warning mb-0 !text-3xl"><?= array_sum(array_column($categories, 'count')); ?></p>
            </article>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administration',
    'title' => 'Kategori Menu',
    'description' => 'Kelola kategori menu untuk pengelompokan yang lebih rapi.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
