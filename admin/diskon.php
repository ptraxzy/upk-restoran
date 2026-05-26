<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Manajemen Diskon';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

// Get parameters
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

// Sanitize filter
if (!in_array($filter, ['all', 'active', 'scheduled', 'expired'])) {
    $filter = 'all';
}

$pdo = db();

// Fetch summary metrics
$countActive = (int)$pdo->query("SELECT COUNT(*) FROM voucher WHERE status_voucher = 'Active'")->fetchColumn();
$countScheduled = (int)$pdo->query("SELECT COUNT(*) FROM voucher WHERE status_voucher = 'Scheduled'")->fetchColumn();
$countExpired = (int)$pdo->query("SELECT COUNT(*) FROM voucher WHERE status_voucher = 'Expired'")->fetchColumn();
$totalCount = (int)$pdo->query("SELECT COUNT(*) FROM voucher")->fetchColumn();

// Build query
$query = "SELECT v.*, u.username AS pembuat, u.level AS role FROM voucher v LEFT JOIN user u ON v.id_user = u.id_user WHERE 1=1";
$params = [];

if ($search !== '') {
    $query .= " AND (v.kode_voucher LIKE :search OR v.nama_voucher LIKE :search)";
    $params['search'] = '%' . $search . '%';
}

if ($filter === 'active') {
    $query .= " AND v.status_voucher = 'Active'";
} elseif ($filter === 'scheduled') {
    $query .= " AND v.status_voucher = 'Scheduled'";
} elseif ($filter === 'expired') {
    $query .= " AND v.status_voucher = 'Expired'";
}

$query .= " ORDER BY v.id_voucher DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$vouchers = $stmt->fetchAll();

// Get search-specific count
$filteredCount = count($vouchers);

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 5;
$totalPages = ceil($filteredCount / $limit);
$paginatedVouchers = array_slice($vouchers, ($page - 1) * $limit, $limit);

$startItem = ($filteredCount > 0) ? (($page - 1) * $limit) + 1 : 0;
$endItem = min($page * $limit, $filteredCount);

ob_start();
?>
<style>
/* Custom Figma style overrides for Voucher management */
.voucher-title-area {
    margin-bottom: 30px;
}
.voucher-sub-title {
    font-family: var(--font-sans);
    font-size: 12px;
    letter-spacing: 0.04em;
    color: rgba(255, 255, 255, 0.38);
    text-transform: uppercase;
    margin-top: 4px;
}
.btn-add-voucher {
    background-color: var(--gold) !important;
    color: #000000 !important;
    font-family: var(--font-sans) !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: 0.08em !important;
    text-transform: uppercase !important;
    border-radius: 4px !important;
    padding: 10px 20px !important;
    border: none !important;
    transition: all 0.2s ease !important;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}
.btn-add-voucher:hover {
    background-color: #fff !important;
    color: #000000 !important;
    transform: translateY(-1px);
}
.voucher-paginator-text {
    font-family: var(--font-sans);
    font-size: 13px;
    color: rgba(255, 255, 255, 0.38);
}
.paginator-arrow {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    background: rgba(255, 255, 255, 0.02);
    color: rgba(255, 255, 255, 0.6);
    border-radius: 4px;
    text-decoration: none;
    transition: all 0.2s ease;
}
.paginator-arrow:hover {
    background: rgba(255, 255, 255, 0.08);
    color: #fff;
}
.segmented-control {
    display: inline-flex;
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 6px;
    padding: 4px;
}
.segment-item {
    padding: 8px 20px;
    font-size: 12px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.4);
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.segment-item:hover {
    color: rgba(255, 255, 255, 0.85);
}
.segment-item.active {
    background: var(--gold);
    color: #000000;
}
.voucher-table-header {
    display: grid;
    grid-template-columns: 2.2fr 1fr 1fr 1fr 60px;
    padding: 12px 24px;
    font-size: 11px;
    letter-spacing: 0.08em;
    color: rgba(255, 255, 255, 0.3);
    text-transform: uppercase;
    font-weight: 600;
    border-bottom: 1px solid rgba(255, 255, 255, 0.04);
}
.voucher-list {
    margin-top: 10px;
}
.voucher-row {
    display: grid;
    grid-template-columns: 2.2fr 1fr 1fr 1fr 60px;
    align-items: center;
    background: rgba(255, 255, 255, 0.01);
    border: 1px solid rgba(255, 255, 255, 0.04);
    border-radius: 8px;
    padding: 20px 24px;
    margin-bottom: 12px;
    transition: all 0.25s ease;
}
.voucher-row:hover {
    border-color: rgba(197, 160, 89, 0.2);
    background: rgba(255, 255, 255, 0.03);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}
