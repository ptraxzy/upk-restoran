<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Daftar Karyawan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';

$stmt = db()->query("
    SELECT id_user, username, level, status FROM user
    WHERE level IN ('admin', 'kasir')
    ORDER BY level, username
");
$karyawans = $stmt->fetchAll();

$countAdmin = (int) db()->query("SELECT COUNT(*) FROM user WHERE level = 'admin'")->fetchColumn();
$countKasir = (int) db()->query("SELECT COUNT(*) FROM user WHERE level = 'kasir'")->fetchColumn();
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
                <a class="btn btn-warning py-2 px-3" style="font-size: 12px;" href="<?= htmlspecialchars(base_url('admin/karyawan_tambah.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah Karyawan</a>
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
                            <td class="text-uppercase small"><?= htmlspecialchars($karyawan['level'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php if (($karyawan['status'] ?? 'Aktif') === 'Aktif'): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-3">
                                    <a href="<?= htmlspecialchars(base_url('admin/karyawan_edit.php?id=' . $karyawan['id_user']), ENT_QUOTES, 'UTF-8'); ?>" class="text-gold small">Edit</a>
                                    <a href="<?= htmlspecialchars(base_url('actions/karyawan/delete.php?id=' . $karyawan['id_user']), ENT_QUOTES, 'UTF-8'); ?>" class="text-danger small" onclick="return confirm('Hapus akses karyawan ini?')">Hapus</a>
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
