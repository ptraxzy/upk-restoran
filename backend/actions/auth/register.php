<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../config/database.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    set_flash('error', 'Username dan password wajib diisi.');
    redirect(frontend_url('pembeli/auth/register.php'));
}

$check = db()->prepare('SELECT id_user FROM user WHERE username = :username LIMIT 1');
$check->execute(['username' => $username]);

if ($check->fetch()) {
    set_flash('error', 'Username sudah dipakai.');
    redirect(frontend_url('pembeli/auth/register.php'));
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$insert = db()->prepare(
    'INSERT INTO user (username, password, level) VALUES (:username, :password, :level)'
);

$insert->execute([
    'username' => $username,
    'password' => $hashedPassword,
    'level' => 'pelanggan',
]);

set_flash('success', 'Register berhasil. Silakan login sebagai member.');
redirect(frontend_url('login.php'));
