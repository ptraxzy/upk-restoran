# Actions

Folder ini dipakai untuk endpoint form sederhana.

Status sekarang:

- `auth/` sudah dipakai untuk login, register, dan logout.
- `menu/`, `karyawan/`, `pesanan/`, dan `pembayaran/` masih stub. File-nya sudah ada supaya route form tidak 404, tapi query insert/update belum dikerjakan.

Catatan backend:

- pakai prepared statement PDO dari `backend/config/database.php`
- validasi input sebelum simpan
- pakai `set_flash()` untuk feedback
- redirect balik ke halaman asal setelah proses selesai
