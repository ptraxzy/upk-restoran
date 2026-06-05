<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('kasir');

$title = 'Layanan Aktif';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

// Fetch active and recently completed orders from database
$pdo = db();
$stmt = $pdo->query("
    SELECT p.id_pesanan, p.no_meja, p.status_pesanan, p.tanggal_pesanan,
           TIMESTAMPDIFF(MINUTE, p.tanggal_pesanan, NOW()) as menit_menunggu,
           pl.username, pb.status AS status_pembayaran
    FROM pesanan p
    LEFT JOIN pelanggan pl ON p.id_pelanggan = pl.id_pelanggan
    LEFT JOIN pembayaran pb ON p.id_pesanan = pb.id_pesanan
    WHERE p.status_pesanan IN ('Diproses', 'Sedang Disiapkan', 'Siap Saji', 'Menunggu Pembayaran', 'Selesai')
    ORDER BY p.id_pesanan DESC
");
$pesananList = $stmt->fetchAll();

// Fetch details for each order
$pesananDetails = [];
if (count($pesananList) > 0) {
    $ids = array_column($pesananList, 'id_pesanan');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmtDetail = $pdo->prepare("
        SELECT dp.id_pesanan, dp.jumlah, m.nama_menu
        FROM detail_pesanan dp
        JOIN menu m ON dp.id_menu = m.id_menu
        WHERE dp.id_pesanan IN ($placeholders)
    ");
    $stmtDetail->execute($ids);
    $details = $stmtDetail->fetchAll();
    
    foreach ($details as $detail) {
        $pesananDetails[$detail['id_pesanan']][] = $detail;
    }
}

// Calculate active orders (excluding completed Selesai)
$activeOrders = array_filter($pesananList, fn($p) => $p['status_pesanan'] !== 'Selesai');
$countActive = count($activeOrders);

// "Needs attention" count: active orders that are unpaid OR waiting for more than 30 minutes
$countPerhatian = count(array_filter($activeOrders, fn($p) => $p['status_pesanan'] === 'Menunggu Pembayaran' || $p['menit_menunggu'] >= 30));

// Calculate counts for all tabs
$countSemua = $countActive;
$countDisiapkan = count(array_filter($pesananList, fn($p) => $p['status_pesanan'] === 'Sedang Disiapkan' || $p['status_pesanan'] === 'Diproses'));
$countSiap = count(array_filter($pesananList, fn($p) => $p['status_pesanan'] === 'Siap Saji'));
$countSelesai = count(array_filter($pesananList, fn($p) => $p['status_pesanan'] === 'Selesai'));

// Filter display list
$filter = $_GET['filter'] ?? 'semua';
$displayList = $pesananList;
if ($filter === 'semua') {
    $displayList = $activeOrders;
} elseif ($filter === 'disiapkan') {
    $displayList = array_filter($pesananList, fn($p) => $p['status_pesanan'] === 'Sedang Disiapkan' || $p['status_pesanan'] === 'Diproses');
} elseif ($filter === 'siap') {
    $displayList = array_filter($pesananList, fn($p) => $p['status_pesanan'] === 'Siap Saji');
} elseif ($filter === 'selesai') {
    $displayList = array_filter($pesananList, fn($p) => $p['status_pesanan'] === 'Selesai');
}

ob_start();
?>
<style>
/* Custom Dropdown Styling */
.custom-dropdown {
    position: relative;
    width: 100%;
}

.custom-dropdown-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    background-color: #0c0c0c;
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.15);
    padding: 8px 12px;
    font-size: 12px;
    font-family: var(--font-body);
    cursor: pointer;
    text-align: left;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.custom-dropdown-trigger:hover, .custom-dropdown-trigger:focus {
    border-color: var(--gold);
    outline: none;
}

.custom-dropdown-trigger .chevron {
    transition: transform 0.2s;
    opacity: 0.7;
}

.custom-dropdown.open .custom-dropdown-trigger .chevron {
    transform: rotate(180deg);
}

.custom-dropdown-menu {
    display: none;
    position: absolute;
    bottom: 100%;
    left: 0;
    width: 100%;
    background-color: #0a0a0a;
    border: 1px solid rgba(255, 255, 255, 0.15);
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.8);
    z-index: 1050;
    margin-bottom: 4px;
    max-height: 220px;
    overflow-y: auto;
}

