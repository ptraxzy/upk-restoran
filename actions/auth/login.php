<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../../config/database.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role = trim($_POST['role'] ?? 'pelanggan');

if ($username === '' || $password === '') {
    set_flash('error', 'Username dan password wajib diisi.');
    redirect(base_url('login.php'));
}

if (!in_array($role, ['admin', 'kasir', 'pelanggan'], true)) {
    set_flash('error', 'Peran tidak valid.');
    redirect(base_url('login.php'));
}

$t = match($role) {
    'admin' => ['table' => 'admin',     'id' => 'id_admin',     'name' => 'nama_admin',     'role' => 'admin'],
    'kasir' => ['table' => 'karyawan',  'id' => 'id_karyawan',  'name' => 'nama_karyawan',  'role' => 'kasir'],
    default => ['table' => 'pelanggan', 'id' => 'id_pelanggan', 'name' => 'nama_pelanggan', 'role' => 'pelanggan'],
};

$stmt = db()->prepare("SELECT {$t['id']}, username, password, {$t['name']}, status FROM {$t['table']} WHERE username = :username LIMIT 1");
$stmt->execute(['username' => $username]);
$foundUser = $stmt->fetch();

$foundRole = $t['role'];
$foundIdCol = $t['id'];
$foundTable = $t['table'];

if (!$foundUser) {
    set_flash('error', 'Username atau password salah.');
    redirect(base_url('login.php'));
}

if (($foundUser['status'] ?? 'Aktif') === 'Nonaktif') {
    set_flash('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.');
    redirect(base_url('login.php'));
}

$storedPassword = (string) $foundUser['password'];
$isValid = false;

if (is_hashed_password($storedPassword)) {
    $isValid = password_verify($password, $storedPassword);
} elseif ($storedPassword === $password) {
    $isValid = true;

    // Upgrade akun seed lama ke hash begitu berhasil login.
    $rehash = password_hash($password, PASSWORD_DEFAULT);
    $update = db()->prepare("UPDATE {$foundTable} SET password = :password WHERE {$foundIdCol} = :id");
    $update->execute([
        'password' => $rehash,
        'id' => $foundUser[$foundIdCol],
    ]);
}

if (!$isValid) {
    set_flash('error', 'Username atau password salah.');
    redirect(base_url('login.php'));
}

$nameCol = match($foundRole) {
    'admin' => 'nama_admin',
    'kasir' => 'nama_karyawan',
    'pelanggan' => 'nama_pelanggan',
};

$_SESSION['id_user'] = (int) $foundUser[$foundIdCol];
$_SESSION['user_name'] = (string) ($foundUser[$nameCol] ?: $foundUser['username']);
$_SESSION['user_role'] = $foundRole;
$_SESSION['username'] = (string) $foundUser['username'];
$_SESSION['level'] = $foundRole;

redirect(role_dashboard_path($foundRole));
