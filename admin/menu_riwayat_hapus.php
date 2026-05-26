<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Riwayat Hapus Menu';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';
$pdo = db();

// Fetch soft-deleted menu items with creator and destroyer info
$stmt = $pdo->query("
    SELECT m.*, k.nama_kategori, 
           a.username AS pembuat, a.level AS pembuat_role,
           d.username AS penghapus, d.level AS penghapus_role
    FROM menu m
    LEFT JOIN kategori k ON m.id_kategori = k.id_kategori
    LEFT JOIN user a ON m.id_user = a.id_user
    LEFT JOIN user d ON m.deleted_by = d.id_user
    WHERE m.deleted_at IS NOT NULL
    ORDER BY m.deleted_at DESC
");
$deletedItems = $stmt->fetchAll();

ob_start();
?>
<section class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0 animate-fade-in-up">
    <div class="d-flex flex-column gap-2 flex-md-row align-items-md-end justify-content-md-between mb-4">
        <div>
            <h3 class="h3 mb-1 text-warning">Log Audit & Riwayat Penghapusan</h3>
            <p class="text-muted small mb-0">Daftar menu yang telah diarsipkan/dihapus dari katalog aktif beserta jejak audit pelaku tindakan.</p>
        </div>
        <div>
            <a class="btn btn-outline-warning rounded-0 fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('admin/menu.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali ke Menu</a>
        </div>
    </div>

    <?php render_flash_messages(); ?>

    <div class="table-responsive">
        <table class="table table-dark table-hover table-bordered border-secondary mb-0 align-middle">
            <thead>
                <tr class="text-gold">
                    <th style="width: 80px;">Thumbnail</th>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Ditambahkan Oleh</th>
                    <th>Dihapus Oleh</th>
                    <th>Waktu Dihapus</th>
                    <th style="width: 120px;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($deletedItems as $item): ?>
                <tr>
                    <td>
                        <?php if ($item['gambar']): ?>
                            <img src="<?= htmlspecialchars(menu_image($item['gambar']), ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item['nama_menu'], ENT_QUOTES, 'UTF-8'); ?>" class="object-cover border border-secondary" style="width: 60px; height: 60px;">
                        <?php else: ?>
                            <div class="bg-black text-secondary d-flex align-items-center justify-content-center border border-secondary" style="width: 60px; height: 60px; font-size: 10px;">No Image</div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong class="text-white"><?= htmlspecialchars($item['nama_menu'], ENT_QUOTES, 'UTF-8'); ?></strong>
                        <span class="d-block text-muted small" style="font-size: 11px; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($item['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                    </td>
                    <td><?= htmlspecialchars($item['nama_kategori'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td class="text-white">Rp <?= number_format((float)$item['harga'], 0, ',', '.'); ?></td>
                    <td>
                        <span class="text-white"><?= htmlspecialchars($item['pembuat'] ?? 'Sistem', ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="text-warning small d-block" style="font-size: 9px; text-transform: uppercase;">(<?= htmlspecialchars($item['pembuat_role'] ?? 'admin', ENT_QUOTES, 'UTF-8'); ?>)</span>
                    </td>
                    <td>
                        <span class="text-white"><?= htmlspecialchars($item['penghapus'] ?? 'Sistem', ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="text-danger small d-block" style="font-size: 9px; text-transform: uppercase;">(<?= htmlspecialchars($item['penghapus_role'] ?? 'admin', ENT_QUOTES, 'UTF-8'); ?>)</span>
                    </td>
                    <td class="text-gold small">
                        <?= date('d M Y H:i', strtotime($item['deleted_at'])); ?>
                    </td>
                    <td class="text-center">
                        <a href="<?= htmlspecialchars(base_url('actions/menu/restore.php?id=' . $item['id_menu']), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-outline-success rounded-0 px-3 py-1" onclick="return confirm('Pulihkan menu ini kembali ke katalog aktif?')">Pulihkan</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($deletedItems)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mb-3 text-secondary"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            <p class="mb-0">Tidak ada menu yang terhapus/diarsipkan saat ini.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'brand' => 'Lumière',
    'badge' => 'Audit Console',
    'title' => 'Riwayat Hapus Menu',
    'description' => 'Log audit relasional untuk merekam aktivitas penghapusan menu.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
