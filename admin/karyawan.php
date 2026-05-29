<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Daftar Karyawan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';

$stmt = db()->query("
    SELECT id_karyawan, nama_karyawan, username, status FROM karyawan
    ORDER BY username
");
$karyawans = $stmt->fetchAll();

$search = trim($_GET['search'] ?? '');
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 5;

$filteredKaryawans = [];
foreach ($karyawans as $karyawan) {
    if ($search === '') {
        $filteredKaryawans[] = $karyawan;
    } else {
        $s = strtolower($search);
        $uStr = strtolower((string)$karyawan['username']);
        $lStr = 'kasir';
        if (strpos($uStr, $s) !== false || strpos($lStr, $s) !== false) {
            $filteredKaryawans[] = $karyawan;
        }
    }
}

$totalRows = count($filteredKaryawans);
$totalPages = ceil($totalRows / $limit);
$paginatedKaryawans = array_slice($filteredKaryawans, ($page - 1) * $limit, $limit);

$countAdmin = (int) db()->query("SELECT COUNT(*) FROM admin")->fetchColumn();
$countKasir = (int) db()->query("SELECT COUNT(*) FROM karyawan")->fetchColumn();
$total = $countAdmin + $countKasir;

ob_start();
?>
<section class="row g-5">
    <div class="col-lg-8">
        <article class="section-panel h-100">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom border-soft pb-4 mb-4 gap-3">
                <div>
                    <h3 class="font-display text-white m-0" style="font-size: 24px;">Daftar Karyawan</h3>
                    <p class="text-secondary small mb-0 mt-1">Manajemen akses dan tim operasional.</p>
                </div>
                <div class="d-flex gap-2">
                    <form method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control bg-black text-white border-secondary rounded-0" placeholder="Cari nama atau level..." value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="btn btn-outline-warning rounded-0">Cari</button>
                    </form>
                    <a class="btn btn-warning py-2 px-3 text-nowrap" style="font-size: 12px;" href="<?= htmlspecialchars(base_url('admin/karyawan_tambah.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah Karyawan</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Nama Pengguna</th>
                            <th>Level Akses</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paginatedKaryawans as $karyawan): ?>
                        <tr>
                            <td class="fw-medium text-white"><?= htmlspecialchars($karyawan['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-uppercase small">Kasir</td>
                            <td>
                                <?php if (($karyawan['status'] ?? 'Aktif') === 'Aktif'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                             <td class="text-end">
                                <div class="d-flex justify-content-end gap-3">
                                    <a href="<?= htmlspecialchars(base_url('admin/karyawan_edit.php?id=' . $karyawan['id_karyawan']), ENT_QUOTES, 'UTF-8'); ?>" class="text-gold small">Edit</a>
                                </div>
                             </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($paginatedKaryawans)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Belum ada data karyawan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
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
        </article>
    </div>

    <aside class="col-lg-4">
        <article class="section-panel h-100">
            <h3 class="font-display text-white mb-4" style="font-size: 24px;">Komposisi Tim</h3>
            <div class="d-flex flex-column gap-3">
                <div class="p-3 border border-soft bg-black d-flex justify-content-between align-items-center">
                    <span class="text-secondary small">Kasir</span>
                    <span class="h4 text-gold font-display m-0"><?= $countKasir; ?></span>
                </div>
                <div class="p-3 border border-soft bg-black d-flex justify-content-between align-items-center">
                    <span class="text-secondary small">Administrator</span>
                    <span class="h4 text-gold font-display m-0"><?= $countAdmin; ?></span>
                </div>
                <div class="p-3 border border-gold border-opacity-20 bg-gold bg-opacity-5 d-flex justify-content-between align-items-center mt-3">
                    <span class="text-white small fw-medium">Total Anggota</span>
                    <span class="h3 text-gold font-display m-0"><?= $total; ?></span>
                </div>
            </div>
        </article>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'brand' => 'Lumière',
    'badge' => 'Administration',
    'title' => 'Manajemen Karyawan',
    'description' => 'Kelola tim operasional restoran dari satu panel yang padat tapi tetap elegan.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
