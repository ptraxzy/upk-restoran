# UPK Restoran

Aplikasi manajemen restoran berbasis PHP Native untuk kebutuhan admin, kasir, dan pelanggan. Project ini memakai MySQL sebagai database, Bootstrap 5 untuk layout dasar, dan CSS custom untuk tampilan dark luxury restoran.

## Tech Stack

- **Backend:** PHP 8 Native
- **Database:** MySQL
- **Koneksi database:** PDO
- **Frontend:** HTML, CSS custom, Bootstrap 5
- **Server lokal:** Laragon/XAMPP atau Docker Apache
- **Tools:** Docker Compose, phpMyAdmin/HeidiSQL

## Fitur Utama

- Login, logout, register pelanggan
- Role akses untuk admin, kasir, dan pelanggan
- Dashboard untuk setiap role
- CRUD menu restoran
- CRUD data karyawan
- Keranjang dan checkout pelanggan
- Status pesanan
- Simulasi pembayaran QRIS
- Laporan penjualan
- Lupa password dan reset password via email

## Akun Demo

**Admin**
```text
Username: admin
Password: admin123
```

**Kasir**
```text
Username: kasir
Password: kasir123
```

**Pelanggan**
```text
Username: testmember
Password: secret123
```

## Setup Dengan Docker

1. Clone repository.

```bash
git clone https://github.com/ptraxzy/upk-restoran.git
cd upk-restoran
```

2. Jalankan container.

```bash
docker compose up -d --build
```

3. Buka aplikasi.

```text
http://localhost:8001/login.php
```

4. Buka phpMyAdmin.

```text
http://localhost:8080
```

Jika database Docker ingin di-reset total:

```bash
docker compose down -v
docker compose up -d --build
```

Catatan: `docker compose down -v` akan menghapus database lokal di Docker.

## Setup Dengan Laragon / XAMPP

1. Clone repository ke folder web server.

Laragon:

```bash
cd C:\laragon\www
git clone https://github.com/ptraxzy/upk-restoran.git
```

XAMPP:

```bash
cd C:\xampp\htdocs
git clone https://github.com/ptraxzy/upk-restoran.git
```

2. Buat database baru bernama:

```text
db_restoran
```

3. Import file SQL:

```text
database/sql/001-init.sql
```

4. Copy `.env.example` menjadi `.env`, lalu sesuaikan konfigurasi database.

Contoh konfigurasi Laragon:

```env
APP_NAME=UPK Restoran
APP_ENV=local
APP_URL=http://localhost/upk-restoran

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=db_restoran
DB_USER=root
DB_PASS=
```

5. Buka aplikasi:

```text
http://localhost/upk-restoran/login.php
```

Jika pakai auto virtual host Laragon:

```text
http://upk-restoran.test/login.php
```

## Struktur Folder

```text
upk-restoran/
├── actions/            # Proses form dan aksi database
├── admin/              # Halaman khusus admin
├── assets/             # File frontend statis seperti CSS
├── config/             # Konfigurasi aplikasi
├── database/           # File SQL database
├── includes/           # File PHP yang dipakai ulang
├── kasir/              # Halaman khusus kasir
├── pelanggan/          # Halaman khusus pelanggan
├── storage/            # Tempat upload atau file aplikasi
├── forgot_password.php # Endpoint lupa password
├── index.php           # Redirect awal
├── login.php           # Halaman login
├── logout.php          # Proses logout
├── register.php        # Halaman daftar pelanggan
└── reset_password.php  # Halaman reset password
```

## Penjelasan Folder

### `assets/`

Berisi file tampilan/frontend statis.

Contoh:

```text
assets/css/style.css
```

File ini mengatur warna, font, button, card, form, navbar, sidebar, dan tampilan umum aplikasi.

### `includes/`

Berisi file PHP yang digunakan ulang di banyak halaman.

Contoh:

```text
includes/bootstrap.php
includes/database.php
includes/auth.php
includes/header.php
includes/footer.php
includes/ui.php
includes/helpers.php
```

