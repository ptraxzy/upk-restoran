<?php

declare(strict_types=1);

function base_path(string $path = ''): string
{
    $base = dirname(__DIR__, 2);

    return $path === '' ? $base : $base . '/' . ltrim($path, '/');
}

function app_path_prefix(): string
{
    static $prefix = null;

    if ($prefix !== null) {
        return $prefix;
    }

    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);

    if (is_string($requestPath) && str_contains($requestPath, '/frontend')) {
        $matched = preg_replace('#^(.*?/frontend).*$#', '$1', $requestPath);

        if (is_string($matched) && $matched !== '') {
            $prefix = rtrim($matched, '/');
            return $prefix;
        }
    }

    $app = require base_path('backend/config/app.php');
    $path = parse_url($app['url'] ?? '', PHP_URL_PATH);

    if (!is_string($path) || $path === '') {
        $prefix = '/frontend';
        return $prefix;
    }

    $prefix = rtrim($path, '/');

    return $prefix === '' ? '/frontend' : $prefix;
}

function frontend_url(string $path = ''): string
{
    $base = app_path_prefix();

    return $path === '' ? $base : $base . '/' . ltrim($path, '/');
}

function backend_url(string $path = ''): string
{
    $base = preg_replace('#/frontend$#', '/backend', app_path_prefix()) ?: '/backend';

    return $path === '' ? $base : $base . '/' . ltrim($path, '/');
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
        'admin' => frontend_url('admin/dashboard/index.php'),
        'kasir' => frontend_url('karyawan/dashboard/index.php'),
        'pelanggan' => frontend_url('pembeli/dashboard/index.php'),
        default => frontend_url('index.php'),
    };
}

function role_login_path(string $role): string
{
    return frontend_url('login.php');
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
