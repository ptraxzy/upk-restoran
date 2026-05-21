<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('pelanggan');

$selectedCategory = isset($_GET['category']) ? (int) $_GET['category'] : 0;

// Fetch active menus
if ($selectedCategory > 0) {
    $stmt = db()->prepare("SELECT * FROM menu WHERE status = 'Tersedia' AND deleted_at IS NULL AND id_kategori = ? ORDER BY id_menu ASC LIMIT 5");
    $stmt->execute([$selectedCategory]);
} else {
    $stmt = db()->query("SELECT * FROM menu WHERE status = 'Tersedia' AND deleted_at IS NULL ORDER BY id_menu ASC LIMIT 5");
}
$items = $stmt->fetchAll();

// Handle AJAX menu request
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    header('Content-Type: application/json');
    $formattedItems = [];
    foreach ($items as $item) {
        $formattedItems[] = [
            'id_menu' => $item['id_menu'],
            'nama_menu' => $item['nama_menu'],
            'deskripsi' => $item['deskripsi'] ?: 'Detail hidangan belum tersedia.',
            'harga_formatted' => rupiah((float) $item['harga']),
            'gambar' => $item['gambar'] ?: 'https://placehold.co/1200x800?text=Menu',
            'detail_url' => base_url('pelanggan/menu_detail.php?id=' . $item['id_menu']),
            'action_url' => base_url('actions/tambah_keranjang.php')
        ];
    }
    echo json_encode([
        'success' => true,
        'items' => $formattedItems,
        'selectedCategory' => $selectedCategory
    ]);
    exit;
}

$title = 'Dashboard';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

$stmtKat = db()->query("SELECT * FROM kategori ORDER BY id_kategori ASC");
$categories = $stmtKat->fetchAll();

ob_start();
?>

<style>
/* CORE DESIGN SYSTEM - DARK LUXURY */
:root {
    --gold: #C9A84C;
    --gold-dim: rgba(201, 168, 76, 0.15);
    --bg-dark: #131313;
    --card-bg: rgba(15, 15, 15, 0.6);
    --text-primary: #E5E2E1;
    --text-secondary: #9A8F80;
    --border-soft: rgba(154, 143, 128, 0.15);

    --font-serif: var(--font-display, 'Libre Baskerville', serif);
    --font-sans: var(--font-body, 'DM Sans', sans-serif);
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
    letter-spacing: 0.06em;
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
    display: inline-block;
    padding: 0 0 1rem 0;
    font-family: var(--font-sans);
    font-weight: 600;
    font-size: 0.75rem;
    letter-spacing: 0.06em;
    color: var(--text-secondary);
    position: relative;
    text-decoration: none;
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

/* LUXURY UNIFORM CATALOG GRID */
.menu-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2.5rem;
}

@media (min-width: 768px) {
    .menu-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1200px) {
    .menu-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

.menu-card {
    background: var(--card-bg);
    border: 1px solid var(--border-soft);
    display: flex;
    flex-direction: column;
    height: 100%;
    transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.4s ease, box-shadow 0.4s ease;
    overflow: hidden;
    position: relative;
}

.menu-card:hover {
    transform: translateY(-6px);
    border-color: var(--gold);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.5);
}

.menu-card-img-wrapper {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 10;
    overflow: hidden;
    background: #0d0c0a;
    border-bottom: 1px solid var(--border-soft);
}

.menu-card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

.menu-card:hover .menu-card-img {
    transform: scale(1.06);
}

.menu-card-body {
    padding: 1.75rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.menu-card-title {
    font-family: var(--font-serif);
    font-size: 1.5rem;
    font-weight: 400;
    line-height: 1.3;
    color: var(--text-primary);
    margin-bottom: 0.75rem;
}

.menu-card-title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.menu-card-title a:hover {
    color: var(--gold);
}

.menu-card-desc {
    font-family: var(--font-sans);
    font-size: 0.875rem;
    line-height: 1.6;
    color: var(--text-secondary);
    margin-bottom: 1.75rem;
    flex-grow: 1;
}

.menu-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 1.25rem;
    border-top: 1px solid var(--border-soft);
    margin-top: auto;
}

.menu-card-price {
    font-family: var(--font-sans);
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--gold);
    letter-spacing: 0.02em;
}

