<?php

declare(strict_types=1);

require_once __DIR__ . '/./includes/bootstrap.php';
require_once __DIR__ . '/./includes/database.php';

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$error = null;

if (empty($token)) {
    redirect(base_url('login.php'));
}

$pdo = db();

// 1. Cek Token valid & belum expired
$stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW() LIMIT 1");
$stmt->execute([$token]);
$reset = $stmt->fetch();

if (!$reset) {
    set_flash('error', 'Maaf, link ini sudah tidak bisa dipakai atau sudah kedaluwarsa.');
    redirect(base_url('login.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 6) {
        $error = 'Minimal 6 karakter ya, biar lebih aman.';
    } elseif ($password !== $confirm) {
        $error = 'Ketikan sandi Anda tidak cocok, coba cek lagi.';
    } else {
        // 2. Update Kata Sandi Baru
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmtUpdate = $pdo->prepare("UPDATE user SET password = ? WHERE email = ?");
        $stmtUpdate->execute([$hashed, $reset['email']]);

        // 3. Hapus Token lama
        $stmtDel = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmtDel->execute([$reset['email']]);

        set_flash('success', 'Selesai! Kata sandi Anda sudah diperbarui. Silakan masuk kembali.');
        redirect(base_url('login.php'));
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOCTRA | Buat Sandi Baru</title>
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
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
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

        .reset-box {
            position: relative;
            background: var(--card-bg);
            padding: 40px;
            width: 100%;
            max-width: 350px;
            text-align: center;
            animation: fadeUp 0.8s 0.2s both;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.03);
        }

        .reset-box::before {
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

        h1 { font-family: var(--font-display); color: var(--gold); letter-spacing: 6px; margin-bottom: 2px; font-size: 24px; text-transform: uppercase; }
        .subtitle { font-size: 9px; text-transform: uppercase; letter-spacing: 2px; color: #666; margin-bottom: 30px; }

        .form-group { margin-bottom: 16px; text-align: left; }
        .label { display: block; font-size: 9px; color: var(--gold); text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 6px; font-weight: 600; }
        .input { width: 100%; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); padding: 11px 13px; color: white; box-sizing: border-box; font-size: 13px; }
        .input:focus { outline: none; border-color: var(--gold); background: rgba(255,255,255,0.06); }

        .btn { width: 100%; background: var(--gold); color: black; border: none; padding: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; cursor: pointer; margin-top: 15px; font-size: 11px; }
        .btn:hover { background: #f3e5ab; box-shadow: 0 5px 20px rgba(201, 168, 76, 0.2); }

        .alert { background: rgba(220, 53, 69, 0.1); color: #ff6b6b; padding: 10px; font-size: 10px; margin-bottom: 20px; text-align: center; border: 1px solid rgba(255,255,255,0.05); }
    </style>
</head>
<body>
    <div class="bg-layer"></div>
    <main class="reset-box">
        <h1>NOCTRA</h1>
        <p class="subtitle">Buat Sandi Baru</p>

        <?php if ($error): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="form-group">
                <label class="label">Sandi Baru</label>
                <input type="password" name="password" class="input" placeholder="••••••••" required autofocus>
            </div>

            <div class="form-group">
                <label class="label">Ulangi Sandi</label>
                <input type="password" name="confirm_password" class="input" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn">Perbarui Sandi</button>
        </form>
    </main>
</body>
</html>
