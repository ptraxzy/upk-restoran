<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_role('pelanggan');

$title = 'Dashboard';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';
ob_start();
?>

<!-- Import Premium Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">

<style>
/* CORE DESIGN SYSTEM - DARK LUXURY */
:root {
    --gold: #E9C176;
    --gold-dim: rgba(233, 193, 118, 0.2);
    --bg-dark: #050505;
    --card-bg: rgba(15, 15, 15, 0.6);
    --text-primary: #E5E2E1;
    --text-secondary: #9A8F80;
    --border-soft: rgba(154, 143, 128, 0.15);

    --font-serif: 'Playfair Display', serif;
    --font-sans: 'Inter', sans-serif;
}

body {
    background-color: var(--bg-dark) !important;
    color: var(--text-primary) !important;
    font-family: var(--font-sans);
    -webkit-font-smoothing: antialiased;
}

/* OVERRIDE CONTAINER FOR FULL BLEED HERO */
.full-bleed {
    width: 100vw;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    margin-top: -1.5rem; /* negate the container py-4 from shell */
}

/* HERO SECTION */
.luxury-hero {
    position: relative;
    height: 75vh;
    min-height: 600px;
    display: flex;
    align-items: flex-end;
    padding-bottom: 5rem;
    border-bottom: 1px solid var(--border-soft);
    overflow: hidden;
}

.luxury-hero-bg {
    position: absolute;
    inset: 0;
    background-image: url("https://images.unsplash.com/photo-1558030006-450675393462?auto=format&fit=crop&w=1920&q=80");
    background-size: cover;
    background-position: center 60%;
    opacity: 0.35;
    mix-blend-mode: luminosity;
    transform: scale(1.05);
    transition: transform 10s ease-out;
}

.luxury-hero:hover .luxury-hero-bg {
    transform: scale(1);
}

.luxury-hero-gradient {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to bottom,
        rgba(5, 5, 5, 0.2) 0%,
        rgba(5, 5, 5, 0.6) 60%,
        var(--bg-dark) 100%
    );
}

.hero-content {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 2rem;
}

.eyebrow {
    font-family: var(--font-sans);
    font-weight: 600;
    font-size: 0.75rem;
    letter-spacing: 0.15em;
    color: var(--gold);
    text-transform: uppercase;
    margin-bottom: 1.5rem;
    display: block;
}

.hero-title {
    font-family: var(--font-serif);
    font-size: clamp(3rem, 6vw, 5rem);
    line-height: 1.1;
    font-weight: 400;
    color: var(--text-primary);
    margin-bottom: 2rem;
    letter-spacing: -0.02em;
}

.hero-desc {
    font-family: var(--font-sans);
    font-size: 1.125rem;
    line-height: 1.7;
    color: var(--text-secondary);
    max-width: 480px;
    padding-left: 1.5rem;
    border-left: 1px solid var(--gold-dim);
}

/* MENU SECTION */
.menu-discovery {
    max-width: 1280px;
    margin: 4rem auto 6rem;
    padding: 0 1rem;
}

/* CATEGORY TABS */
.category-tabs {
    display: flex;
    gap: 3rem;
    border-bottom: 1px solid var(--border-soft);
    margin-bottom: 4rem;
    overflow-x: auto;
    scrollbar-width: none;
}
.category-tabs::-webkit-scrollbar { display: none; }

.tab-btn {
    background: none;
    border: none;
    padding: 0 0 1rem 0;
    font-family: var(--font-sans);
    font-weight: 600;
    font-size: 0.75rem;
    letter-spacing: 0.15em;
    color: var(--text-secondary);
    text-transform: uppercase;
    position: relative;
    cursor: pointer;
    transition: color 0.3s ease;
    white-space: nowrap;
}

.tab-btn:hover {
    color: var(--text-primary);
}

.tab-btn.active {
    color: var(--gold);
}

.tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: var(--gold);
}

/* BENTO GRID */
.bento-grid {
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    gap: 2rem;
}

@media (min-width: 992px) {
    .bento-grid {
        grid-template-columns: repeat(12, 1fr);
        grid-template-rows: auto auto;
    }
}

