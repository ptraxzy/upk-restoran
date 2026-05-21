<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

function require_role(string $role): void
{
    $currentRole = $_SESSION['user_role'] ?? $_SESSION['level'] ?? null;
    $currentUserId = $_SESSION['id_user'] ?? null;

    if ($currentRole !== $role || !$currentUserId) {
        set_flash('error', 'Silakan login terlebih dahulu.');
        redirect(base_url('login.php'));
    }

    // Cek real-time status keaktifan user di database
    require_once __DIR__ . '/database.php';
    try {
        $stmt = db()->prepare('SELECT status FROM user WHERE id_user = ? LIMIT 1');
        $stmt->execute([$currentUserId]);
        $status = $stmt->fetchColumn();

        if ($status === 'Nonaktif') {
            // Hancurkan session
            $_SESSION = [];
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_destroy();
            }
            
            // Set flash message di session baru
            session_start();
            set_flash('error', 'Akun Anda telah dinonaktifkan. Akses ditolak.');
            redirect(base_url('login.php'));
        }
    } catch (Throwable $e) {
        // Abaikan error database agar sistem tidak mati jika koneksi terganggu
    }
}
