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

$title = 'Detail Menu - ' . $menu['nama_menu'];
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>

<style>
/* FIGMA 1:1 KERN & LEAD PRECISION STYLING */
:root {
    --gold: #C9A84C;
    --gold-dim: rgba(201, 168, 76, 0.15);
    --bg-dark: #131313; /* Exact Figma canvas fill */
    --text-primary: #FFFFFF;
    --text-secondary: #9A8F80;
    --border-soft: rgba(154, 143, 128, 0.15);

    --font-serif: 'Libre Baskerville', serif;
    --font-sans: 'DM Sans', sans-serif;
}

body {
    background-color: var(--bg-dark) !important;
    color: var(--text-primary) !important;
    font-family: var(--font-sans);
    -webkit-font-smoothing: antialiased;
}

/* Page container with precise grids */
.detail-page-container {
    max-width: 1200px;
    margin: 2rem auto 4rem;
    padding: 0 1.5rem;
}

/* Square Media Container - 1:1 Aspect Ratio */
.premium-media-container {
    position: relative;
    width: 100%;
    aspect-ratio: 1 / 1;
    border: 1px solid var(--border-soft);
    background-color: #0d0c0a;
    overflow: hidden;
}

.premium-media-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Linear gradient overlay covering the bottom 30% */
.media-gradient-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 30%;
    background: linear-gradient(to bottom, rgba(19, 19, 19, 0) 0%, rgba(19, 19, 19, 0.9) 100%);
    pointer-events: none;
}

