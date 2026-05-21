<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('pelanggan');

$title = 'Indeks Kuliner';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

$selectedCategory = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Fetch all categories
$stmtKat = db()->query("SELECT * FROM kategori ORDER BY id_kategori ASC");
$categories = $stmtKat->fetchAll();

// Fetch menu items based on chosen category
if ($selectedCategory > 0) {
    $stmt = db()->prepare("SELECT * FROM menu WHERE status = 'Tersedia' AND deleted_at IS NULL AND id_kategori = ? ORDER BY id_menu DESC");
    $stmt->execute([$selectedCategory]);
} else {
    $stmt = db()->query("SELECT * FROM menu WHERE status = 'Tersedia' AND deleted_at IS NULL ORDER BY id_menu DESC");
}
$items = $stmt->fetchAll();

// Fetch chef recommendations
$stmtPromo = db()->query("SELECT * FROM menu WHERE status = 'Tersedia' AND deleted_at IS NULL ORDER BY harga DESC LIMIT 2");
$promoItems = $stmtPromo->fetchAll();

ob_start();
?>

<style>
/* PREMIUM INDEX KULINER STYLING */
:root {
    --gold: #C9A84C;
    --gold-dim: rgba(201, 168, 76, 0.15);
    --bg-dark: #1E1E1E;
    --card-bg: rgba(15, 15, 15, 0.4);
    --text-primary: #E5E2E1;
    --text-secondary: #9A8F80;
    --border-soft: rgba(154, 143, 128, 0.15);

    --font-serif: 'Libre Baskerville', serif;
    --font-sans: 'DM Sans', sans-serif;
}

body {
    background-color: var(--bg-dark) !important;
    color: var(--text-primary) !important;
    font-family: var(--font-sans);
}

.category-nav {
    display: flex;
    gap: 32px;
    overflow-x: auto;
    scrollbar-width: none;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--border-soft);
}
.category-nav::-webkit-scrollbar { display: none; }

.category-link {
    font-family: var(--font-sans);
    font-size: 13px;
    color: var(--text-secondary);
    letter-spacing: 0.06em;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    padding-bottom: 12px;
    border-bottom: 2px solid transparent;
    transition: all 0.3s ease;
    text-transform: uppercase;
}
.category-link:hover, .category-link.active {
    color: var(--gold);
    border-bottom-color: var(--gold);
}

/* LUXURY UNIFORM CATALOG GRID */
.menu-grid {
    display: grid;
    grid-template-columns: repeat(1, 1fr);
    gap: 2rem;
    margin-top: 3rem;
}
@media (min-width: 768px) {
    .menu-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (min-width: 1200px) {
    .menu-grid { grid-template-columns: repeat(4, 1fr); }
}

.menu-card {
    background-color: var(--card-bg);
    border: 1px solid var(--border-soft);
    display: flex;
    flex-direction: column;
    height: 100%;
    transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.4s ease;
    position: relative;
    border-radius: 0;
}

.menu-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
    border-color: rgba(201, 168, 76, 0.3);
}

.menu-card-img-wrapper {
    position: relative;
    width: 100%;
    aspect-ratio: 3 / 2;
    overflow: hidden;
    background-color: #0d0c0a;
}

.menu-card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.menu-card:hover .menu-card-img {
    transform: scale(1.05);
}

