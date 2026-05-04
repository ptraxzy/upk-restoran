# Desain Arsitektur Project Restoran UPK

Tanggal: 2026-05-04
Status: Disetujui untuk direview sebelum implementasi

## Latar Belakang

Project Restoran UPK akan dibangun dengan PHP murni. Berdasarkan sitemap dan mockup yang tersedia, aplikasi memiliki tiga aktor utama:

- Admin
- Karyawan
- Pembeli

Ketiga aktor tersebut berbagi domain bisnis yang sama, tetapi memiliki pengalaman antarmuka yang berbeda. Area pembeli berfokus pada katalog dan transaksi dengan tampilan visual yang lebih kaya, sedangkan area admin dan karyawan berfokus pada operasi internal, data, dan efisiensi kerja.

Karena itu, struktur project perlu dipisahkan antara frontend dan backend, dengan frontend dibagi menurut pengalaman pengguna, dan backend dibagi menurut domain bisnis.

## Tujuan

- Memisahkan tanggung jawab tampilan dan logika aplikasi secara jelas.
- Menjaga aplikasi tetap full PHP tanpa berganti stack.
- Memudahkan pengembangan modul admin, karyawan, dan pembeli secara paralel.
- Menyediakan lingkungan database lokal dengan Docker dan phpMyAdmin.
- Menyiapkan struktur yang mudah dirawat ketika fitur bertambah.

## Pendekatan yang Dipilih

Pendekatan yang dipilih adalah:

- `frontend/` dipisah berdasarkan pengalaman pengguna
- `backend/` dipisah berdasarkan domain bisnis
- Docker digunakan untuk `mysql` dan `phpmyadmin`

Pendekatan ini dipilih karena paling sesuai dengan dua referensi utama:

- sitemap menunjukkan pemisahan fitur menurut aktor
- mockup menunjukkan gaya antarmuka pembeli berbeda dari area operasional admin dan karyawan

## Alternatif yang Dipertimbangkan

### 1. Frontend per pengalaman pengguna + backend per domain bisnis

Ini adalah pendekatan yang dipilih.

Kelebihan:

- Selaras dengan sitemap dan mockup
- Pemisahan tanggung jawab jelas
- Logic bisnis terpusat dan dapat dipakai lintas role
- Mudah memperluas UI publik dan dashboard tanpa mencampur struktur

Kekurangan:

- Membutuhkan disiplin pemanggilan controller dan service agar tidak kembali bercampur

### 2. Frontend tetap role-based penuh + backend hanya shared logic

Kelebihan:

- Mirip struktur awal yang sudah dibayangkan di README
- Lebih cepat dipahami pada tahap awal

Kekurangan:

- Domain bisnis berisiko tersebar di banyak folder role
- Controller dan proses form mudah terdorong kembali ke folder tampilan

### 3. Frontend dan backend keduanya dipisah per fitur

Kelebihan:

- Sangat modular
- Baik untuk tim besar atau aplikasi yang sudah matang

Kekurangan:

- Terlalu berat untuk tahap awal project ini
- Menambah kompleksitas navigasi struktur pada codebase PHP murni

## Struktur Folder yang Disetujui

```text
upk-restoran/
├── frontend/
│   ├── public/
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── menu/
│   │   ├── keranjang/
│   │   ├── pesanan/
│   │   └── profil/
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
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   ├── images/
│   │   └── fonts/
│   └── index.php
├── backend/
│   ├── config/
│   ├── controllers/
│   ├── services/
│   ├── repositories/
│   ├── middlewares/
│   ├── helpers/
│   └── views/
│       └── partials/
├── database/
│   ├── migrations/
│   └── seeds/
├── docker/
├── docs/
├── storage/
│   ├── logs/
│   └── uploads/
├── .env.example
├── docker-compose.yml
└── README.md
```

## Alasan Penamaan dan Boundary

### `frontend/public`

