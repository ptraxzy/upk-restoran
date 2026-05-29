<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/database.php';
require_role('pelanggan');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect(base_url('pelanggan/pesanan_riwayat.php'));
}

$id_pesanan = (int)($_POST['id_pesanan'] ?? 0);
$rating = (int)($_POST['rating'] ?? 5);
$komentar = trim($_POST['komentar'] ?? '');
$id_pelanggan = $_SESSION['id_user'] ?? 0;

if ($id_pesanan <= 0 || $rating < 1 || $rating > 5 || empty($komentar)) {
    set_flash('error', 'Semua bidang wajib diisi dengan benar.');
    redirect(base_url('pelanggan/pesanan_riwayat.php'));
}

try {
    $pdo = db();

    // Validasi otoritas pengguna atas transaksi yang telah diselesaikan
    $stmtCheck = $pdo->prepare("SELECT id_pesanan FROM pesanan WHERE id_pesanan = ? AND id_pelanggan = ? AND status_pesanan = 'Selesai'");
    $stmtCheck->execute([$id_pesanan, $id_pelanggan]);
    if (!$stmtCheck->fetch()) {
        set_flash('error', 'Pesanan tidak valid atau belum selesai.');
        redirect(base_url('pelanggan/pesanan_riwayat.php'));
    }

    // Persistensi data ulasan pelanggan
    $stmtInsert = $pdo->prepare("
        INSERT INTO ulasan (id_pesanan, id_pelanggan, rating, komentar)
        VALUES (?, ?, ?, ?)
    ");
    $stmtInsert->execute([$id_pesanan, $id_pelanggan, $rating, $komentar]);

    set_flash('success', 'Ulasan Anda berhasil dikirim! Terima kasih atas feedback Anda.');
} catch (Exception $e) {
    // Jika user mencoba submit ulasan ganda untuk pesanan yang sama
    if ($e->getCode() === '23000') {
        set_flash('error', 'Anda sudah memberikan ulasan untuk pesanan ini.');
    } else {
        set_flash('error', 'Gagal mengirimkan ulasan: ' . $e->getMessage());
    }
}

redirect(base_url('pelanggan/pesanan_riwayat.php'));
