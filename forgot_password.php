<?php

declare(strict_types=1);

require_once __DIR__ . '/./includes/bootstrap.php';
require_once __DIR__ . '/./includes/database.php';
require_once __DIR__ . '/./includes/mail.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
    exit;
}

$email = trim($_POST['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Format email sepertinya salah.']);
    exit;
}

$pdo = db();

// Verifikasi eksistensi pelanggan dalam sistem
$stmt = $pdo->prepare("SELECT username FROM pelanggan WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    // Tetap kasih pesan sukses demi keamanan (agar tidak bisa tebak email terdaftar)
    echo json_encode(['success' => true, 'message' => 'Jika email Anda terdaftar, instruksi akan segera sampai.']);
    exit;
}

// Generate secure token dan batas waktu kadaluarsa
$token = bin2hex(random_bytes(32));
$expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

// Persistensi token ke dalam database
$pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)")
    ->execute([$email, $token, $expiresAt]);

// Inisiasi pengiriman email pemulihan
$resetLink = base_url("reset_password.php?token=$token");
$username = htmlspecialchars((string) $user['username'], ENT_QUOTES, 'UTF-8');
$safeResetLink = htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8');

$emailContent = "
    <div style='font-family: serif; color: #333; padding: 20px; border: 1px solid #ddd;'>
        <h2 style='color: #C9A84C;'>Halo, $username.</h2>
        <p>Kami menerima permintaan untuk mengatur ulang kata sandi Anda di <b>Lumière</b>.</p>
        <p>Silakan klik tombol di bawah ini untuk membuat sandi baru (berlaku 1 jam):</p>
        <p style='margin: 30px 0;'>
            <a href='$safeResetLink' style='background: #C9A84C; color: #fff; padding: 12px 25px; text-decoration: none; border-radius: 4px; font-weight: bold;'>Buat Sandi Baru</a>
        </p>
        <p style='font-size: 12px; color: #888;'>Jika Anda tidak merasa meminta ini, abaikan saja email ini.</p>
    </div>
";

$sent = send_mail($email, "Atur Ulang Kata Sandi Lumière", $emailContent);

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Instruksi sudah kami kirim ke email Anda ya.']);
} else {
    echo json_encode(['success' => true, 'message' => 'Jika email Anda terdaftar, instruksi akan segera sampai.']);
}
