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
        ['label' => 'Dashboard', 'href' => base_url('pelanggan/dashboard.php')],
        ['label' => 'Menu', 'href' => base_url('pelanggan/menu.php')],
        ['label' => 'Keranjang', 'href' => base_url('pelanggan/keranjang.php')],
        ['label' => 'Pesanan', 'href' => base_url('pelanggan/pesanan.php')],
        ['label' => 'Profil', 'href' => base_url('pelanggan/profil.php')],
    ];
}       

function admin_nav_sections(): array
{
    return [
        'Utama' => [
            ['label' => 'Dashboard', 'href' => base_url('admin/dashboard.php')],
        ],
        'Manajemen' => [
            ['label' => 'Manajemen Menu', 'href' => base_url('admin/menu.php')],
            ['label' => 'Manajemen Diskon', 'href' => base_url('admin/diskon.php')],
            ['label' => 'Manajemen Karyawan', 'href' => base_url('admin/karyawan.php')],
        ],
        'Kontrol' => [
            ['label' => 'Laporan', 'href' => base_url('admin/laporan.php')],
            ['label' => 'Pengaturan', 'href' => base_url('admin/pengaturan.php')],
        ],
    ];
}

function staff_nav_sections(): array
{
    return [
        'Utama' => [
            ['label' => 'Dashboard', 'href' => base_url('kasir/dashboard.php')],
        ],
        'Manajemen' => [
            ['label' => 'Manajemen Pesanan', 'href' => base_url('kasir/pesanan.php')],
            ['label' => 'Manajemen Pembayaran', 'href' => base_url('kasir/pembayaran.php')],
        ],
        'Personal' => [
            ['label' => 'Profil', 'href' => base_url('kasir/profil.php')],
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

    if ($badge === 'Service Floor') {
        $items = array_merge(...array_values($sections));
        ?>
        <main class="employee-shell">
            <header class="employee-topbar">
                <div class="employee-brand">
                    <a class="employee-logo" href="<?= htmlspecialchars(base_url('kasir/dashboard.php'), ENT_QUOTES, 'UTF-8'); ?>">
                        <?= htmlspecialchars($brand, ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <nav class="employee-nav" aria-label="Navigasi karyawan">
                        <?php foreach ($items as $item): ?>
                            <?php $active = is_active_path($item['href']); ?>
                            <a class="employee-nav-link <?= $active ? 'active' : ''; ?>" href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                </div>

                <div class="employee-user">
                    <div class="employee-user-info">
                        <div class="employee-user-name"><?= $userName; ?></div>
                        <div class="employee-user-role"><?= $roleName; ?></div>
                    </div>
                    <a class="btn btn-outline-warning" style="padding: 8px 16px; font-size: 10px;" href="<?= htmlspecialchars(base_url('logout.php'), ENT_QUOTES, 'UTF-8'); ?>">Logout</a>
                </div>
            </header>

            <section class="employee-main">
                <header class="mb-5">
                    <p class="text-gold small text-uppercase letter-spacing-2 mb-2"><?= htmlspecialchars($badge, ENT_QUOTES, 'UTF-8'); ?></p>
                    <h2 class="font-display text-white" style="font-size: 36px;"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p class="text-secondary"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></p>
                </header>

                <?php render_flash_messages(); ?>
                <?= $content; ?>
            </section>
        </main>
        <?php
        return;
    }
    ?>
    <main class="dashboard-shell">
        <aside class="dashboard-sidebar">
            <div class="sidebar-brand">
                <p class="sidebar-brand-mark"><?= htmlspecialchars($brand, ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="sidebar-brand-copy"><?= htmlspecialchars($badge, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>

            <div class="d-flex flex-column pb-5">
                <?php foreach ($sections as $sectionTitle => $items): ?>
                    <div>
                        <p class="sidebar-section-title"><?= htmlspecialchars($sectionTitle, ENT_QUOTES, 'UTF-8'); ?></p>
                        <nav class="d-grid gap-1">
                            <?php foreach ($items as $item): ?>
                                <?php $active = is_active_path($item['href']); ?>
                                <a class="sidebar-link <?= $active ? 'sidebar-link-active' : ''; ?>" href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            <?php endforeach; ?>
                        </nav>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-auto p-4 border-top border-secondary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-gold small text-uppercase letter-spacing-1 mb-1" style="font-size: 10px;"><?= $roleName; ?></p>
                        <p class="text-white m-0" style="font-size: 13px;"><?= $userName; ?></p>
                    </div>
                    <a class="text-secondary hover-gold" title="Logout" href="<?= htmlspecialchars(base_url('logout.php'), ENT_QUOTES, 'UTF-8'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </aside>

        <section class="dashboard-main">
            <header class="dashboard-topbar">
                <div>
                    <p class="text-gold small text-uppercase letter-spacing-2 mb-2"><?= htmlspecialchars($badge, ENT_QUOTES, 'UTF-8'); ?></p>
                    <h2 class="dashboard-topbar-title font-display text-white"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p class="dashboard-topbar-desc"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <div class="text-end">
                    <p class="text-secondary small text-uppercase letter-spacing-1 m-0"><?= date('d F Y'); ?></p>
                </div>
            </header>

            <?php render_flash_messages(); ?>
            <?= $content; ?>
        </section>
    </main>
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
        <header class="container">
            <div class="public-header d-flex justify-content-between align-items-center">
                <a href="<?= base_url('pelanggan/dashboard.php') ?>" class="public-brand text-decoration-none">
                    <?= htmlspecialchars($brand, ENT_QUOTES, 'UTF-8'); ?>
                </a>
                <nav class="public-nav">
                    <?php foreach (public_nav_items() as $item): ?>
                        <?php $active = is_active_path($item['href']); ?>
                        <a class="public-nav-link <?= $active ? 'active' : ''; ?>" href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>" style="position: relative;">
                            <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?>
                            <?php if ($item['label'] === 'Keranjang' && count($_SESSION['cart'] ?? []) > 0): ?>
                                <span class="cart-count"><?= count($_SESSION['cart']); ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                    <a class="btn btn-outline-warning ms-2" style="padding: 8px 16px; font-size: 10px;" href="<?= htmlspecialchars(base_url('logout.php'), ENT_QUOTES, 'UTF-8'); ?>">Logout</a>
                </nav>
            </div>
        </header>

        <?php if (!$hideHero): ?>
        <section class="container">
            <div class="public-hero">
                <div class="public-hero-eyebrow"><?= htmlspecialchars($eyebrow, ENT_QUOTES, 'UTF-8'); ?></div>
                <h1 class="public-hero-title font-display text-white"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
                <p class="public-hero-desc"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></p>
                
                <?php if (!empty($actions)): ?>
                <div class="d-flex justify-content-center gap-3 mt-5">
                    <?php foreach ($actions as $action): ?>
                        <?php $variant = $action['variant'] ?? 'primary'; ?>
                        <a class="<?= $variant === 'secondary' ? 'btn btn-outline-warning' : 'btn btn-warning'; ?>" href="<?= htmlspecialchars($action['href'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?= htmlspecialchars($action['label'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>

        <section class="container py-5 mb-5 flex-grow-1">
            <?php render_flash_messages(); ?>
            <?= $content; ?>
        </section>
        
        <footer class="mt-auto py-5 border-top border-secondary text-center">
            <p class="text-secondary small text-uppercase letter-spacing-2 m-0">© <?= date('Y') ?> <?= htmlspecialchars($brand, ENT_QUOTES, 'UTF-8'); ?>. All Rights Reserved.</p>
        </footer>
    </main>
    <?php
}
