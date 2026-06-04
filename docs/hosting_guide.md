# Panduan Hosting UPK Restoran (Free dengan Git Integration)

Karena project ini dibuat menggunakan **PHP Native** dan database **MySQL**, cara hosting gratis terbaik yang mendukung integrasi Git (auto-deploy saat `git push`) adalah dengan menggunakan salah satu dari dua metode di bawah ini.

---

## Pilihan 1: InfinityFree + GitHub Actions (Sangat Direkomendasikan)

**InfinityFree** menyediakan hosting PHP dan database MySQL 100% gratis tanpa batas waktu. Kita bisa menghubungkannya dengan **GitHub Actions** agar setiap kali Anda melakukan `git push` ke GitHub, kode di web hosting otomatis ter-update (layaknya Vercel/Netlify).

### Langkah 1: Siapkan Hosting di InfinityFree
1. Daftar akun gratis di [InfinityFree](https://infinityfree.com/).
2. Buat akun hosting baru (*Create Account*), pilih nama domain gratis yang disediakan (contoh: `lumiereresto.infinityfreeapp.com`).
3. Setelah akun aktif, buka panel kontrol (*Client Area*) dan catat informasi berikut dari menu **FTP Details**:
   * **FTP Hostname** (contoh: `ftpupload.net`)
   * **FTP Username** (contoh: `if0_3xxxxxx`)
   * **FTP Password** (klik *Show/Hide*)
4. Masuk ke **MySQL Databases** di control panel, buat database baru (misal: `db_restoran`), dan catat:
   * **MySQL Hostname**
   * **MySQL Username**
   * **MySQL Password**

### Langkah 2: Import Database
1. Buka **phpMyAdmin** dari panel control InfinityFree.
2. Pilih database yang baru dibuat, klik tab **Import**, lalu pilih file `database/sql/001-init.sql` dari laptop Anda untuk di-upload.

### Langkah 3: Konfigurasi Git/GitHub Repository
1. Upload folder project `upk-restoran` Anda ke repositori GitHub pribadi/publik (misal: `https://github.com/username/upk-restoran`).
2. Masuk ke halaman repositori GitHub Anda, buka tab **Settings** -> **Secrets and variables** -> **Actions**.
3. Klik **New repository secret** dan tambahkan 3 variabel berikut berdasarkan detail FTP InfinityFree tadi:
   * `FTP_SERVER` = (FTP Hostname Anda)
   * `FTP_USERNAME` = (FTP Username Anda)
   * `FTP_PASSWORD` = (FTP Password Anda)

### Langkah 4: Setup Auto-Deploy (GitHub Actions)
1. Buat folder baru bernama `.github/workflows/` di dalam project Anda.
2. Buat file bernama `deploy.yml` di dalam folder tersebut: `.github/workflows/deploy.yml`.
3. Tulis script workflow berikut ke dalamnya:

```yaml
name: Deploy Website via FTP

on:
  push:
    branches:
      - main  # Ubah ke master jika branch utama Anda bernama master

jobs:
  web-deploy:
    name: 🎉 Deploy
    runs-on: ubuntu-latest
    steps:
    - name: 🚚 Get latest code
      uses: actions/checkout@v3
    
    - name: 📂 Sync files to Web Server
      uses: SamKirkland/FTP-Deploy-Action@v4.3.4
      with:
        server: ${{ secrets.FTP_SERVER }}
        username: ${{ secrets.FTP_USERNAME }}
        password: ${{ secrets.FTP_PASSWORD }}
        server-dir: /htdocs/  # InfinityFree menaruh file web di folder htdocs
```

4. Buat file `.env` khusus untuk production di server hosting. Karena InfinityFree tidak mendukung pembacaan `.env` di luar folder publik dengan mudah, pastikan Anda menyesuaikan kredensial database di file `includes/database.php` menggunakan variabel environment hosting secara aman.

Sekarang, setiap kali Anda melakukan `git push origin main`, GitHub akan secara otomatis mengupload file yang berubah ke hosting InfinityFree Anda!

---

## Pilihan 2: Koyeb (Docker Hosting) + Aiven/TiDB (Free MySQL Database)

Karena project Anda sudah memiliki konfigurasi **Docker** (`Dockerfile` dan `docker-compose.yml`), Anda bisa memanfaatkan layanan Docker hosting gratis seperti **Koyeb**.

### Langkah 1: Buat Database MySQL Gratis
Layanan Docker gratis biasanya tidak menyediakan database MySQL yang persisten (datanya akan hilang saat server restart). Jadi kita menggunakan database cloud gratis terpisah:
1. Daftar di [Aiven.io](https://aiven.io/) atau [TiDB Cloud](https://tidbcloud.com/).
2. Buat cluster MySQL gratis (pilih region terdekat seperti Singapura agar cepat).
3. Catat kredensial koneksinya (Host, Port, Database Name, User, Password).
4. Akses database tersebut menggunakan tool seperti HeidiSQL/DBeaver di laptop Anda, lalu jalankan/import file `database/sql/001-init.sql`.

### Langkah 2: Hubungkan Repositori ke Koyeb
1. Daftar akun gratis di [Koyeb](https://www.koyeb.com/).
2. Klik **Create App**, lalu hubungkan dengan akun GitHub Anda.
3. Pilih repositori `upk-restoran`.
4. Pilih metode deployment **Dockerfile** (Koyeb akan membaca file Dockerfile Anda secara otomatis).
5. Pada bagian **Environment Variables**, tambahkan variabel database berikut agar terhubung ke database eksternal Aiven/TiDB Anda:
   * `DB_HOST` = (Host database eksternal)
   * `DB_PORT` = `3306`
   * `DB_NAME` = `db_restoran`
   * `DB_USER` = (Username database)
   * `DB_PASS` = (Password database)
   * `APP_URL` = `https://<nama-app-anda>.koyeb.app`
6. Klik **Deploy**.

Setiap kali Anda melakukan push ke GitHub, Koyeb akan otomatis men-trigger *build* ulang Docker image dan melakukan *zero-downtime deployment*.
