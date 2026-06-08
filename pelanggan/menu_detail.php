<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('pelanggan');

require_once __DIR__ . '/../includes/database.php';

$id = $_GET['id'] ?? null;
$menu = null;

if ($id) {
    $stmt = db()->prepare("
        SELECT m.*, k.nama_kategori
        FROM menu m
        JOIN kategori k ON m.id_kategori = k.id_kategori
        WHERE m.id_menu = ? AND m.deleted_at IS NULL
    ");
    $stmt->execute([$id]);
    $menu = $stmt->fetch();
}

if (!$menu) {
    set_flash('error', 'Menu tidak ditemukan.');
    redirect(base_url('pelanggan/dashboard.php'));
}

// Tailor composition and allergens tags based on menu name (High fidelity to Figma design)
$komposisi = [];
$allergens = [];
$menuLower = strtolower($menu['nama_menu']);
if (str_contains($menuLower, 'scallop')) {
    $komposisi = ['HOKKAIDO SCALLOP', 'YUZU PLUM', 'SOY MILK', 'MIRIN ORANGE'];
    $allergens = ['ALERGEN: SEAFOOD'];
} elseif (str_contains($menuLower, 'wagyu') || str_contains($menuLower, 'ribeye')) {
    $komposisi = ['A5 JAPANESE WAGYU', 'BLACK GARLIC BUTTER', 'SMOKED SEA SALT'];
    $allergens = ['ALERGEN: DAIRY (BUTTER)'];
} elseif (str_contains($menuLower, 'duck')) {
    $komposisi = ['DRY-AGED DUCK BREAST', 'CHERRY REDUCTION', 'PARSNIP PUREE'];
    $allergens = ['ALERGEN: GLUTEN-FREE'];
} elseif (str_contains($menuLower, 'risotto') || str_contains($menuLower, 'truffle')) {
    $komposisi = ['BERAS CARNAROLI', 'JAMUR PORCINI', 'BLACK TRUFFLE'];
    $allergens = ['ALERGEN: SUSU (DAIRY)'];
} else {
    $komposisi = ['PREMIUM INGREDIENTS', 'SECRET SPICES', "CHEF'S SIGNATURE"];
    $allergens = ['ALERGEN: BEBAS ALERGI UTAMA'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($menu['nama_menu']); ?> - Lumière</title>
    <!-- Load identical premium Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <!-- Clean reset and custom full-bleed split styling -->
    <style>
        :root {
            --gold: #C9A84C;
            --gold-hover: #e2be59;
            --bg-dark: #131313; /* Exact Figma Fill color */
            --text-primary: #FFFFFF;
            --text-secondary: #9A8F80;
            --border-soft: rgba(154, 143, 128, 0.15);
            --font-serif: 'Libre Baskerville', serif;
            --font-sans: 'DM Sans', sans-serif;
        }

        /* Full viewport reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-primary);
            font-family: var(--font-sans);
            -webkit-font-smoothing: antialiased;
            overflow: hidden; /* Lock viewport scroll */
        }

        /* 50/50 Split Viewport Grid */
        .split-viewport {
            display: flex;
            width: 100vw;
            height: 100vh;
        }

        /* Left Side (Full Bleed Media) */
        .split-left {
            width: 50%;
            height: 100%;
            position: relative;
            background-color: #0d0c0a;
            overflow: hidden;
        }

        .premium-media-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Bottom masking gradient */
        .media-gradient-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 35%;
            background: linear-gradient(to bottom, rgba(19, 19, 19, 0) 0%, rgba(19, 19, 19, 0.9) 100%);
            pointer-events: none;
        }

        /* Floating circular back button in image corner */
        .btn-floating-back {
            position: absolute;
            top: 40px;
            left: 40px;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(10, 10, 10, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.25s ease;
            z-index: 20;
            text-decoration: none;
        }

        .btn-floating-back:hover {
            background: #000;
            border-color: var(--gold);
            color: var(--gold);
        }

        .btn-floating-back svg {
            width: 16px;
            height: 16px;
        }

        /* Brand Emblem Overlay exactly matching Figma visual specifications */
        .lumiere-brand-overlay {
            position: absolute;
            top: 108px;
            left: 40px;
            width: 110px;
            height: 110px;
            background: rgba(10, 10, 10, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 20;
        }

        .lumiere-brand-inner {
            border: 1px solid var(--gold);
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 4px;
        }

        .lumiere-overlay-badge {
            border: 1.5px solid var(--gold);
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--font-serif);
            font-size: 13px;
            color: var(--gold);
            font-weight: 400;
            margin-bottom: 4px;
        }

        .lumiere-overlay-text {
            font-size: 8.5px;
            color: var(--gold);
            letter-spacing: 0.2em;
            text-transform: uppercase;
            font-weight: 700;
            line-height: 1;
            margin-top: 1px;
        }

        .lumiere-overlay-subtext {
            font-size: 4px;
            color: var(--gold);
            letter-spacing: 0.1em;
            text-transform: uppercase;
            opacity: 0.7;
            margin-top: 2px;
        }

        /* Right Side (Content Scroll Pane) */
        .split-right {
            width: 50%;
            height: 100%;
            background-color: var(--bg-dark);
            overflow-y: auto;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 80px 10%; /* Elegant, spacious layout */
        }

        /* Right top action and labeling */
        .top-action-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .detail-category-label {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            color: var(--text-secondary);
            text-transform: uppercase;
        }

        .btn-fav-circle {
            background: none;
            border: 1px solid rgba(255, 255, 255, 0.15);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .btn-fav-circle:hover,
        .btn-fav-circle.active {
            border-color: var(--gold);
            color: var(--gold);
            background: rgba(201, 168, 76, 0.1);
        }

        .btn-fav-circle.active svg {
            fill: var(--gold);
            stroke: var(--gold);
        }

        .btn-fav-circle svg {
            width: 15px;
            height: 15px;
        }

        /* Heading & Pricing */
        .detail-menu-title {
            font-family: var(--font-serif);
            font-size: 3.5rem;
            font-weight: 400;
            line-height: 1.1;
            color: var(--gold);
            margin-bottom: 1rem;
            letter-spacing: -0.01em;
        }

        .detail-menu-price {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gold);
            margin-bottom: 2rem;
        }

        .detail-menu-desc {
            font-size: 0.95rem;
            line-height: 1.8;
            color: var(--text-secondary);
            margin-bottom: 2.25rem;
            max-width: 95%;
        }

        /* Subtle dividers */
        .detail-separator {
            border: none;
            border-top: 1px solid var(--border-soft);
            margin: 1.5rem 0;
            width: 100%;
        }

        /* Composition & Allergens Panel */
        .allergens-title {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            color: var(--text-secondary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            text-transform: uppercase;
        }

        .allergens-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .allergen-tag {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding: 8px 16px;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: var(--text-secondary);
            border-radius: 0;
        }

        .allergen-tag.allergen-highlight {
            border-color: rgba(201, 168, 76, 0.4);
            color: var(--gold);
        }

        /* Action Panel */
        .action-panel {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            margin-top: 0.5rem;
        }

        .qty-stepper-premium {
            display: flex;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, 0.15);
            height: 48px;
            background: transparent;
            border-radius: 0;
        }

        .stepper-btn {
            background: none;
            border: none;
            width: 44px;
            height: 100%;
            color: var(--text-primary);
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.25s ease;
        }

        .stepper-btn:hover {
            color: var(--gold);
        }

        .stepper-display {
            width: 40px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .btn-luxury-submit {
            flex-grow: 1;
            height: 48px;
            background: var(--gold);
            border: none;
            color: #131313;
            font-weight: 700;
            font-size: 0.78rem;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.25s ease;
            border-radius: 0;
        }

        .btn-luxury-submit:hover {
            background: var(--gold-hover);
            transform: translateY(-1px);
        }

        /* TOAST ALERT OVERLAYS */
        .luxury-toast-container {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            pointer-events: none;
        }

        .luxury-toast {
            background: rgba(14, 28, 18, 0.95);
            border: 1px solid rgba(40, 167, 69, 0.4);
            color: #e5e2e1;
            padding: 16px 24px;
            min-width: 320px;
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            gap: 14px;
            transform: translateX(120%);
            transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.5s ease;
            opacity: 0;
            pointer-events: auto;
        }

        .luxury-toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .luxury-toast-icon {
            color: #28a745;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
        }

        .luxury-toast-content {
            font-size: 13px;
            line-height: 1.4;
        }

        .luxury-toast-close {
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 4px;
            margin-left: auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>

<div class="split-viewport">
    <!-- Left Side: Image Content -->
    <div class="split-left">
        <!-- Floating circular back button in image corner -->
        <a href="<?= htmlspecialchars(base_url('pelanggan/menu.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn-floating-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>

        <!-- Lumiere Logo Frame badge inside image corner -->
        <div class="lumiere-brand-overlay">
            <div class="lumiere-brand-inner">
                <div class="lumiere-overlay-badge">L</div>
                <span class="lumiere-overlay-text">Lumière</span>
                <span class="lumiere-overlay-subtext">Resto & Lounge</span>
            </div>
        </div>

        <img src="<?= htmlspecialchars(menu_image($menu['gambar'] ?? '')); ?>" alt="<?= htmlspecialchars($menu['nama_menu'], ENT_QUOTES, 'UTF-8'); ?>" class="premium-media-img">
        <div class="media-gradient-overlay"></div>
    </div>

    <!-- Right Side: Text & Actions Scroll Pane -->
    <div class="split-right">
        <div class="top-action-row">
            <span class="detail-category-label">
                <?= htmlspecialchars($menu['nama_kategori']); ?> • SIGNATURE SELECTION
            </span>
            <button class="btn-fav-circle" onclick="this.classList.toggle('active')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
            </button>
        </div>

        <h1 class="detail-menu-title"><?= htmlspecialchars($menu['nama_menu']); ?></h1>
        <p class="detail-menu-price">Rp <?= number_format((float)$menu['harga'], 0, ',', '.'); ?></p>

        <p class="detail-menu-desc">
            <?= htmlspecialchars($menu['deskripsi'] ?: 'Sebuah mahakarya cita rasa kuliner terbaik yang diolah secara presisi menggunakan bahan-bahan segar berkualitas tinggi demi sensasi rasa yang memikat.'); ?>
        </p>

        <hr class="detail-separator">

        <!-- Composition & Allergens Panel -->
        <div class="allergens-section">
            <h3 class="allergens-title">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px; vertical-align: middle;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                ALERGI
            </h3>
            <div class="allergens-grid">
                <?php foreach ($komposisi as $k): ?>
                    <span class="allergen-tag"><?= htmlspecialchars($k); ?></span>
                <?php endforeach; ?>
                <?php foreach ($allergens as $a): ?>
                    <span class="allergen-tag allergen-highlight"><?= htmlspecialchars($a); ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <hr class="detail-separator">

        <!-- Stepper and Cart Actions -->
        <form method="post" action="<?= htmlspecialchars(base_url('actions/tambah_keranjang.php'), ENT_QUOTES, 'UTF-8'); ?>" class="js-detail-cart-form">
            <div class="action-panel">
                <div class="qty-stepper-premium">
                    <button type="button" class="stepper-btn" onclick="const i = document.getElementById('qty'); i.value = Math.max(1, parseInt(i.value) - 1); document.getElementById('qty-display').innerText = i.value;">&minus;</button>
                    <span id="qty-display" class="stepper-display">1</span>
                    <input type="hidden" name="id_menu" value="<?= htmlspecialchars((string)$menu['id_menu']); ?>">
                    <input type="hidden" name="qty" id="qty" value="1">
                    <button type="button" class="stepper-btn" onclick="const i = document.getElementById('qty'); i.value = parseInt(i.value) + 1; document.getElementById('qty-display').innerText = i.value;">&plus;</button>
                </div>
                
                <button type="submit" class="btn-luxury-submit">
                    TAMBAH KE KERANJANG
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
