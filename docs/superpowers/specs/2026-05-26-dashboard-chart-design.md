# Spesifikasi Desain: Integrasi Grafik Dashboard Admin (Chart.js)

Dokumen ini menjelaskan spesifikasi penambahan grafik visual dual-axis (pemasukan harian dan volume pesanan) pada dashboard administrasi restoran Lumière.

## 1. Latar Belakang & Tujuan
Dashboard admin Lumière saat ini menampilkan informasi metrik harian dalam bentuk angka tekstual yang statis. Untuk memberikan pemantauan tren bisnis yang lebih intuitif dan estetis bagi pemilik/admin, kita akan menambahkan grafik interaktif yang menampilkan data kinerja keuangan dan aktivitas transaksi selama 7 hari terakhir (mingguan).

## 2. Pilihan Pendekatan & Teknologi
* **Pustaka Grafik**: Chart.js (versi 4.x via CDN). Terpilih karena performanya yang ringan, responsif, dan mudah dikustomisasi agar selaras dengan estetika dark-gold premium milik Lumière.
* **Tipe Grafik**: Dual-Axis Mixed Chart (Kombinasi grafik Batang/Bar untuk Pemasukan dan grafik Garis/Line untuk Jumlah Pesanan).
* **Posisi Layout**: **Layout A (Full-Width Hero)**. Diletakkan di bagian paling atas halaman dashboard admin (di bawah judul "Ringkasan Aktivitas" dan sebelum deretan kartu metrik utama).

## 3. Desain Antarmuka (UI/UX)
* **Tema Warna**:
  - Latar Belakang Grafik: Gelap (`#121212` atau `#1e1e1f`) dengan garis grid semi-transparan (`rgba(255, 255, 255, 0.05)`).
  - Pemasukan (Bar): Warna emas khas Lumière (`#d4af37`).
  - Jumlah Pesanan (Line): Warna putih (`#ffffff`) atau kuning terang (`#ffc107`).
* **Interaktivitas**: Tooltip kustom saat pointer di-hover di atas grafik untuk menampilkan nilai rupiah pemasukan dan jumlah pesanan secara presisi.
* **Responsivitas**: Tinggi grafik dibatasi pada `280px` - `320px` dengan pengaturan responsif dari Chart.js agar muat di layar mobile maupun desktop tanpa merusak tata letak.

## 4. Struktur Data & Kueri Database
Data dikumpulkan untuk **7 hari terakhir** (termasuk hari ini) dari database:

### A. Kueri Pemasukan (Tabel `pembayaran`)
Mengambil total pembayaran sukses (`status = 'Lunas'`) yang dikelompokkan berdasarkan tanggal:
```sql
SELECT DATE(tanggal_pembayaran) AS tanggal, COALESCE(SUM(total_bayar), 0) AS total_pemasukan
FROM pembayaran
WHERE status = 'Lunas' 
  AND tanggal_pembayaran >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
GROUP BY DATE(tanggal_pembayaran)
ORDER BY tanggal ASC;
```

### B. Kueri Jumlah Pesanan (Tabel `pesanan`)
Mengambil total pesanan yang dibuat yang dikelompokkan berdasarkan tanggal:
```sql
SELECT DATE(tanggal_pesanan) AS tanggal, COUNT(*) AS total_pesanan
FROM pesanan
WHERE tanggal_pesanan >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
GROUP BY DATE(tanggal_pesanan)
ORDER BY tanggal ASC;
```

### C. Pengolahan Data di PHP
Untuk memastikan grafik menampilkan 7 hari terakhir secara lengkap tanpa ada tanggal yang melompat (jika ada hari tanpa transaksi), data PHP akan melakukan inisialisasi array untuk 7 hari terakhir dengan nilai default `0`, kemudian menimpanya dengan hasil kueri database sebelum dikonversi ke format JSON untuk Javascript.

## 5. Rencana Pengujian
* **Fungsional**:
  - Memastikan grafik ter-render dengan benar menggunakan data dinamis dari database.
  - Memastikan sumbu kiri menampilkan skala Rupiah dengan format yang rapi.
  - Memastikan sumbu kanan menampilkan jumlah pesanan bulat.
* **Visual / Responsif**:
  - Memastikan tampilan grafik tidak meluap (overflow) di layar mobile.
  - Memastikan kontras warna grafik di dark mode sangat baik dan nyaman dibaca.
* **Data Validasi**:
  - Mencocokkan nilai hari ini di grafik dengan nilai di kartu metrik "Pemasukan Hari Ini" dan "Pesanan Hari Ini".
