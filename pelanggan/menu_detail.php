<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('pelanggan');

$title = 'Detail Menu';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="detail-layout">
    <div class="detail-media" style="background-image:url('https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&w=1600&q=80');"></div>
    <article class="detail-body">
        <p class="text-gold small text-uppercase letter-spacing-2 mb-2">Menu Utama • Pasta Plate</p>
        <h2 class="font-display text-white mb-2" style="font-size: 48px;">Truffle Mushroom Risotto</h2>
        <p class="price-inline" style="font-size: 18px;">Rp 195.000</p>
        <p class="detail-copy">Sebuah interpretasi kuliner klasik dari kitchen artisan. Beras carnaroli premium yang dimasak perlahan hingga mencapai tekstur al dente yang menyatu, melapisi parmesan hangat dan mushroom glaze pekat. Diakhiri dengan irisan truffle hitam, veal jus lembut, dan sentuhan akhir buttery yang tenang namun tegas.</p>

        <div class="mt-4 d-flex flex-wrap gap-2">
            <span class="badge bg-secondary">Mushroom & Umami</span>
            <span class="badge bg-secondary">Rich Butter</span>
            <span class="badge bg-secondary">Autumn Selection</span>
        </div>

        <form method="post" action="<?= htmlspecialchars(base_url('actions/cart_add.php'), ENT_QUOTES, 'UTF-8'); ?>">
            <div class="detail-actions">
                <div class="qty-stepper">
                    <button type="button" onclick="const i = document.getElementById('qty'); i.value = Math.max(1, parseInt(i.value) - 1); document.getElementById('qty-display').innerText = i.value;">-</button>
                    <span id="qty-display">1</span>
                    <input type="hidden" name="id_menu" value="3">
                    <input type="hidden" name="qty" id="qty" value="1">
                    <button type="button" onclick="const i = document.getElementById('qty'); i.value = parseInt(i.value) + 1; document.getElementById('qty-display').innerText = i.value;">+</button>
                </div>
                <div class="d-flex flex-wrap gap-3">
                    <button type="submit" class="btn btn-warning">Tambah ke Keranjang</button>
                    <a class="btn btn-outline-warning" href="<?= htmlspecialchars(base_url('pelanggan/menu.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
                </div>
            </div>
        </form>
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