.custom-dropdown.open .custom-dropdown-menu {
    display: block;
}

.custom-dropdown-item {
    padding: 8px 12px;
    font-size: 12px;
    font-family: var(--font-body);
    color: rgba(255, 255, 255, 0.8);
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
}

.custom-dropdown-item:hover:not(.disabled) {
    background-color: rgba(201, 168, 76, 0.1);
    color: var(--gold);
}

.custom-dropdown-item.selected {
    background-color: rgba(201, 168, 76, 0.15);
    color: var(--gold);
    font-weight: 500;
}

.custom-dropdown-item.disabled {
    color: #555555;
    cursor: not-allowed;
    background-color: transparent;
}
</style>
<section style="background: transparent; border: none; padding: 0;">
    <div class="d-flex flex-column gap-3 flex-md-row justify-content-md-between align-items-md-start mb-5">
        <div style="max-width: 600px;">
            <h2 class="font-display text-white mb-2" style="font-size: 36px; font-weight: normal; letter-spacing: -0.01em;">Layanan Aktif</h2>
            <p class="text-secondary small" style="line-height: 1.6; font-size: 13px;">
                Memantau <?= $countActive ?> meja aktif. <?= $countPerhatian ?> membutuhkan perhatian segera.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 text-secondary" style="font-size: 13px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            <span><?= date('H:i T') ?></span>
        </div>
    </div>

    <div class="d-flex gap-4 mb-5 border-bottom border-soft overflow-auto" style="scrollbar-width: none;">
        <a class="<?= $filter === 'semua' ? 'text-gold border-bottom border-gold border-2' : 'text-secondary hover-gold' ?> small fw-medium text-decoration-none pb-3 whitespace-nowrap flex-shrink-0" href="?filter=semua" style="letter-spacing: 0.06em; text-transform: uppercase;">Semua Aktif (<?= $countSemua ?>)</a>
        <a class="<?= $filter === 'disiapkan' ? 'text-gold border-bottom border-gold border-2' : 'text-secondary hover-gold' ?> small fw-medium text-decoration-none pb-3 whitespace-nowrap flex-shrink-0" href="?filter=disiapkan" style="letter-spacing: 0.06em; text-transform: uppercase;">Sedang Disiapkan (<?= $countDisiapkan ?>)</a>
        <a class="<?= $filter === 'siap' ? 'text-gold border-bottom border-gold border-2' : 'text-secondary hover-gold' ?> small fw-medium text-decoration-none pb-3 whitespace-nowrap flex-shrink-0" href="?filter=siap" style="letter-spacing: 0.06em; text-transform: uppercase;">Siap Saji (<?= $countSiap ?>)</a>
        <a class="<?= $filter === 'selesai' ? 'text-gold border-bottom border-gold border-2' : 'text-secondary hover-gold' ?> small fw-medium text-decoration-none pb-3 whitespace-nowrap flex-shrink-0" href="?filter=selesai" style="letter-spacing: 0.06em; text-transform: uppercase;">Selesai (<?= $countSelesai ?>)</a>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 row-cols-xxl-4 g-4">
        <?php if (empty($displayList)): ?>
            <div class="col-12 w-100 py-5 text-center">
                <p class="text-muted">Tidak ada pesanan untuk kategori ini.</p>
            </div>
        <?php endif; ?>

        <?php foreach ($displayList as $p): ?>
            <?php
            $statusClass = 'order-badge-pill-waiting';
            $statusText = 'DIPROSES';
            
            if ($p['status_pesanan'] === 'Menunggu Pembayaran') {
                $statusClass = 'order-badge-pill-unpaid';
                $statusText = 'BELUM BAYAR';
            } elseif ($p['status_pesanan'] === 'Sedang Disiapkan') {
                $statusClass = 'order-badge-pill-preparing';
                $statusText = 'SEDANG DISIAPKAN';
            } elseif ($p['status_pesanan'] === 'Siap Saji') {
                $statusClass = 'order-badge-pill-ready';
                $statusText = 'SIAP DIAMBIL';
            } elseif ($p['status_pesanan'] === 'Selesai') {
                $statusClass = 'order-badge-pill-completed';
                $statusText = 'SELESAI';
            }
            
            $cardBorder = '';
            if ($p['status_pesanan'] === 'Siap Saji') {
                $cardBorder = 'border-color: rgba(201, 168, 76, 0.4); box-shadow: 0 0 20px rgba(201, 168, 76, 0.05);';
            } elseif ($p['status_pesanan'] === 'Menunggu Pembayaran') {
                $cardBorder = 'border-color: rgba(220, 53, 69, 0.25);';
            }

            // Check if there is Asparagus in the order details
            $hasAsparagus = false;
            if (isset($pesananDetails[$p['id_pesanan']])) {
                foreach ($pesananDetails[$p['id_pesanan']] as $detail) {
                    if (stripos($detail['nama_menu'], 'Asparagus') !== false) {
                        $hasAsparagus = true;
                        break;
                    }
                }
            }
            ?>
            <div class="col animate-fade-in-up">
                <article class="premium-card-glass h-100 d-flex flex-column" style="border-radius: 0; padding: 24px; <?= $cardBorder ?>">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom border-soft">
                        <div class="d-flex align-items-center gap-2">
                            <span class="font-display text-white" style="font-size: 32px; line-height: 1; font-weight: bold;"><?= htmlspecialchars($p['no_meja'], ENT_QUOTES, 'UTF-8') ?></span>
                            <div>
                                <p class="text-secondary small m-0" style="font-size: 10px; letter-spacing: 0.06em; font-weight: 600;">#LP-<?= $p['id_pesanan'] ?> &bull; MEJA &bull; <?= htmlspecialchars((string)($p['username'] ?? 'Guest'), ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="text-muted small m-0" style="font-size: 9px; margin-top: 2px; white-space: nowrap;"><?= date('d M Y, H:i', strtotime($p['tanggal_pesanan'])) ?></p>
                            </div>
                        </div>
                        
                        <div class="order-badge-pill <?= $statusClass ?>">
                            <span class="badge-time">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <?= $p['menit_menunggu'] ?>m
                            </span>
                            <span style="font-weight: bold; font-size: 9px;"><?= $statusText ?></span>
                        </div>
                    </div>
                    
                    <!-- Menu items list container (scrolls if > 3 items) -->
                    <div class="card-menu-list flex-grow-1 mb-3">
                        <?php if (isset($pesananDetails[$p['id_pesanan']])): ?>
                            <?php foreach ($pesananDetails[$p['id_pesanan']] as $detail): ?>
                            <div class="d-flex gap-2 align-items-baseline mb-2">
                                <span class="text-gold fw-medium" style="font-size: 11px; font-family: var(--font-body);"><?= $detail['jumlah'] ?>x</span>
                                <span class="text-white" style="font-size: 12px; font-family: var(--font-body); opacity: 0.95;"><?= htmlspecialchars($detail['nama_menu'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted small">Tidak ada detail menu.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Allergen Alert Box (Markdown-style) -->
                    <?php if ($hasAsparagus): ?>
                        <blockquote style="border-left: 3px solid #ff6b6b; background: rgba(220, 53, 69, 0.05); padding: 12px; margin: 12px 0 16px 0; font-size: 11px; line-height: 1.5; color: rgba(255, 255, 255, 0.85); font-family: var(--font-body); font-style: normal;">
                            <strong style="color: #ff6b6b; letter-spacing: 0.04em; font-size: 9px; display: block; margin-bottom: 4px; text-transform: uppercase;">PERINGATAN ALERGI</strong>
                            Dilarang keras menggunakan produk susu untuk persiapan Asparagus. Gunakan minyak zaitun.
                        </blockquote>
                    <?php endif; ?>

                    <!-- Action buttons area -->
                    <div class="mt-auto pt-3 border-top border-soft">
                        <div id="actions-<?= $p['id_pesanan'] ?>" class="d-flex gap-2 w-100">
                            <?php if ($p['status_pesanan'] === 'Menunggu Pembayaran'): ?>
                                <button type="button" class="btn-figma-secondary px-3" onclick="toggleStatusSelector(<?= $p['id_pesanan'] ?>)" aria-label="Ubah status">
                                    UBAH
                                </button>
                                <a href="<?= base_url('kasir/pembayaran_cetak.php?id=' . $p['id_pesanan']) ?>" class="btn-figma-primary flex-grow-1">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="12" y1="10" x2="12" y2="10"></line><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                                    PROSES BAYAR
                                </a>
                            <?php elseif ($p['status_pesanan'] === 'Diproses'): ?>
                                <button type="button" class="btn-figma-secondary px-3" onclick="toggleStatusSelector(<?= $p['id_pesanan'] ?>)" aria-label="Ubah status">
                                    UBAH
                                </button>
                                <a href="<?= base_url('actions/pesanan/update_status.php?id=' . $p['id_pesanan'] . '&status=Sedang+Disiapkan&filter=' . $filter) ?>" class="btn-figma-primary flex-grow-1">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                    MULAI MENYIAPKAN
                                </a>
                            <?php elseif ($p['status_pesanan'] === 'Sedang Disiapkan'): ?>
                                <button type="button" class="btn-figma-secondary px-3" onclick="toggleStatusSelector(<?= $p['id_pesanan'] ?>)" aria-label="Ubah status">
                                    UBAH
                                </button>
                                <a href="<?= base_url('actions/pesanan/update_status.php?id=' . $p['id_pesanan'] . '&status=Siap+Saji&filter=' . $filter) ?>" class="btn-figma-primary flex-grow-1">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
                                    SIAP DIHIDANGKAN
                                </a>
                            <?php elseif ($p['status_pesanan'] === 'Siap Saji'): ?>
                                <button type="button" class="btn-figma-secondary px-3" onclick="toggleStatusSelector(<?= $p['id_pesanan'] ?>)" aria-label="Ubah status">
                                    UBAH
                                </button>
                                <a href="<?= base_url('actions/pesanan/update_status.php?id=' . $p['id_pesanan'] . '&status=Selesai&filter=' . $filter) ?>" class="btn-figma-primary flex-grow-1">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    TANDAI SELESAI
                                </a>
                            <?php elseif ($p['status_pesanan'] === 'Selesai'): ?>
                                <button type="button" class="btn-figma-secondary w-100" onclick="toggleStatusSelector(<?= $p['id_pesanan'] ?>)">
                                    UBAH STATUS
                                </button>
                            <?php endif; ?>
                        </div>

                        <!-- Dropdown fallback form (toggled on Ubah) -->
                        <form id="form-<?= $p['id_pesanan'] ?>" method="get" action="<?= base_url('actions/pesanan/update_status.php') ?>" class="m-0 d-none">
                            <input type="hidden" name="id" value="<?= $p['id_pesanan'] ?>">
                            <input type="hidden" name="filter" value="<?= htmlspecialchars($filter, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="small text-secondary" style="font-size: 10px; letter-spacing: 0.04em;">Perbarui Status</label>
                                <button type="button" class="btn p-0 text-secondary border-0 small" style="font-size: 10px;" onclick="toggleStatusSelector(<?= $p['id_pesanan'] ?>)">Batal</button>
                            </div>
                            <div class="custom-dropdown" id="dropdown-container-<?= $p['id_pesanan'] ?>">
                                <input type="hidden" name="status" id="input-status-<?= $p['id_pesanan'] ?>" value="<?= htmlspecialchars($p['status_pesanan'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="button" class="custom-dropdown-trigger" onclick="toggleDropdownMenu(<?= $p['id_pesanan'] ?>, event)">
                                    <span id="trigger-label-<?= $p['id_pesanan'] ?>"><?= htmlspecialchars($p['status_pesanan'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="chevron"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </button>
                                <div class="custom-dropdown-menu" id="dropdown-menu-<?= $p['id_pesanan'] ?>">
                                    <?php
                                    $isLunas = ($p['status_pembayaran'] === 'Lunas');
                                    $options = [
                                        ['value' => 'Menunggu Pembayaran', 'label' => 'Menunggu Pembayaran', 'disabled' => false],
                                        ['value' => 'Diproses', 'label' => 'Diproses' . (!$isLunas ? ' (Belum Bayar)' : ''), 'disabled' => !$isLunas],
                                        ['value' => 'Sedang Disiapkan', 'label' => 'Sedang Disiapkan' . (!$isLunas ? ' (Belum Bayar)' : ''), 'disabled' => !$isLunas],
                                        ['value' => 'Siap Saji', 'label' => 'Siap Saji' . (!$isLunas ? ' (Belum Bayar)' : ''), 'disabled' => !$isLunas],
                                        ['value' => 'Selesai', 'label' => 'Selesai' . (!$isLunas ? ' (Belum Bayar)' : ''), 'disabled' => !$isLunas],
                                        ['value' => 'Dibatalkan', 'label' => 'Dibatalkan', 'disabled' => false],
                                    ];
                                    foreach ($options as $opt):
                                    ?>
                                        <div class="custom-dropdown-item <?= $opt['disabled'] ? 'disabled' : '' ?> <?= $p['status_pesanan'] === $opt['value'] ? 'selected' : '' ?>" 
                                             onclick="selectDropdownOption(<?= $p['id_pesanan'] ?>, '<?= htmlspecialchars($opt['value'], ENT_QUOTES, 'UTF-8') ?>', <?= $opt['disabled'] ? 'true' : 'false' ?>)">
                                            <?= htmlspecialchars($opt['label'], ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script>
function toggleStatusSelector(id) {
    const actionsDiv = document.getElementById('actions-' + id);
    const formEl = document.getElementById('form-' + id);
    if (actionsDiv && formEl) {
        if (actionsDiv.classList.contains('d-none')) {
            actionsDiv.classList.remove('d-none');
            formEl.classList.add('d-none');
        } else {
            actionsDiv.classList.add('d-none');
            formEl.classList.remove('d-none');
            // Reset custom dropdown state when opening/closing
            const dropdown = document.getElementById('dropdown-container-' + id);
            if (dropdown) dropdown.classList.remove('open');
        }
    }
}

function toggleDropdownMenu(id, event) {
    event.stopPropagation();
    const dropdown = document.getElementById('dropdown-container-' + id);
    if (!dropdown) return;
    const isOpen = dropdown.classList.contains('open');
    
    // Close all other dropdowns
    document.querySelectorAll('.custom-dropdown').forEach(el => {
        el.classList.remove('open');
    });
    
    if (!isOpen) {
        dropdown.classList.add('open');
    }
}

function selectDropdownOption(id, value, disabled) {
    if (disabled) return;
    const input = document.getElementById('input-status-' + id);
    if (input) {
        input.value = value;
        const form = document.getElementById('form-' + id);
        if (form) {
            form.submit();
        }
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.custom-dropdown')) {
        document.querySelectorAll('.custom-dropdown').forEach(el => {
            el.classList.remove('open');
        });
    }
});
</script>
<?php
$content = ob_get_clean();
render_internal_shell([
    'brand' => 'Lumière',
    'badge' => 'Service Floor',
    'title' => 'Layanan Aktif',
    'description' => 'Memantau meja aktif dan pesanan real-time.',
    'nav_sections' => staff_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
