<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('pelanggan');

$title = 'Keranjang';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

$cart = $_SESSION['cart'] ?? [];
$subtotal = 0;
foreach ($cart as $item) {
    $subtotal += ($item['harga'] * $item['jumlah']);
}
$tax = $subtotal * 0.11;
$total = $subtotal + $tax;

ob_start();
?>
<section class="cart-layout">
    <article class="cart-panel">
        <div class="border-bottom border-secondary pb-3 mb-4">
            <h2 class="font-display text-white mb-0">Daftar Pesanan</h2>
        </div>
        
        <div>
            <?php if (empty($cart)): ?>
                <div class="py-5 text-center">
                    <p class="text-secondary">Keranjang Anda masih kosong.</p>
                    <a class="btn btn-outline-warning mt-3" href="<?= htmlspecialchars(base_url('pelanggan/menu.php'), ENT_QUOTES, 'UTF-8'); ?>">Lihat Menu</a>
                </div>
            <?php else: ?>
                <?php foreach ($cart as $index => $item): ?>
                <div class="cart-item">
                    <img class="cart-item-image" src="<?= htmlspecialchars($item['gambar'] ?: 'https://via.placeholder.com/120', ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item['nama_menu'], ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div>
                                <h3 class="cart-item-title"><?= htmlspecialchars($item['nama_menu'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                <p class="mt-2 small text-muted"><?= htmlspecialchars('Harga: ' . rupiah($item['harga']), ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                            <span class="price-inline"><?= htmlspecialchars(rupiah($item['harga'] * $item['jumlah']), ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="mt-4 d-flex align-items-center justify-content-between">
                            <div class="qty-stepper">
                                <span class="px-3"><?= $item['jumlah']; ?></span>
                            </div>
                            <a href="<?= htmlspecialchars(base_url('actions/cart_remove.php?index=' . $index), ENT_QUOTES, 'UTF-8'); ?>" class="text-danger small text-uppercase letter-spacing-1 text-decoration-none">Hapus</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </article>

    <aside class="summary-panel">
        <h3 class="font-display text-white mb-4">Ringkasan Biaya</h3>
        
        <div>
            <div class="summary-row border-bottom border-soft pb-3 mb-3"><span>Subtotal</span><span><?= htmlspecialchars(rupiah($subtotal), ENT_QUOTES, 'UTF-8'); ?></span></div>
            <div class="summary-row"><span>Pajak (11%)</span><span><?= htmlspecialchars(rupiah($tax), ENT_QUOTES, 'UTF-8'); ?></span></div>
        </div>
        
        <div class="mt-4 pt-4 border-top border-secondary d-flex align-items-center justify-content-between">
            <span class="text-uppercase small letter-spacing-1 text-secondary">Total Akhir</span>
            <span class="font-display text-gold" style="font-size: 28px;"><?= htmlspecialchars(rupiah($total), ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        
        <div class="mt-5 d-flex flex-column gap-3">
            <?php if (!empty($cart)): ?>
            <a class="btn btn-warning w-100" href="<?= htmlspecialchars(base_url('pelanggan/keranjang_checkout.php'), ENT_QUOTES, 'UTF-8'); ?>">Lanjut Pembayaran</a>
            <?php endif; ?>
            <a class="btn btn-outline-warning w-100" href="<?= htmlspecialchars(base_url('pelanggan/menu.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali Belanja</a>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Art Culinaire",
    'eyebrow' => 'Cart & Payment',
    'title' => 'Pemesanan Anda',
    'description' => 'Review pesanan Anda sebelum melanjutkan ke proses pembayaran.',
], $content);
require __DIR__ . '/../includes/footer.php';