Berisi halaman pembeli. Nama `public` dipilih untuk membedakan area pengguna umum dari area operasional internal. Folder ini memuat halaman katalog, detail menu, keranjang, checkout, riwayat pesanan, dan profil akun.

### `frontend/admin`

Berisi halaman operasional untuk administrator. Fokusnya pada pengelolaan data, kontrol sistem, dan pelaporan.

### `frontend/karyawan`

Berisi halaman operasional untuk petugas atau staf restoran. Nama `karyawan` dipertahankan agar konsisten dengan sitemap dan istilah yang sudah dipakai di project.

### `backend`

Seluruh logika inti aplikasi ditempatkan di sini. Folder frontend hanya bertanggung jawab sebagai lapisan halaman dan pemanggilan controller.

## Domain Backend

Struktur internal backend akan mengikuti domain bisnis berikut:

- `Auth`
- `Menu`
- `Order`
- `Payment`
- `Employee`
- `Report`
- `Profile`
- `RestaurantSetting`

Setiap domain dapat memiliki kombinasi file pada:

- `controllers/`
- `services/`
- `repositories/`

Contoh:

- `backend/controllers/MenuController.php`
- `backend/services/MenuService.php`
- `backend/repositories/MenuRepository.php`

## Pola Interaksi Antar Layer

Alur request standar:

1. User mengakses file PHP pada `frontend/...`
2. File frontend memuat bootstrap, session, dan dependency dasar
3. Frontend memanggil controller yang sesuai dari `backend/controllers/`
4. Controller memvalidasi request dan meneruskan proses ke service
5. Service menjalankan aturan bisnis
6. Repository menjalankan query database
7. Hasil dikembalikan ke frontend untuk dirender

Boundary utama:

- `frontend` tidak menulis query database langsung
- `frontend` tidak menyimpan logika bisnis utama
- `service` tidak merender HTML
- `repository` tidak menangani session atau redirect

## Komponen Shared

Komponen bersama akan dipisahkan sebagai berikut:

- `backend/config/` untuk konfigurasi aplikasi, koneksi database, dan loader environment
- `backend/middlewares/` untuk pengecekan login, role, dan proteksi halaman
- `backend/helpers/` untuk helper kecil seperti redirect, flash message, formatter, atau request helper
- `backend/views/partials/` untuk potongan tampilan PHP yang dipakai berulang, misalnya head, navbar, sidebar, footer, atau komponen alert

## Pemetaan Sitemap ke Struktur

### Admin

- `frontend/admin/auth/`
- `frontend/admin/dashboard/`
- `frontend/admin/menu/`
- `frontend/admin/pesanan/`
- `frontend/admin/karyawan/`
- `frontend/admin/laporan/`
- `frontend/admin/pengaturan/`

### Karyawan

- `frontend/karyawan/auth/`
- `frontend/karyawan/dashboard/`
- `frontend/karyawan/menu/`
- `frontend/karyawan/pembayaran/`
- `frontend/karyawan/pesanan/`
- `frontend/karyawan/profil/`

### Pembeli

- `frontend/public/auth/`
- `frontend/public/dashboard/`
- `frontend/public/menu/`
- `frontend/public/keranjang/`
- `frontend/public/pesanan/`
- `frontend/public/profil/`

## Prinsip Routing Awal

Project tetap menggunakan PHP murni. Pada tahap awal, routing dapat dilakukan melalui file dan folder PHP biasa, misalnya:

- `frontend/admin/menu/index.php`
- `frontend/admin/menu/create.php`
- `frontend/public/menu/detail.php`

Setiap file halaman akan memanggil controller atau service yang sesuai. Tidak ada kewajiban memakai router framework pada tahap ini.

Jika nanti aplikasi tumbuh lebih besar, satu file bootstrap atau front controller tetap bisa ditambahkan tanpa mengubah boundary utama frontend dan backend.

## Session, Auth, dan Role

