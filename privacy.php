<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$title = 'Kebijakan Privasi';
$assetBase = 'assets';
require __DIR__ . '/includes/header.php';

ob_start();
?>
<section class="row justify-content-center">
    <div class="col-lg-8 animate-fade-in-up">
        <article class="card bg-dark text-white border-secondary p-5 mb-4 rounded-0">
            <div class="border-bottom border-soft pb-4 mb-4">
                <h1 class="font-display text-warning mb-1" style="font-size: 32px;">Kebijakan Privasi</h1>
                <p class="text-secondary small mb-0">Terakhir diperbarui: <?= date('d M Y') ?></p>
            </div>
            
            <div class="text-secondary small" style="line-height: 1.8; font-size: 13px;">
                <p class="text-white mb-3">Selamat datang di Lumière. Kami sangat menghargai privasi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda saat menggunakan sistem pemesanan digital mandiri (self-service dine-in portal) di dalam restoran kami.</p>
                
                <h3 class="text-warning font-display mt-4 mb-3" style="font-size: 18px;">1. Informasi yang Kami Kumpulkan</h3>
                <p>Untuk memproses pesanan Anda langsung dari meja makan, kami mengumpulkan beberapa informasi dasar sebagai berikut:</p>
                <ul>
                    <li><strong class="text-white">Informasi Akun:</strong> Nama Lengkap, Username, Alamat Email, dan Kata Sandi (yang dienkripsi secara aman).</li>
                    <li><strong class="text-white">Detail Kunjungan:</strong> Nomor meja aktif Anda saat memesan hidangan di restoran.</li>
                    <li><strong class="text-white">Detail Transaksi:</strong> Daftar hidangan yang dipesan, harga total, waktu pemesanan, dan metode pembayaran (QRIS atau Tunai).</li>
                </ul>
                
                <h3 class="text-warning font-display mt-4 mb-3" style="font-size: 18px;">2. Bagaimana Kami Menggunakan Data Anda</h3>
                <p>Kami menggunakan data Anda hanya untuk kepentingan operasional layanan dine-in Lumière, termasuk:</p>
                <ul>
                    <li>Mengirimkan detail pesanan Anda ke dapur chef kami agar hidangan bisa segera disiapkan.</li>
                    <li>Memastikan pesanan diantarkan ke meja makan yang benar.</li>
                    <li>Memproses verifikasi pembayaran instan via sistem QRIS atau mencatat tagihan kasir Anda.</li>
                    <li>Menampilkan riwayat pesanan pribadi Anda di halaman dashboard pelanggan.</li>
                </ul>
                
                <h3 class="text-warning font-display mt-4 mb-3" style="font-size: 18px;">3. Perlindungan & Keamanan Data</h3>
                <p>Kami berkomitmen untuk menjaga keamanan data pribadi Anda dengan langkah-langkah berikut:</p>
                <ul>
                    <li>Kata sandi akun Anda disimpan dalam bentuk hash terenkripsi di database menggunakan standar industri yang kuat.</li>
                    <li>Kami tidak pernah menjual, menyewakan, atau membagikan informasi pribadi Anda kepada pihak ketiga manapun untuk kepentingan iklan atau pemasaran komersial eksternal.</li>
                </ul>
                
                <h3 class="text-warning font-display mt-4 mb-3" style="font-size: 18px;">4. Kontak Kami</h3>
                <p>Jika Anda memiliki pertanyaan mengenai Kebijakan Privasi ini atau operasional sistem portal mandiri kami, silakan hubungi kasir atau staf floor manager kami di restoran.</p>
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
    'title' => 'Kebijakan Privasi',
    'hide_hero' => true,
], $content);
require __DIR__ . '/includes/footer.php';
