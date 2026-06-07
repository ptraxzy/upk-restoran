<?php

declare(strict_types=1);

function render_flash_messages(): void
{
    $error = get_flash('error');
    $success = get_flash('success');

    if ($error !== null) {
        echo '<div class="alert alert-danger rounded-0 small mb-4 border-0 text-center">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</div>';
    }

    if ($success !== null) {
        echo '<div class="alert alert-success rounded-0 small mb-4 border-0 text-center">' . htmlspecialchars($success, ENT_QUOTES, 'UTF-8') . '</div>';
    }
}

function public_nav_items(): array
{
    return [
        ['label' => 'Beranda', 'href' => base_url('pelanggan/dashboard.php')],
        ['label' => 'Daftar Menu', 'href' => base_url('pelanggan/menu.php')],
        ['label' => 'Keranjang', 'href' => base_url('pelanggan/keranjang.php')],
        ['label' => 'Pesanan Saya', 'href' => base_url('pelanggan/pesanan.php')],
        ['label' => 'Profil', 'href' => base_url('pelanggan/profil.php')],
    ];
}

function cart_count(): int
{
    if (!isset($_SESSION['id_user'])) {
        return count($_SESSION['cart'] ?? []);
    }

    require_once __DIR__ . '/database.php';

    try {
        $stmt = db()->prepare('SELECT COUNT(*) AS total FROM keranjang k JOIN menu m ON k.id_menu = m.id_menu WHERE k.id_pelanggan = ?');
        $stmt->execute([$_SESSION['id_user']]);
        $row = $stmt->fetch();

        return (int) ($row['total'] ?? 0);
    } catch (Throwable) {
        return count($_SESSION['cart'] ?? []);
    }
}

function admin_nav_sections(): array
{
    return [
        'Ikhtisar' => [
            ['label' => 'Ringkasan', 'href' => base_url('admin/dashboard.php')],
        ],
        'Pengelolaan' => [
            ['label' => 'Data Menu', 'href' => base_url('admin/menu.php')],
            ['label' => 'Kategori Menu', 'href' => base_url('admin/menu_kategori.php')],
            ['label' => 'Pesanan Restoran', 'href' => base_url('admin/pesanan.php')],
            ['label' => 'Promo & Diskon', 'href' => base_url('admin/diskon.php')],
            ['label' => 'Data Tim', 'href' => base_url('admin/karyawan.php')],
        ],
        'Sistem' => [
            ['label' => 'Catatan Penjualan', 'href' => base_url('admin/laporan.php')],
            ['label' => 'Ulasan Pelanggan', 'href' => base_url('admin/ulasan.php')],
            ['label' => 'Pengaturan', 'href' => base_url('admin/pengaturan.php')],
        ],
    ];
}

function staff_nav_sections(): array
{
    return [
        'Harian' => [
            ['label' => 'Ringkasan', 'href' => base_url('kasir/dashboard.php')],
        ],
        'Layanan' => [
            ['label' => 'Daftar Pesanan', 'href' => base_url('kasir/pesanan.php')],
            ['label' => 'Daftar Menu', 'href' => base_url('kasir/menu.php')],
            ['label' => 'Proses Bayar', 'href' => base_url('kasir/pembayaran.php')],
        ],
        'Akun' => [
            ['label' => 'Profil Saya', 'href' => base_url('kasir/profil.php')],
        ],
    ];
}

function is_active_path(string $href): bool
{
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';

    return str_contains($requestPath, $href);
}

