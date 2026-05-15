<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Tambah Menu';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="row g-5">
    <div class="col-lg-8">
        <article class="section-panel">
            <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">New Signature</p>
            <h3 class="panel-title mb-1">Tambah Menu Baru</h3>
            <p class="panel-desc mb-5">Masukkan detail menu baru untuk ditambahkan ke katalog restoran.</p>

            <form action="<?= htmlspecialchars(base_url('actions/menu/store.php'), ENT_QUOTES, 'UTF-8'); ?>" method="post">
                <div class="form-grid-layout mb-4">
                    <div>
                        <label class="form-label">URL Foto Makanan</label>
                        <input class="form-control" type="text" name="gambar" placeholder="https://example.com/image.jpg">
                    </div>
                    <div class="d-flex flex-column gap-4">
                        <div>
                            <label class="form-label">Nama Hidangan</label>
                            <input class="form-control" type="text" name="nama_menu" placeholder="E.g. Truffle Infused Wagyu" required>
                        </div>
                        <div>
                            <label class="form-label d-block mb-3">Kategori</label>
                            <div class="d-flex flex-wrap gap-2">
                                <label class="badge bg-warning text-dark px-3 py-2 cursor-pointer border border-warning" style="cursor: pointer;">
                                    <input type="radio" name="id_kategori" value="2" class="d-none" checked> Appetizer
                                </label>
                                <label class="badge bg-secondary text-light px-3 py-2 cursor-pointer border border-secondary" style="cursor: pointer;">
                                    <input type="radio" name="id_kategori" value="1" class="d-none"> Main Course
                                </label>
                                <label class="badge bg-secondary text-light px-3 py-2 cursor-pointer border border-secondary" style="cursor: pointer;">
                                    <input type="radio" name="id_kategori" value="3" class="d-none"> Dessert
                                </label>
                                <label class="badge bg-secondary text-light px-3 py-2 cursor-pointer border border-secondary" style="cursor: pointer;">
                                    <input type="radio" name="id_kategori" value="4" class="d-none"> Beverage
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Deskripsi Kuliner</label>
                            <textarea class="form-control" name="deskripsi" rows="4" placeholder="Deskripsikan profil rasa, tekstur, dan sisi visual utama hidangan elegan."></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="row g-4 mb-5">
                    <div class="col-md-4">
                        <label class="form-label">Harga</label>
                        <input class="form-control" type="number" name="harga" placeholder="195000" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status Ketersediaan</label>
                        <select class="form-select" name="status">
                            <option value="Tersedia">Tersedia</option>
                            <option value="Habis">Habis</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jumlah Porsi</label>
                        <input class="form-control" type="number" name="porsi" value="10" required>
                    </div>
                </div>
                
                <div class="d-flex flex-wrap gap-3 pt-4 border-top border-soft">
                    <button class="btn btn-warning" type="submit">Simpan Menu</button>
                    <a class="btn btn-outline-warning" href="<?= htmlspecialchars(base_url('admin/menu.php'), ENT_QUOTES, 'UTF-8'); ?>">Batal</a>
                </div>
            </form>
        </article>
    </div>

    <aside class="col-lg-4">
        <article class="hero-card">
            <p class="text-secondary small text-uppercase letter-spacing-1 mb-2">Preview Mood</p>
            <div class="mt-4" style="height: 300px; background-image: url('https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&w=1200&q=80'); background-size: cover; background-position: center; border: 1px solid var(--border);"></div>
            <p class="mt-4 small text-secondary line-height-1.6">Gunakan panel ini untuk menjaga tone visual menu baru tetap seragam dengan presentasi akhir ke pelanggan.</p>
        </article>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administration',
    'title' => 'Tambah Menu Baru',
    'description' => 'Gunakan formulir ini untuk merancang sajian baru yang sesuai dengan standar estetika restoran.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
