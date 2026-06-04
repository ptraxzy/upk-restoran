<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('kasir');

$title = 'Status Pesanan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';
$pdo = db();

// Handle POST form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pesanan = (int)($_POST['id_pesanan'] ?? 0);
    $status = $_POST['status'] ?? '';
    $note = trim($_POST['note'] ?? '');
    $valid_statuses = ['Menunggu Pembayaran', 'Diproses', 'Sedang Disiapkan', 'Siap Saji', 'Selesai', 'Dibatalkan'];

    if ($id_pesanan > 0 && in_array($status, $valid_statuses, true)) {
        // Ambil status pembayaran pesanan saat ini
        $stmtCheckPay = $pdo->prepare("SELECT status FROM pembayaran WHERE id_pesanan = ?");
        $stmtCheckPay->execute([$id_pesanan]);
        $payStatus = $stmtCheckPay->fetchColumn();

        if ($payStatus !== 'Lunas' && in_array($status, ['Diproses', 'Sedang Disiapkan', 'Siap Saji', 'Selesai'], true)) {
            set_flash('error', 'Gagal: Pesanan belum dibayar. Silakan selesaikan pembayaran terlebih dahulu!');
            redirect(base_url('kasir/pesanan_status.php?id=' . $id_pesanan));
        }

        $pdo->beginTransaction();
        try {
            $id_karyawan = $_SESSION['id_user'] ?? null;
            
            // Update status pesanan
            $stmt = $pdo->prepare("UPDATE pesanan SET status_pesanan = ?, id_karyawan = ? WHERE id_pesanan = ?");
            $stmt->execute([$status, $id_karyawan, $id_pesanan]);
            
            // Sinkronisasi status pembayaran secara otomatis
            if (in_array($status, ['Diproses', 'Sedang Disiapkan', 'Siap Saji', 'Selesai'], true)) {
                $stmtBayar = $pdo->prepare("
                    UPDATE pembayaran 
                    SET status = 'Lunas', 
                        tanggal_pembayaran = COALESCE(tanggal_pembayaran, NOW()),
                        id_karyawan = COALESCE(id_karyawan, ?) 
                    WHERE id_pesanan = ? AND status != 'Lunas'
                ");
                $stmtBayar->execute([$id_karyawan, $id_pesanan]);
            } elseif ($status === 'Dibatalkan') {
                $stmtBayar = $pdo->prepare("
                    UPDATE pembayaran 
                    SET status = 'Batal' 
                    WHERE id_pesanan = ?
                ");
                $stmtBayar->execute([$id_pesanan]);
            }
            
            $pdo->commit();
            set_flash('success', "Status pesanan #LP-$id_pesanan berhasil diubah menjadi $status.");
        } catch (Exception $e) {
            $pdo->rollBack();
            set_flash('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    } else {
        set_flash('error', 'Permintaan tidak valid.');
    }
    redirect(base_url('kasir/pesanan_status.php'));
}

// Get selected order (if via ?id=)
$selectedId = (int)($_GET['id'] ?? 0);
$selected = null;
$selectedDetails = [];
if ($selectedId > 0) {
    $stmt = $pdo->prepare("SELECT p.*, u.username, pb.status AS status_pembayaran FROM pesanan p LEFT JOIN pelanggan u ON p.id_pelanggan = u.id_pelanggan LEFT JOIN pembayaran pb ON p.id_pesanan = pb.id_pesanan WHERE p.id_pesanan = ?");
    $stmt->execute([$selectedId]);
    $selected = $stmt->fetch();

    if ($selected) {
        $stmtD = $pdo->prepare("SELECT dp.jumlah, m.nama_menu FROM detail_pesanan dp JOIN menu m ON dp.id_menu = m.id_menu WHERE dp.id_pesanan = ?");
        $stmtD->execute([$selectedId]);
        $selectedDetails = $stmtD->fetchAll();
    }
}

// List active orders for selection
$stmtActive = $pdo->query("
    SELECT p.id_pesanan, p.no_meja, p.status_pesanan, u.username
    FROM pesanan p
    LEFT JOIN pelanggan u ON p.id_pelanggan = u.id_pelanggan
    WHERE p.status_pesanan IN ('Diproses', 'Sedang Disiapkan', 'Siap Saji', 'Menunggu Pembayaran')
    ORDER BY p.tanggal_pesanan ASC
");
$activeOrders = $stmtActive->fetchAll();

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
    padding: 10px 14px;
    font-size: 13px;
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
    top: 100%;
    left: 0;
    width: 100%;
    background-color: #0a0a0a;
    border: 1px solid rgba(255, 255, 255, 0.15);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.8);
    z-index: 1050;
    margin-top: 4px;
    max-height: 220px;
    overflow-y: auto;
}

.custom-dropdown.open .custom-dropdown-menu {
    display: block;
}

.custom-dropdown-item {
    padding: 10px 14px;
    font-size: 13px;
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
<section class="row g-5">
    <div class="col-lg-7">
        <article class="section-panel h-100">
            <h3 class="panel-title mb-2">Update Status Pesanan</h3>
            <p class="text-secondary small mb-4">Panel status untuk memindahkan order dari diproses ke selesai atau dibatalkan.</p>

            <?php if ($selected): ?>
            <div class="p-4 border border-secondary mb-4" style="background: rgba(197,160,89,0.05);">
                <p class="text-muted small mb-2">Order Terpilih</p>
                <h4 class="font-display text-white mb-1" style="font-size: 22px;">#LP-<?= $selected['id_pesanan']; ?> • Meja <?= htmlspecialchars($selected['no_meja'], ENT_QUOTES, 'UTF-8'); ?></h4>
                <p class="text-muted small mb-3"><?= htmlspecialchars($selected['username'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?> • <?= date('H:i', strtotime($selected['tanggal_pesanan'])); ?></p>
                <?php foreach ($selectedDetails as $d): ?>
                    <span class="text-gold" style="font-size: 12px;"><?= $d['jumlah']; ?>x <?= htmlspecialchars($d['nama_menu'], ENT_QUOTES, 'UTF-8'); ?></span><br>
                <?php endforeach; ?>

                <form class="mt-4 d-flex flex-column gap-4" action="<?= htmlspecialchars(base_url('kasir/pesanan_status.php'), ENT_QUOTES, 'UTF-8'); ?>" method="POST">
                    <input type="hidden" name="id_pesanan" value="<?= $selected['id_pesanan']; ?>">
                    <div>
                        <label class="form-label small text-muted mb-1" for="status">Status Pesanan</label>
                        <div class="custom-dropdown" id="dropdown-container">
                            <input type="hidden" name="status" id="status-input" value="<?= htmlspecialchars($selected['status_pesanan'], ENT_QUOTES, 'UTF-8') ?>">
                            <button type="button" class="custom-dropdown-trigger" onclick="toggleDropdownMenuSingle(event)">
                                <span id="trigger-label"><?= htmlspecialchars($selected['status_pesanan'], ENT_QUOTES, 'UTF-8') ?></span>
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="chevron"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                            <div class="custom-dropdown-menu">
                                <?php
                                $isLunas = (($selected['status_pembayaran'] ?? '') === 'Lunas');
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
                                    <div class="custom-dropdown-item <?= $opt['disabled'] ? 'disabled' : '' ?> <?= $selected['status_pesanan'] === $opt['value'] ? 'selected' : '' ?>" 
                                         data-value="<?= htmlspecialchars($opt['value'], ENT_QUOTES, 'UTF-8') ?>"
                                         onclick="selectDropdownOptionStatus('<?= htmlspecialchars($opt['value'], ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($opt['label'], ENT_QUOTES, 'UTF-8') ?>', <?= $opt['disabled'] ? 'true' : 'false' ?>)">
                                        <?= htmlspecialchars($opt['label'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label small text-muted mb-1" for="note">Catatan Shift</label>
                        <textarea class="form-control bg-dark text-white border-secondary rounded-0" id="note" name="note" placeholder="Catatan singkat untuk kitchen atau kasir berikutnya."></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-warning rounded-0 fw-medium px-4 py-2" type="submit">Simpan Status</button>
                        <a class="btn btn-outline-warning rounded-0 fw-medium px-4 py-2 text-white" href="<?= htmlspecialchars(base_url('kasir/pesanan.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <div class="py-5 text-center">
                <p class="text-muted mb-3">Pilih pesanan dari daftar untuk mengubah statusnya.</p>
            </div>
            <?php endif; ?>
        </article>
    </div>

    <aside class="col-lg-5">
        <article class="section-panel h-100">
            <h3 class="panel-title mb-4">Pesanan Aktif</h3>
            <div class="compact-list">
                <?php foreach ($activeOrders as $o): ?>
                <div class="compact-list-item">
                    <div>
                        <p class="fw-medium text-white mb-1">#LP-<?= $o['id_pesanan']; ?> • Meja <?= htmlspecialchars($o['no_meja'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="small text-secondary mb-0"><?= htmlspecialchars($o['username'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?> • <?= htmlspecialchars($o['status_pesanan'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <a class="text-gold small text-decoration-none border-bottom border-gold pb-1" href="<?= htmlspecialchars(base_url('kasir/pesanan_status.php?id=' . $o['id_pesanan']), ENT_QUOTES, 'UTF-8'); ?>">Update</a>
                </div>
                <?php endforeach; ?>
                <?php if (empty($activeOrders)): ?>
                    <p class="text-muted small text-center py-4">Tidak ada pesanan aktif.</p>
                <?php endif; ?>
            </div>
        </article>
    </aside>
</section>
<script>
function toggleDropdownMenuSingle(event) {
    event.stopPropagation();
    const dropdown = document.getElementById('dropdown-container');
    if (dropdown) {
        dropdown.classList.toggle('open');
    }
}

function selectDropdownOptionStatus(value, label, disabled) {
    if (disabled) return;
    const input = document.getElementById('status-input');
    const triggerLabel = document.getElementById('trigger-label');
    if (input && triggerLabel) {
        input.value = value;
        triggerLabel.textContent = value;
        
        // Update selected class in items
        document.querySelectorAll('.custom-dropdown-item').forEach(el => {
            el.classList.remove('selected');
            if (el.getAttribute('data-value') === value) {
                el.classList.add('selected');
            }
        });
    }
    // Close dropdown
    const dropdown = document.getElementById('dropdown-container');
    if (dropdown) {
        dropdown.classList.remove('open');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.custom-dropdown')) {
        const dropdown = document.getElementById('dropdown-container');
        if (dropdown) {
            dropdown.classList.remove('open');
        }
    }
});
</script>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Service Floor',
    'title' => 'Update Status',
    'description' => 'Kontrol cepat untuk menjaga flow pesanan tetap terlihat dan sinkron dengan dapur.',
    'nav_sections' => staff_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
