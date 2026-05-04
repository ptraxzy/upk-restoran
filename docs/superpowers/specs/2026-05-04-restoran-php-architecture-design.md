# Desain Arsitektur Sederhana Project Restoran UPK

Tanggal: 2026-05-04
Status: Revisi untuk direview sebelum implementasi

## Tujuan

Struktur project ini dibuat khusus untuk kebutuhan UPK, jadi alur kode harus sederhana, mudah dipahami, dan mudah dikerjakan dengan PHP murni.

Tujuan utamanya:

- memisahkan tampilan dan proses
- tetap full PHP
- tidak memakai pola yang terlalu kompleks
- mudah dijalankan dengan MySQL dan phpMyAdmin

## Keputusan Utama

Project akan dipisah menjadi dua bagian:

- `frontend/` untuk halaman
- `backend/` untuk file proses, koneksi database, auth, dan helper

Pemisahan ini hanya untuk merapikan project. Ini bukan arsitektur besar dengan banyak layer.

## Struktur Folder yang Disetujui

```text
upk-restoran/
├── frontend/
│   ├── admin/
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── menu/
│   │   ├── pesanan/
│   │   ├── karyawan/
│   │   ├── laporan/
│   │   └── pengaturan/
│   ├── karyawan/
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── menu/
│   │   ├── pembayaran/
│   │   ├── pesanan/
│   │   └── profil/
│   ├── pembeli/
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── menu/
│   │   ├── keranjang/
│   │   ├── pesanan/
│   │   └── profil/
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── images/
│   └── index.php
├── backend/
│   ├── config/
│   ├── includes/
│   ├── auth/
│   ├── functions/
│   └── actions/
├── database/
│   └── sql/
├── storage/
│   ├── uploads/
│   └── logs/
├── docker-compose.yml
├── .env.example
└── README.md
```

## Penjelasan Folder

### `frontend/`

Semua halaman yang dilihat user ada di sini.

- `admin/` untuk admin
- `karyawan/` untuk karyawan
- `pembeli/` untuk pembeli
- `assets/` untuk CSS, JS, dan gambar

Frontend hanya fokus pada:

- tampilan
- form
- tabel
- layout
- pemanggilan file proses dari backend

### `backend/config/`

Berisi konfigurasi utama seperti:

- koneksi database
- konfigurasi aplikasi
- environment variable sederhana

### `backend/includes/`

Berisi file yang dipakai berulang, misalnya:

- header
- footer
- navbar
- sidebar
- session bootstrap

### `backend/auth/`

Berisi proses login, logout, dan pengecekan role.

Contoh:

- login admin
- login karyawan
- login pembeli
- logout
- cek session

### `backend/functions/`

Berisi fungsi bantu sederhana yang dipakai berulang, misalnya:

- redirect
- flash message
- format rupiah
- validasi input ringan

### `backend/actions/`

Berisi file proses form dan aksi data.

Contoh:

- simpan menu
- edit menu
- hapus menu
- tambah pesanan
- update status pesanan
- simpan data karyawan

Folder ini menjadi tempat utama proses bisnis sederhana project.

## Alur Kode yang Disetujui

Alurnya dibuat sesimpel ini:

1. user membuka halaman di `frontend/`
2. halaman menampilkan form atau data
3. saat submit, form diarahkan ke file di `backend/actions/`
4. file action memanggil koneksi database dan fungsi yang diperlukan
5. setelah proses selesai, user diarahkan kembali ke halaman yang sesuai

Contoh sederhana:

- `frontend/admin/menu/tambah.php`
- submit ke `backend/actions/menu/store.php`
- action memproses input dan menyimpan ke database
- selesai lalu redirect ke `frontend/admin/menu/index.php`

## Batasan Supaya Tetap Simpel

Supaya project ini tetap ringan:

- tidak perlu `controllers/`
- tidak perlu `services/`
- tidak perlu `repositories/`
- tidak perlu pola OOP yang berat kalau belum dibutuhkan

Aturan sederhananya:

- query database boleh ditulis di `backend/actions/`
- helper umum ditaruh di `backend/functions/`
- file frontend jangan mengurus proses simpan, edit, atau hapus langsung

## Pemetaan Berdasarkan Sitemap

### Admin

- login
- dashboard
- manajemen menu
- manajemen pesanan
- manajemen karyawan
- laporan
- pengaturan

### Karyawan

- login
- dashboard
- menu
- pembayaran
- pesanan
- profil

### Pembeli

- login
- dashboard
- menu
- keranjang
- pesanan
- profil

Struktur folder frontend harus mengikuti pembagian ini.

## Docker

Docker dipakai hanya untuk database dan phpMyAdmin.

Service minimal:

- `mysql`
- `phpmyadmin`

Port awal yang dipakai:

- MySQL: `3306`
- phpMyAdmin: `8080`

Tujuannya:

- database mudah dijalankan
- tabel mudah dicek lewat browser
- tidak perlu setup phpMyAdmin manual

Untuk tahap awal, aplikasi PHP tidak wajib dijalankan lewat Docker.

## Database

Folder `database/sql/` dipakai untuk menyimpan:

- file schema
- dump database
- seed data awal jika perlu

Karena project ini sederhana, SQL biasa sudah cukup.

## Pengujian Minimal

Setelah implementasi, yang perlu dipastikan:

- folder `frontend/` dan `backend/` sudah terpisah
- Docker MySQL berjalan
- Docker phpMyAdmin bisa dibuka
- halaman PHP masih bisa diakses
- form penting bisa mengarah ke file action yang benar
- login dan logout dasar berjalan

## Ruang Lingkup Tahap Pertama

Tahap pertama implementasi mencakup:

- merapikan struktur folder
- memperbarui `README.md`
- membuat folder backend sederhana
- membuat `docker-compose.yml` untuk MySQL dan phpMyAdmin

Tahap ini belum harus mengisi semua halaman aplikasi.
