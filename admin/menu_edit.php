<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

require_once __DIR__ . '/../includes/database.php';

$id = $_GET['id'] ?? null;
$menu = null;

if ($id) {
    $stmt = db()->prepare("
        SELECT m.*, a.username AS pembuat 
        FROM menu m 
        LEFT JOIN admin a ON m.id_admin = a.id_admin 
        WHERE m.id_menu = ?
    ");
    $stmt->execute([$id]);
    $menu = $stmt->fetch();
}

if (!$menu) {
    set_flash('error', 'Menu tidak ditemukan.');
    redirect(base_url('admin/menu.php'));
}

$title = 'Ubah Menu';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<div class="mb-5 animate-fade-in-up">
    <a href="<?= htmlspecialchars(base_url('admin/menu.php'), ENT_QUOTES, 'UTF-8'); ?>" class="premium-back-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="align-middle me-1"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Kembali Ke Inventaris
    </a>
</div>

<section class="premium-card-glass animate-fade-in-up" style="animation-delay: 0.1s;">
    <form action="<?= htmlspecialchars(base_url('actions/menu/update.php'), ENT_QUOTES, 'UTF-8'); ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id_menu" value="<?= htmlspecialchars((string)$menu['id_menu']); ?>">
        
        <div class="row g-5">
            <!-- Left Column: Aspect-ratio 4:5 Photo Box -->
            <div class="col-lg-5 col-md-12">
                <div class="mb-4">
                    <label class="form-label text-secondary small mb-3">Tinjau Visual Menu</label>
                    <div class="premium-photo-box" id="photoPreviewContainer" style="cursor: pointer; position: relative;">
                        <div class="text-center p-4" id="photoPlaceholder" style="<?= !empty($menu['gambar']) ? 'display: none;' : ''; ?>">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.5" class="mb-3 mx-auto d-block"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                            <span class="d-block text-secondary small mb-1">Klik untuk Unggah Foto</span>
                            <span class="d-block text-muted" style="font-size: 11px;">Rasio 4:5 direkomendasikan</span>
                        </div>
                        <img src="<?= htmlspecialchars(menu_image($menu['gambar'] ?? '')); ?>" alt="Preview Makanan" id="photoPreview" style="<?= empty($menu['gambar']) ? 'display: none;' : 'display: block;'; ?> width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <input type="file" name="gambar_file" id="gambarFileInput" accept="image/*" style="display: none;">
                </div>
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label text-secondary small mb-0">Atau Tempel URL Foto</label>
                        <button type="button" class="btn btn-link text-decoration-none text-warning small p-0" id="btnCancelFile" style="display: none; font-size: 11px;">
                            Batal Unggah File
                        </button>
                    </div>
                    <input class="form-control premium-input-standard" type="text" name="gambar" id="gambarInput" value="<?= htmlspecialchars($menu['gambar'] ?? ''); ?>" placeholder="Masukkan atau tempel URL gambar..." autocomplete="off">
                </div>
            </div>

            <!-- Right Column: Details Fields -->
            <div class="col-lg-7 col-md-12 d-flex flex-column gap-4">
                <div>
                    <label class="form-label text-secondary small mb-1">Nama Hidangan</label>
                    <input class="form-control premium-input-large" type="text" name="nama_menu" value="<?= htmlspecialchars($menu['nama_menu']); ?>" placeholder="E.g. Truffle Infused Wagyu" required autocomplete="off">
                </div>

                <div>
                    <label class="form-label text-secondary small mb-3">Kategori</label>
                    <div class="premium-category-container">
                        <label class="premium-chip-label">
                            <input type="radio" name="id_kategori" value="2" class="premium-chip-input" <?= $menu['id_kategori'] == 2 ? 'checked' : ''; ?>>
                            <div class="premium-chip-box">Appetizer</div>
                        </label>
                        <label class="premium-chip-label">
                            <input type="radio" name="id_kategori" value="1" class="premium-chip-input" <?= $menu['id_kategori'] == 1 ? 'checked' : ''; ?>>
                            <div class="premium-chip-box">Main Course</div>
                        </label>
                        <label class="premium-chip-label">
                            <input type="radio" name="id_kategori" value="3" class="premium-chip-input" <?= $menu['id_kategori'] == 3 ? 'checked' : ''; ?>>
                            <div class="premium-chip-box">Dessert</div>
                        </label>
                        <label class="premium-chip-label">
                            <input type="radio" name="id_kategori" value="4" class="premium-chip-input" <?= $menu['id_kategori'] == 4 ? 'checked' : ''; ?>>
                            <div class="premium-chip-box">Beverage</div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="form-label text-secondary small mb-2">Deskripsi Kuliner</label>
                    <textarea class="form-control premium-textarea" name="deskripsi" rows="5" placeholder="Deskripsikan profil rasa, tekstur, bahan baku eksklusif, dan daya tarik visual utama dari hidangan premium ini."><?= htmlspecialchars($menu['deskripsi'] ?? ''); ?></textarea>
                </div>

                <div class="row g-4">
                    <div class="col-sm-6">
                        <label class="form-label text-secondary small mb-2">Harga Hidangan</label>
                        <div class="input-group">
                            <span class="premium-currency-badge">IDR</span>
                            <input class="form-control premium-input-standard" type="number" name="harga" value="<?= htmlspecialchars((string)$menu['harga']); ?>" placeholder="195000" required style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-secondary small mb-2">Jumlah Porsi</label>
                        <input class="form-control premium-input-standard" type="number" name="porsi" value="<?= htmlspecialchars((string)$menu['porsi']); ?>" required>
                    </div>
                </div>

                <div>
                    <label class="form-label text-secondary small mb-2">Aktivasi Katalog</label>
                    <div class="premium-switch">
                        <div style="min-width: 0; padding-right: 16px;">
                            <span class="text-white fw-medium d-block" style="font-size: 14px;">Status Ketersediaan</span>
                            <span class="text-muted d-block" style="font-size: 12px; line-height: 1.4;">Menu ini langsung aktif dan dapat dipesan pelanggan jika ketersediaan aktif.</span>
                        </div>
                        <select class="form-select premium-input-standard" name="status" style="width: auto; min-width: 140px;">
                            <option value="Tersedia" <?= $menu['status'] === 'Tersedia' ? 'selected' : ''; ?>>Tersedia</option>
                            <option value="Habis" <?= $menu['status'] === 'Habis' ? 'selected' : ''; ?>>Habis</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="form-label text-secondary small mb-2">Informasi Petugas</label>
                    <div class="premium-input-standard bg-black d-flex flex-column gap-2" style="padding: 12px 16px; border: 1px dashed rgba(197, 160, 89, 0.25);">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small" style="font-size: 11px;">Pembuat Awal:</span>
                            <span class="text-white small" style="font-size: 12px;"><?= htmlspecialchars($menu['pembuat'] ?? 'Sistem / Tidak Diketahui', ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small" style="font-size: 11px;">Pengubah Saat Ini:</span>
                            <span class="text-white small" style="font-size: 12px;">
                                <?= htmlspecialchars($_SESSION['user_name'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?>
                                <span class="text-secondary ms-2" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em;">(<?= htmlspecialchars($_SESSION['user_role'] ?? 'admin', ENT_QUOTES, 'UTF-8'); ?>)</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 pt-4 mt-2" style="border-top: 1px solid rgba(197, 160, 89, 0.1);">
                    <a class="btn premium-btn-outline" href="<?= htmlspecialchars(base_url('admin/menu.php'), ENT_QUOTES, 'UTF-8'); ?>">Batal</a>
                    <button class="btn premium-btn-gold" type="submit">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const gambarInput = document.getElementById('gambarInput');
    const gambarFileInput = document.getElementById('gambarFileInput');
    const photoPreviewContainer = document.getElementById('photoPreviewContainer');
    const photoPreview = document.getElementById('photoPreview');
    const photoPlaceholder = document.getElementById('photoPlaceholder');
    const btnCancelFile = document.getElementById('btnCancelFile');

    const updatePreview = () => {
        const url = gambarInput.value.trim();
        if (url) {
            photoPreview.src = url;
            photoPreview.style.display = 'block';
            photoPlaceholder.style.display = 'none';
        } else {
            // If there was an original image but we cleared the input, revert or hide
            photoPreview.style.display = 'none';
            photoPlaceholder.style.display = 'block';
        }
    };

    // Open file selector when clicking the preview container
    photoPreviewContainer.addEventListener('click', () => {
        gambarFileInput.click();
    });

    // Handle file selection
    gambarFileInput.addEventListener('change', () => {
        const file = gambarFileInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                photoPreview.src = e.target.result;
                photoPreview.style.display = 'block';
                photoPlaceholder.style.display = 'none';
                gambarInput.value = ''; // clear URL input
                btnCancelFile.style.display = 'inline-block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle cancel file upload
    btnCancelFile.addEventListener('click', (e) => {
        e.stopPropagation(); // prevent triggering container click
        gambarFileInput.value = '';
        btnCancelFile.style.display = 'none';
        // Revert to original input value if any
        if (gambarInput.value.trim()) {
            updatePreview();
        } else {
            // Revert to original menu image if it existed
            const origImg = '<?= htmlspecialchars(menu_image($menu['gambar'] ?? '')); ?>';
            const hasOrigImg = <?= !empty($menu['gambar']) ? 'true' : 'false'; ?>;
            if (hasOrigImg) {
                photoPreview.src = origImg;
                photoPreview.style.display = 'block';
                photoPlaceholder.style.display = 'none';
            } else {
                updatePreview();
            }
        }
    });

    // Listen to changes on URL input
    gambarInput.addEventListener('input', () => {
        gambarFileInput.value = '';
        btnCancelFile.style.display = 'none';
        updatePreview();
    });
    
    // Initial run - if we already have an image in the DB, it is displayed by PHP
});
</script>
<?php
$content = ob_get_clean();
render_internal_shell([
    'brand' => 'Lumière',
    'badge' => 'Administration',
    'title' => 'Ubah Menu',
    'description' => 'Perbarui detail menu yang sudah ada dengan presisi visual yang premium.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