.bento-card {
    background: var(--card-bg);
    border: 1px solid var(--border-soft);
    display: flex;
    flex-direction: column;
    transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.4s ease;
    overflow: hidden;
}

.bento-card:hover {
    transform: translateY(-4px);
    border-color: var(--gold-dim);
}

.card-img-wrapper {
    position: relative;
    overflow: hidden;
}

.card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

.bento-card:hover .card-img {
    transform: scale(1.05);
}

.card-content {
    padding: 2rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.card-title {
    font-family: var(--font-serif);
    font-size: 2rem;
    font-weight: 400;
    color: var(--text-primary);
    margin-bottom: 1rem;
    letter-spacing: -0.01em;
}

.card-desc {
    font-family: var(--font-sans);
    font-size: 0.9rem;
    line-height: 1.6;
    color: var(--text-secondary);
    margin-bottom: 2rem;
    flex-grow: 1;
}

.card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
}

.card-price {
    font-family: var(--font-sans);
    font-size: 1.25rem;
    color: var(--gold);
}

.btn-add {
    background: none;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    font-family: var(--font-sans);
    font-weight: 600;
    font-size: 0.7rem;
    letter-spacing: 0.1em;
    color: var(--gold);
    text-transform: uppercase;
    cursor: pointer;
    padding: 0;
    transition: opacity 0.3s ease;
}

.btn-add:hover {
    opacity: 0.8;
}

.btn-add svg {
    width: 10px;
    height: 10px;
}

/* SPECIFIC GRID PLACEMENTS */
@media (min-width: 992px) {
    .item-large {
        grid-column: 1 / 8;
        grid-row: 1 / 2;
    }

    .item-tall {
        grid-column: 8 / 13;
        grid-row: 1 / 1;
    }

    .item-standard-1 {
        grid-column: 1 / 5;
        grid-row: 2 / 3;
    }

    .item-standard-2 {
        grid-column: 5 / 9;
        grid-row: 2 / 3;
    }

    .item-standard-3 {
        grid-column: 9 / 13;
        grid-row: 2 / 3;
    }
}

/* Adjustments for specific cards */
.item-large .card-img-wrapper {
    height: 380px;
}
.item-large .card-content {
    flex-direction: row;
    align-items: flex-start;
    gap: 2rem;
    padding: 2.5rem;
}
.item-large .card-text-wrapper {
    flex: 1;
}
.item-large .card-title {
    font-size: 2.5rem;
}
.item-large .card-footer {
    flex-direction: column;
    align-items: flex-end;
    gap: 1.5rem;
    margin-top: 0;
}
.item-large .card-price {
    font-size: 2rem;
}

.item-tall .card-img-wrapper {
    height: 480px;
}
.item-tall .card-title {
    font-size: 2rem;
}

.item-standard-1 .card-img-wrapper,
.item-standard-2 .card-img-wrapper,
.item-standard-3 .card-img-wrapper {
    height: 240px;
}
.item-standard-1 .card-title,
.item-standard-2 .card-title,
.item-standard-3 .card-title {
    font-size: 1.75rem;
}
.item-standard-1 .card-footer,
.item-standard-2 .card-footer,
.item-standard-3 .card-footer {
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-soft);
}

</style>

<div class="full-bleed">
    <section class="luxury-hero">
        <div class="luxury-hero-bg"></div>
        <div class="luxury-hero-gradient"></div>

        <div class="hero-content">
            <span class="eyebrow">Pencicipan Khas</span>
            <h1 class="hero-title">Simfoni<br>Musim Gugur</h1>
            <p class="hero-desc">Perjalanan terkurasi melalui rasa musiman, menonjolkan keseimbangan halus antara bumi dan laut.</p>
        </div>
    </section>
</div>

