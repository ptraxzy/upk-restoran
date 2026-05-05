# UPK Restoran

Ini project restoran pakai PHP.

Bayangkan project ini seperti restoran:

- `frontend/` = ruang depan yang dilihat orang.
- `backend/` = dapur yang memproses data.
- `database/` = buku penyimpanan data.
- `storage/` = gudang file upload dan log.

## Cara Buka

Jalankan:

```bash
docker compose up -d --build
```

Buka aplikasi:

```text
http://localhost:8001/frontend/login.php
```

Buka phpMyAdmin:

```text
http://localhost:8080
```

## Akun Dummy

Admin:

```text
username: admin
password: admin123
```

Karyawan:

```text
username: kasir
password: kasir123

username: kasir.senja
password: kasir456

username: kasir.raka
password: kasir789
```

Pembeli:

```text
username: testmember
password: secret123
```

## Struktur Paling Gampang

```text
upk-restoran/
├── frontend/
│   ├── login.php
│   ├── admin/
│   ├── karyawan/
│   ├── pembeli/
│   └── assets/css/
├── backend/
│   ├── actions/
│   ├── auth/
│   ├── config/
│   ├── functions/
│   └── includes/
├── database/
│   └── sql/
├── storage/
├── Dockerfile
├── docker-compose.yml
└── README.md
```

## Arti Folder

`frontend/login.php`

Tempat semua user login. Admin, karyawan, dan pembeli login di halaman yang sama.

`frontend/admin/`

Halaman untuk admin.

`frontend/karyawan/`

Halaman untuk karyawan atau kasir.

`frontend/pembeli/`

Halaman untuk pembeli/member.

`frontend/assets/css/`

Tempat style tampilan website.

`backend/actions/`

Tempat proses form, misalnya login, register, simpan menu, dan simpan karyawan.

`backend/auth/`

Tempat cek apakah user sudah login dan rolenya benar.

`backend/config/`

Tempat setting aplikasi dan database.

`backend/includes/`

Potongan file yang dipakai bareng-bareng, seperti header, footer, dan layout.

`database/sql/`

File SQL untuk membuat tabel dan data awal.

`storage/`

Tempat file runtime seperti upload dan log.

## Alur Super Simpel

```text
User buka halaman
        ↓
User isi form
        ↓
Form dikirim ke backend/actions
        ↓
Backend proses data
        ↓
User balik ke halaman tujuan
```

Contoh:

```text
frontend/admin/menu/create.php
        ↓
backend/actions/menu/store.php
        ↓
frontend/admin/menu/index.php
```

## Catatan Penting

Jangan hapus folder ini:

- `frontend/`
- `backend/`
- `database/`
- `storage/`

Folder itu yang bikin project jalan.
