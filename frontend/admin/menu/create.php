<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('admin');

$title = 'Tambah Menu';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

ob_start();
?>
<section class="content-grid">
    <article class="section-panel">
        <p class="eyebrow">New Signature</p>
        <h3 class="section-title mt-3">Tambah Menu Baru</h3>
        <p class="section-subtitle">Masukkan detail menu baru untuk ditambahkan ke katalog restoran.</p>

        <form class="mt-6 space-y-5" action="<?= htmlspecialchars(backend_url('actions/menu/store.php'), ENT_QUOTES, 'UTF-8'); ?>" method="post">
            <div class="grid gap-6 lg:grid-cols-[160px_minmax(0,1fr)]">
                <div>
                    <label class="field-label">Foto Makanan</label>
                    <div class="flex min-h-[124px] items-center justify-center border border-white/10 bg-white/[0.03] text-[10px] uppercase tracking-[0.2em] text-stone-500">Unggah Foto</div>
                </div>
                <div class="space-y-5">
                    <div>
                        <label class="field-label">Nama Hidangan</label>
                        <input class="field-input" type="text" placeholder="E.g. Truffle Infused Wagyu">
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="badge badge-gold">Appetizer</span>
                        <span class="badge badge-muted">Main Course</span>
                        <span class="badge badge-muted">Dessert</span>
                        <span class="badge badge-muted">Beverage</span>
                    </div>
                    <div>
                        <label class="field-label">Deskripsi Kuliner</label>
                        <textarea class="textarea-input" placeholder="Deskripsikan profil rasa, tekstur, dan sisi visual utama hidangan elegan."></textarea>
                    </div>
                </div>
            </div>
            <div class="form-grid">
                <div>
                    <label class="field-label">Harga</label>
                    <input class="field-input" type="text" placeholder="Rp 195000">
                </div>
                <div>
                    <label class="field-label">Status Ketersediaan</label>
                    <select class="field-input">
                        <option>Tersedia</option>
                        <option>Habis</option>
                    </select>
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <button class="cta-primary" type="submit">Simpan Menu</button>
                <a class="cta-secondary" href="<?= htmlspecialchars(frontend_url('admin/menu/index.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
            </div>
        </form>
    </article>

    <aside class="hero-card">
        <p class="eyebrow">Preview Mood</p>
        <img class="mt-5 h-72 w-full object-cover" src="https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&w=1200&q=80" alt="Preview makanan premium">
        <p class="mt-5 text-sm leading-7 text-stone-300">Gunakan panel ini untuk menjaga tone visual menu baru tetap seragam dengan sisi pembeli.</p>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administration',
    'title' => 'Tambah Menu Baru',
    'description' => 'Gunakan formulir ini untuk menambah menu baru.',
    'nav_sections' => admin_nav_sections(),
], $content);
require base_path('backend/includes/footer.php');