Fungsinya:

- `bootstrap.php`: memulai session dan memuat helper utama.
- `database.php`: koneksi database MySQL menggunakan PDO.
- `auth.php`: cek login dan role user.
- `header.php`: bagian `<head>`, load Bootstrap dan CSS.
- `footer.php`: load JavaScript Bootstrap.
- `ui.php`: layout utama seperti sidebar, navbar, dan wrapper halaman.
- `helpers.php`: fungsi bantuan seperti `base_url()`, `redirect()`, dan `rupiah()`.

### `config/`

Berisi konfigurasi aplikasi.

Contoh:

```text
config/app.php
config/env.php
config/payment.php
```

Fungsinya:

- `app.php`: nama aplikasi dan URL utama.
- `env.php`: membaca isi file `.env`.
- `payment.php`: konfigurasi pembayaran/QRIS.

### `actions/`

Berisi file proses form dan aksi database. Folder ini tidak fokus pada tampilan.

Contoh:

```text
actions/menu/store.php
actions/menu/update.php
actions/menu/delete.php
actions/karyawan/store.php
actions/pesanan/checkout.php
```

Fungsinya untuk menyimpan, mengubah, menghapus, checkout pesanan, dan update status.

### `admin/`

Berisi halaman untuk admin.

Contoh:

```text
admin/dashboard.php
admin/menu.php
admin/karyawan.php
admin/laporan.php
```

Admin dapat mengelola menu, karyawan, laporan, dan pengaturan.

### `kasir/`

Berisi halaman untuk kasir.

Contoh:

```text
kasir/dashboard.php
kasir/pesanan.php
kasir/pembayaran.php
```

Kasir dapat melihat pesanan, mengubah status pesanan, dan memproses pembayaran.

### `pelanggan/`

Berisi halaman untuk pelanggan/member.

Contoh:

```text
pelanggan/dashboard.php
pelanggan/menu.php
pelanggan/keranjang.php
pelanggan/pesanan.php
```

Pelanggan dapat melihat menu, memasukkan menu ke keranjang, checkout, dan melihat status pesanan.

### `database/`

Berisi file SQL untuk membuat tabel dan data awal.

Contoh:

```text
database/sql/001-init.sql
```

File ini dipakai saat import manual di Laragon/XAMPP atau otomatis saat Docker pertama kali membuat database.

### `storage/`

Berisi file hasil upload atau file yang disimpan aplikasi.

Contoh:

```text
storage/uploads/
```

## Alur Singkat Aplikasi

1. User membuka `login.php`.
2. User login sebagai admin, kasir, atau pelanggan.
3. Sistem mengarahkan user ke dashboard sesuai role.
4. Admin mengelola menu, karyawan, dan laporan.
5. Pelanggan memilih menu, masuk keranjang, lalu checkout.
6. Kasir memantau pesanan dan pembayaran.

## URL Penting

Docker:

```text
http://localhost:8001/login.php
```

Laragon/XAMPP:

```text
http://localhost/upk-restoran/login.php
```

phpMyAdmin Docker:

```text
http://localhost:8080
```

## Perintah Git Yang Sering Dipakai

Update project:

```bash
git pull origin main
```

Commit perubahan:

```bash
git add .
git commit -m "feat: deskripsi perubahan"
git push origin main
```

## Troubleshooting

### Access denied for user `upk_user`

Jika pakai Laragon/XAMPP, ubah `.env` menjadi:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=db_restoran
DB_USER=root
DB_PASS=
```

Jika pakai Docker, buka URL Docker:

```text
http://localhost:8001/login.php
```

### Halaman mengarah ke `/frontend/login.php`

Pastikan `.env` memakai URL yang benar.

Docker:

```env
APP_URL=http://localhost:8001
```

Laragon/XAMPP:

```env
APP_URL=http://localhost/upk-restoran
```

### Database error setelah update

Import ulang:

```text
database/sql/001-init.sql
```

Untuk Docker, bisa reset database dengan:

```bash
docker compose down -v
docker compose up -d --build
```