.voucher-code-title {
    font-family: var(--font-display);
    font-size: 15px;
    font-weight: 700;
    color: var(--gold);
    letter-spacing: 0.05em;
}
.voucher-campaign-desc {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.6);
    margin-top: 4px;
}
.voucher-value-text {
    font-size: 14px;
    font-weight: 600;
    color: #ffffff;
}
.voucher-expiry-text {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.7);
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    padding: 5px 12px;
    border-radius: 20px;
}
.status-pill.active {
    background: rgba(197, 160, 89, 0.08);
    color: var(--gold);
    border: 1px solid rgba(197, 160, 89, 0.2);
}
.status-pill.scheduled {
    background: rgba(23, 162, 184, 0.08);
    color: #17a2b8;
    border: 1px solid rgba(23, 162, 184, 0.2);
}
.status-pill.expired {
    background: rgba(255, 255, 255, 0.02);
    color: rgba(255, 255, 255, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.06);
}
.dropdown-menu {
    background-color: #0b0b0b !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    border-radius: 4px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5) !important;
}
.dropdown-item {
    font-size: 12px !important;
    padding: 10px 16px !important;
    color: rgba(255, 255, 255, 0.7) !important;
    transition: all 0.2s ease !important;
}
.dropdown-item:hover {
    background-color: rgba(197, 160, 89, 0.08) !important;
    color: var(--gold) !important;
}
.dropdown-item.text-danger:hover {
    background-color: rgba(220, 53, 69, 0.08) !important;
    color: #ff6b6b !important;
}
.ticket-icon {
    width: 36px;
    height: 36px;
    background: rgba(197, 160, 89, 0.05);
    border: 1px dashed rgba(197, 160, 89, 0.2);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gold);
}
</style>

