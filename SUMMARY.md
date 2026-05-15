# Ringkasan Pembaruan Proyek UPK Restoran

Sesi: Perombakan UI/UX "Dark Luxury" & Integrasi Backend Dine-in (12 Mei 2026)

## 1. Perombakan Total UI/UX (Estetika "Dark Luxury")
Seluruh antarmuka (*frontend*) telah diselaraskan agar sama persis dengan desain Figma yang diberikan. Proyek tetap mempertahankan fondasi Bootstrap 5 yang dipadukan dengan kustomisasi CSS tingkat lanjut pada `assets/css/style.css`:
*   **Warna & Tipografi:** Penerapan tema gelap pekat (`#0a0a0a`), aksen *Gold* mewah, serta tipografi **Cormorant Garamond** (untuk judul) dan **DM Sans** (untuk teks standar). Penyesuaian kontras teks agar lebih terbaca (menaikkan kecerahan variabel warna teks).
*   **Halaman Autentikasi:** Mendesain ulang `login.php` dan `register.php` menggunakan *background* foto restoran premium dan *form card* bergaya *glassmorphism* tembus pandang, lengkap dengan branding **NOCTRA**.
*   **Peningkatan Skala UI:** Memperbesar ukuran dasar font (14px menjadi 15px), ukuran tombol, input form, dan jarak tabel agar lebih mudah dinavigasi (UX yang lebih baik).
*   **Halaman Khusus Role:**
    *   **Pelanggan:** Membangun Dashboard (dengan hero section dan menu kurasi), grid card Indeks Kuliner, Detail Menu bergaya split layout, dan halaman Checkout dua kolom dengan fitur Input Voucher Promo.
    *   **Kasir:** Merombak pemantauan Meja menjadi Grid Cards untuk "Layanan Aktif", dan memodifikasi halaman Cetak Struk bergaya minimalis klasik layaknya struk premium.
    *   **Admin:** Merombak Dashboard (layout grid emas), mengubah halaman Laporan Penjualan menjadi tata letak *Light Mode* yang berfokus pada grafik dan tabel riwayat, serta menyesuaikan form Tambah Karyawan & Menu agar terstruktur dengan rapi.

## 2. Implementasi Sistem Scan QR Meja (Dine-in Logic)
Mengubah alur logika aplikasi dari "Reservasi" menjadi sistem "Pemesanan Langsung di Meja" (*Dine-In*):
*   Aplikasi kini dapat membaca parameter URL (contoh: `login.php?meja=12`) hasil dari pelanggan yang memindai QR Code di atas meja.
*   Sistem menangkap nomor meja tersebut dan menyimpannya secara otomatis ke dalam sesi (`$_SESSION['scanned_meja']`) ketika pengguna mendaftar atau *login*.
*   Semua alur pemesanan, dari Keranjang hingga Checkout, secara dinamis mengambil nomor meja aktual yang terikat ke sesi pengguna tersebut, menghapus kebutuhan *input* meja manual.

## 3. Pembangunan Backend Database & Alur Pemesanan (*Order Flow*)
Aplikasi telah dihubungkan dengan database MySQL agar fungsional:
*   **Database Schema:** Menambahkan struktur tabel lengkap (`kategori`, `menu`, `pesanan`, `detail_pesanan`, dan `pembayaran`) melalui script inisialisasi `001-init.sql`, beserta penambahan data *seeder* menu.
*   **Keranjang Belanja (*Cart*):** Membangun script PHP murni (`cart_add.php` dan `cart_remove.php`) untuk mengelola penambahan/pengurangan jumlah item pesanan di dalam *Session*.
*   **Checkout & Pembayaran:** Mengembangkan `checkout.php` yang dapat menghitung subtotal, aplikasi diskon dari *voucher* promo (seperti `DISC15WINE`), dan kalkulasi pajak (11%). Skrip ini memindahkan data *cart* langsung ke tabel pesanan di database dengan status transaksi *"Menunggu"*.
*   **Simulasi QRIS:** Halaman konfirmasi checkout menampilkan *barcode* QRIS sesuai ID transaksi. Fitur *testing* simulasi pembayaran lunas disediakan untuk memicu proses perubahan status ke dapur.
*   **Antrean Karyawan:** Menghubungkan dashboard Kasir ("Layanan Aktif") ke database untuk menarik daftar pesanan secara *real-time* yang siap diproses, dilengkapi skrip `update_status.php` untuk memfasilitasi status alur layanan: Diproses -> Sedang Disiapkan -> Siap Saji -> Selesai.

## 4. Manajemen Repositori Git
## 5. Penyelesaian CRUD & Laporan Dinamis (13 Mei 2026)
Melengkapi fungsionalitas backend yang sebelumnya masih bersifat hardcoded:
*   **CRUD Menu Lengkap:** Menghubungkan halaman Indeks Kuliner ke database, mengimplementasikan form Tambah (store.php), Edit (update.php), dan fitur Hapus (delete.php) yang sinkron dengan tabel `menu`.
*   **Manajemen Karyawan:** Menghubungkan manajemen tim ke tabel `user`, memungkinkan Admin untuk menambah, mengubah kredensial, dan menghapus akses karyawan secara langsung.
*   **Laporan Penjualan Real-time:** Merombak `admin/laporan.php` dari data statis menjadi query agregasi database (Total Pendapatan, Total Transaksi, Menu Terlaris, dan Riwayat Transaksi Terbaru).
*   **Finalisasi Alur Pemesanan:** Memverifikasi fungsionalitas `checkout.php` dan `simulate_pay.php` untuk memastikan siklus hidup pesanan (dari keranjang hingga lunas) tercatat dengan benar di database.
*   **Sinkronisasi Dokumentasi:** Memperbarui README.md untuk menandai seluruh modul inti sebagai "Selesai".