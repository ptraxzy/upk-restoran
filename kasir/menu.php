<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('kasir');

$title = 'Katalog Menu';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

$pdo = db();

$stmt = $pdo->query("
    SELECT m.*, k.nama_kategori
    FROM menu m
    LEFT JOIN kategori k ON m.id_kategori = k.id_kategori
    WHERE m.deleted_at IS NULL
    ORDER BY m.id_menu DESC
");
$menuItems = $stmt->fetchAll();

ob_start();
?>
<section class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 animate-fade-in-up">
    <?php foreach ($menuItems as $item): ?>
        <div class="col">
            <article class="h-100 d-flex flex-column position-relative p-3 card" style="background: var(--bg-card); border: 1px solid rgba(197, 160, 89, 0.15);">
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

                <p class="text-secondary small mb-3 flex-grow-1" style="line-height: 1.5; font-size: 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars($item['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>

                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-soft mt-auto">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background-color: <?= $item['status'] === 'Tersedia' ? 'var(--gold)' : '#dc3545'; ?>;"></span>
                        <span class="text-secondary" style="font-size: 12px;"><?= htmlspecialchars($item['status'], ENT_QUOTES, 'UTF-8'); ?> • <?= htmlspecialchars((string)$item['porsi'], ENT_QUOTES, 'UTF-8'); ?> Porsi</span>
                    </div>
                </div>
            </article>
        </div>
    <?php endforeach; ?>
    <?php if (empty($menuItems)): ?>
        <div class="col-12 w-100 py-5 text-center">
            <p class="text-muted">Belum ada item menu terdaftar di database.</p>
        </div>
    <?php endif; ?>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'brand' => 'Lumière',
    'badge' => 'Service Floor',
    'title' => 'Daftar Menu Restoran',
    'description' => 'Lihat seluruh ketersediaan hidangan aktif dan porsi yang tersedia.',
    'nav_sections' => staff_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