<div class="container-fluid p-0">
    <!-- Header Title and Add Button -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start voucher-title-area gap-3">
        <div>
            <h2 class="font-display text-white mb-0" style="font-size: 28px; letter-spacing: 0.02em;">Manajament Diskon</h2>
            <div class="voucher-sub-title">Kode Voucher</div>
        </div>
        <div>
            <a href="<?= htmlspecialchars(base_url('admin/diskon_tambah.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn-add-voucher">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                ADD NEW VOUCHER
            </a>
        </div>
    </div>

    <!-- Notification Messages -->
    <?php if ($msg = get_flash('success')): ?>
        <div class="alert alert-success bg-opacity-10 bg-success border-success text-success rounded-0 mb-4" style="font-size: 13px;">
            <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>
    <?php if ($msg = get_flash('error')): ?>
        <div class="alert alert-danger bg-opacity-10 bg-danger border-danger text-danger rounded-0 mb-4" style="font-size: 13px;">
            <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <!-- Paginator summary row -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="voucher-paginator-text">
            Showing <?= $startItem ?>-<?= $endItem ?> of <?= $filteredCount ?> vouchers
        </div>
        <div class="d-flex gap-1">
            <a href="?filter=<?= urlencode($filter) ?><?= $search ? '&search='.urlencode($search) : '' ?>&page=<?= max(1, $page - 1) ?>" class="paginator-arrow <?= $page <= 1 ? 'opacity-50 pe-none' : '' ?>" aria-label="Previous page">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" style="transform: scaleX(-1);"><path fill="currentColor" d="M5 13.5h3v-3H5zm5 0h3v-3h-3zM17 9l-1 1l2 2l-2 2l1 1l3-3z"></path></svg>
            </a>
            <a href="?filter=<?= urlencode($filter) ?><?= $search ? '&search='.urlencode($search) : '' ?>&page=<?= min(max(1, $totalPages), $page + 1) ?>" class="paginator-arrow <?= $page >= $totalPages ? 'opacity-50 pe-none' : '' ?>" aria-label="Next page">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"><path fill="currentColor" d="M5 13.5h3v-3H5zm5 0h3v-3h-3zM17 9l-1 1l2 2l-2 2l1 1l3-3z"></path></svg>
            </a>
        </div>
    </div>

    <!-- Search & Filter Tab Area -->
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <!-- Search -->
        <form method="get" action="" class="flex-grow-1 m-0">
            <div class="position-relative">
                <span class="position-absolute top-50 start-0 translate-middle-y ps-3" style="color: rgba(255, 255, 255, 0.25);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" name="search" class="form-control bg-black text-white border-secondary ps-5 rounded-0" placeholder="Search by voucher code or campaign name..." value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" style="font-size: 13px; height: 42px; border-color: rgba(255, 255, 255, 0.08) !important;">
                <?php if ($filter !== 'all'): ?>
                    <input type="hidden" name="filter" value="<?= htmlspecialchars($filter, ENT_QUOTES, 'UTF-8') ?>">
                <?php endif; ?>
            </div>
        </form>

        <!-- Segment Controller Filter -->
        <div class="segmented-control">
            <a href="?filter=all<?= $search ? '&search='.urlencode($search) : '' ?>" class="segment-item <?= $filter === 'all' ? 'active' : '' ?>">All</a>
            <a href="?filter=active<?= $search ? '&search='.urlencode($search) : '' ?>" class="segment-item <?= $filter === 'active' ? 'active' : '' ?>">Active</a>
            <a href="?filter=scheduled<?= $search ? '&search='.urlencode($search) : '' ?>" class="segment-item <?= $filter === 'scheduled' ? 'active' : '' ?>">Scheduled</a>
            <a href="?filter=expired<?= $search ? '&search='.urlencode($search) : '' ?>" class="segment-item <?= $filter === 'expired' ? 'active' : '' ?>">Expired</a>
        </div>
    </div>

    <!-- Voucher Details Grid Table -->
    <div class="voucher-table-header d-none d-md-grid">
        <div>Voucher Details</div>
        <div>Value</div>
        <div>Expiry Date</div>
        <div>Status</div>
        <div class="text-end">Actions</div>
    </div>

    <div class="voucher-list">
        <?php foreach ($paginatedVouchers as $v): ?>
            <?php
            // Date formatting
            $expiryFormatted = date('M d, Y', strtotime($v['tanggal_berakhir']));
            
            // Value formatting
            $valueDisplay = '';
            if ($v['jenis_voucher'] === 'Persentase') {
                $valueDisplay = (int)$v['nilai_voucher'] . '% OFF';
            } else {
                $valueDisplay = 'Rp ' . number_format((float)$v['nilai_voucher'], 0, ',', '.');
            }

            // Status style
            $statusClass = 'active';
            $statusLabel = 'Active';
            if ($v['status_voucher'] === 'Expired') {
                $statusClass = 'expired';
                $statusLabel = 'Expired';
            } elseif ($v['status_voucher'] === 'Scheduled') {
                $statusClass = 'scheduled';
                $statusLabel = 'Scheduled';
            }
            ?>
            <div class="voucher-row flex-column flex-md-row gap-3 gap-md-0">
                <!-- Voucher Details -->
                <div class="d-flex align-items-center gap-3">
                    <div class="ticket-icon d-none d-sm-flex">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 12h2M3 12h2M12 3v2M12 19v2M2 5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v4a2 2 0 0 0-2 2 2 2 0 0 0 2 2v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-4a2 2 0 0 0 2-2 2 2 0 0 0-2-2V5z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="voucher-code-title"><?= htmlspecialchars($v['kode_voucher'], ENT_QUOTES, 'UTF-8') ?></div>
                        <div class="voucher-campaign-desc">
                            <?= htmlspecialchars($v['nama_voucher'], ENT_QUOTES, 'UTF-8') ?>
                            <span class="text-secondary small d-block" style="font-size: 10px; margin-top: 2px;">
                                Oleh: <?= htmlspecialchars($v['pembuat'] ?? 'Sistem', ENT_QUOTES, 'UTF-8') ?> 
                                <span class="text-gold" style="font-size: 9px; text-transform: uppercase;">(<?= htmlspecialchars($v['role'] ?? 'admin', ENT_QUOTES, 'UTF-8') ?>)</span>
                            </span>
                            <span class="text-secondary small d-block" style="font-size: 10px; margin-top: 2px;">
                                Min. Pembelian: <span class="text-white">Rp <?= number_format((float)$v['minimal_pembelian'], 0, ',', '.') ?></span>
                                &bull; Min. Porsi: <span class="text-white"><?= (int)$v['minimal_porsi'] ?> porsi</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Value -->
                <div>
                    <span class="d-inline-block d-md-none text-secondary small me-2">Value:</span>
                    <span class="voucher-value-text"><?= htmlspecialchars($valueDisplay, ENT_QUOTES, 'UTF-8') ?></span>
                </div>

                <!-- Expiry Date -->
                <div>
                    <span class="d-inline-block d-md-none text-secondary small me-2">Expires:</span>
                    <span class="voucher-expiry-text">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity: 0.5;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <?= $expiryFormatted ?>
                    </span>
                </div>

                <!-- Status -->
                <div>
                    <span class="d-inline-block d-md-none text-secondary small me-2">Status:</span>
                    <span class="status-pill <?= $statusClass ?>">
                        <?= $statusLabel ?>
                    </span>
                </div>

                <!-- Actions -->
                <div class="text-md-end w-100 w-md-auto">
                    <a href="<?= htmlspecialchars(base_url('admin/diskon_edit.php?id=' . $v['id_voucher']), ENT_QUOTES, 'UTF-8'); ?>" class="text-gold small fw-medium text-decoration-none">Edit</a>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($paginatedVouchers)): ?>
            <div class="py-5 text-center bg-black-50 border border-secondary border-opacity-10 rounded-3">
                <p class="text-secondary small mb-0">Tidak ada voucher ditemukan.</p>
                <?php if ($search !== '' || $filter !== 'all'): ?>
                    <a href="?filter=all" class="btn btn-link text-gold small mt-2 text-decoration-none">Reset Filter & Pencarian</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administration',
    'title' => 'Manajemen Diskon',
    'description' => 'Tingkatkan retensi dan sales dengan manajemen diskon yang terkontrol.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
