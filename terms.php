<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$title = 'Syarat & Ketentuan';
$assetBase = 'assets';
require __DIR__ . '/includes/header.php';

ob_start();
?>
<section class="row justify-content-center">
    <div class="col-lg-8 animate-fade-in-up">
        <article class="card bg-dark text-white border-secondary p-5 mb-4 rounded-0">
            <div class="border-bottom border-soft pb-4 mb-4">
                <h1 class="font-display text-warning mb-1" style="font-size: 32px;">Syarat & Ketentuan</h1>
                <p class="text-secondary small mb-0">Terakhir diperbarui: <?= date('d M Y') ?></p>
            </div>
            
            <div class="text-secondary small" style="line-height: 1.8; font-size: 13px;">
                <p class="text-white mb-3">Syarat & Ketentuan ini mengatur penggunaan sistem pemesanan digital mandiri (self-service dine-in portal) di restoran Lumière. Dengan membuat akun dan memesan makanan melalui sistem kami, Anda menyetujui ketentuan di bawah ini.</p>
                
                <h3 class="text-warning font-display mt-4 mb-3" style="font-size: 18px;">1. Penggunaan Layanan Di Tempat (Dine-in Only)</h3>
                <p>Layanan pemesanan digital ini hanya tersedia untuk tamu yang berada langsung di dalam restoran Lumière (Dine-in). Kami tidak melayani pengiriman (delivery) atau pesanan luar kota melalui sistem web ini.</p>
                
                <h3 class="text-warning font-display mt-4 mb-3" style="font-size: 18px;">2. Tanggung Jawab Nomor Meja</h3>
                <p>Saat melakukan pemesanan (checkout), pelanggan wajib memasukkan nomor meja yang benar sesuai dengan meja yang ditempati saat itu. Lumière tidak bertanggung jawab atas kesalahan pengantaran hidangan akibat salah memasukkan nomor meja.</p>
                
                <h3 class="text-warning font-display mt-4 mb-3" style="font-size: 18px;">3. Ketentuan Pembayaran</h3>
                <ul>
                    <li><strong class="text-white">QRIS (Otomatis):</strong> Pembayaran menggunakan QRIS akan terverifikasi secara instan melalui sistem pembayaran digital kami.</li>
                    <li><strong class="text-white">Tunai (Cash):</strong> Jika Anda memilih metode pembayaran tunai, Anda wajib melakukan pembayaran ke kasir utama terlebih dahulu sebelum pesanan mulai disiapkan dan dihidangkan.</li>
                </ul>
                
                <h3 class="text-warning font-display mt-4 mb-3" style="font-size: 18px;">4. Pembatalan & Pengembalian Uang</h3>
                <p>Semua pesanan yang telah dikonfirmasi dan telah masuk ke dapur chef tidak dapat dibatalkan, diubah, atau dikembalikan uangnya (*non-refundable*), kecuali dalam kondisi tertentu atas persetujuan floor manager kami.</p>
                
                <h3 class="text-warning font-display mt-4 mb-3" style="font-size: 18px;">5. Voucher & Kode Promo</h3>
                <p>Penggunaan voucher/diskon promo tunduk pada batas minimal pembelian dan jumlah porsi yang ditentukan. Setiap voucher hanya berlaku untuk satu kali transaksi per pelanggan. Penyalahgunaan sistem promo dapat menyebabkan akun dinonaktifkan.</p>
            </div>
            
            <div class="mt-5 pt-3 border-top border-soft">
                <a class="btn btn-outline-warning rounded-0 fw-medium px-4 py-2 text-white" style="font-size: 11px;" href="<?= htmlspecialchars(base_url(''), ENT_QUOTES, 'UTF-8'); ?>">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align: -2px; margin-right: 5px;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    KEMBALI KE BERANDA
                </a>
            </div>
        </article>
    </div>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => 'Lumière',
    'title' => 'Syarat & Ketentuan',
    'hide_hero' => true,
], $content);
require __DIR__ . '/includes/footer.php';
