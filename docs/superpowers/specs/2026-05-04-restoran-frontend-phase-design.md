# Desain Fase Frontend Berdasarkan Figma dan Sitemap

Tanggal: 2026-05-04
Status: Disetujui untuk direview sebelum implementasi

## Tujuan

Menyamakan seluruh UI frontend project Restoran UPK dengan arah visual Figma yang sudah diberikan, sambil memastikan struktur halaman mengikuti sitemap untuk tiga area utama:

- Admin
- Karyawan
- Pembeli

Fase ini fokus pada tampilan, layout, navigasi, dan konsistensi visual. Bila ada bagian detail yang belum lengkap di Figma, implementasi akan diisi dengan keputusan desain yang masih sejalan dengan bahasa visual yang sama.

## Arah Visual Global

Frontend harus mengikuti karakter visual berikut:

- dark luxury
- aksen emas hangat
- tipografi display elegan untuk heading
- layout editorial untuk pembeli
- dashboard gelap yang lebih operasional untuk admin dan karyawan
- penggunaan foto makanan sebagai elemen utama untuk area publik

UI tidak boleh kembali ke tampilan polos, teks kecil acak, atau layout generik.

## Prinsip Implementasi

- seluruh halaman frontend memakai Tailwind
- role database tetap:
  - `admin`
  - `kasir`
  - `pelanggan`
- struktur folder tetap:
  - `frontend/admin`
  - `frontend/karyawan`
  - `frontend/pembeli`
- backend auth dan alur PHP tetap dipakai
- halaman yang belum punya detail final tetap dibuat sebagai skeleton yang rapi dan konsisten

## Cakupan Halaman Sesuai Sitemap

### Admin

Halaman yang harus ada dalam fase frontend:

- auth login
- dashboard
- manajemen menu
  - daftar menu
  - tambah menu
  - ubah menu
- manajemen pesanan
  - daftar pesanan
  - detail atau ringkasan pesanan
- manajemen karyawan
  - daftar karyawan
  - tambah karyawan
- laporan
- pengaturan

### Karyawan

Halaman yang harus ada dalam fase frontend:

- auth login
- dashboard
- daftar pesanan masuk
- tambah pesanan
- update status pesanan
- menu
- pembayaran
- profil

### Pembeli

Halaman yang harus ada dalam fase frontend:

- auth login
- auth register
- dashboard atau landing member
- daftar menu
- detail menu
- keranjang
- checkout
- pesanan
- profil

## Karakter Per Area

### Pembeli

Pembeli mengikuti sisi paling visual dari Figma:

- hero besar
- foto makanan menonjol
- komposisi editorial
- kartu menu lebih artistik
- CTA member jelas

### Admin

Admin mengikuti dashboard gelap dengan nuansa premium:

- sidebar tetap
- header ringkas
- kartu metrik
- tabel dan form rapi
- halaman tambah dan ubah data mengikuti pola Figma

### Karyawan

Karyawan memakai bahasa visual admin, tetapi lebih operasional:

- lebih fokus ke daftar pesanan
- panel ringkasan
- form aksi lebih cepat dijangkau

## Navigasi

Navigasi harus dibuat konsisten:

- area admin punya sidebar sendiri
- area karyawan punya sidebar sendiri
- area pembeli punya navigasi publik sendiri
- active state dan hierarchy jelas

## Komponen Utama yang Perlu Dibangun

- shell layout admin
- shell layout karyawan
- shell layout pembeli
- card menu
- card metrik
- tabel gelap
- form gelap dengan aksen emas
- banner atau hero publik
- panel auth
- state error dan success yang rapi

## Aturan untuk Bagian yang Tidak Lengkap di Figma

Jika Figma tidak menunjukkan semua halaman secara detail:

- gunakan komponen yang sudah ada di halaman lain sebagai acuan
- pertahankan tone visual yang sama
- isi struktur halaman berdasarkan sitemap
- hindari improvisasi yang keluar dari karakter restoran premium

## Urutan Implementasi

Fase frontend ini dikerjakan dengan urutan:

1. fondasi layout global dan navigasi
2. homepage dan auth
3. area pembeli
4. area admin
5. area karyawan
6. penyamaan detail visual antar halaman

## Hasil yang Diharapkan

Setelah fase ini selesai:

- frontend terasa selaras dengan Figma
- seluruh role punya UI yang konsisten dan jelas
- sitemap sudah tercermin dalam struktur halaman frontend
- project siap dilanjutkan ke isi fitur dan koneksi data yang lebih lengkap
