<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../config/database.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    set_flash('error', 'Username dan password wajib diisi.');
    redirect(frontend_url('login.php'));
}

$statement = db()->prepare('SELECT id_user, username, password, level FROM user WHERE username = :username LIMIT 1');
$statement->execute(['username' => $username]);
$user = $statement->fetch();

if (!$user) {
    set_flash('error', 'Username atau password salah.');
    redirect(frontend_url('login.php'));
}

$storedPassword = (string) $user['password'];
$isValid = false;

if (is_hashed_password($storedPassword)) {
    $isValid = password_verify($password, $storedPassword);
} elseif ($storedPassword === $password) {
    $isValid = true;

    // Upgrade akun seed lama ke hash begitu berhasil login.
    $rehash = password_hash($password, PASSWORD_DEFAULT);
    $update = db()->prepare('UPDATE user SET password = :password WHERE id_user = :id_user');
    $update->execute([
        'password' => $rehash,
        'id_user' => $user['id_user'],
    ]);
}

if (!$isValid) {
    set_flash('error', 'Username atau password salah.');
    redirect(frontend_url('login.php'));
}

$_SESSION['user_id'] = (int) $user['id_user'];
$_SESSION['user_name'] = (string) $user['username'];
$_SESSION['user_role'] = (string) $user['level'];
$_SESSION['username'] = (string) $user['username'];
$_SESSION['level'] = (string) $user['level'];

redirect(role_dashboard_path((string) $user['level']));
