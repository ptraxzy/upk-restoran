<?php

declare(strict_types=1);

require_once __DIR__ . '/./includes/bootstrap.php';
require_once __DIR__ . '/./includes/database.php';

$currentRole = $_SESSION['user_role'] ?? $_SESSION['level'] ?? null;
if (is_string($currentRole) && $currentRole !== '') {
    redirect(role_dashboard_path($currentRole));
}

$error = get_flash('error');
$success = get_flash('success');

$selectedRole = 'pelanggan';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username === '' || $password === '') {
        $error = 'Nama pengguna dan kata sandi jangan dikosongkan ya.';
    } else {
        $tables = [
            ['table' => 'admin',     'id' => 'id_admin',     'name' => 'nama_admin',     'role' => 'admin'],
            ['table' => 'karyawan',  'id' => 'id_karyawan',  'name' => 'nama_karyawan',  'role' => 'kasir'],
            ['table' => 'pelanggan', 'id' => 'id_pelanggan', 'name' => 'nama_pelanggan', 'role' => 'pelanggan'],
        ];

        $foundUser = null;
        $t = null;

        foreach ($tables as $tableDef) {
            $stmt = db()->prepare("SELECT {$tableDef['id']}, username, password, {$tableDef['name']}, status FROM {$tableDef['table']} WHERE username = :username LIMIT 1");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();
            if ($user) {
                $foundUser = $user;
                $t = $tableDef;
                break;
            }
        }

        $isValid = false;
        if ($foundUser) {
            // Cek status aktif
            if (($foundUser['status'] ?? 'Aktif') === 'Nonaktif') {
                $error = 'Akun Anda telah dinonaktifkan. Hubungi administrator.';
            } else {
                $storedPassword = (string) $foundUser['password'];
                if (password_verify($password, $storedPassword)) {
                    $isValid = true;
                } elseif ($storedPassword === $password) {
                    $isValid = true;
                    $rehash = password_hash($password, PASSWORD_DEFAULT);
                    $update = db()->prepare("UPDATE {$t['table']} SET password = :password WHERE {$t['id']} = :id");
                    $update->execute(['password' => $rehash, 'id' => $foundUser[$t['id']]]);
                }
            }
        }

        if ($foundUser && $isValid) {
            session_regenerate_id(true);
            $_SESSION['id_user'] = (int) $foundUser[$t['id']];
            $_SESSION['user_name'] = (string) ($foundUser[$t['name']] ?: $foundUser['username']);
            $_SESSION['user_role'] = $t['role'];
            $_SESSION['username'] = (string) $foundUser['username'];
            $_SESSION['level'] = $t['role'];
            redirect(role_dashboard_path($t['role']));
        }
        if (!isset($error)) {
            $error = 'Maaf, nama pengguna atau sandi Anda salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lumière | Selamat Datang</title>
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
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
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

        .login-box {
            position: relative;
            background: var(--card-bg);
            padding: 44px;
            width: 100%;
            max-width: 380px;
            text-align: center;
            animation: fadeUp 0.7s 0.15s both;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.04);
        }

        .login-box::before {
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

        .brand-logo {
            font-family: var(--font-display);
            font-size: 28px;
            letter-spacing: 0.06em;
            color: var(--gold);
            margin-bottom: 4px;
        }

        .brand-subtitle {
            font-size: 13px;
            color: #666;
            margin-bottom: 32px;
        }

        .form-group {
            margin-bottom: 18px;
            text-align: left;
            opacity: 0;
            animation: fieldFade 0.5s forwards;
        }
        .form-group:nth-child(1) { animation-delay: 0.6s; }
        .form-group:nth-child(2) { animation-delay: 0.75s; }

        .field-label {
            display: block;
            font-size: 12px;
            color: var(--gold);
            margin-bottom: 7px;
            font-weight: 600;
        }

        .input-control {
            width: 100%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 12px 14px;
            color: white;
            font-family: var(--font-body);
            font-size: 14px;
            transition: all 0.25s var(--ease);
        }

        .input-control:focus {
            outline: none;
            border-color: var(--gold);
            background: rgba(255, 255, 255, 0.06);
        }

        .input-control::placeholder {
            color: rgba(255, 255, 255, 0.4) !important;
        }

        .forgot-link {
            display: block;
            text-align: right;
            font-size: 12px;
            color: var(--gold);
            margin-top: 8px;
            text-decoration: none;
        }

        .btn-wrap {
            margin-top: 28px;
            opacity: 0;
            animation: fieldFade 0.5s 0.9s forwards;
        }

        .btn-primary {
            width: 100%;
            background: var(--gold);
            color: #000;
            border: none;
            padding: 13px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s var(--ease);
        }

        .btn-primary:hover {
            background: #f3e5ab;
            box-shadow: 0 4px 16px rgba(201, 168, 76, 0.2);
        }

        .footer-links {
            margin-top: 28px;
            font-size: 13px;
            color: #777;
        }

        .footer-links a { color: var(--gold); text-decoration: none; font-weight: 600; }

        .alert {
            font-size: 13px;
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .alert-danger { background: rgba(220, 53, 69, 0.1); color: #ff6b6b; }
        .alert-success { background: rgba(40, 167, 69, 0.1); color: #51cf66; }

        .hidden { display: none !important; opacity: 0; }

        .tab-btn.active {
            color: var(--gold);
            border-bottom-color: var(--gold);
            font-weight: 600;
        }

        /* Preloader Styles */
        #preloader {
            position: fixed;
            inset: 0;
            background: #0a0a0a;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.8s var(--ease), visibility 0.8s var(--ease);
        }
        
        .preloader-content {
            text-align: center;
        }
        
        .preloader-logo {
            font-family: var(--font-display);
            font-size: 42px;
            letter-spacing: 0.15em;
            color: var(--gold);
            opacity: 0;
            transform: translateY(15px);
            animation: preloaderFadeIn 1.1s 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        
        .preloader-line {
            width: 0;
            height: 1px;
            background: var(--gold);
            margin: 18px auto 0;
            animation: preloaderLineGrow 1.3s 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        
        @keyframes preloaderFadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes preloaderLineGrow {
            to {
                width: 140px;
            }
        }
        
        .preloader-hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <!-- Preloader / Intro Animation -->
    <div id="preloader">
        <div class="preloader-content">
            <div class="preloader-logo">Lumière</div>
            <div class="preloader-line"></div>
        </div>
    </div>

    <div class="bg-layer"></div>

    <main class="login-box">
        <header>
            <h1 class="brand-logo">Lumière</h1>
            <p class="brand-subtitle">Silakan Masuk</p>
        </header>

        <div id="alert-container">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
        </div>

        <!-- Bagian Login -->
        <div id="login-section">
            <form method="post" action="login.php" id="form-login">
                <div class="form-group" style="animation-delay: 0.1s;">
                    <label class=   "field-label">Nama Pengguna</label>
                    <input type="text" name="username" class="input-control" placeholder="ID masuk" required>
                </div>
                <div class="form-group" style="animation-delay: 0.2s;">
                    <label class="field-label">Kata Sandi</label>
                    <input type="password" name="password" class="input-control" placeholder="••••••••" required>
                    <a href="#" class="forgot-link" onclick="toggleSection('forgot')">Lupa kata sandi?</a>
                </div>
                <div class="btn-wrap" style="animation-delay: 0.3s;">
                    <button type="submit" class="btn-primary">Masuk</button>
                </div>
            </form>

            <footer class="footer-links" id="register-footer">
                Baru di sini? <a href="register.php">Buat Akun</a>
            </footer>
        </div>

        <!-- Bagian Lupa Password -->
        <div id="forgot-section" class="hidden">
            <p class="text-secondary small mb-4" style="color:#aaa; font-size:11px; line-height:1.4;">Masukkan email Anda, nanti kami kirimkan cara buat sandi baru.</p>
            <form id="forgot-password-form">
                <div class="form-group" style="opacity: 1;">
                    <label class="field-label">Alamat Email</label>
                    <input type="email" id="forgot-email" class="input-control" placeholder="email@anda.com" required>
                </div>

                <div class="btn-wrap" style="opacity: 1;">
                    <button type="submit" id="forgot-btn" class="btn-primary">Kirimkan Link</button>
                </div>
            </form>

            <footer class="footer-links">
                <a href="#" onclick="toggleSection('login')">Kembali masuk</a>
            </footer>
        </div>
    </main>

    <script>
        // Intro Animation control
        document.addEventListener('DOMContentLoaded', function() {
            const preloader = document.getElementById('preloader');
            window.addEventListener('load', function() {
                setTimeout(function() {
                    if (preloader) {
                        preloader.classList.add('preloader-hidden');
                    }
                }, 1800);
            });
        });

        // (Tabs removed)

        function toggleSection(section) {
            const loginSection = document.getElementById('login-section');
            const forgotSection = document.getElementById('forgot-section');
            const alertContainer = document.getElementById('alert-container');

            if (section === 'forgot') {
                loginSection.classList.add('hidden');
                forgotSection.classList.remove('hidden');
                alertContainer.innerHTML = '';
            } else {
                loginSection.classList.remove('hidden');
                forgotSection.classList.add('hidden');
            }
        }

        document.getElementById('forgot-password-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('forgot-email').value;
            const btn = document.getElementById('forgot-btn');
            const alertContainer = document.getElementById('alert-container');

            btn.innerText = 'SEBENTAR...';
            btn.disabled = true;

            fetch('forgot_password.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'email=' + encodeURIComponent(email)
            })
            .then(response => response.json())
            .then(data => {
                btn.innerText = 'Kirimkan Link';
                btn.disabled = false;
                if (data.success) {
                    alertContainer.innerHTML = '<div class="alert alert-success">Cek email Anda ya, link buat sandi baru sudah dikirim.</div>';
                    document.getElementById('forgot-email').value = '';
                } else {
                    alertContainer.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                }
            })
            .catch(error => {
                btn.innerText = 'Kirimkan Link';
                btn.disabled = false;
                alertContainer.innerHTML = '<div class="alert alert-danger">Maaf, ada kendala koneksi.</div>';
            });
        });
    </script>
</body>
</html>
