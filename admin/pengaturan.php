<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Pengaturan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="row g-5">
    <div class="col-lg-8">
        <article class="section-panel">
            <div class="border-bottom border-soft pb-3 mb-4">
                <h3 class="font-display text-white m-0" style="font-size: 24px;">Profil Restoran</h3>
                <p class="text-secondary small mt-1">Kelola identitas publik dan kontak operasional.</p>
            </div>

            <form class="d-flex flex-column gap-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Nama Restoran</label>
                        <input class="form-control" type="text" value="L'Art Culinaire">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor Telepon</label>
                        <input class="form-control" type="text" value="+62 812 555 1200">
                    </div>
                </div>
                <div>
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea class="form-control" rows="3">Jl. Gastronomi 21, Fine Dining District, Jakarta</textarea>
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Jam Operasional</label>
                        <input class="form-control" type="text" value="11:00 - 23:00">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mode Layanan Utama</label>
                        <select class="form-select">
                            <option selected>Dine In & Delivery</option>
                            <option>Dine In Only</option>
                        </select>
                    </div>
                </div>
                <div class="pt-3 border-top border-soft mt-2">
                    <button class="btn btn-warning px-5" type="submit">Simpan Pengaturan</button>
                </div>
            </form>
        </article>
    </div>

    <aside class="col-lg-4">
        <article class="section-panel h-100">
            <h3 class="font-display text-white mb-4" style="font-size: 24px;">Filosofi Brand</h3>
            <div class="d-flex flex-column gap-4">
                <div class="pb-3 border-bottom border-soft">
                    <p class="fw-medium text-gold mb-2" style="font-size: 14px;">Arah Visual</p>
                    <p class="text-secondary small m-0">Dark luxury, brass accent, dan fotografi kuliner yang presisi.</p>
                </div>
                <div>
                    <p class="fw-medium text-gold mb-2" style="font-size: 14px;">Nada Layanan</p>
                    <p class="text-secondary small m-0">Hangat, tenang, dan premium tanpa kompromi pada kecepatan.</p>
                </div>
            </div>
        </article>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administration',
    'title' => 'Pengaturan Sistem',
    'description' => 'Konfigurasi identitas restoran dan preferensi layanan global.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
