# Desain UI Homepage dan Auth Project Restoran UPK

Tanggal: 2026-05-04
Status: Disetujui untuk direview sebelum implementasi

## Tujuan

Merombak tampilan halaman utama dan halaman auth agar lebih layak dilihat, lebih sesuai dengan referensi Figma restoran, dan tetap memakai alur backend yang sudah ada.

## Arah Visual

Gaya yang dipilih:

- dark luxury
- aksen emas hangat
- nuansa restoran premium
- foto makanan sebagai pendukung visual
- judul besar dengan kesan elegan

UI baru harus terasa jauh lebih rapi dibanding tampilan polos saat ini.

## Cakupan Halaman

Halaman yang akan diperbarui:

- `frontend/index.php`
- `frontend/admin/auth/login.php`
- `frontend/karyawan/auth/login.php`
- `frontend/pembeli/auth/login.php`
- `frontend/pembeli/auth/register.php`
- `frontend/assets/css/app.css`

## Homepage

Homepage menjadi pintu masuk utama aplikasi.

Isi utamanya:

- identitas restoran
- teks pembuka singkat
- tombol atau link masuk untuk:
  - admin
  - karyawan
  - member
- tombol register member

Susunan halaman:

1. hero utama dengan headline dan deskripsi
2. blok pilihan area sistem
3. penekanan pada member login dan register

## Halaman Auth

### Admin Login

- tampil rapi dan formal
- fokus pada akses internal

### Karyawan Login

- tampil konsisten dengan admin
- tetap sederhana dan jelas

### Member Login

- lebih ramah untuk pengguna umum
- tetap satu gaya visual dengan homepage

### Member Register

- paling mengundang dipakai
- tetap sederhana: username dan password

## Komponen UI

Komponen yang perlu ada:

- heading yang jelas
- subtext singkat
- form field yang nyaman dibaca
- tombol utama yang menonjol
- link pindah halaman auth
- pesan error dan pesan sukses yang lebih rapi

## Batasan Teknis

- backend auth tidak diubah konsepnya
- role database tetap:
  - `admin`
  - `kasir`
  - `pelanggan`
- login dan register tetap memakai action yang sudah dibuat
- tidak menambah framework frontend

## Hasil yang Diharapkan

Setelah implementasi:

- homepage tidak lagi terlihat kosong
- auth terasa lebih proper dan presentable
- UI lebih dekat ke identitas restoran premium
- flow login dan register tetap berjalan
