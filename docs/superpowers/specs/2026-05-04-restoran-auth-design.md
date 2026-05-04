# Desain Fitur Login dan Register Project Restoran UPK

Tanggal: 2026-05-04
Status: Disetujui untuk direview sebelum implementasi

## Tujuan

Menambahkan halaman login dan register yang terhubung ke tabel `user` pada database `db_restoran`.

Fitur ini dibuat untuk kebutuhan project UPK dengan alur sederhana dan mengikuti role bawaan database yang sudah ada.

## Role yang Dipakai

Role pada tabel `user.level` tetap menggunakan nilai bawaan database:

- `admin`
- `kasir`
- `pelanggan`

Mapping ke struktur project:

- `admin` masuk ke `frontend/admin/`
- `kasir` masuk ke `frontend/karyawan/`
- `pelanggan` masuk ke `frontend/pembeli/`

## Pendekatan yang Dipilih

Pendekatan yang dipilih:

- halaman login dipisah per area
- halaman register hanya untuk pelanggan
- proses backend tetap sederhana lewat `backend/actions/auth/`

## Struktur File

File yang akan ditambahkan atau diperbarui:

- `frontend/admin/auth/login.php`
- `frontend/karyawan/auth/login.php`
- `frontend/pembeli/auth/login.php`
- `frontend/pembeli/auth/register.php`
- `backend/actions/auth/login.php`
- `backend/actions/auth/register.php`
- `backend/actions/auth/logout.php`
- `backend/auth/check.php`
- `backend/functions/helpers.php`
- `frontend/index.php`

## Alur Login

1. user membuka halaman login sesuai area
2. user mengisi `username` dan `password`
3. form dikirim ke `backend/actions/auth/login.php`
4. backend mencari data user berdasarkan `username`
5. backend memeriksa password
6. backend memastikan role user cocok dengan area login
7. jika valid, session disimpan lalu user diarahkan ke dashboard yang sesuai

Redirect:

- `admin` ke `frontend/admin/dashboard/index.php`
- `kasir` ke `frontend/karyawan/dashboard/index.php`
- `pelanggan` ke `frontend/pembeli/dashboard/index.php`

## Alur Register

Register hanya untuk `pelanggan`.

1. user membuka `frontend/pembeli/auth/register.php`
2. user mengisi `username` dan `password`
3. form dikirim ke `backend/actions/auth/register.php`
4. backend memeriksa apakah username sudah dipakai
5. jika belum, backend membuat user baru dengan `level='pelanggan'`
6. password disimpan dalam bentuk hash
7. setelah berhasil, user diarahkan ke halaman login pembeli

## Aturan Password

Untuk user baru:

- password disimpan dengan `password_hash()`

Untuk proses login:

- login utama memakai `password_verify()`
- bila perlu, bisa ditambahkan fallback sederhana untuk data lama yang masih plaintext

Tujuannya agar fitur baru tetap aman, tetapi masih bisa menyesuaikan jika database lama belum seragam.

## Session

Session minimal yang disimpan:

- `user_id`
- `user_name`
- `user_role`

Session dipakai untuk:

- cek apakah user sudah login
- cek role user pada halaman tertentu
- menentukan redirect dashboard

## Validasi

Validasi minimum:

- `username` wajib diisi
- `password` wajib diisi
- username register tidak boleh duplikat
- login gagal jika role tidak cocok dengan area yang dipilih

## Pesan Error

Pesan yang perlu ditampilkan:

- username atau password salah
- akun tidak sesuai dengan area login
- username sudah dipakai
- register berhasil, silakan login

## Ruang Lingkup Implementasi

Implementasi tahap ini mencakup:

- membuat halaman login admin
- membuat halaman login karyawan
- membuat halaman login pembeli
- membuat halaman register pembeli
- menghubungkan login ke tabel `user`
- menghubungkan register ke tabel `user`
- memperbarui session check sesuai mapping role

Tahap ini belum mencakup:

- lupa password
- edit profil akun
- manajemen user oleh admin
