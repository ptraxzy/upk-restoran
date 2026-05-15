<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Daftar Karyawan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';

$stmt = db()->query("SELECT * FROM user WHERE level IN ('admin', 'kasir') ORDER BY level, username");
$karyawans = $stmt->fetchAll();

$stmtCounts = db()->query("SELECT level, COUNT(*) as count FROM user WHERE level IN ('admin', 'kasir') GROUP BY level");
$counts = $stmtCounts->fetchAll(PDO::FETCH_KEY_PAIR);
$countAdmin = $counts['admin'] ?? 0;
$countKasir = $counts['kasir'] ?? 0;
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
                <a class="btn btn-warning py-2 px-3" style="font-size: 10px;" href="<?= htmlspecialchars(base_url('admin/karyawan_tambah.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah Karyawan</a>
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
                        <?php foreach ($karyawans as $karyawan): ?>
                        <tr>
                            <td class="fw-medium text-white"><?= htmlspecialchars($karyawan['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-uppercase small letter-spacing-1"><?= htmlspecialchars($karyawan['level'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20">Aktif</span></td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-3">
                                    <a href="<?= htmlspecialchars(base_url('admin/karyawan_edit.php?id=' . $karyawan['id_user']), ENT_QUOTES, 'UTF-8'); ?>" class="text-gold small text-uppercase letter-spacing-1">Edit</a>
                                    <a href="<?= htmlspecialchars(base_url('actions/karyawan/delete.php?id=' . $karyawan['id_user']), ENT_QUOTES, 'UTF-8'); ?>" class="text-danger small text-uppercase letter-spacing-1" onclick="return confirm('Hapus akses karyawan ini?')">Hapus</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($karyawans)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Belum ada data karyawan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>
    </div>

    <aside class="col-lg-4">
        <article class="section-panel h-100">
            <h3 class="font-display text-white mb-4" style="font-size: 24px;">Komposisi Tim</h3>
            <div class="d-flex flex-column gap-3">
                <div class="p-3 border border-soft bg-black d-flex justify-content-between align-items-center">
                    <span class="text-secondary small text-uppercase letter-spacing-1">Kasir</span>
                    <span class="h4 text-gold font-display m-0"><?= $countKasir; ?></span>
                </div>
                <div class="p-3 border border-soft bg-black d-flex justify-content-between align-items-center">
                    <span class="text-secondary small text-uppercase letter-spacing-1">Administrator</span>
                    <span class="h4 text-gold font-display m-0"><?= $countAdmin; ?></span>
                </div>
                <div class="p-3 border border-gold border-opacity-20 bg-gold bg-opacity-5 d-flex justify-content-between align-items-center mt-3">
                    <span class="text-white small text-uppercase letter-spacing-2 fw-medium">Total Anggota</span>
                    <span class="h3 text-gold font-display m-0"><?= $total; ?></span>
                </div>
            </div>
        </article>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'brand' => 'NOCTRA',
    'badge' => 'Administration',
    'title' => 'Manajemen Karyawan',
    'description' => 'Kelola tim operasional restoran dari satu panel yang padat tapi tetap elegan.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