Aturan awal:

- Session dikelola terpusat
- Login admin, karyawan, dan pembeli tetap bisa memiliki form dan halaman berbeda
- Pemeriksaan role dilakukan lewat middleware atau helper backend
- Redirect setelah login disesuaikan dengan role:
  - admin ke area admin
  - karyawan ke area karyawan
  - pembeli ke area public

## Strategi Database

Database dijalankan melalui Docker dengan MySQL sebagai database utama.

Struktur database tidak didefinisikan penuh dalam spesifikasi ini, tetapi modul backend harus dirancang agar:

- query tidak tersebar di file tampilan
- akses tabel dipusatkan lewat repository
- migrasi atau SQL seed dapat disimpan dalam folder `database/`

## Desain Docker

`docker-compose.yml` akan menyediakan minimal dua service:

### `mysql`

Tanggung jawab:

- menyimpan database project restoran
- menyediakan port database lokal
- menyimpan data persisten melalui volume

### `phpmyadmin`

Tanggung jawab:

- memberi antarmuka browser untuk melihat tabel, data, dan query
- terhubung ke service `mysql`
- diekspos ke port lokal terpisah

Konfigurasi yang diharapkan:

- nama database default project
- user database non-root untuk aplikasi
- password dikontrol lewat environment variable
- port lokal MySQL menggunakan `3306`
- port lokal phpMyAdmin menggunakan `8080`

Pada tahap implementasi pertama, Docker hanya wajib menangani database dan phpMyAdmin. Runtime aplikasi PHP boleh tetap dijalankan langsung dari environment lokal pengguna.

## Konvensi Pengembangan

- Nama folder mengikuti bahasa domain yang sudah dipakai di sitemap
- Halaman tampilan tetap berada di `frontend`
- Proses data dan bisnis dipindahkan ke `backend`
- File baru mengikuti pola penamaan yang konsisten per domain
- Aset visual global diletakkan di `frontend/assets`
- Upload user dan file runtime tidak disimpan di folder frontend

## Risiko dan Mitigasi

### Risiko: frontend kembali berisi logic bisnis

Mitigasi:

- seluruh operasi database dan aturan bisnis dipaksa lewat backend
- controller dan service dibuat sejak awal, bukan belakangan

### Risiko: admin dan karyawan berbagi tampilan yang terlalu berbeda

Mitigasi:

- masing-masing area punya folder sendiri
- komponen bersama hanya diambil jika benar-benar reusable

### Risiko: pembeli dan admin berbagi asset atau partial yang tidak cocok

Mitigasi:

- partial reusable disimpan terpisah dan dibuat netral
- bila perlu, shell layout public dan dashboard dibuat terpisah

## Strategi Pengujian

Pada tahap implementasi awal, pengujian minimal meliputi:

- validasi struktur folder sesuai desain
- koneksi PHP ke database berjalan
- service `mysql` dan `phpmyadmin` dapat dijalankan
- halaman frontend dapat memanggil backend tanpa error include path
- auth dan role guard dasar berjalan sesuai aktor

## Hasil yang Diharapkan

Setelah implementasi tahap pertama:

- project memiliki pemisahan `frontend/` dan `backend/`
- struktur role pada frontend sesuai sitemap
- backend siap menjadi pusat logic aplikasi
- Docker dapat menjalankan MySQL dan phpMyAdmin
- codebase lebih siap dikembangkan untuk fitur admin, karyawan, dan pembeli

## Ruang Lingkup Implementasi Tahap Pertama

Tahap pertama implementasi dari desain ini mencakup:

- restrukturisasi folder project
- penyesuaian README agar mencerminkan arsitektur baru
- pembuatan skeleton backend utama
- pembuatan file konfigurasi Docker untuk `mysql` dan `phpmyadmin`

Tahap ini belum harus mencakup penyelesaian seluruh halaman atau seluruh fitur bisnis final.
