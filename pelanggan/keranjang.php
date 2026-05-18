<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('pelanggan');

$title = 'Keranjang';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

$userId = $_SESSION['user_id'] ?? 0;
$cart = [];
$subtotal = 0;

if ($userId > 0) {
    $stmt = db()->prepare("SELECT k.id_keranjang, k.qty, m.id_menu, m.nama_menu, m.harga, m.gambar FROM keranjang k JOIN menu m ON k.id_menu = m.id_menu WHERE k.user_id = ? ORDER BY k.id_keranjang DESC");
    $stmt->execute([$userId]);
    $cart = $stmt->fetchAll();

    foreach ($cart as $item) {
        $subtotal += ((float)$item['harga'] * (int)$item['qty']);
    }
}
$tax = $subtotal * 0.11;
$total = $subtotal + $tax;

ob_start();
?>
<style>
    .cart-item-image {
        width: 80px !important;
        height: 80px !important;
        object-fit: cover;
        border: 1px solid var(--border);
    }
    .cart-item-title {
        font-size: 18px !important;
        margin-bottom: 2px !important;
    }
    .summary-panel {
        background-color: var(--bg-card);
        border: 1px solid var(--border);
        padding: 24px !important;
    }
    .cart-item {
        padding: 16px 0 !important;
    }
</style>
<section class="cart-layout">
    <article class="cart-panel">
        <div class="border-bottom border-secondary pb-3 mb-4">
            <h2 class="font-display text-white mb-0" style="font-size: 32px;">Daftar Pesanan</h2>
        </div>

        <div class="d-flex flex-column">
            <?php if (empty($cart)): ?>
                <div class="py-5 text-center">
                    <p class="text-secondary">Keranjang Anda masih kosong.</p>
                    <a class="btn btn-outline-warning mt-3" href="<?= htmlspecialchars(base_url('pelanggan/menu.php'), ENT_QUOTES, 'UTF-8'); ?>">Lihat Menu</a>
                </div>
            <?php else: ?>
                <?php foreach ($cart as $index => $item): ?>
                <div class="cart-item d-flex gap-3 border-bottom border-soft">
                    <?php if (isset($item['gambar']) && $item['gambar']): ?>
                        <img class="cart-item-image" src="<?= htmlspecialchars($item['gambar'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item['nama_menu'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php else: ?>
                        <div class="cart-item-image bg-black d-flex align-items-center justify-content-center border border-secondary">
                            <span class="text-muted" style="font-size: 10px;">N/A</span>
                        </div>
                    <?php endif; ?>

                    <div class="flex-grow-1">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <h3 class="cart-item-title text-white font-display"><?= htmlspecialchars($item['nama_menu'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                <p class="text-muted small mb-0"><?= (int)$item['qty']; ?>x @ <?= rupiah((float)$item['harga']); ?></p>
                            </div>
                            <span class="text-gold fw-medium" style="font-size: 14px;"><?= rupiah((float)$item['harga'] * (int)$item['qty']); ?></span>
                        </div>
                        <div class="mt-2">
                            <a href="<?= htmlspecialchars(base_url('actions/cart_remove.php?id=' . $item['id_keranjang']), ENT_QUOTES, 'UTF-8'); ?>" class="text-danger small text-uppercase letter-spacing-1 text-decoration-none" style="font-size: 10px;">Hapus</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </article>

    <aside>
        <div class="summary-panel">
            <h3 class="font-display text-white mb-4" style="font-size: 24px;">Ringkasan Biaya</h3>

            <div class="d-flex flex-column gap-2">
                <div class="summary-row d-flex justify-content-between small">
                    <span class="text-muted">Subtotal</span>
                    <span class="text-white"><?= rupiah((float)$subtotal); ?></span>
                </div>
                <div class="summary-row d-flex justify-content-between small">
                    <span class="text-muted">Pajak (11%)</span>
                    <span class="text-white"><?= rupiah((float)$tax); ?></span>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top border-secondary d-flex align-items-center justify-content-between">
                <span class="text-uppercase small letter-spacing-1 text-muted" style="font-size: 10px;">Total Akhir</span>
                <span class="font-display text-gold" style="font-size: 24px;"><?= rupiah((float)$total); ?></span>
            </div>

            <div class="mt-4 d-flex flex-column gap-2">
                <?php if (!empty($cart)): ?>
                <a class="btn btn-warning w-100 py-3" style="font-size: 12px;" href="<?= htmlspecialchars(base_url('pelanggan/keranjang_checkout.php'), ENT_QUOTES, 'UTF-8'); ?>">Lanjut Pembayaran</a>
                <?php endif; ?>
                <a class="btn btn-outline-warning w-100 py-3" style="font-size: 12px;" href="<?= htmlspecialchars(base_url('pelanggan/menu.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali Belanja</a>
            </div>
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
