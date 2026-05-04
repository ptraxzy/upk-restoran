# Struktur Folder Project UPK Restoran

Project ini menggunakan arsitektur folder berbasis role/aktor berdasarkan sitemap aplikasi (Admin, Karyawan, dan Pembeli). Pendekatan ini memudahkan pemisahan logika aplikasi dan kontrol hak akses.

## Direktori Utama

Berikut adalah penjelasan fungsi dari setiap direktori yang ada di dalam project:

```text
upk-restoran/
├── admin/             # Modul khusus untuk Administrator
│   ├── auth/          # Proses Login dan Logout Admin
│   ├── dashboard/     # Halaman utama (Overview) Admin
│   ├── karyawan/      # Manajemen Karyawan (Tambah, Edit, Hapus)
│   ├── laporan/       # Laporan Penjualan (Harian/Bulanan)
│   ├── menu/          # Manajemen Menu (Tambah, Edit, Hapus, Kategori)
│   ├── pengaturan/    # Pengaturan aplikasi (Profil Restoran)
│   └── pesanan/       # Manajemen Pesanan (Lihat Semua, Update Status)
│
├── assets/            # Tempat penyimpanan aset statis aplikasi
│   ├── css/           # File Cascading Style Sheets (.css)
│   ├── images/        # Gambar, logo, dan ikon
│   └── js/            # File JavaScript (.js)
│
├── config/            # File konfigurasi utama (koneksi database, variabel global, dll)
│
├── includes/          # File komponen antarmuka yang digunakan berulang (Header, Footer, Navbar, dll)
│
├── karyawan/          # Modul khusus untuk Karyawan
│   ├── auth/          # Proses Login dan Logout Karyawan
│   ├── menu/          # Halaman Lihat Menu
│   ├── pembayaran/    # Manajemen Pembayaran (Cek Status, Riwayat, Cetak Struk/Bukti Bayar)
│   ├── pesanan/       # Manajemen Pesanan Masuk (Daftar, Tambah, Update Status)
│   └── profil/        # Pengaturan Data Diri Karyawan
│
└── pembeli/           # Modul khusus untuk Pembeli/Pelanggan
    ├── auth/          # Proses Login dan Logout Pembeli
    ├── dashboard/     # Halaman utama Pembeli
    ├── keranjang/     # Manajemen Keranjang (Lihat, Edit, Hapus, Checkout)
    ├── menu/          # Lihat Menu, Promo/Rekomendasi, Detail Menu, Tambah ke Keranjang
    ├── pesanan/       # Lacak Pesanan (Riwayat, Status)
    └── profil/        # Pengaturan Akun (Data Diri, Alamat)
```

## Panduan Pengembangan

- **Pemisahan Hak Akses**: Setiap aktor (Admin, Karyawan, Pembeli) memiliki foldernya masing-masing. Pastikan pengecekan sesi (session check) diletakkan dengan benar di setiap folder aktor untuk keamanan.
- **Aset Global**: Seluruh file statis disatukan di folder `assets` agar mudah diakses dari modul mana pun dan tidak tercampur dengan logic PHP.
- **Konfigurasi Global**: Gunakan folder `config` untuk menyimpan hal-hal terkait konfigurasi utama seperti `database.php`.
- **Komponen UI Berulang**: Gunakan folder `includes` untuk memisahkan bagian kode yang akan di-_require_ berulang-ulang seperti file koneksi, _header_, _footer_, atau _sidebar_ navigasi.