<div class="menu-discovery">
    <nav class="category-tabs">
        <button class="tab-btn active">SEMUA</button>
        <button class="tab-btn">PEMBUKA</button>
        <button class="tab-btn">UTAMA</button>
        <button class="tab-btn">PENCUCI MULUT</button>
    </nav>

    <div class="bento-grid">

        <!-- Large Item: Wagyu -->
        <article class="bento-card item-large">
            <div class="card-img-wrapper">
                <img src="https://images.unsplash.com/photo-1558030006-450675393462?auto=format&fit=crop&w=1200&q=80" alt="A5 Wagyu Ribeye" class="card-img">
            </div>
            <div class="card-content">
                <div class="card-text-wrapper">
                    <h2 class="card-title">A5 Wagyu Ribeye</h2>
                    <p class="card-desc">Marmer yang luar biasa, dipanggang dengan presisi. Disajikan dengan pure kentang truffle hitam, wortel pusaka panggang, dan jus sumsum tulang yang kaya.</p>
                </div>
                <div class="card-footer">
                    <span class="card-price">Rp1.100.000</span>
                    <button class="btn-add">
                        Tambah Ke<br>Keranjang
                        <svg viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 0V10M0 5H10" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </button>
                </div>
            </div>
        </article>

        <!-- Tall Item: Mille Crepe -->
        <article class="bento-card item-tall">
            <div class="card-img-wrapper">
                <img src="https://images.unsplash.com/photo-1511690656952-34342bb7c2f2?auto=format&fit=crop&w=800&q=80" alt="Matcha Mille Crêpe" class="card-img">
            </div>
            <div class="card-content">
                <h2 class="card-title">Matcha Mille Crêpe</h2>
                <p class="card-desc">Dua puluh lapis crêpes buatan tangan yang lembut diinfus dengan matcha Uji premium, krim mascarpone ringan, dan taburan debu emas yang dapat dimakan.</p>
                <div class="card-footer">
                    <span class="card-price">Rp105.000</span>
                    <button class="btn-add">
                        Tambah
                        <svg viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 0V10M0 5H10" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </button>
                </div>
            </div>
        </article>

        <!-- Standard Item 1: Tartare -->
        <article class="bento-card item-standard-1">
            <div class="card-img-wrapper">
                <img src="https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?auto=format&fit=crop&w=800&q=80" alt="Tartare Sirip Biru" class="card-img">
            </div>
            <div class="card-content">
                <h2 class="card-title">Tartare Sirip Biru</h2>
                <p class="card-desc">Mousse alpukat, emulsi ponzu, akar talas renyah.</p>
                <div class="card-footer">
                    <span class="card-price">Rp175.000</span>
                    <button class="btn-add">
                        Tambah
                        <svg viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 0V10M0 5H10" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </button>
                </div>
            </div>
        </article>

        <!-- Standard Item 2: Duck Breast -->
        <article class="bento-card item-standard-2">
            <div class="card-img-wrapper">
                <img src="https://images.unsplash.com/photo-1559480674-3112060688b1?auto=format&fit=crop&w=800&q=80" alt="Bebek Warisan" class="card-img">
            </div>
            <div class="card-content">
                <h2 class="card-title">Bebek Warisan</h2>
                <p class="card-desc">Dada yang dikeringkan, kroket kaki confit, ceri yang diawetkan, akar seledri.</p>
                <div class="card-footer">
                    <span class="card-price">Rp280.000</span>
                    <button class="btn-add">
                        Tambah
                        <svg viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 0V10M0 5H10" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </button>
                </div>
            </div>
        </article>

        <!-- Standard Item 3: Hokkaido Scallop -->
        <article class="bento-card item-standard-3">
            <div class="card-img-wrapper">
                <img src="https://images.unsplash.com/photo-1587174486073-ae5e5c693a02?auto=format&fit=crop&w=800&q=80" alt="Kerang Hokkaido" class="card-img">
            </div>
            <div class="card-content">
                <h2 class="card-title">Kerang Hokkaido</h2>
                <p class="card-desc">Dipanggang, pure kacang polong manis, guanciale renyah, saus mentega lemon meyer.</p>
                <div class="card-footer">
                    <span class="card-price">Rp220.000</span>
                    <button class="btn-add">
                        Tambah
                        <svg viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 0V10M0 5H10" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </button>
                </div>
            </div>
        </article>

    </div>
</div>

<?php
$content = ob_get_clean();
// Hide the default public shell hero section since we built a custom luxury hero
render_public_shell(['title' => 'Menu & Ordering', 'hide_hero' => true], $content);
require __DIR__ . '/../includes/footer.php';
