<?php

declare(strict_types=1);

function render_flash_messages(): void
{
    $error = get_flash('error');
    $success = get_flash('success');

    if ($error !== null) {
        echo '<div class="alert alert-danger rounded-0 small mb-4 border-0 text-center text-uppercase letter-spacing-1">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</div>';
    }

    if ($success !== null) {
        echo '<div class="alert alert-success rounded-0 small mb-4 border-0 text-center text-uppercase letter-spacing-1">' . htmlspecialchars($success, ENT_QUOTES, 'UTF-8') . '</div>';
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

function admin_nav_sections(): array
{
    return [
        'Ikhtisar' => [
            ['label' => 'Ringkasan', 'href' => base_url('admin/dashboard.php')],
        ],
        'Pengelolaan' => [
            ['label' => 'Data Menu', 'href' => base_url('admin/menu.php')],
            ['label' => 'Promo & Diskon', 'href' => base_url('admin/diskon.php')],
            ['label' => 'Data Tim', 'href' => base_url('admin/karyawan.php')],
        ],
        'Sistem' => [
            ['label' => 'Catatan Penjualan', 'href' => base_url('admin/laporan.php')],
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
    $brand = $config['brand'] ?? "L'Art Culinaire";
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
                        <p class="text-gold m-0 text-uppercase" style="font-size: 9px; letter-spacing: 1px;"><?= $roleName; ?></p>
                        <p class="text-white m-0 fw-medium text-truncate" style="font-size: 13px;"><?= $userName; ?></p>
                    </div>
                </div>
                <a class="btn btn-outline-warning w-100 py-2" style="font-size: 10px; letter-spacing: 2px;" href="<?= htmlspecialchars(base_url('logout.php')); ?>">LOGOUT</a>
            </div>
        </aside>

        <section class="dashboard-main">
            <header class="dashboard-topbar d-flex justify-content-between align-items-start border-bottom border-soft pb-4 mb-5">
                <div class="animate-fade-in-up">
                    <p class="text-gold small text-uppercase letter-spacing-2 mb-2"><?= htmlspecialchars($badge); ?></p>
                    <h2 class="dashboard-topbar-title font-display text-white m-0" style="font-size: 32px;"><?= htmlspecialchars($title); ?></h2>
                    <p class="text-secondary small mt-2 mb-0"><?= htmlspecialchars($description); ?></p>
                </div>
                <div class="d-none d-md-block text-end animate-fade-in-up" style="animation-delay: 0.2s;">
                    <p class="text-muted small text-uppercase letter-spacing-1 m-0"><?= date('l, d F Y'); ?></p>
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
    $brand = $config['brand'] ?? "L'Art Culinaire";
    $actions = $config['actions'] ?? [];
    $hideHero = $config['hide_hero'] ?? false;
    ?>
    <main class="shell">
        <nav class="navbar navbar-expand-lg navbar-dark border-bottom border-soft py-3 sticky-top bg-black">
            <div class="container">
                <a href="<?= base_url('pelanggan/dashboard.php') ?>" class="public-brand text-decoration-none">
                    <?= htmlspecialchars($brand); ?>
                </a>
                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="publicNav">
                    <ul class="navbar-nav ms-auto gap-lg-4 align-items-lg-center mt-4 mt-lg-0">
                        <?php foreach (public_nav_items() as $item): ?>
                            <li class="nav-item">
                                <?php $active = is_active_path($item['href']); ?>
                                <a class="public-nav-link text-decoration-none <?= $active ? 'active' : ''; ?> position-relative" href="<?= htmlspecialchars($item['href']); ?>">
                                    <?= htmlspecialchars($item['label']); ?>
                                    <?php if ($item['label'] === 'Keranjang' && count($_SESSION['cart'] ?? []) > 0): ?>
                                        <span class="cart-count"><?= count($_SESSION['cart']); ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-outline-warning w-100 w-lg-auto" style="padding: 8px 16px; font-size: 10px;" href="<?= htmlspecialchars(base_url('logout.php')); ?>">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

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
            <p class="text-secondary small text-uppercase letter-spacing-2 m-0" style="font-size: 9px;">© <?= date('Y') ?> <?= htmlspecialchars($brand); ?>. All Rights Reserved.</p>
        </footer>
    </main>
    <?php
}
