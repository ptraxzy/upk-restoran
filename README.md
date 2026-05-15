# UPK Restoran

Aplikasi manajemen restoran berbasis PHP native dengan Docker.

---

## Prasyarat

Pastikan sudah terinstall di komputer:

- [Git](https://git-scm.com/downloads)
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (sudah termasuk Docker Compose)

### Cara Setup (Docker - Rekomendasi)

1. **Clone repository**
   ```bash
   git clone https://github.com/ptraxzy/upk-restoran.git
   cd upk-restoran
   ```

2. **Jalankan Docker**
   ```bash
   docker compose up -d --build
   ```

3. **Buka di browser**
   - Aplikasi: http://localhost:8001/login.php
   - phpMyAdmin: http://localhost:8080

---

### Cara Setup (Laragon / XAMPP)

1. **Clone repository** ke folder `www` (Laragon) atau `htdocs` (XAMPP).
   ```bash
   cd C:\laragon\www
   git clone https://github.com/ptraxzy/upk-restoran.git
   ```

2. **Siapkan Database**
   - Buka Database Manager (HeidiSQL/phpMyAdmin).
   - Buat database baru bernama `db_restoran`.
   - Import file `database/sql/001-init.sql` ke database tersebut.

3. **Konfigurasi Environment**
   - Copy file `.env.example` menjadi `.env`.
   - Sesuaikan `DB_HOST`, `DB_USER`, `DB_PASS` sesuai settingan Laragon-mu (biasanya user `root` dan password kosong).

4. **Buka di browser**
   - URL: http://localhost/upk-restoran/login.php
   - (Atau http://upk-restoran.test/login.php jika pakai auto-virtualhost Laragon).

---

### Akun Demo (Semua Method)

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
├── login.php           # Halaman login utama
├── register.php        # Halaman daftar member
├── admin/              # Panel admin
├── kasir/              # Panel kasir
├── pelanggan/          # Panel pelanggan/member
├── actions/            # Endpoint proses form (POST handler)
├── config/             # Konfigurasi app, env, database
├── includes/           # Helper dan template UI
├── assets/css/         # Stylesheet
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
