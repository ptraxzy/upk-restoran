<?php

declare(strict_types=1);

function base_path(string $path = ''): string
{
    $base = dirname(__DIR__); // Root folder

    return $path === '' ? $base : $base . '/' . ltrim($path, '/');
}

function base_url(string $path = ''): string
{
    $app = require base_path('config/app.php');
    $baseUrl = rtrim($app['url'] ?? 'http://localhost:8001', '/');

    return $path === '' ? $baseUrl : $baseUrl . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function set_flash(string $key, string $message): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $_SESSION['flash'][$key] = $message;
}

function get_flash(string $key): ?string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $message = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);

    return $message;
}

function rupiah(int|float $amount): string
{
    return 'Rp' . number_format((float) $amount, 0, ',', '.');
}

function role_dashboard_path(string $role): string
{
    return match ($role) {
        'admin' => base_url('admin/dashboard.php'),
        'kasir' => base_url('kasir/dashboard.php'),
        'pelanggan' => base_url('pelanggan/dashboard.php'),
        default => base_url('index.php'),
    };
}

function role_login_path(string $role): string
{
    return base_url('login.php');
}

function role_label(string $role): string
{
    return match ($role) {
        'admin' => 'Admin',
        'kasir' => 'Karyawan',
        'pelanggan' => 'Member',
        default => 'User',
    };
}

function is_hashed_password(string $password): bool
{
    return password_get_info($password)['algo'] !== null;
}
