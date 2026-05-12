<?php

declare(strict_types=1);

require_once __DIR__ . '/./includes/bootstrap.php';
require_once __DIR__ . '/./includes/database.php';

// Capture table number from QR code scan
if (isset($_GET['meja']) && trim($_GET['meja']) !== '') {
    $_SESSION['scanned_meja'] = trim($_GET['meja']);
}

$currentRole = $_SESSION['user_role'] ?? $_SESSION['level'] ?? null;

if (is_string($currentRole) && $currentRole !== '') {
    redirect(role_dashboard_path($currentRole));
}

$error = get_flash('error');
$success = get_flash('success');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $statement = db()->prepare('SELECT id_user, username, password, level FROM user WHERE username = :username LIMIT 1');
        $statement->execute(['username' => $username]);
        $user = $statement->fetch();

        $isValid = false;

        if ($user) {
            $storedPassword = (string) $user['password'];

            if (is_hashed_password($storedPassword)) {
                $isValid = password_verify($password, $storedPassword);
            } elseif ($storedPassword === $password) {
                $isValid = true;
                // Migrate akun dummy lama yang masih memakai password plain text.
                $rehash = password_hash($password, PASSWORD_DEFAULT);
                $update = db()->prepare('UPDATE user SET password = :password WHERE id_user = :id_user');
                $update->execute([
                    'password' => $rehash,
                    'id_user' => $user['id_user'],
                ]);
            }
        }

        if ($user && $isValid) {
            $_SESSION['user_id'] = (int) $user['id_user'];
            $_SESSION['user_name'] = (string) $user['username'];
            $_SESSION['user_role'] = (string) $user['level'];
            $_SESSION['username'] = (string) $user['username'];
            $_SESSION['level'] = (string) $user['level'];

            if ($user['level'] === 'pelanggan') {
                $_SESSION['meja_aktif'] = $_SESSION['scanned_meja'] ?? '01';
            }

            redirect(role_dashboard_path((string) $user['level']));
        }

        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOCTRA | Akses Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>?v=<?= time(); ?>">
    <style>
        .login-bg {
            background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.8)), url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
        }
        .login-card {
            background: rgba(10, 10, 10, 0.6);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            padding: 48px;
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body class="login-bg min-vh-100 d-flex align-items-center justify-content-center">
    <main class="w-100 d-flex justify-content-center px-3">
        <section class="login-card">
            <header class="text-center mb-5">
                <h1 class="font-display text-gold mb-2" style="font-size: 36px; letter-spacing: 6px;">NOCTRA</h1>
                <p class="text-secondary small text-uppercase letter-spacing-2 m-0">Akses Portal</p>
            </header>

            <?php if ($error !== null): ?>
                <div class="alert alert-danger rounded-0 small p-3 mb-4 border-0 bg-danger bg-opacity-10 text-danger text-center text-uppercase letter-spacing-1"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php elseif ($success !== null): ?>
                <div class="alert alert-success rounded-0 small p-3 mb-4 border-0 bg-success bg-opacity-10 text-success text-center text-uppercase letter-spacing-1"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form method="post" action="<?= htmlspecialchars(base_url('login.php'), ENT_QUOTES, 'UTF-8'); ?>">
                <div class="mb-4">
                    <input id="username" name="username" type="text" class="form-control" autocomplete="username" placeholder="Username" required>
                </div>

                <div class="mb-5">
                    <input id="password" name="password" type="password" class="form-control" autocomplete="current-password" placeholder="Kata Sandi" required>
                </div>

                <button class="btn btn-warning w-100 mb-4" type="submit">MASUK</button>
            </form>

            <p class="text-center small text-muted m-0">Bukan staf kami? <a class="text-gold text-decoration-none border-bottom border-gold pb-1" href="<?= htmlspecialchars(base_url('register.php'), ENT_QUOTES, 'UTF-8'); ?>">Daftar Member</a></p>
        </section>
    </main>
</body>
</html>
