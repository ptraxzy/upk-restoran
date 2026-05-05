<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('admin');

$title = 'Ubah Menu';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

ob_start();
?>
<section class="content-grid">
    <article class="section-panel">
        <p class="eyebrow">Menu Revision</p>
        <h3 class="section-title mt-3">Ubah Menu</h3>
        <p class="section-subtitle">Edit item menu dengan preview foto, informasi harga, dan status yang lebih jelas.</p>

        <div class="mt-6 grid gap-6 lg:grid-cols-[220px_minmax(0,1fr)]">
            <img class="h-64 w-full object-cover" src="https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=900&q=80" alt="Preview menu">
            <form class="space-y-5" action="#" method="post">
                <div class="form-grid">
                    <div>
                        <label class="field-label">Nama Menu</label>
                        <input class="field-input" type="text" value="Truffle Beef Wellington">
                    </div>
                    <div>
                        <label class="field-label">Harga</label>
                        <input class="field-input" type="text" value="315000">
                    </div>
                </div>
                <div class="form-grid">
                    <div>
                        <label class="field-label">Kategori</label>
                        <select class="field-input">
                            <option selected>Signature</option>
                        </select>
                    </div>
                    <div>
                        <label class="field-label">Status Menu</label>
                        <select class="field-input">
                            <option selected>Tersedia</option>
                            <option>Habis</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="field-label">Deskripsi</label>
                    <textarea class="textarea-input">Tenderloin, mushroom duxelles, butter glaze, dan puff pastry dengan plating gelap premium.</textarea>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button class="cta-primary" type="submit">Simpan Perubahan</button>
                    <a class="cta-secondary" href="<?= htmlspecialchars(frontend_url('admin/menu/index.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
                </div>
            </form>
        </div>
    </article>

    <aside class="section-panel">
        <h3 class="section-title">Catatan Editor</h3>
        <div class="list-stack mt-5">
            <div class="stack-item">
                <div>
                    <p class="font-medium text-stone-100">Visual pembeli</p>
                    <p class="mt-2 text-sm text-stone-400">Gunakan nama dan deskripsi yang lebih puitis untuk sisi member.</p>
                </div>
            </div>
            <div class="list-item">
                <div>
                    <p class="font-medium text-stone-100">Status stok</p>
                    <p class="mt-2 text-sm text-stone-400">Sinkronkan ketersediaan dengan kitchen pass sebelum prime time.</p>
                </div>
            </div>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administration',
    'title' => 'Ubah Menu',
    'description' => 'Perbarui detail menu yang sudah ada.',
    'nav_sections' => admin_nav_sections(),
], $content);
require base_path('backend/includes/footer.php');
