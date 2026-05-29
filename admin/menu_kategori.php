<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('admin');

$pdo = db();

// Handle post request to add category
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['nama_kategori'])) {
    $namaKategori = trim($_POST['nama_kategori']);
    if ($namaKategori !== '') {
        try {
            $stmt = $pdo->prepare('INSERT INTO kategori (nama_kategori, id_admin) VALUES (?, ?)');
            $stmt->execute([$namaKategori, $_SESSION['id_user'] ?? null]);
            set_flash('success', 'Kategori baru berhasil ditambahkan ke Lumière.');
        } catch (Throwable $e) {
            set_flash('error', 'Gagal menambahkan kategori: ' . $e->getMessage());
        }
        redirect(base_url('admin/menu_kategori.php'));
    } else {
        set_flash('error', 'Nama kategori tidak boleh dikosongkan.');
    }
}

// Handle delete request
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $idKategori = (int)$_GET['id'];
    
    // Check if there are menus using this category
    $stmtCheck = $pdo->prepare('SELECT COUNT(*) FROM menu WHERE id_kategori = ?');
    $stmtCheck->execute([$idKategori]);
    $menuCount = (int)$stmtCheck->fetchColumn();

    if ($menuCount > 0) {
        set_flash('error', 'Kategori ini tidak dapat dihapus karena masih digunakan oleh ' . $menuCount . ' item menu.');
    } else {
        try {
            $stmtDel = $pdo->prepare('DELETE FROM kategori WHERE id_kategori = ?');
            $stmtDel->execute([$idKategori]);
            set_flash('success', 'Kategori berhasil dihapus.');
        } catch (Throwable $e) {
            set_flash('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
    redirect(base_url('admin/menu_kategori.php'));
}

$title = 'Kategori Menu';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

// Fetch all categories and count menus for each
$stmt = $pdo->query('
    SELECT k.id_kategori, k.nama_kategori, \'admin\' AS role, u.username AS pembuat, COUNT(m.id_menu) AS jumlah_menu
    FROM kategori k
    LEFT JOIN menu m ON k.id_kategori = m.id_kategori
    LEFT JOIN admin u ON k.id_admin = u.id_admin
    GROUP BY k.id_kategori, k.nama_kategori, u.username
    ORDER BY k.id_kategori ASC
');
$categories = $stmt->fetchAll();

ob_start();
?>
<section class="row row-cols-1 row-cols-lg-2 g-4">
    <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <div class="d-flex flex-column gap-2 flex-md-row align-items-md-end justify-content-md-between border-bottom border-soft pb-4 mb-4">
            <div>
                <h3 class="h3 mb-1 text-warning font-display" style="font-size: 24px;">Kategori Menu</h3>
                <p class="text-secondary small mb-0">Kelola kategori untuk mengelompokkan menu restoran.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-warning rounded-0 fw-medium px-3 py-2 text-white" style="font-size: 12px;" href="<?= htmlspecialchars(base_url('admin/menu.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali ke Menu</a>
            </div>
        </div>

        <div class="mt-4 mb-4">
            <form class="d-flex gap-2" action="<?= htmlspecialchars(base_url('admin/menu_kategori.php'), ENT_QUOTES, 'UTF-8'); ?>" method="post">
                <input class="form-control bg-dark text-white border-secondary rounded-0 flex-grow-1" type="text" name="nama_kategori" placeholder="Nama kategori baru..." required>
                <button class="btn btn-warning rounded-0 fw-medium px-4 py-2" style="font-size: 11px;" type="submit">Tambah</button>
            </form>
        </div>

        <table class="table table-dark table-hover table-bordered border-secondary mt-4 mb-0">
            <thead>
                <tr>
                    <th style="width: 80px;">No</th>
                    <th>Nama Kategori</th>
                    <th>Jumlah Menu</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $i => $cat): ?>
                    <tr>
                        <td><?= $i + 1; ?></td>
                        <td class="text-white fw-medium">
                            <?= htmlspecialchars((string)$cat['nama_kategori'], ENT_QUOTES, 'UTF-8'); ?>
                            <div class="text-secondary" style="font-size: 10px; margin-top: 2px;">
                                Oleh: <?= htmlspecialchars($cat['pembuat'] ?? 'Sistem', ENT_QUOTES, 'UTF-8'); ?> 
                                <span class="text-warning" style="font-size: 9px; text-transform: uppercase;">(<?= htmlspecialchars($cat['role'] ?? 'admin', ENT_QUOTES, 'UTF-8'); ?>)</span>
                            </div>
                        </td>
                        <td class="text-secondary"><?= $cat['jumlah_menu']; ?> item</td>
                        <td><span class="badge bg-warning text-dark">Aktif</span></td>
                        <td class="text-end">
                            <a class="text-danger small text-decoration-none" href="<?= htmlspecialchars(base_url('admin/menu_kategori.php?action=delete&id=' . $cat['id_kategori']), ENT_QUOTES, 'UTF-8'); ?>" onclick="return confirm('Hapus kategori ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada kategori terdaftar.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </article>

    <aside class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <h3 class="h3 mb-4 text-warning font-display" style="font-size: 24px;">Ringkasan Kategori</h3>
        <div class="row row-cols-1 row-cols-md-3 g-3 mb-4 mt-2">
            <article class="p-3 border border-soft bg-black d-flex flex-column h-100">
                <p class="text-secondary small mb-2">Total Kategori</p>
                <p class="h2 text-gold font-display mb-0" style="font-size: 32px;"><?= count($categories); ?></p>
            </article>
            <article class="p-3 border border-soft bg-black d-flex flex-column h-100">
                <p class="text-secondary small mb-2">Kategori Aktif</p>
                <p class="h2 text-gold font-display mb-0" style="font-size: 32px;"><?= count($categories); ?></p>
            </article>
            <article class="p-3 border border-soft bg-black d-flex flex-column h-100">
                <p class="text-secondary small mb-2">Total Menu</p>
                <p class="h2 text-gold font-display mb-0" style="font-size: 32px;"><?= array_sum(array_map('intval', array_column($categories, 'jumlah_menu'))); ?></p>
            </article>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'brand' => 'Lumière',
    'badge' => 'Administration',
    'title' => 'Kategori Menu',
    'description' => 'Kelola kategori menu untuk pengelompokan yang lebih rapi.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';

