<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

// Deteksi base URL secara dinamis jika tidak diatur di environment
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? '') == 443 ? 'https' : 'http';
$defaultUrl = "$scheme://$host";

return [
    'name' => env('APP_NAME', 'UPK Restoran'),
    'env' => env('APP_ENV', 'local'),
    'url' => env('APP_URL', $defaultUrl),
];