.menu-card-body {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.menu-card-title {
    font-family: var(--font-serif);
    font-size: 1.25rem;
    font-weight: 400;
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.menu-card-title a {
    color: var(--text-primary);
    text-decoration: none;
    transition: color 0.3s ease;
}

.menu-card-title a:hover {
    color: var(--gold);
}

.menu-card-desc {
    font-family: var(--font-sans);
    font-size: 0.8rem;
    line-height: 1.6;
    color: var(--text-secondary);
    margin-bottom: 1.75rem;
    flex-grow: 1;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
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
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--gold);
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
</style>

<section class="py-4">
    <!-- Chef Recommendation Banner -->
    <?php if (!empty($promoItems)): ?>
    <div class="mb-5 animate-fade-in-up">
        <p class="detail-category-label">REKOMENDASI CHEF & PROMO</p>
        <h3 class="font-display text-white mb-4" style="font-size: 28px;">Pencicipan Istimewa Hari Ini</h3>
        <div class="row g-4">
            <?php foreach ($promoItems as $promo): ?>
            <div class="col-md-6">
                <div class="card text-white border border-secondary p-0 rounded-0 overflow-hidden h-100 d-flex flex-row align-items-center" style="background-color: var(--card-bg);">
                    <?php if ($promo['gambar']): ?>
                        <img src="<?= htmlspecialchars($promo['gambar'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($promo['nama_menu'], ENT_QUOTES, 'UTF-8'); ?>" style="width: 150px; height: 150px; object-fit: cover; border-right: 1px solid var(--border-soft);">
                    <?php else: ?>
                        <div class="bg-dark text-muted d-flex align-items-center justify-content-center" style="width: 150px; height: 150px; border-right: 1px solid var(--border-soft);">
                            <span style="font-size: 12px;">N/A</span>
                        </div>
                    <?php endif; ?>
                    <div class="p-4 flex-grow-1">
                        <span class="badge mb-2" style="font-size: 10px; font-weight: 600; border: 1px solid var(--gold); color: var(--gold); background: transparent;">10% OFF</span>
                        <h4 class="font-display text-white mb-1" style="font-size: 17px;"><?= htmlspecialchars($promo['nama_menu'], ENT_QUOTES, 'UTF-8'); ?></h4>
                        <p class="text-secondary small mb-3" style="font-size: 12px; line-height: 1.4;"><?= htmlspecialchars(substr($promo['deskripsi'] ?? '', 0, 48), ENT_QUOTES, 'UTF-8'); ?>...</p>
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <span class="text-muted text-decoration-line-through small me-2" style="font-size: 12px;">Rp <?= number_format((float)$promo['harga'], 0, ',', '.'); ?></span>
                                <span class="text-gold fw-semibold" style="font-size: 14px;">Rp <?= number_format((float)$promo['harga'] * 0.9, 0, ',', '.'); ?></span>
                            </div>
                            <form method="post" action="<?= htmlspecialchars(base_url('actions/tambah_keranjang.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn-add-form m-0">
                                <input type="hidden" name="id_menu" value="<?= htmlspecialchars((string)$promo['id_menu'], ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="qty" value="1">
                                <button type="submit" class="btn-add-to-cart">
                                    Tambah
                                    <svg viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M5 0V10M0 5H10" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Indeks Kuliner Header -->
    <div class="mb-5 pb-4 border-bottom border-secondary animate-fade-in-up" style="animation-delay: 0.1s;">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <p class="detail-category-label">EDISI TERBATAS</p>
                <h2 class="font-display text-white mb-0" style="font-size: 42px;">Indeks Kuliner</h2>
            </div>
        </div>

        <nav class="category-nav">
            <a href="<?= base_url('pelanggan/menu.php'); ?>" class="category-link <?= $selectedCategory === 0 ? 'active' : ''; ?>">Semua</a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= base_url('pelanggan/menu.php?category=' . $cat['id_kategori']); ?>"
                   class="category-link <?= $selectedCategory === (int)$cat['id_kategori'] ? 'active' : ''; ?>">
                    <?= htmlspecialchars($cat['nama_kategori']); ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>

    <!-- Menu Items Grid -->
    <div class="menu-grid animate-fade-in-up" style="animation-delay: 0.2s;">
        <?php if (empty($items)): ?>
            <div class="col-12 py-5 text-center" style="grid-column: 1 / -1;">
                <p class="text-muted">Tidak ada hidangan yang tersedia untuk kategori ini.</p>
            </div>
        <?php endif; ?>
        <?php foreach ($items as $item): ?>
            <article class="menu-card">
                <div class="menu-card-img-wrapper">
                    <a href="<?= htmlspecialchars(base_url('pelanggan/menu_detail.php?id=' . $item['id_menu']), ENT_QUOTES, 'UTF-8'); ?>">
                        <img src="<?= htmlspecialchars((string) ($item['gambar'] ?: 'https://placehold.co/1200x800?text=Menu'), ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item['nama_menu'], ENT_QUOTES, 'UTF-8'); ?>" class="menu-card-img">
                    </a>
                </div>
                <div class="menu-card-body">
                    <h3 class="menu-card-title">
                        <a href="<?= htmlspecialchars(base_url('pelanggan/menu_detail.php?id=' . $item['id_menu']), ENT_QUOTES, 'UTF-8'); ?>">
                            <?= htmlspecialchars($item['nama_menu'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </h3>
                    <p class="menu-card-desc"><?= htmlspecialchars((string) ($item['deskripsi'] ?: 'Detail hidangan belum tersedia.'), ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="menu-card-footer">
                        <span class="menu-card-price">Rp <?= number_format((float)$item['harga'], 0, ',', '.'); ?></span>
                        <form method="post" action="<?= htmlspecialchars(base_url('actions/tambah_keranjang.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn-add-form m-0">
                            <input type="hidden" name="id_menu" value="<?= htmlspecialchars((string)$item['id_menu'], ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="qty" value="1">
                            <button type="submit" class="btn-add-to-cart">
                                Tambah
                                <svg viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 0V10M0 5H10" stroke="currentColor" stroke-width="1.5"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<!-- Luxury Toast Notification Container -->
<div class="luxury-toast-container" id="toastContainer"></div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toastContainer = document.getElementById('toastContainer');

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

    // Intercept form submissions for Add to Cart
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
    'eyebrow' => 'Menu Eksklusif',
    'title' => 'Simfoni Musim Gugur',
    'description' => 'Pengalaman hidangan kuratorial untuk meja restoran, menonjolkan keseimbangan rasa hangat dan gelap.',
], $content);
require __DIR__ . '/../includes/footer.php';
