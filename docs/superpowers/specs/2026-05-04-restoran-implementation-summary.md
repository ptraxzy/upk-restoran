# Summary Implementasi Restoran UPK

Tanggal: 2026-05-04

## Ringkasan Umum

Pada sesi ini project Restoran UPK diperbarui pada tiga area utama:

- penyatuan flow login semua role
- penambahan akun dummy untuk seluruh role
- perombakan fondasi UI agar lebih dekat ke arah Figma dan lebih layak untuk review QA

Selain itu project juga dibuat bisa dijalankan penuh lewat Docker, termasuk service PHP, MySQL, dan phpMyAdmin.

## Flow Login

Sebelumnya login admin, karyawan, dan pembeli berada di halaman terpisah.

Sekarang login disatukan ke satu halaman:

`/frontend/auth/login.php`

Role dibedakan lewat tab pilihan:

- Admin
- Karyawan
- Pembeli

Halaman login lama tetap ada, tetapi sekarang hanya melakukan redirect:

- `frontend/admin/auth/login.php`
- `frontend/karyawan/auth/login.php`
- `frontend/pembeli/auth/login.php`

Homepage lama juga tidak lagi menjadi pintu utama experience. `frontend/index.php` sekarang langsung mengarahkan user ke login terpadu.

## URL Runtime

Project aktif di:

- App: `http://localhost:8001/frontend/`
- Login utama: `http://localhost:8001/frontend/auth/login.php`
- phpMyAdmin: `http://localhost:8080`

## Akun Dummy

### Admin

- `admin` / `admin123`
- `admin.ops` / `admin456`
- `admin.floor` / `admin789`

### Karyawan

- `kasir` / `kasir123`
- `kasir.senja` / `kasir456`
- `kasir.raka` / `kasir789`

### Pembeli

- `testmember` / `secret123`
- `member.ayla` / `member456`
- `member.nara` / `member789`

## Perubahan Backend dan Runtime

Perubahan backend dan infrastruktur yang sudah dilakukan:

- menambahkan service `app` pada `docker-compose.yml`
- menambahkan `Dockerfile` untuk PHP + Apache + `pdo_mysql`
- memperbaiki pembacaan environment agar bisa membaca variable runtime container
- memperbaiki redirect auth agar tidak bergantung pada path relatif lama
- menambahkan action dummy yang sebelumnya kosong:
  - `backend/actions/menu/store.php`
  - `backend/actions/karyawan/store.php`
- memperbarui seed SQL agar akun dummy tersedia juga pada bootstrap database baru

## Perubahan UI

### Fondasi Visual

Fondasi UI dirombak agar tidak terasa seperti template generik:

- font display diganti ke `Cormorant Garamond`
- font body diganti ke `Inter`
- hierarki heading, spacing, panel, dan form diperketat
- auth screen dibikin lebih rapi dan lebih premium
- shell publik dan dashboard internal dibuat lebih konsisten
- beberapa layout dibikin lebih responsif dibanding sebelumnya

### Halaman yang Dirombak

Perubahan sudah masuk ke halaman-halaman penting berikut:

- login terpadu
- dashboard pembeli
- menu pembeli
- detail menu pembeli
- keranjang
- voucher / checkout
- profil pembeli
- pesanan pembeli
- dashboard admin
- manajemen menu admin
- tambah menu
- edit menu
- tambah karyawan
- laporan
- pengaturan
- dashboard karyawan
- incoming orders
- tambah pesanan
- pembayaran / cetak struk
- profil karyawan

### Shared Layout yang Diubah

Komponen shared yang banyak mempengaruhi tampilan:

- `backend/includes/ui.php`
- `backend/includes/header.php`
- `frontend/assets/css/input.css`

## Build dan Verifikasi

Langkah yang sudah dijalankan:

- build Tailwind via `npm run build`
- build dan run Docker via `docker compose up -d --build`
- verifikasi app merespons `200 OK`
- verifikasi login admin berhasil
- verifikasi login pembeli berhasil
- verifikasi register pembeli berhasil
- verifikasi akun dummy masuk ke database

## Catatan QA

Walau fondasi dan banyak halaman sudah dibersihkan, masih ada area yang perlu dipoles lebih ketat bila targetnya lolos QA visual yang sangat detail:

- penyamaan pixel-level dengan seluruh frame Figma
- penghalusan responsif mobile pada semua halaman pembeli
- konsistensi ritme spacing antar dashboard admin dan karyawan
- penggantian beberapa copy placeholder agar terasa lebih final

## File Penting yang Ditambah atau Diubah

Beberapa file utama yang terlibat:

- `frontend/auth/login.php`
- `frontend/index.php`
- `frontend/assets/css/input.css`
- `backend/functions/helpers.php`
- `backend/config/env.php`
- `backend/includes/header.php`
- `backend/includes/ui.php`
- `backend/actions/auth/register.php`
- `backend/actions/auth/logout.php`
- `backend/actions/menu/store.php`
- `backend/actions/karyawan/store.php`
- `database/sql/001-init.sql`
- `docker-compose.yml`
- `Dockerfile`
- `README.md`

## Perintah Penting

Menjalankan ulang app:

```bash
docker compose up -d --build
```

Menghentikan container:

```bash
docker compose down
```

Build CSS:

```bash
npm run build
```

## Status Akhir

Project saat ini:

- sudah bisa dijalankan penuh
- sudah punya login terpadu lintas role
- sudah punya dummy account untuk semua role
- sudah jauh lebih rapi dibanding versi awal
- masih layak dilanjutkan untuk polishing final agar makin ketat terhadap Figma
