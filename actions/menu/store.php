<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_role('admin');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect(base_url('admin/menu_tambah.php'));
}

$nama_menu = $_POST['nama_menu'] ?? '';
$id_kategori = $_POST['id_kategori'] ?? '';
$deskripsi = $_POST['deskripsi'] ?? '';
$harga = $_POST['harga'] ?? 0;
$status = $_POST['status'] ?? 'Tersedia';
$porsi = $_POST['porsi'] ?? 10;
$gambar = $_POST['gambar'] ?? '';

if (isset($_FILES['gambar_file']) && $_FILES['gambar_file']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['gambar_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['gambar_file']['tmp_name'];
        $fileName = $_FILES['gambar_file']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        // Allowed extensions
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $fileMimeType = mime_content_type($fileTmpPath);
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (in_array($fileExtension, $allowedExtensions) && in_array($fileMimeType, $allowedMimeTypes)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = __DIR__ . '/../../assets/img/menu/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $gambar = 'assets/img/menu/' . $newFileName;
            } else {
                set_flash('error', 'Gagal mengunggah file gambar ke folder tujuan.');
                redirect(base_url('admin/menu_tambah.php'));
            }
        } else {
            set_flash('error', 'Format file tidak didukung. Gunakan JPG, JPEG, PNG, WEBP, atau GIF.');
            redirect(base_url('admin/menu_tambah.php'));
        }
    } else {
        $errorCode = $_FILES['gambar_file']['error'];
        $errorMessage = 'Gagal mengunggah gambar. Kode error: ' . $errorCode;
        if ($errorCode === UPLOAD_ERR_INI_SIZE || $errorCode === UPLOAD_ERR_FORM_SIZE) {
            $errorMessage = 'Ukuran file gambar terlalu besar. Maksimal 20MB.';
        }
        set_flash('error', $errorMessage);
        redirect(base_url('admin/menu_tambah.php'));
    }
}


if (empty($nama_menu) || empty($id_kategori) || empty($harga)) {
    set_flash('error', 'Semua field wajib diisi.');
    redirect(base_url('admin/menu_tambah.php'));
}

try {
    $stmt = db()->prepare("INSERT INTO menu (id_kategori, nama_menu, deskripsi, harga, gambar, status, porsi, id_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id_kategori, $nama_menu, $deskripsi, $harga, $gambar, $status, $porsi, $_SESSION['id_user'] ?? null]);

    set_flash('success', 'Menu baru berhasil ditambahkan.');
} catch (Exception $e) {
    set_flash('error', 'Gagal menyimpan menu: ' . $e->getMessage());
}

redirect(base_url('admin/menu.php'));
