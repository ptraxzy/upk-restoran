<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Indeks Kuliner';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';

$stmt = db()->query("
    SELECT m.*, k.nama_kategori
    FROM menu m
    LEFT JOIN kategori k ON m.id_kategori = k.id_kategori
    ORDER BY m.id_menu DESC
");
$menuItems = $stmt->fetchAll();

ob_start();
?>
<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4">
    <?php foreach ($menuItems as $item): ?>
        <div class="col">
            <article class="h-100 d-flex flex-column position-relative p-3 card" style="transition: transform 0.3s ease;">
                <?php if ($item['gambar']): ?>
                    <img src="<?= htmlspecialchars($item['gambar'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item['nama_menu'], ENT_QUOTES, 'UTF-8'); ?>" class="w-100 object-cover mb-3" style="height: 160px; border: 1px solid var(--border);">
                <?php else: ?>
                    <div class="w-100 mb-3 d-flex align-items-center justify-content-center bg-black" style="height: 160px; border: 1px solid var(--border);">
                        <span class="text-secondary small text-uppercase letter-spacing-2" style="font-size: 10px;">No Image</span>
                    </div>
                <?php endif; ?>

                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    <h4 class="font-display text-white m-0" style="font-size: 18px; line-height: 1.2;"><?= htmlspecialchars($item['nama_menu'], ENT_QUOTES, 'UTF-8'); ?></h4>
                </div>
                <p class="text-gold small fw-medium mb-2">Rp <?= number_format((float)$item['harga'], 0, ',', '.'); ?></p>

                <p class="text-secondary small mb-3 flex-grow-1" style="line-height: 1.5; font-size: 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars($item['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>

                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-soft mt-auto">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background-color: <?= $item['status'] === 'Tersedia' ? 'var(--gold)' : '#dc3545'; ?>;"></span>
                        <span class="text-secondary" style="font-size: 10px;"><?= htmlspecialchars($item['status'], ENT_QUOTES, 'UTF-8'); ?> • <?= htmlspecialchars((string)$item['porsi'], ENT_QUOTES, 'UTF-8'); ?> Porsi</span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= htmlspecialchars(base_url('admin/menu_edit.php?id=' . $item['id_menu']), ENT_QUOTES, 'UTF-8'); ?>" class="text-gold text-decoration-none" style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px;">Edit</a>
                        <a href="<?= htmlspecialchars(base_url('actions/menu/delete.php?id=' . $item['id_menu']), ENT_QUOTES, 'UTF-8'); ?>" class="text-danger text-decoration-none" style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px;" onclick="return confirm('Hapus menu ini?')">Hapus</a>
                    </div>
                </div>
            </article>
        </div>
    <?php endforeach; ?>
</div>
<?php
$content = ob_get_clean();
render_internal_shell([
    'brand' => 'NOCTRA',
    'badge' => 'Menu Management',
    'title' => 'Indeks Kuliner',
    'description' => 'Katalog menu restoran, kategori, dan ketersediaan aktif.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
