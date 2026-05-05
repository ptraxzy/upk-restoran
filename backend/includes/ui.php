<?php

declare(strict_types=1);

function render_flash_messages(): void
{
    $error = get_flash('error');
    $success = get_flash('success');

    if ($error !== null) {
        echo '<div class="notice notice-error mb-6">' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</div>';
    }

    if ($success !== null) {
        echo '<div class="notice notice-success mb-6">' . htmlspecialchars($success, ENT_QUOTES, 'UTF-8') . '</div>';
    }
}

function public_nav_items(): array
{
    return [
        ['label' => 'Home', 'href' => frontend_url('pembeli/dashboard/index.php')],
        ['label' => 'Menu', 'href' => frontend_url('pembeli/menu/index.php')],
        ['label' => 'Keranjang', 'href' => frontend_url('pembeli/keranjang/index.php')],
        ['label' => 'Pesanan', 'href' => frontend_url('pembeli/pesanan/index.php')],
        ['label' => 'Profil', 'href' => frontend_url('pembeli/profil/index.php')],
    ];
}

function admin_nav_sections(): array
{
    return [
        'Core' => [
            ['label' => 'Dashboard', 'href' => frontend_url('admin/dashboard/index.php')],
            ['label' => 'Menu', 'href' => frontend_url('admin/menu/index.php')],
            ['label' => 'Pesanan', 'href' => frontend_url('admin/pesanan/index.php')],
            ['label' => 'Karyawan', 'href' => frontend_url('admin/karyawan/index.php')],
        ],
        'Control' => [
            ['label' => 'Laporan', 'href' => frontend_url('admin/laporan/index.php')],
            ['label' => 'Pengaturan', 'href' => frontend_url('admin/pengaturan/index.php')],
        ],
    ];
}

function staff_nav_sections(): array
{
    return [
        'Operasional' => [
            ['label' => 'Dashboard', 'href' => frontend_url('karyawan/dashboard/index.php')],
            ['label' => 'Pesanan', 'href' => frontend_url('karyawan/pesanan/index.php')],
            ['label' => 'Tambah Pesanan', 'href' => frontend_url('karyawan/pesanan/create.php')],
            ['label' => 'Status Pesanan', 'href' => frontend_url('karyawan/pesanan/status.php')],
        ],
        'Layanan' => [
            ['label' => 'Menu', 'href' => frontend_url('karyawan/menu/index.php')],
            ['label' => 'Pembayaran', 'href' => frontend_url('karyawan/pembayaran/index.php')],
            ['label' => 'Profil', 'href' => frontend_url('karyawan/profil/index.php')],
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
                <a class="employee-logo" href="<?= htmlspecialchars(frontend_url('karyawan/dashboard/index.php'), ENT_QUOTES, 'UTF-8'); ?>">
                    <?= htmlspecialchars($brand, ENT_QUOTES, 'UTF-8'); ?>
                </a>

                <nav class="employee-nav" aria-label="Navigasi karyawan">
                    <?php foreach ($items as $item): ?>
                        <?php $active = is_active_path($item['href']); ?>
                        <a class="<?= $active ? 'employee-nav-link employee-nav-link-active' : 'employee-nav-link'; ?>" href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <div class="employee-user">
                    <span><?= $userName; ?></span>
                    <span class="employee-role"><?= $roleName; ?></span>
                    <a class="employee-logout" href="<?= htmlspecialchars(backend_url('actions/auth/logout.php'), ENT_QUOTES, 'UTF-8'); ?>">Logout</a>
                </div>
            </header>

            <section class="employee-main">
                <header class="employee-page-head">
                    <div>
                        <p class="eyebrow"><?= htmlspecialchars($badge, ENT_QUOTES, 'UTF-8'); ?></p>
                        <h2 class="employee-page-title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h2>
                        <p class="employee-page-copy"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
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

            <div class="mt-10 space-y-8">
                <?php foreach ($sections as $sectionTitle => $items): ?>
                    <div>
                        <p class="sidebar-section-title"><?= htmlspecialchars($sectionTitle, ENT_QUOTES, 'UTF-8'); ?></p>
                        <nav class="mt-3 space-y-1">
                            <?php foreach ($items as $item): ?>
                                <?php $active = is_active_path($item['href']); ?>
                                <a class="<?= $active ? 'sidebar-link sidebar-link-active' : 'sidebar-link'; ?>" href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            <?php endforeach; ?>
                        </nav>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="sidebar-profile">
                <p class="sidebar-profile-role"><?= $roleName; ?></p>
                <p class="sidebar-profile-name"><?= $userName; ?></p>
                <a class="sidebar-profile-link" href="<?= htmlspecialchars(backend_url('actions/auth/logout.php'), ENT_QUOTES, 'UTF-8'); ?>">Logout</a>
            </div>
        </aside>

        <section class="dashboard-main">
            <header class="dashboard-topbar">
                <div>
                    <div class="dashboard-title-row">
                        <p class="eyebrow"><?= htmlspecialchars($badge, ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="dashboard-date">04 Mei 2026</p>
                    </div>
                    <h2 class="dashboard-page-title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p class="dashboard-page-copy"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <div class="dashboard-chip-wrap">
                    <span class="dashboard-chip"><?= $roleName; ?></span>
                    <span class="dashboard-chip"><?= $userName; ?></span>
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
    $brand = $config['brand'] ?? "L'Essence";
    $actions = $config['actions'] ?? [];
    ?>
    <main class="shell">
        <section class="public-shell">
            <div class="shell-inner">
                <header class="public-topbar">
                    <div>
                        <p class="public-brand"><?= htmlspecialchars($brand, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <nav class="public-nav">
                        <?php foreach (public_nav_items() as $item): ?>
                            <?php $active = is_active_path($item['href']); ?>
                            <a class="<?= $active ? 'nav-link text-brass' : 'nav-link'; ?>" href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?>
                                <?php if ($item['label'] === 'Keranjang'): ?>
                                    <span class="cart-count"><?= count($_SESSION['cart'] ?? []); ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                        <a class="nav-link" href="<?= htmlspecialchars(backend_url('actions/auth/logout.php'), ENT_QUOTES, 'UTF-8'); ?>">Logout</a>
                    </nav>
                </header>

                <div class="public-intro">
                    <div>
                        <p class="eyebrow"><?= htmlspecialchars($eyebrow, ENT_QUOTES, 'UTF-8'); ?></p>
                        <h1 class="public-title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
                        <p class="public-copy"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="public-actions">
                        <?php foreach ($actions as $action): ?>
                            <?php $variant = $action['variant'] ?? 'primary'; ?>
                            <a class="<?= $variant === 'secondary' ? 'cta-secondary' : 'cta-primary'; ?>" href="<?= htmlspecialchars($action['href'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?= htmlspecialchars($action['label'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="shell-inner public-content">
            <?php render_flash_messages(); ?>
            <?= $content; ?>
        </section>
    </main>
    <?php
}
