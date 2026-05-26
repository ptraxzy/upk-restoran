# Rencana Implementasi: Integrasi Grafik Dashboard Admin (Chart.js)

Dokumen ini mendetailkan langkah-langkah implementasi teknis untuk menambahkan grafik dual-axis pemasukan harian dan volume pesanan pada dashboard admin Lumière.

## Rencana Fase Pekerjaan

### Fase 1: Penyiapan Data Dinamis di PHP (`admin/dashboard.php`)
1. Buat deretan tanggal untuk 7 hari terakhir (hari ini hingga 6 hari yang lalu) di PHP.
2. Lakukan kueri data pemasukan sukses harian (`total_bayar`) dari tabel `pembayaran` selama 7 hari terakhir.
3. Lakukan kueri data jumlah pesanan harian (`id_pesanan`) dari tabel `pesanan` selama 7 hari terakhir.
4. Lakukan pemetaan (mapping) data kueri ke dalam array tanggal 7 hari terakhir agar tidak ada tanggal yang kosong (melompat).
5. Konversi data array label tanggal, pemasukan (rupiah), dan pesanan (jumlah) ke format JSON:
   - `$chartLabelsJSON`
   - `$chartRevenueJSON`
   - `$chartOrdersJSON`

### Fase 2: Pembuatan Komponen Wrapper UI
1. Tambahkan kontainer `<section>` baru untuk grafik di atas kontainer metrik (`<section class="row row-cols-1 ...">`).
2. Desain kontainer grafik menggunakan kelas `.card` bootstrap dengan penyesuaian gaya Lumière:
   - Latar belakang: Gelap/Hitam (`background: #111`).
   - Garis tepi: Tipis lembut (`border: 1px solid var(--border)`).
   - Judul grafik: "Tren Transaksi Mingguan".
   - Subjudul grafik: "Analisis Pemasukan & Volume Pesanan 7 Hari Terakhir".

### Fase 3: Integrasi Chart.js & Konfigurasi Canvas
1. Muat pustaka Chart.js menggunakan tag `<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>` tepat sebelum elemen inisialisasi grafik.
2. Definisikan elemen `<canvas id="weeklyChart" style="height: 300px;"></canvas>`.
3. Inisialisasi Chart.js secara asinkron dengan opsi:
   - Jenis: Mixed (`bar` untuk Pemasukan, `line` untuk Jumlah Pesanan).
   - Data Pemasukan: Menggunakan sumbu Y kiri (Emas, `#d4af37`, dengan format mata uang rupiah di tooltip dan label).
   - Data Pesanan: Menggunakan sumbu Y kanan (Putih, `#ffffff`, dengan format angka desimal bulat).
   - Skala X & Y: Kustomisasi warna teks ke abu-abu terang (`#86868b`) dan warna grid semi-transparan (`rgba(255, 255, 255, 0.05)`).
   - Responsivitas aktif.

### Fase 4: Pengujian & Validasi
1. Akses halaman `http://localhost:8001/admin/dashboard.php` untuk memverifikasi halaman ter-render tanpa error PHP maupun JavaScript.
2. Periksa konsol browser (F12) untuk memastikan Chart.js dimuat dengan sukses tanpa error library.
3. Bandingkan angka hari ini di grafik dengan metrik "Pemasukan Hari Ini" dan "Pesanan Hari Ini" di bawahnya untuk memastikan konsistensi data.
