<?php

declare(strict_types=1);

date_default_timezone_set('Asia/Jakarta');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Tangkap parameter nomor meja dari QR Code (URL query ?meja=XX)
if (isset($_GET['meja']) && trim((string)$_GET['meja']) !== '') {
    $_SESSION['meja_aktif'] = trim((string)$_GET['meja']);
}

// Set default nomor meja menjadi '01' jika belum di-set di session
if (!isset($_SESSION['meja_aktif']) || trim((string)$_SESSION['meja_aktif']) === '') {
    $_SESSION['meja_aktif'] = '01';
}

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/ui.php';
