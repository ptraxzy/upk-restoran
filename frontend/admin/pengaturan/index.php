<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('admin');

$title = 'Pengaturan';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

ob_start();
?>
<section class="content-grid">
    <article class="section-panel">
        <p class="eyebrow">Restaurant Profile</p>
        <h3 class="section-title mt-3">Pengaturan Restoran</h3>
        <p class="section-subtitle">Panel pengaturan untuk identitas restoran, kontak, dan preferensi layanan yang lebih rapi.</p>

        <form class="mt-6 space-y-5">
            <div class="form-grid">
                <div>
                    <label class="field-label">Nama Restoran</label>
                    <input class="field-input" type="text" value="L'Art Culinaire">
                </div>
                <div>
                    <label class="field-label">Nomor Telepon</label>
                    <input class="field-input" type="text" value="+62 812 555 1200">
                </div>
            </div>
            <div>
                <label class="field-label">Alamat</label>
                <textarea class="textarea-input">Jl. Gastronomi 21, Fine Dining District, Jakarta</textarea>
            </div>
            <div class="form-grid">
                <div>
                    <label class="field-label">Jam Operasional</label>
                    <input class="field-input" type="text" value="11:00 - 23:00">
                </div>
                <div>
                    <label class="field-label">Mode Layanan</label>
                    <select class="field-input">
                        <option selected>Dine In & Delivery</option>
                    </select>
                </div>
            </div>
            <button class="cta-primary" type="submit">Simpan Pengaturan</button>
        </form>
    </article>

    <aside class="section-panel">
        <h3 class="section-title">Nada Brand</h3>
        <div class="compact-list mt-5">
            <div class="compact-list-item">
                <div>
                    <p class="font-medium text-stone-100">Arah Visual</p>
                    <p class="mt-2 text-sm text-stone-500">Dark luxury, brass accent, dan foto makanan yang bersih.</p>
                </div>
            </div>
            <div class="compact-list-item">
                <div>
                    <p class="font-medium text-stone-100">Nada Layanan</p>
                    <p class="mt-2 text-sm text-stone-500">Hangat, presisi, dan premium tanpa terasa berisik.</p>
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
require base_path('backend/includes/footer.php');
