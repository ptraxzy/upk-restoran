<?php

declare(strict_types=1);

require_once __DIR__ . '/./includes/bootstrap.php';
require_once __DIR__ . '/./includes/database.php';

$currentRole = $_SESSION['user_role'] ?? $_SESSION['level'] ?? null;
if (is_string($currentRole) && $currentRole !== '') {
    redirect(role_dashboard_path($currentRole));
}

$error = null;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $nama = trim($_POST['nama_user'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nama === '' || $username === '' || $email === '' || $password === '') {
        $error = 'Mohon isi semua data pendaftaran ya.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email Anda sepertinya salah.';
    } else {
        $pdo = db();
        $statement = $pdo->prepare('SELECT id_user FROM user WHERE username = :username OR email = :email LIMIT 1');
        $statement->execute(['username' => $username, 'email' => $email]);

        if ($statement->fetch()) {
            $error = 'Nama pengguna atau email ini sudah ada yang pakai.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare('INSERT INTO user (nama_user, username, email, password, level) VALUES (:nama, :username, :email, :password, "pelanggan")');
            $success = $insert->execute([
                'nama' => $nama,
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword,
            ]);

            if ($success) {
                set_flash('success', 'Pendaftaran berhasil! Silakan masuk ke akun Anda.');
                redirect(base_url('login.php'));
            }
            $error = 'Aduh, ada sedikit kendala teknis. Coba lagi nanti ya.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOCTRA | Gabung Member</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0a0a0a;
            --card-bg: rgba(10, 10, 10, 0.92);
            --gold: #C9A84C;
            --font-display: 'Cormorant Garamond', serif;
            --font-body: 'Plus Jakarta Sans', sans-serif;
            --ease: cubic-bezier(0.16, 1, 0.3, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-color: var(--bg);
            color: #ffffff;
            font-family: var(--font-body);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            padding: 20px;
        }

        .bg-layer {
            position: absolute;
            inset: 0;
            background-image: linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.85)), url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            animation: bgZoom 30s infinite alternate ease-in-out;
            z-index: -1;
        }

        @keyframes bgZoom { from { transform: scale(1); } to { transform: scale(1.08); } }

        .register-box {
            position: relative;
            background: var(--card-bg);
            padding: 45px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            animation: fadeUp 0.8s 0.2s both;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.03);
        }

        .register-box::before {
            content: '';
            position: absolute;
            inset: 0;
            border: 1px solid var(--gold);
            animation: traceIn 1.4s 0.5s var(--ease) both;
            clip-path: inset(0 100% 0 0);
            pointer-events: none;
        }

        @keyframes traceIn { to { clip-path: inset(0 0% 0 0); } }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fieldFade { to { opacity: 1; } }

        h1 { font-family: var(--font-display); color: var(--gold); letter-spacing: 8px; margin-bottom: 2px; font-size: 34px; text-transform: uppercase; }
        .subtitle { font-size: 11px; text-transform: uppercase; letter-spacing: 3px; color: #666; margin-bottom: 30px; }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
            opacity: 0;
            animation: fieldFade 0.6s forwards;
        }

        .label { display: block; font-size: 10px; color: var(--gold); text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 6px; font-weight: 600; }
        .input { width: 100%; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); padding: 12px; color: white; box-sizing: border-box; transition: all 0.3s var(--ease); font-size: 14px; font-family: var(--font-body); }
        .input:focus { outline: none; border-color: var(--gold); background: rgba(255, 255, 255, 0.06); }

        .btn { width: 100%; background: var(--gold); color: black; border: none; padding: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; cursor: pointer; margin-top: 10px; transition: all 0.3s var(--ease); font-size: 12px; }
        .btn:hover { background: #f3e5ab; box-shadow: 0 5px 20px rgba(201, 168, 76, 0.2); }

        .alert { background: rgba(220, 53, 69, 0.1); color: #ff6b6b; padding: 12px; font-size: 11px; margin-bottom: 25px; text-align: center; border: 1px solid rgba(255,255,255,0.05); }
        .footer { text-align: center; margin-top: 30px; font-size: 12px; color: #777; }
        .footer a { color: var(--gold); text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="bg-layer"></div>

    <main class="register-box">
        <h1>NOCTRA</h1>
        <p class="subtitle">Gabung Member</p>

        <?php if ($error): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group" style="animation-delay: 0.7s;">
                <label class="label">Nama Lengkap</label>
                <input type="text" name="nama_user" class="input" placeholder="Siapa nama Anda?" required autofocus>
            </div>

            <div class="form-group" style="animation-delay: 0.8s;">
                <label class="label">Nama Pengguna</label>
                <input type="text" name="username" class="input" placeholder="Untuk ID masuk Anda" required>
            </div>

            <div class="form-group" style="animation-delay: 0.9s;">
                <label class="label">Alamat Email</label>
                <input type="email" name="email" class="input" placeholder="email@anda.com" required>
            </div>

            <div class="form-group" style="animation-delay: 1.0s;">
                <label class="label">Kata Sandi</label>
                <input type="password" name="password" class="input" placeholder="Minimal 6 karakter" required>
            </div>

            <button type="submit" class="btn" style="opacity: 0; animation: fieldFade 0.6s 1.2s forwards;">Daftar Sekarang</button>
        </form>

        <footer class="footer" style="opacity: 0; animation: fieldFade 0.6s 1.4s forwards;">
            Sudah punya akun? <a href="login.php">Masuk kembali</a>
        </footer>
    </main>
</body>
</html>
