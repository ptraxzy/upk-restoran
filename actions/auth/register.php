<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../../config/database.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    set_flash('error', 'Username dan password wajib diisi.');
    redirect(base_url('register.php'));
}

$exists = false;
$chkUser = db()->prepare('SELECT 1 FROM pelanggan WHERE username = :username LIMIT 1');
$chkUser->execute(['username' => $username]);
if ($chkUser->fetch()) {
    $exists = true;
}

if ($exists) {
    set_flash('error', 'Username sudah dipakai.');
    redirect(base_url('register.php'));
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$insert = db()->prepare(
    'INSERT INTO pelanggan (nama_pelanggan, username, password) VALUES (:username, :username, :password)'
);

$insert->execute([
    'username' => $username,
    'password' => $hashedPassword,
]);

set_flash('success', 'Register berhasil. Silakan login sebagai member.');
redirect(base_url('login.php'));
