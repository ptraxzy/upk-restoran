# UPK Restoran

Aplikasi manajemen restoran berbasis PHP native dengan Docker.

---

## Prasyarat

Pastikan sudah terinstall di komputer:

- [Git](https://git-scm.com/downloads)
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (sudah termasuk Docker Compose)

## Cara Setup (Untuk yang Baru Clone)

### 1. Clone repository

```bash
git clone https://github.com/ptraxzy/upk-restoran.git
cd upk-restoran
```

### 2. Jalankan Docker

```bash
docker compose up -d --build
```

Tunggu sampai selesai (~1-2 menit pertama kali). Perintah ini otomatis:

- Build container PHP + Apache
- Jalankan MySQL 8.0
- Import database dari `database/sql/`
- Jalankan phpMyAdmin

### 3. Buka di browser

| Layanan    | URL                                        |
| ---------- | ------------------------------------------ |
| Aplikasi   | http://localhost:8001/frontend/login.php    |
| phpMyAdmin | http://localhost:8080                       |

### 4. Login dengan akun demo

**Admin:**

```
Username: admin
Password: admin123
```

**Karyawan:**

```
Username: kasir
Password: kasir123
```

**Pembeli:**

```
Username: testmember
Password: secret123
```

---

## Perintah Berguna

```bash
# Jalankan container
docker compose up -d

# Stop container
docker compose down

# Rebuild setelah ubah Dockerfile
docker compose up -d --build

# Lihat log
docker compose logs -f app

# Reset database (hapus volume lalu rebuild)
docker compose down -v
docker compose up -d --build
```

---

## Struktur Folder

```text
upk-restoran/
├── frontend/           # Halaman yang dilihat user
│   ├── login.php
│   ├── admin/          # Panel admin
│   ├── karyawan/       # Panel karyawan/kasir
│   ├── pembeli/        # Panel pembeli/member
│   └── assets/css/     # Stylesheet
├── backend/
│   ├── actions/        # Endpoint proses form (POST handler)
│   ├── auth/           # Middleware cek login & role
│   ├── config/         # Konfigurasi app, env, database
│   ├── functions/      # Helper functions
│   └── includes/       # Template header, footer, sidebar
├── database/sql/       # SQL init (auto-import saat docker up)
├── storage/            # Upload & log (git-ignored)
├── Dockerfile
└── docker-compose.yml
```

## Cara Berkontribusi

### Workflow Git

```bash
# 1. Buat branch baru untuk fitur yang dikerjakan
git checkout -b fitur/nama-fitur

# 2. Kerjakan perubahan, lalu commit
git add .
git commit -m "feat: deskripsi perubahan"

# 3. Push branch ke GitHub
git push origin fitur/nama-fitur

# 4. Buat Pull Request di GitHub untuk di-review
```

### Konvensi Commit

```
feat: fitur baru          → feat: tambah halaman pesanan
fix: perbaikan bug        → fix: validasi login gagal
style: perubahan tampilan → style: update warna sidebar
docs: dokumentasi         → docs: update README
```

## Status Pengerjaan

### ✅ Sudah selesai

- Login / Logout / Register pembeli
- Role guard (admin, karyawan, pembeli)
- Layout & navigasi semua role
- CRUD menu (form)
- Form karyawan

### 🔧 Perlu dilanjutkan

- [ ] CRUD menu → sambungkan ke database
- [ ] Data karyawan → sambungkan ke database
- [ ] Pesanan → buat flow pemesanan lengkap
- [ ] Pembayaran → proses pembayaran
- [ ] Laporan → tampilkan data dari database

---

## Tech Stack

- **Backend:** PHP 8 Native (tanpa framework)
- **Database:** MySQL 8.0
- **Frontend:** HTML + CSS (Tailwind CSS via PostCSS)
- **Server:** Apache (via Docker)
- **Tools:** Docker Compose, phpMyAdmin
