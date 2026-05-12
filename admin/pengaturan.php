<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Pengaturan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="row row-cols-1 row-cols-lg-2 g-4">
    <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <p class="text-muted small text-uppercase mb-1">Restaurant Profile</p>
        <h3 class="h3 mb-1 text-warning mt-2">Pengaturan Restoran</h3>
        <p class="text-muted small mb-4">Panel pengaturan untuk identitas restoran, kontak, dan preferensi layanan yang lebih rapi.</p>

        <form class="mt-4 d-flex flex-column gap-4">
            <div class="row row-cols-1 row-cols-md-2 g-3 mb-3">
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Nama Restoran</label>
                    <input class="form-control bg-dark text-white border-secondary rounded-0" type="text" value="L'Art Culinaire">
                </div>
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Nomor Telepon</label>
                    <input class="form-control bg-dark text-white border-secondary rounded-0" type="text" value="+62 812 555 1200">
                </div>
            </div>
            <div>
                <label class="form-label small text-muted text-uppercase mb-1">Alamat</label>
                <textarea class="form-control bg-dark text-white border-secondary rounded-0">Jl. Gastronomi 21, Fine Dining District, Jakarta</textarea>
            </div>
            <div class="row row-cols-1 row-cols-md-2 g-3 mb-3">
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Jam Operasional</label>
                    <input class="form-control bg-dark text-white border-secondary rounded-0" type="text" value="11:00 - 23:00">
                </div>
                <div>
                    <label class="form-label small text-muted text-uppercase mb-1">Mode Layanan</label>
                    <select class="form-control bg-dark text-white border-secondary rounded-0">
                        <option selected>Dine In & Delivery</option>
                    </select>
                </div>
            </div>
            <button class="btn btn-warning rounded-0 text-uppercase fw-medium px-4 py-2" type="submit">Simpan Pengaturan</button>
        </form>
    </article>

    <aside class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <h3 class="h3 mb-1 text-warning">Nada Brand</h3>
        <div class="compact-list mt-4">
            <div class="compact-list-item">
                <div>
                    <p class="fw-medium text-light">Arah Visual</p>
                    <p class="mt-2 small text-muted">Dark luxury, brass accent, dan foto makanan yang bersih.</p>
                </div>
            </div>
            <div class="compact-list-item">
                <div>
                    <p class="fw-medium text-light">Nada Layanan</p>
                    <p class="mt-2 small text-muted">Hangat, presisi, dan premium tanpa terasa berisik.</p>
                </div>
            </div>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administration',
    'title' => 'Pengaturan',
    'description' => 'Area kontrol identitas restoran dan preferensi utama layanan.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
