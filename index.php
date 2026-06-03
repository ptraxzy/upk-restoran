<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/database.php';

$isLoggedIn = isset($_SESSION['user_role']) && isset($_SESSION['id_user']);
$userRole = $_SESSION['user_role'] ?? '';

// Determine redirect paths based on session
$orderUrl = base_url('login.php');
if ($isLoggedIn) {
    if ($userRole === 'pelanggan') {
        $orderUrl = base_url('pelanggan/dashboard.php');
    } elseif ($userRole === 'kasir') {
        $orderUrl = base_url('kasir/dashboard.php');
    } elseif ($userRole === 'admin') {
        $orderUrl = base_url('admin/dashboard.php');
    }
}

$staffUrl = base_url('login.php');
if ($isLoggedIn) {
    if ($userRole === 'kasir') {
        $staffUrl = base_url('kasir/dashboard.php');
    } elseif ($userRole === 'admin') {
        $staffUrl = base_url('admin/dashboard.php');
    }
}

// Fetch 3 premium featured menus dynamically
$pdo = db();
$stmt = $pdo->query("SELECT * FROM menu WHERE status = 'Tersedia' AND deleted_at IS NULL ORDER BY harga DESC LIMIT 3");
$featuredMenu = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lumière - Dine-in Smart Order Portal</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    
    <style>
        :root {
            --gold: #C9A84C;
            --gold-dim: rgba(201, 168, 76, 0.15);
            --bg-dark: #131313;
            --card-bg: rgba(15, 15, 15, 0.6);
            --text-primary: #E5E2E1;
            --text-secondary: #9A8F80;
            --border-soft: rgba(154, 143, 128, 0.15);
            --font-serif: 'Libre Baskerville', serif;
            --font-sans: 'DM Sans', sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-primary);
            font-family: var(--font-sans);
            overflow-x: hidden;
        }

        /* HEADER */
        .navbar-custom {
            padding: 24px 0;
            border-bottom: 1px solid var(--border-soft);
        }
        .brand-logo {
            font-family: var(--font-serif);
            font-size: 24px;
            color: var(--gold) !important;
            letter-spacing: 0.06em;
            text-decoration: none;
        }
        
        /* HERO SECTION */
        .hero-section {
            position: relative;
            min-height: 80vh;
            display: flex;
            align-items: center;
            padding: 80px 0;
            border-bottom: 1px solid var(--border-soft);
            background: linear-gradient(to bottom, rgba(19, 19, 19, 0.3) 0%, var(--bg-dark) 100%),
                        url("https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1920&q=80") center center/cover no-repeat;
        }
        .hero-title {
            font-family: var(--font-serif);
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            line-height: 1.15;
            margin-bottom: 24px;
        }
        .hero-desc {
            font-size: 1.1rem;
            color: var(--text-secondary);
            max-width: 550px;
            line-height: 1.6;
            margin-bottom: 40px;
        }
        
        /* BUTTONS */
        .btn-gold {
            background-color: var(--gold);
            color: #000 !important;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 16px 36px;
            border-radius: 0;
            font-size: 13px;
            border: 1px solid var(--gold);
            transition: all 0.3s ease;
        }
        .btn-gold:hover {
            background-color: transparent;
            color: var(--gold) !important;
            box-shadow: 0 0 20px rgba(201, 168, 76, 0.2);
        }
        .btn-outline-gold {
            background-color: transparent;
            color: var(--gold) !important;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 10px 24px;
            border-radius: 0;
            font-size: 12px;
            border: 1px solid var(--gold);
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-outline-gold:hover {
            background-color: var(--gold);
            color: #000 !important;
        }

        /* FEATURED MENU */
        .section-title {
            font-family: var(--font-serif);
            font-size: 32px;
            color: var(--text-primary);
            margin-bottom: 12px;
        }
        .section-subtitle {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 60px;
        }
        
        .menu-card {
            background: var(--card-bg);
            border: 1px solid var(--border-soft);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.4s ease;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .menu-card:hover {
            transform: translateY(-8px);
            border-color: var(--gold);
        }
        .menu-card-img {
            width: 100%;
            aspect-ratio: 16/10;
            object-fit: cover;
            border-bottom: 1px solid var(--border-soft);
        }
        .menu-card-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .menu-card-title {
            font-family: var(--font-serif);
            font-size: 20px;
            color: var(--text-primary);
            margin-bottom: 8px;
        }
        .menu-card-desc {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 24px;
            line-height: 1.5;
            flex-grow: 1;
        }
        .menu-card-price {
            font-size: 16px;
            font-weight: 600;
            color: var(--gold);
            border-top: 1px solid var(--border-soft);
            padding-top: 16px;
            margin-top: auto;
        }

        /* HOW IT WORKS */
        .step-num {
            font-family: var(--font-serif);
            font-size: 48px;
            color: var(--gold);
            opacity: 0.3;
            line-height: 1;
            margin-bottom: 16px;
            font-weight: bold;
        }
        .step-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }
        .step-desc {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* FOOTER */
        footer {
            border-top: 1px solid var(--border-soft);
            padding: 60px 0;
            background-color: #0c0c0c;
        }
        .footer-link {
            color: var(--text-secondary);
            font-size: 12px;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .footer-link:hover {
            color: var(--gold);
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-custom">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="#" class="brand-logo">Lumière</a>
            <div>
                <?php if ($isLoggedIn && ($userRole === 'kasir' || $userRole === 'admin')): ?>
                    <a href="<?= $staffUrl ?>" class="btn-outline-gold">Dashboard Karyawan</a>
                <?php else: ?>
                    <a href="<?= base_url('login.php') ?>" class="btn-outline-gold">Portal Staf</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <header class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <p class="text-gold text-uppercase fw-semibold mb-3" style="font-size: 11px; letter-spacing: 0.15em;">Lumière Smart Order Portal</p>
                    <h1 class="hero-title">Sajian Premium<br>Langsung ke Meja Anda</h1>
                    <p class="hero-desc">Nikmati pengalaman bersantap eksklusif. Pesan hidangan utama, makanan pembuka, hingga hidangan pencuci mulut terbaik langsung dari meja makan Anda melalui portal digital mandiri kami.</p>
                    <a href="<?= $orderUrl ?>" class="btn btn-gold px-5 py-3">Mulai Memesan Sekarang</a>
                </div>
            </div>
        </div>
    </header>

    <!-- FEATURED MENU -->
    <section class="py-5 my-5">
        <div class="container">
            <div class="text-center">
                <h2 class="section-title">Menu Unggulan Kami</h2>
                <p class="section-subtitle">Pilihan hidangan berkualitas premium yang disiapkan dengan dedikasi penuh dari dapur chef.</p>
            </div>
            
            <div class="row g-4 justify-content-center">
                <?php foreach ($featuredMenu as $item): ?>
                    <div class="col-md-4">
                        <article class="menu-card h-100">
                            <img src="<?= htmlspecialchars((string)($item['gambar'] ?? 'assets/images/default.jpg'), ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item['nama_menu'], ENT_QUOTES, 'UTF-8'); ?>" class="menu-card-img">
                            <div class="menu-card-body">
                                <h3 class="menu-card-title"><?= htmlspecialchars($item['nama_menu'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                <p class="menu-card-desc"><?= htmlspecialchars((string)($item['deskripsi'] ?? 'Detail hidangan belum tersedia.'), ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="menu-card-price"><?= rupiah((float)$item['harga']); ?></p>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="py-5 my-5 border-top border-soft">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="section-title">Pemesanan Mandiri dari Meja</h2>
                <p class="section-subtitle">Langkah mudah memesan makanan tanpa perlu mengantre.</p>
            </div>
            
            <div class="row g-4 text-center text-md-start">
                <div class="col-md-3">
                    <div class="step-num">01</div>
                    <h4 class="step-title">Daftar / Masuk Akun</h4>
                    <p class="step-desc">Masuk menggunakan akun member pelanggan Anda agar pesanan tercatat di profil pribadi.</p>
                </div>
                <div class="col-md-3">
                    <div class="step-num">02</div>
                    <h4 class="step-title">Pilih Hidangan</h4>
                    <p class="step-desc">Pilih menu makanan dan minuman premium terbaik kami langsung dari e-catalog.</p>
                </div>
                <div class="col-md-3">
                    <div class="step-num">03</div>
                    <h4 class="step-title">Input Meja & Bayar</h4>
                    <p class="step-desc">Checkout dengan mengisi nomor meja Anda, lalu bayar instan via QRIS atau secara tunai di kasir.</p>
                </div>
                <div class="col-md-3">
                    <div class="step-num">04</div>
                    <h4 class="step-title">Pesanan Disajikan</h4>
                    <p class="step-desc">Pesanan Anda akan dikirim ke dapur chef secara real-time dan diantarkan langsung ke meja Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="text-center">
        <div class="container">
            <p class="text-secondary mb-3" style="font-size: 12px;">© <?= date('Y') ?> Lumière. All Rights Reserved.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="<?= base_url('privacy.php') ?>" class="footer-link">Kebijakan Privasi</a>
                <span class="text-muted" style="font-size: 11px;">&bull;</span>
                <a href="<?= base_url('terms.php') ?>" class="footer-link">Syarat & Ketentuan</a>
            </div>
        </div>
    </footer>

</body>
</html>