/* Back Button exactly like Figma positioning */
.btn-floating-back {
    position: absolute;
    top: 20px;
    left: 20px;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(10, 10, 10, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: var(--text-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.25s ease;
    z-index: 10;
    text-decoration: none;
}

.btn-floating-back:hover {
    background: #000;
    border-color: var(--gold);
    color: var(--gold);
}

.btn-floating-back svg {
    width: 15px;
    height: 15px;
}

/* Brand Frame Box overlay exactly like Figma branding */
.lumiere-brand-overlay {
    position: absolute;
    top: 72px;
    left: 20px;
    width: 88px;
    height: 88px;
    background: rgba(10, 10, 10, 0.85);
    border: 1px solid rgba(255, 255, 255, 0.08);
    padding: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.lumiere-brand-inner {
    border: 1px solid var(--gold);
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2px;
}

.lumiere-overlay-badge {
    border: 1.5px solid var(--gold);
    border-radius: 50%;
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--font-serif);
    font-size: 10px;
    color: var(--gold);
    font-weight: 400;
    margin-bottom: 2px;
}

.lumiere-overlay-text {
    font-size: 7px;
    color: var(--gold);
    letter-spacing: 0.18em;
    text-transform: uppercase;
    font-weight: 700;
    line-height: 1;
    margin-top: 1px;
}

.lumiere-overlay-subtext {
    font-size: 3.5px;
    color: var(--gold);
    letter-spacing: 0.08em;
    text-transform: uppercase;
    opacity: 0.7;
    margin-top: 1px;
}

/* Right Content Column - Ultra Neat Spacing */
.info-column {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.category-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem; /* Tight spacing */
}

.detail-category-label {
    font-family: var(--font-sans);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    color: var(--text-secondary);
    text-transform: uppercase;
    margin: 0;
}

/* Floating Heart Icon */
.btn-floating-heart-inline {
    background: none;
    border: 1px solid rgba(255, 255, 255, 0.15);
    width: 36px;
    height: 36px;
    border-radius: 50%;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.25s ease;
}

.btn-floating-heart-inline:hover,
.btn-floating-heart-inline.active {
    border-color: var(--gold);
    color: var(--gold);
    background: rgba(201, 168, 76, 0.1);
}

.btn-floating-heart-inline.active svg {
    fill: var(--gold);
    stroke: var(--gold);
}

.btn-floating-heart-inline svg {
    width: 14px;
    height: 14px;
}

/* Elegant editorial titles */
.detail-menu-title {
    font-family: var(--font-serif);
    font-size: 3rem; /* Highly balanced */
    font-weight: 400;
    line-height: 1.15;
    color: var(--gold);
    margin-bottom: 0.5rem; /* Tight spacing to price */
    letter-spacing: -0.01em;
}

.detail-menu-price {
    font-family: var(--font-sans);
    font-size: 1.3rem;
    font-weight: 600;
    color: var(--gold);
    margin-bottom: 1.25rem; /* Tight spacing to description */
}

.detail-menu-desc {
    font-family: var(--font-sans);
    font-size: 0.88rem;
    line-height: 1.7;
    color: var(--text-secondary);
    margin-bottom: 1.5rem; /* Tight spacing to separators */
}

/* Subtle line separators */
.detail-separator {
    border: none;
    border-top: 1px solid var(--border-soft);
    margin: 1rem 0; /* Tight separator margins */
    width: 100%;
}

/* Composition tags */
.allergens-section {
    margin-bottom: 0.75rem;
}

.allergens-title {
    font-family: var(--font-sans);
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    color: var(--text-secondary);
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    text-transform: uppercase;
}

.allergens-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.allergen-tag {
    font-family: var(--font-sans);
    font-size: 9.5px;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    padding: 6px 12px;
    background: transparent;
    border: 1px solid rgba(255, 255, 255, 0.08);
    color: var(--text-secondary);
    border-radius: 0;
}

.allergen-tag.allergen-highlight {
    border-color: rgba(201, 168, 76, 0.35);
    color: var(--gold);
}

/* Stepper and Button panel */
.action-panel {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 0.5rem;
}

.qty-stepper-premium {
    display: flex;
    align-items: center;
    border: 1px solid rgba(255, 255, 255, 0.12);
    height: 42px;
    background: transparent;
    border-radius: 0;
}

.stepper-btn {
    background: none;
    border: none;
    width: 38px;
    height: 100%;
    color: var(--text-primary);
    font-size: 16px;
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
    width: 36px;
    text-align: center;
    font-family: var(--font-sans);
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
}

.btn-luxury-submit {
    flex-grow: 1;
    height: 42px;
    background: var(--gold);
    border: none;
    color: #131313;
    font-family: var(--font-sans);
    font-weight: 700;
    font-size: 0.75rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    cursor: pointer;
    transition: all 0.25s ease;
    border-radius: 0;
}

.btn-luxury-submit:hover {
    background: #e2be59;
    transform: translateY(-1px);
}

/* LUXURY TOAST NOTIFICATION */
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
    font-family: var(--font-sans);
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

<div class="detail-page-container">
    <div class="row g-5">
        <!-- Media Column -->
        <div class="col-lg-6">
            <div class="premium-media-container shadow-lg animate-fade-in-up">
                <!-- Floating Back Button -->
                <a href="<?= htmlspecialchars(base_url('pelanggan/dashboard.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn-floating-back">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                </a>

                <!-- Lumiere Emblem Box Badge -->
                <div class="lumiere-brand-overlay">
                    <div class="lumiere-brand-inner">
                        <div class="lumiere-overlay-badge">L</div>
                        <span class="lumiere-overlay-text">Lumière</span>
                        <span class="lumiere-overlay-subtext">Resto & Lounge</span>
                    </div>
                </div>

                <!-- Product image with a precise 1:1 rendering -->
                <img src="<?= htmlspecialchars($menu['gambar'] ?: 'https://placehold.co/1200x800?text=Menu'); ?>" alt="<?= htmlspecialchars($menu['nama_menu'], ENT_QUOTES, 'UTF-8'); ?>" class="premium-media-img">

                <!-- Fades out the bottom edge beautifully -->
                <div class="media-gradient-overlay"></div>
            </div>
        </div>

        <!-- Info Column -->
        <article class="col-lg-6 info-column animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="ps-lg-3">
                <div class="category-row">
                    <p class="detail-category-label"><?= htmlspecialchars($menu['nama_kategori']); ?> • SIGNATURE SELECTION</p>
                    
                    <!-- Favorite Button aligned with category label -->
                    <button class="btn-floating-heart-inline" onclick="this.classList.toggle('active');">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                    </button>
                </div>

                <h1 class="detail-menu-title"><?= htmlspecialchars($menu['nama_menu']); ?></h1>
                <p class="detail-menu-price">Rp <?= number_format((float)$menu['harga'], 0, ',', '.'); ?></p>

                <p class="detail-menu-desc">
                    <?= htmlspecialchars($menu['deskripsi'] ?? 'Tidak ada deskripsi untuk hidangan ini.'); ?>
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
                        KOMPOSISI & ALERGI
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
                <form method="post" action="<?= htmlspecialchars(base_url('actions/tambah_keranjang.php'), ENT_QUOTES, 'UTF-8'); ?>" class="js-detail-cart-form m-0">
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
        </article>
    </div>
</div>

<!-- Luxury Toast Notification Container -->
<div class="luxury-toast-container" id="toastContainer"></div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toastContainer = document.getElementById('toastContainer');
    const form = document.querySelector('.js-detail-cart-form');

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'luxury-toast';
        toast.innerHTML = `
            <div class="luxury-toast-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <div class="luxury-toast-content">${message}</div>
            <button class="luxury-toast-close">&times;</button>
        `;

        toastContainer.appendChild(toast);
        toast.offsetHeight;
        toast.classList.add('show');

        toast.querySelector('.luxury-toast-close').addEventListener('click', () => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 500);
        });

        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 500);
            }
        }, 3500);
    }

    form?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = form.querySelector('.btn-luxury-submit');
        if (submitBtn) submitBtn.disabled = true;

        try {
            const formData = new FormData(form);
            formData.append('ajax', '1');

            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                showToast(data.message);
                const cartCountEl = document.querySelector('.js-cart-count');
                if (cartCountEl) {
                    cartCountEl.textContent = data.cart_count;
                    cartCountEl.style.display = data.cart_count > 0 ? 'inline-block' : 'none';
                }
            } else {
                showToast(data.message || 'Gagal menambahkan ke keranjang.');
            }
        } catch (err) {
            console.error(err);
            showToast('Koneksi internet bermasalah. Silakan coba lagi.');
        } finally {
            if (submitBtn) submitBtn.disabled = false;
        }
    });
});
</script>

<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Art Culinaire",
    'eyebrow' => 'Detail Menu',
    'title' => htmlspecialchars($menu['nama_menu']),
    'description' => 'Detail hidangan eksklusif dari Lumière.',
    'hide_hero' => true
], $content);
require __DIR__ . '/../includes/footer.php';
