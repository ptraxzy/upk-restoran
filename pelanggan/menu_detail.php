<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('pelanggan');

$title = 'Detail Menu';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';

$id = $_GET['id'] ?? null;
$menu = null;

if ($id) {
    $stmt = db()->prepare("
        SELECT m.*, k.nama_kategori
        FROM menu m
        JOIN kategori k ON m.id_kategori = k.id_kategori
        WHERE m.id_menu = ?
    ");
    $stmt->execute([$id]);
    $menu = $stmt->fetch();
}

if (!$menu) {
    set_flash('error', 'Menu tidak ditemukan.');
    redirect(base_url('pelanggan/menu.php'));
}

ob_start();
?>
<section class="row g-5 align-items-center mt-2">
    <div class="col-lg-6">
        <?php if ($menu['gambar']): ?>
            <div class="detail-media shadow-lg" style="background-image:url('<?= htmlspecialchars($menu['gambar']); ?>'); height: 500px; border: 1px solid var(--border);"></div>
        <?php else: ?>
            <div class="detail-media d-flex align-items-center justify-content-center bg-black border border-secondary" style="height: 500px;">
                <span class="text-muted text-uppercase letter-spacing-2">No Image Available</span>
            </div>
        <?php endif; ?>
    </div>

    <article class="col-lg-6">
        <div class="ps-lg-4">
            <p class="text-gold small text-uppercase letter-spacing-2 mb-2"><?= htmlspecialchars($menu['nama_kategori']); ?> • Signature Selection</p>
            <h2 class="font-display text-white mb-3" style="font-size: 56px; line-height: 1.1;"><?= htmlspecialchars($menu['nama_menu']); ?></h2>
            <p class="text-gold mb-4" style="font-size: 24px; font-family: var(--font-body); font-weight: 500;">Rp <?= number_format((float)$menu['harga'], 0, ',', '.'); ?></p>

            <p class="text-secondary mb-5" style="font-size: 15px; line-height: 1.8; max-width: 500px;">
                <?= htmlspecialchars($menu['deskripsi'] ?? 'Tidak ada deskripsi untuk hidangan ini.'); ?>
            </p>

            <div class="mb-5 d-flex gap-2">
                <span class="badge bg-secondary">Fresh Ingredients</span>
                <span class="badge bg-secondary">Chef's Choice</span>
            </div>

            <form method="post" action="<?= htmlspecialchars(base_url('actions/tambah_keranjang.php'), ENT_QUOTES, 'UTF-8'); ?>">
                <div class="d-flex flex-wrap align-items-center gap-4 border-top border-soft pt-5">
                    <div class="qty-stepper">
                        <button type="button" onclick="const i = document.getElementById('qty'); i.value = Math.max(1, parseInt(i.value) - 1); document.getElementById('qty-display').innerText = i.value;">-</button>
                        <span id="qty-display" style="width: 40px; text-align: center; font-size: 14px;">1</span>
                        <input type="hidden" name="id_menu" value="<?= htmlspecialchars((string)$menu['id_menu']); ?>">
                        <input type="hidden" name="qty" id="qty" value="1">
                        <button type="button" onclick="const i = document.getElementById('qty'); i.value = parseInt(i.value) + 1; document.getElementById('qty-display').innerText = i.value;">+</button>
                    </div>
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-warning px-5">Tambah ke Keranjang</button>
                        <a class="btn btn-outline-warning" href="<?= htmlspecialchars(base_url('pelanggan/menu.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
                    </div>
                </div>
            </form>
        </div>
    </article>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Art Culinaire",
    'eyebrow' => 'Customer Detail',
    'title' => 'Eksplorasi Rasa',
    'description' => 'Detail sajian eksklusif dari chef kami, dibuat dengan bahan-bahan terbaik.',
], $content);
require __DIR__ . '/../includes/footer.php';