.btn-add-to-cart {
    background: none;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-family: var(--font-sans);
    font-weight: 600;
    font-size: 0.75rem;
    letter-spacing: 0.08em;
    color: var(--gold);
    text-transform: uppercase;
    cursor: pointer;
    padding: 0;
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.btn-add-to-cart:hover {
    opacity: 0.8;
    transform: translateX(2px);
}

.btn-add-to-cart svg {
    width: 10px;
    height: 10px;
    transition: transform 0.3s ease;
}
.btn-add-to-cart:hover svg {
    transform: rotate(90deg);
}

.btn-add-form {
    margin: 0;
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
    flex-grow: 1;
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
    transition: color 0.3s ease;
}

.luxury-toast-close:hover {
    color: var(--text-primary);
}

.menu-grid.loading {
    opacity: 0.5;
    pointer-events: none;
    transition: opacity 0.3s ease;
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
        <a href="<?= htmlspecialchars(base_url('pelanggan/dashboard.php'), ENT_QUOTES, 'UTF-8'); ?>" class="tab-btn <?= $selectedCategory === 0 ? 'active' : ''; ?>">SEMUA</a>
        <?php foreach ($categories as $category): ?>
            <a
                href="<?= htmlspecialchars(base_url('pelanggan/dashboard.php?category=' . $category['id_kategori']), ENT_QUOTES, 'UTF-8'); ?>"
                class="tab-btn <?= $selectedCategory === (int) $category['id_kategori'] ? 'active' : ''; ?>"
            >
                <?= htmlspecialchars(strtoupper((string) $category['nama_kategori']), ENT_QUOTES, 'UTF-8'); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="menu-grid">
        <?php if (empty($items)): ?>
            <article class="menu-card" style="grid-column: 1 / -1; min-height: 200px; justify-content: center; align-items: center;">
                <div class="menu-card-body text-center">
                    <h2 class="menu-card-title">Belum Ada Menu</h2>
                    <p class="menu-card-desc mb-0">Belum ada hidangan tersedia untuk kategori ini.</p>
                </div>
            </article>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
            <article class="menu-card animate-fade-in-up">
                <div class="menu-card-img-wrapper">
                    <a href="<?= htmlspecialchars(base_url('pelanggan/menu_detail.php?id=' . $item['id_menu']), ENT_QUOTES, 'UTF-8'); ?>">
                        <img src="<?= htmlspecialchars((string) ($item['gambar'] ?: 'https://placehold.co/1200x800?text=Menu'), ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item['nama_menu'], ENT_QUOTES, 'UTF-8'); ?>" class="menu-card-img">
                    </a>
                </div>
                <div class="menu-card-body">
                    <h2 class="menu-card-title">
                        <a href="<?= htmlspecialchars(base_url('pelanggan/menu_detail.php?id=' . $item['id_menu']), ENT_QUOTES, 'UTF-8'); ?>">
                            <?= htmlspecialchars($item['nama_menu'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </h2>
                    <p class="menu-card-desc"><?= htmlspecialchars((string) ($item['deskripsi'] ?: 'Detail hidangan belum tersedia.'), ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="menu-card-footer">
                        <span class="menu-card-price"><?= rupiah((float) $item['harga']); ?></span>
                        <div class="d-flex align-items-center gap-3">
                            <a href="<?= htmlspecialchars(base_url('pelanggan/menu_detail.php?id=' . $item['id_menu']), ENT_QUOTES, 'UTF-8'); ?>" class="btn-add-to-cart" style="color: var(--text-secondary); text-decoration: none;">
                                Detail
                            </a>
                            <form method="post" action="<?= htmlspecialchars(base_url('actions/tambah_keranjang.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn-add-form">
                                <input type="hidden" name="id_menu" value="<?= htmlspecialchars((string) $item['id_menu'], ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="qty" value="1">
                                <button type="submit" class="btn-add-to-cart">
                                    Tambah +
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Luxury Toast Notification Container -->
    <div class="luxury-toast-container" id="toastContainer"></div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const menuGrid = document.querySelector('.menu-grid');
        const tabBtns = document.querySelectorAll('.tab-btn');
        const toastContainer = document.getElementById('toastContainer');

        // Toast Helper function
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

            // Trigger reflow & fade-in slide
            toast.offsetHeight;
            toast.classList.add('show');

            // Close button click handler
            const closeBtn = toast.querySelector('.luxury-toast-close');
            closeBtn.addEventListener('click', () => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 500);
            });

            // Auto dismiss toast after 3.5 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 500);
                }
            }, 3500);
        }

        // 1. AJAX Add To Cart Handler
        document.addEventListener('submit', async (e) => {
            const form = e.target.closest('.btn-add-form');
            if (!form) return;

            e.preventDefault();

            const submitBtn = form.querySelector('button[type="submit"]');
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
                    // Show beautiful green notification
                    showToast(data.message);

                    // Dynamically update Navbar Cart Counter Badge
                    const cartCountEl = document.querySelector('.js-cart-count');
                    if (cartCountEl) {
                        cartCountEl.textContent = data.cart_count;
                        cartCountEl.style.display = data.cart_count > 0 ? 'inline-block' : 'none';
                    }
                } else {
                    // Show error notification if failed
                    showToast(data.message || 'Gagal menambahkan menu.');
                }
            } catch (err) {
                console.error(err);
                showToast('Koneksi internet bermasalah. Sila coba lagi.');
            } finally {
                if (submitBtn) submitBtn.disabled = false;
            }
        });

        // 2. AJAX Category Tab Filter Handler
        tabBtns.forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();

                // Active tab highlight transition
                tabBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                // Add smooth opacity transition overlay to grid
                menuGrid.classList.add('loading');

                const fetchUrl = new URL(btn.href);
                fetchUrl.searchParams.set('ajax', '1');

                try {
                    const response = await fetch(fetchUrl);
                    const data = await response.json();

                    if (data.success) {
                        // Clear grid
                        menuGrid.innerHTML = '';

                        if (data.items.length === 0) {
                            menuGrid.innerHTML = `
                                <article class="menu-card" style="grid-column: 1 / -1; min-height: 200px; justify-content: center; align-items: center;">
                                    <div class="menu-card-body text-center">
                                        <h2 class="menu-card-title">Belum Ada Menu</h2>
                                        <p class="menu-card-desc mb-0">Belum ada hidangan tersedia untuk kategori ini.</p>
                                    </div>
                                </article>
                            `;
                        } else {
                            // Render new items
                            data.items.forEach(item => {
                                const article = document.createElement('article');
                                article.className = 'menu-card animate-fade-in-up';
                                article.innerHTML = `
                                    <div class="menu-card-img-wrapper">
                                        <a href="${item.detail_url}">
                                            <img src="${item.gambar}" alt="${item.nama_menu}" class="menu-card-img">
                                        </a>
                                    </div>
                                    <div class="menu-card-body">
                                        <h2 class="menu-card-title">
                                            <a href="${item.detail_url}">
                                                ${item.nama_menu}
                                            </a>
                                        </h2>
                                        <p class="menu-card-desc">${item.deskripsi}</p>
                                        <div class="menu-card-footer">
                                            <span class="menu-card-price">${item.harga_formatted}</span>
                                            <div class="d-flex align-items-center gap-3">
                                                <a href="${item.detail_url}" class="btn-add-to-cart" style="color: var(--text-secondary); text-decoration: none;">
                                                    Detail
                                                </a>
                                                <form method="post" action="${item.action_url}" class="btn-add-form">
                                                    <input type="hidden" name="id_menu" value="${item.id_menu}">
                                                    <input type="hidden" name="qty" value="1">
                                                    <button type="submit" class="btn-add-to-cart">
                                                        Tambah +
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                menuGrid.appendChild(article);
                            });
                        }

                        // Seamlessly update history state so that browser url matches the active category
                        const cleanUrl = new URL(btn.href);
                        history.pushState({ category: data.selectedCategory }, '', cleanUrl.pathname + cleanUrl.search);
                    }
                } catch (err) {
                    console.error(err);
                    showToast('Gagal memuat kategori menu.');
                } finally {
                    menuGrid.classList.remove('loading');
                }
            });
        });
    });
    </script>
</div>

<?php
$content = ob_get_clean();
// Hide the default public shell hero section since we built a custom luxury hero
render_public_shell(['title' => 'Menu & Ordering', 'hide_hero' => true], $content);
require __DIR__ . '/../includes/footer.php';
