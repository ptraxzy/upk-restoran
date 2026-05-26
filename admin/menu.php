<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Indeks Kuliner';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';

$stmt = db()->query("
    SELECT m.*, k.nama_kategori, a.username AS pembuat, a.level AS pembuat_role
    FROM menu m
    LEFT JOIN kategori k ON m.id_kategori = k.id_kategori
    LEFT JOIN user a ON m.id_user = a.id_user
    WHERE m.deleted_at IS NULL
    ORDER BY m.id_menu DESC
");
$menuItems = $stmt->fetchAll();

$search = trim($_GET['search'] ?? '');
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 5;

$filteredMenuItems = [];
foreach ($menuItems as $item) {
    if ($search === '') {
        $filteredMenuItems[] = $item;
    } else {
        $s = strtolower($search);
        $nStr = strtolower((string)$item['nama_menu']);
        if (strpos($nStr, $s) !== false) {
            $filteredMenuItems[] = $item;
        }
    }
}

$totalRows = count($filteredMenuItems);
$totalPages = ceil($totalRows / $limit);
$paginatedMenuItems = array_slice($filteredMenuItems, ($page - 1) * $limit, $limit);

ob_start();
?>
<div class="d-flex flex-column flex-md-row justify-content-between mb-4 gap-3">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" class="form-control bg-black text-white border-secondary rounded-0" placeholder="Cari nama menu..." value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" style="min-width: 250px;">
        <button type="submit" class="btn btn-warning rounded-0 px-4">Cari</button>
    </form>
    <div class="d-flex gap-2">
        <a href="<?= htmlspecialchars(base_url('admin/menu_tambah.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-warning rounded-0 text-dark fw-medium px-4">Tambah Menu</a>
        <a href="<?= htmlspecialchars(base_url('admin/menu_riwayat_hapus.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-outline-warning rounded-0 text-white fw-medium px-4">Riwayat Hapus Menu</a>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-4">
    <?php foreach ($paginatedMenuItems as $item): ?>
        <div class="col">
            <article class="h-100 d-flex flex-column position-relative p-3 card" style="transition: transform 0.3s ease;">
                <?php if ($item['gambar']): ?>
                    <img src="<?= htmlspecialchars(menu_image($item['gambar']), ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item['nama_menu'], ENT_QUOTES, 'UTF-8'); ?>" class="w-100 object-cover mb-3" style="height: 160px; object-fit: cover; border: 1px solid var(--border);">
                <?php else: ?>
                    <div class="w-100 mb-3 d-flex align-items-center justify-content-center bg-black" style="height: 160px; border: 1px solid var(--border);">
                        <span class="text-secondary small" style="font-size: 12px;">No Image</span>
                    </div>
                <?php endif; ?>

                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    <h4 class="font-display text-white m-0" style="font-size: 18px; line-height: 1.2;"><?= htmlspecialchars($item['nama_menu'], ENT_QUOTES, 'UTF-8'); ?></h4>
                </div>
                <p class="text-gold small fw-medium mb-2">Rp <?= number_format((float)$item['harga'], 0, ',', '.'); ?></p>

                <p class="text-secondary small mb-2 flex-grow-1" style="line-height: 1.5; font-size: 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars($item['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>

                <div class="mb-3 d-flex align-items-center gap-1 text-secondary" style="font-size: 11px; opacity: 0.85;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"/></svg>
                    <span>Oleh: <?= htmlspecialchars($item['pembuat'] ?? 'Sistem', ENT_QUOTES, 'UTF-8'); ?> <span class="text-gold" style="font-size: 9px; text-transform: uppercase;">(<?= htmlspecialchars($item['pembuat_role'] ?? 'admin', ENT_QUOTES, 'UTF-8'); ?>)</span></span>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-soft mt-auto">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background-color: <?= $item['status'] === 'Tersedia' ? 'var(--gold)' : '#dc3545'; ?>;"></span>
                        <span class="text-secondary" style="font-size: 12px;"><?= htmlspecialchars($item['status'], ENT_QUOTES, 'UTF-8'); ?> • <?= htmlspecialchars((string)$item['porsi'], ENT_QUOTES, 'UTF-8'); ?> Porsi</span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= htmlspecialchars(base_url('admin/menu_edit.php?id=' . $item['id_menu']), ENT_QUOTES, 'UTF-8'); ?>" class="text-gold text-decoration-none" style="font-size: 12px; font-weight: 500;">Edit</a>
                        <a href="<?= htmlspecialchars(base_url('actions/menu/delete.php?id=' . $item['id_menu']), ENT_QUOTES, 'UTF-8'); ?>" class="text-danger text-decoration-none" style="font-size: 12px; font-weight: 500;" onclick="return confirm('Hapus menu ini?')">Hapus</a>
                    </div>
                </div>
            </article>
        </div>
    <?php endforeach; ?>
    <?php if (empty($paginatedMenuItems)): ?>
        <div class="col-12 text-center py-5 text-muted">Belum ada menu yang terdaftar atau ditemukan.</div>
    <?php endif; ?>
</div>

<?php if ($totalPages > 1): ?>
    <nav aria-label="Page navigation" class="mt-2 mb-4">
        <ul class="pagination pagination-sm justify-content-center border-0 gap-2 m-0">
            <li class="page-item <?= $page <= 1 ? 'disabled opacity-50 pe-none' : ''; ?>">
                <a class="page-link rounded-0 bg-black text-white border-secondary" href="?page=<?= max(1, $page - 1); ?><?= $search ? '&search=' . urlencode($search) : ''; ?>" aria-label="Previous">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" style="transform: scaleX(-1);"><path fill="currentColor" d="M5 13.5h3v-3H5zm5 0h3v-3h-3zM17 9l-1 1l2 2l-2 2l1 1l3-3z"></path></svg>
                </a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : ''; ?>">
                    <a class="page-link rounded-0 <?= $i === $page ? 'bg-warning text-dark border-warning' : 'bg-black text-white border-secondary'; ?>" href="?page=<?= $i; ?><?= $search ? '&search=' . urlencode($search) : ''; ?>"><?= $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= $page >= $totalPages ? 'disabled opacity-50 pe-none' : ''; ?>">
                <a class="page-link rounded-0 bg-black text-white border-secondary" href="?page=<?= min($totalPages, $page + 1); ?><?= $search ? '&search=' . urlencode($search) : ''; ?>" aria-label="Next">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M5 13.5h3v-3H5zm5 0h3v-3h-3zM17 9l-1 1l2 2l-2 2l1 1l3-3z"></path></svg>
                </a>
            </li>
        </ul>
    </nav>
<?php endif; ?>
<?php
$content = ob_get_clean();
render_internal_shell([
    'brand' => 'Lumière',
    'badge' => 'Menu Management',
    'title' => 'Indeks Kuliner',
    'description' => 'Katalog menu restoran, kategori, dan ketersediaan aktif.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