function render_internal_shell(array $config, string $content): void
{
    $sections = $config['nav_sections'] ?? [];
    $badge = $config['badge'] ?? '';
    $title = $config['title'] ?? '';
    $description = $config['description'] ?? '';
    $brand = $config['brand'] ?? get_setting('nama_restoran', "Lumière");
    $userName = htmlspecialchars($_SESSION['user_name'] ?? 'User', ENT_QUOTES, 'UTF-8');
    $roleName = htmlspecialchars(role_label($_SESSION['user_role'] ?? ''), ENT_QUOTES, 'UTF-8');

    ?>
    <header class="mobile-navbar">
        <a href="#" class="public-brand text-decoration-none" style="font-size: 16px;"><?= htmlspecialchars($brand); ?></a>
        <button class="btn p-0 text-gold border-0 shadow-none" id="sidebarToggle">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
        </button>
    </header>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <main class="dashboard-shell">
        <aside class="dashboard-sidebar" id="sidebar">
            <div class="sidebar-brand">
                <p class="sidebar-brand-mark"><?= htmlspecialchars($brand); ?></p>
                <p class="sidebar-brand-copy"><?= htmlspecialchars($badge); ?></p>
            </div>

            <div class="flex-grow-1 overflow-auto py-3">
                <?php if ($badge === 'Service Floor'): ?>
                    <?php $items = array_merge(...array_values($sections)); ?>
                    <div class="sidebar-group mb-4">
                        <p class="sidebar-section-title">Operasional</p>
                        <nav class="d-flex flex-column">
                            <?php foreach ($items as $item): ?>
                                <?php $active = is_active_path($item['href']); ?>
                                <a class="sidebar-link <?= $active ? 'sidebar-link-active' : ''; ?>" href="<?= htmlspecialchars($item['href']); ?>">
                                    <?= htmlspecialchars($item['label']); ?>
                                </a>
                            <?php endforeach; ?>
                        </nav>
                    </div>
                <?php else: ?>
                    <?php foreach ($sections as $sectionTitle => $items): ?>
                        <div class="sidebar-group mb-4">
                            <p class="sidebar-section-title"><?= htmlspecialchars($sectionTitle); ?></p>
                            <nav class="d-flex flex-column">
                                <?php foreach ($items as $item): ?>
                                    <?php $active = is_active_path($item['href']); ?>
                                    <a class="sidebar-link <?= $active ? 'sidebar-link-active' : ''; ?>" href="<?= htmlspecialchars($item['href']); ?>">
                                        <?= htmlspecialchars($item['label']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </nav>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="mt-auto p-4 border-top border-soft">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="min-width: 0;">
                        <p class="text-gold m-0" style="font-size: 11px;"><?= $roleName; ?></p>
                        <p class="text-white m-0 fw-medium text-truncate" style="font-size: 14px;"><?= $userName; ?></p>
                    </div>
                </div>
                <a class="btn btn-outline-warning w-100 py-2" style="font-size: 12px;" href="<?= htmlspecialchars(base_url('logout.php')); ?>">Logout</a>
            </div>
        </aside>

        <section class="dashboard-main">
            <header class="dashboard-topbar d-flex justify-content-between align-items-start border-bottom border-soft pb-4 mb-5">
                <div class="animate-fade-in-up">
                    <p class="text-gold small mb-2" style="font-size: 12px;"><?= htmlspecialchars($badge); ?></p>
                    <h2 class="dashboard-topbar-title font-display text-white m-0" style="font-size: 28px;"><?= htmlspecialchars($title); ?></h2>
                    <p class="text-secondary mt-2 mb-0" style="font-size: 13px;"><?= htmlspecialchars($description); ?></p>
                </div>
                <div class="d-none d-md-block text-end animate-fade-in-up" style="animation-delay: 0.2s;">
                    <p class="text-muted m-0" style="font-size: 13px;"><?= date('l, d F Y'); ?></p>
                </div>
            </header>

            <?php render_flash_messages(); ?>
            <div class="dashboard-content animate-fade-in-up" style="animation-delay: 0.3s;">
                <?= $content; ?>
            </div>
        </section>
    </main>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggle = document.getElementById('sidebarToggle');

        toggle?.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });

        overlay?.addEventListener('click', function() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });

        document.addEventListener('click', function(event) {
            if (window.innerWidth < 992 && sidebar && !sidebar.contains(event.target) && !toggle.contains(event.target) && !overlay.contains(event.target)) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });
    </script>
    <?php
}

