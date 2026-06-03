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
        
        // Cek apakah username/email sudah dipakai di tabel pelanggan
        $exists = false;
        
        $chkUser = $pdo->prepare('SELECT 1 FROM pelanggan WHERE username = :username OR email = :email LIMIT 1');
        $chkUser->execute(['username' => $username, 'email' => $email]);
        if ($chkUser->fetch()) {
            $exists = true;
        }

        if ($exists) {
            $error = 'Nama pengguna atau email ini sudah ada yang pakai.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare('INSERT INTO pelanggan (nama_pelanggan, username, email, password) VALUES (:nama, :username, :email, :password)');
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
    <title>Lumière | Gabung Member</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0a0a0a;
            --card-bg: rgba(10, 10, 10, 0.92);
            --gold: #C9A84C;
            --font-display: 'Libre Baskerville', serif;
            --font-body: 'DM Sans', sans-serif;
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
            -webkit-font-smoothing: antialiased;
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

        @keyframes bgZoom { from { transform: scale(1); } to { transform: scale(1.06); } }

        .register-box {
            position: relative;
            background: var(--card-bg);
            padding: 44px;
            width: 100%;
            max-width: 400px;
            text-align: center;
            animation: fadeUp 0.7s 0.15s both;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.04);
        }

        .register-box::before {
            content: '';
            position: absolute;
            inset: 0;
            border: 1px solid var(--gold);
            animation: traceIn 1.2s 0.4s var(--ease) both;
            clip-path: inset(0 100% 0 0);
            pointer-events: none;
        }

        @keyframes traceIn { to { clip-path: inset(0 0% 0 0); } }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fieldFade { to { opacity: 1; } }

        h1 { font-family: var(--font-display); color: var(--gold); letter-spacing: 0.06em; margin-bottom: 4px; font-size: 28px; }
        .subtitle { font-size: 13px; color: #666; margin-bottom: 32px; }

        .form-group {
            margin-bottom: 18px;
            text-align: left;
            opacity: 0;
            animation: fieldFade 0.5s forwards;
        }

        .label { display: block; font-size: 12px; color: var(--gold); margin-bottom: 7px; font-weight: 600; }
        .input { width: 100%; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); padding: 12px 14px; color: white; box-sizing: border-box; transition: all 0.25s var(--ease); font-size: 14px; font-family: var(--font-body); }
        .input:focus { outline: none; border-color: var(--gold); background: rgba(255, 255, 255, 0.06); }
        .input::placeholder { color: rgba(255, 255, 255, 0.4) !important; }

        .btn { width: 100%; background: var(--gold); color: black; border: none; padding: 13px; font-weight: 600; cursor: pointer; margin-top: 10px; transition: all 0.25s var(--ease); font-size: 14px; }
        .btn:hover { background: #f3e5ab; box-shadow: 0 4px 16px rgba(201, 168, 76, 0.2); }

        .alert { background: rgba(220, 53, 69, 0.1); color: #ff6b6b; padding: 12px; font-size: 13px; margin-bottom: 24px; text-align: center; border: 1px solid rgba(255,255,255,0.05); }
        .footer { text-align: center; margin-top: 28px; font-size: 13px; color: #777; }
        .footer a { color: var(--gold); text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="bg-layer"></div>

    <main class="register-box">
        <h1>Lumière</h1>
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

            <div class="text-secondary" style="font-size: 11px; margin-bottom: 20px; line-height: 1.5; color: #777; text-align: left; opacity: 0; animation: fieldFade 0.6s 1.1s forwards;">
                Dengan mendaftar, Anda menyetujui <a href="privacy.php" target="_blank" style="color: var(--gold); text-decoration: none;">Kebijakan Privasi</a> dan <a href="terms.php" target="_blank" style="color: var(--gold); text-decoration: none;">Syarat & Ketentuan</a> kami.
            </div>

            <button type="submit" class="btn" style="opacity: 0; animation: fieldFade 0.6s 1.2s forwards;">Daftar Sekarang</button>
        </form>

        <footer class="footer" style="opacity: 0; animation: fieldFade 0.6s 1.4s forwards;">
            Sudah punya akun? <a href="login.php">Masuk kembali</a>
        </footer>
    </main>
</body>
</html>
