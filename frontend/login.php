<?php
// Page: Login | Auth: All roles

declare(strict_types=1);

require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_once __DIR__ . '/../backend/config/database.php';

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
    <title>Login | L'Art Culinaire</title>
    <meta name="description" content="Login sistem restoran untuk admin, karyawan, dan pembeli.">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap');

        :root {
            --bg-main: #0D0D0D;
            --bg-card: #141414;
            --bg-card-hover: #1C1C1C;
            --bg-sidebar: #111111;
            --border: #242424;
            --border-soft: #1E1E1E;
            --gold: #C9A84C;
            --gold-light: #E2B96A;
            --gold-dim: rgba(201,168,76,0.08);
            --text-primary: #F0EDE6;
            --text-secondary: #888888;
            --text-muted: #4A4A4A;
            --font-display: 'Cormorant Garamond', Georgia, serif;
            --font-body: 'DM Sans', system-ui, sans-serif;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            border-radius: 0 !important;
        }

        html {
            color-scheme: dark;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background:
                linear-gradient(rgba(13, 13, 13, 0.94), rgba(13, 13, 13, 0.94)),
                radial-gradient(circle at center, rgba(201,168,76,0.08), transparent 34%),
                var(--bg-main);
            color: var(--text-primary);
            font-family: var(--font-body);
            -webkit-font-smoothing: antialiased;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            opacity: 0.16;
            background-image:
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px);
            background-size: 3px 3px;
        }

        .login-page {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 32px 18px;
        }

        .login-wrap {
            width: min(100%, 430px);
        }

        .brand {
            margin-bottom: 28px;
            text-align: center;
        }

        .brand-logo {
            margin: 0;
            color: var(--gold);
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 400;
            letter-spacing: 0.3em;
            text-transform: uppercase;
        }

        .brand-subtitle {
            margin: 12px 0 0;
            color: var(--text-secondary);
            font-size: 10px;
            font-weight: 400;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .login-card {
            width: 100%;
            padding: 40px 44px;
            background: var(--bg-card);
            border: 1px solid var(--border);
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #666666;
            font-size: 10px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        input {
            width: 100%;
            padding: 12px 16px;
            background: var(--bg-main);
            border: 1px solid #2A2A2A;
            color: var(--text-primary);
            font-family: var(--font-body);
            font-size: 13px;
            outline: none;
        }

        input::placeholder {
            color: var(--text-muted);
        }

        input:focus {
            border-color: var(--gold);
            outline: none;
            box-shadow: none;
        }

        .login-button {
            width: 100%;
            margin-top: 8px;
            padding: 14px 0;
            background: var(--gold);
            border: none;
            color: var(--bg-main);
            cursor: pointer;
            font-family: var(--font-body);
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            transition: background-color 180ms ease;
        }

        .login-button:hover {
            background: var(--gold-light);
        }

        .login-message {
            margin: 0 0 22px;
            padding-left: 12px;
            border-left: 2px solid var(--gold);
            color: var(--gold-light);
            font-size: 12px;
            line-height: 1.6;
        }

        .login-footer {
            margin-top: 22px;
            color: #444444;
            font-size: 10px;
            text-align: center;
        }

        .login-footer a {
            color: var(--text-secondary);
            text-decoration: none;
        }

        .login-footer a:hover {
            color: var(--gold);
        }

        @media (max-width: 520px) {
            .login-card {
                padding: 32px 24px;
            }

            .brand-logo {
                font-size: 19px;
                letter-spacing: 0.24em;
            }
        }
    </style>
</head>
<body>
    <main class="login-page">
        <section class="login-wrap" aria-label="Login akun restoran">
            <header class="brand">
                <h1 class="brand-logo">L'Art Culinaire</h1>
                <p class="brand-subtitle">Masuk ke akun Anda</p>
            </header>

            <div class="login-card">
                <?php if ($error !== null): ?>
                    <p class="login-message"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php elseif ($success !== null): ?>
                    <p class="login-message"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>

                <form method="post" action="<?= htmlspecialchars(frontend_url('login.php'), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input id="username" name="username" type="text" autocomplete="username" placeholder="Masukkan username" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Masukkan password" required>
                    </div>

                    <button class="login-button" type="submit">Masuk</button>
                </form>
            </div>

            <p class="login-footer">© 2026 L'Art Culinaire · <a href="<?= htmlspecialchars(frontend_url('pembeli/auth/register.php'), ENT_QUOTES, 'UTF-8'); ?>">Register member</a></p>
        </section>
    </main>
</body>
</html>