function render_public_shell(array $config, string $content): void
{
    $title = $config['title'] ?? '';
    $description = $config['description'] ?? '';
    $eyebrow = $config['eyebrow'] ?? 'Member Area';
    $brand = ($config['brand'] ?? "L'Art Culinaire") === "L'Art Culinaire" ? get_setting('nama_restoran', "Lumière") : $config['brand'];
    $actions = $config['actions'] ?? [];
    $hideHero = $config['hide_hero'] ?? false;
    $isLoggedIn = isset($_SESSION['id_user']) && ($_SESSION['user_role'] ?? '') === 'pelanggan';
    $brandUrl = $isLoggedIn ? base_url('pelanggan/dashboard.php') : base_url('login.php');
    ?>
    <main class="shell">
        <nav class="navbar navbar-expand-lg navbar-dark border-bottom border-soft py-3 sticky-top bg-black">
            <div class="container">
                <a href="<?= $brandUrl ?>" class="public-brand text-decoration-none">
                    <?= htmlspecialchars($brand); ?>
                </a>
                
                <?php if ($isLoggedIn): ?>
                <!-- Hamburger Button (Toggles slide-in sidebar) -->
                <button class="navbar-toggler border-0 shadow-none custom-hamburger js-menu-toggle" type="button" aria-expanded="false" aria-label="Toggle navigation">
                    <div class="hamburger-box">
                        <span class="hamburger-line line-1"></span>
                        <span class="hamburger-line line-2"></span>
                        <span class="hamburger-line line-3"></span>
                    </div>
                </button>
                
                <!-- Desktop Navigation Menu -->
                <div class="collapse navbar-collapse" id="publicNav">
                    <ul class="navbar-nav ms-auto gap-lg-4 align-items-lg-center mt-4 mt-lg-0">
                        <?php foreach (public_nav_items() as $item): ?>
                            <li class="nav-item">
                                <?php $active = is_active_path($item['href']); ?>
                                <a class="public-nav-link text-decoration-none <?= $active ? 'active' : ''; ?> position-relative" href="<?= htmlspecialchars($item['href']); ?>">
                                    <?= htmlspecialchars($item['label']); ?>
                                    <?php if ($item['label'] === 'Keranjang'): ?>
                                        <span class="cart-count js-cart-count" style="<?= cart_count() > 0 ? '' : 'display: none;' ?>">(<?= cart_count(); ?>)</span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-outline-warning w-100 w-lg-auto" style="padding: 8px 16px; font-size: 12px;" href="<?= htmlspecialchars(base_url('logout.php')); ?>">Logout</a>
                        </li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </nav>

        <?php if ($isLoggedIn): ?>
        <!-- Slide-In Mobile Sidebar & Overlay -->
        <div class="public-sidebar-overlay" id="jsPublicOverlay"></div>
        <div class="public-sidebar" id="jsPublicSidebar">
            <div class="sidebar-header d-flex justify-content-between align-items-center">
                <span class="public-brand"><?= htmlspecialchars($brand); ?></span>
                <button class="btn-close-sidebar js-menu-toggle">&times;</button>
            </div>
            
            <nav class="sidebar-menu-links">
                <?php foreach (public_nav_items() as $item): ?>
                    <?php $active = is_active_path($item['href']); ?>
                    <a class="sidebar-menu-link <?= $active ? 'active' : ''; ?>" href="<?= htmlspecialchars($item['href']); ?>">
                        <?= htmlspecialchars($item['label']); ?>
                        <?php if ($item['label'] === 'Keranjang'): ?>
                            <span class="badge bg-warning text-dark ms-2 js-cart-count" style="<?= cart_count() > 0 ? '' : 'display: none;' ?>"><?= cart_count(); ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
                <div class="border-top border-soft my-3"></div>
                <a class="btn btn-outline-warning w-100 py-2" style="font-size: 13px; font-weight: 600;" href="<?= htmlspecialchars(base_url('logout.php')); ?>">Logout</a>
            </nav>
        </div>
        <?php endif; ?>

        <?php if (!$hideHero): ?>
        <section class="container mt-4">
            <div class="public-hero">
                <div class="public-hero-eyebrow"><?= htmlspecialchars($eyebrow); ?></div>
                <h1 class="public-hero-title font-display text-white"><?= htmlspecialchars($title); ?></h1>
                <p class="public-hero-desc mx-auto"><?= htmlspecialchars($description); ?></p>
                
                <?php if (!empty($actions)): ?>
                <div class="d-flex justify-content-center flex-wrap gap-3 mt-5">
                    <?php foreach ($actions as $action): ?>
                        <?php $variant = $action['variant'] ?? 'primary'; ?>
                        <a class="<?= $variant === 'secondary' ? 'btn btn-outline-warning' : 'btn btn-warning'; ?>" href="<?= htmlspecialchars($action['href']); ?>">
                            <?= htmlspecialchars($action['label']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>

        <section class="container py-4 flex-grow-1">
            <?php render_flash_messages(); ?>
            <?= $content; ?>
        </section>
        
        <footer class="mt-auto py-5 border-top border-soft text-center bg-black">
            <p class="text-secondary m-0" style="font-size: 12px;">© <?= date('Y') ?> <?= htmlspecialchars($brand); ?>. All Rights Reserved.</p>
        </footer>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggles = document.querySelectorAll('.js-menu-toggle');
            const sidebar = document.getElementById('jsPublicSidebar');
            const overlay = document.getElementById('jsPublicOverlay');
            const hamburger = document.querySelector('.custom-hamburger');

            toggles.forEach(toggle => {
                toggle.addEventListener('click', () => {
                    const isOpen = sidebar.classList.toggle('show');
                    overlay.classList.toggle('show');
                    
                    if (hamburger) {
                        hamburger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    }
                });
            });

            overlay?.addEventListener('click', () => {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
                if (hamburger) {
                    hamburger.setAttribute('aria-expanded', 'false');
                }
            });
        });
    </script>
    <?php
}
