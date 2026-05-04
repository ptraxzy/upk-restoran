# Desain Fondasi Tailwind untuk Frontend Project Restoran UPK

Tanggal: 2026-05-04
Status: Disetujui untuk direview sebelum implementasi

## Tujuan

Menjadikan Tailwind CSS sebagai fondasi styling untuk seluruh bagian `frontend` pada project Restoran UPK.

## Pendekatan yang Dipilih

Pendekatan yang dipilih:

- memakai Tailwind build yang proper
- tidak memakai CDN
- tidak memakai Vite
- hasil build berupa file CSS statis yang dipakai langsung oleh halaman PHP

Pendekatan ini dipilih karena paling cocok untuk project PHP sederhana dan tetap nyaman dipakai di Linux maupun `Laragon`.

## Cakupan

Tailwind akan dipakai untuk seluruh frontend:

- `frontend/index.php`
- halaman auth admin
- halaman auth karyawan
- halaman auth member
- halaman frontend lain yang akan ditambah nanti

## Struktur yang Akan Ditambahkan

File yang akan ditambahkan:

- `package.json`
- `package-lock.json`
- `tailwind.config.js`
- `postcss.config.js`
- `frontend/assets/css/input.css`

File yang akan diperbarui:

- `frontend/assets/css/app.css`
- `backend/includes/header.php`
- `README.md`

## Alur Build

Alur styling:

1. class Tailwind ditulis langsung di file PHP frontend
2. source utama disimpan di `frontend/assets/css/input.css`
3. Tailwind build menghasilkan `frontend/assets/css/app.css`
4. halaman PHP memuat `app.css`

## Content Scan

Tailwind harus memindai file berikut:

- `frontend/**/*.php`
- `backend/includes/**/*.php`

Tujuannya agar class yang dipakai di halaman dan komponen bersama tetap ikut ke-build.

## Script NPM

Script yang perlu disediakan:

- `npm run dev` untuk watch mode
- `npm run build` untuk build production

## Hasil yang Diharapkan

Setelah implementasi:

- seluruh frontend siap memakai Tailwind
- styling tidak lagi bergantung pada CSS manual sederhana
- homepage dan auth bisa dibangun ulang dengan kualitas visual yang lebih baik
- teman yang melanjutkan project cukup menjalankan install dan build Tailwind
