<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/auth/check.php';
require_role('kasir');

$title = 'Tambah Pesanan';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');

ob_start();
?>
<section class="content-grid">
    <article class="section-panel">
        <p class="eyebrow">Order Entry</p>
        <h3 class="section-title mt-3">Tambah Pesanan</h3>
        <p class="section-subtitle">Input pesanan baru dengan struktur yang lebih cepat dipindai kasir saat jam layanan.</p>

        <form class="mt-6 space-y-5">
            <div class="form-grid">
                <div>
                    <label class="field-label">Nama Tamu</label>
                    <input class="field-input" type="text" placeholder="Nama pelanggan">
                </div>
                <div>
                    <label class="field-label">Nomor Meja</label>
                    <input class="field-input" type="text" placeholder="Mis. 08 atau VIP-1">
                </div>
            </div>
            <div>
                <label class="field-label">Menu Utama</label>
                <select class="field-input">
                    <option>Truffle Mushroom Risotto</option>
                    <option>A5 Wagyu Ribeye</option>
                    <option>Autumn Potage</option>
                </select>
            </div>
            <div>
                <label class="field-label">Catatan Pesanan</label>
                <textarea class="textarea-input" placeholder="Catatan alergi, tingkat kematangan, atau request khusus."></textarea>
            </div>
            <button class="cta-primary" type="submit">Simpan Pesanan</button>
        </form>
    </article>

    <aside class="section-panel">
        <h3 class="section-title">Quick Notes</h3>
        <div class="compact-list mt-5">
            <div class="compact-list-item">
                <div>
                    <p class="font-medium text-stone-100">Upsell Pairing</p>
                    <p class="mt-2 text-sm text-stone-500">Tawarkan pairing untuk risotto dan wagyu saat jam dinner.</p>
                </div>
            </div>
            <div class="compact-list-item">
                <div>
                    <p class="font-medium text-stone-100">Prioritas Meja</p>
                    <p class="mt-2 text-sm text-stone-500">Dahulukan VIP dan meja dengan waktu tunggu paling lama.</p>
                </div>
            </div>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Service Floor',
    'title' => 'Tambah Pesanan',
    'description' => 'Form input pesanan dirapikan untuk membantu kasir bergerak cepat di jam operasional.',
    'nav_sections' => staff_nav_sections(),
], $content);
require base_path('backend/includes/footer.php');
