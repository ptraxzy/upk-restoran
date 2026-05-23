<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('admin');

$title = 'Kelola Pesanan';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

$pdo = db();

$stmt = $pdo->query('
    SELECT p.*, u.username, pb.metode AS metode_pembayaran, pb.status AS status_pembayaran
    FROM pesanan p
    LEFT JOIN user u ON p.id_user = u.id_user
    LEFT JOIN pembayaran pb ON p.id_pesanan = pb.id_pesanan
    ORDER BY p.tanggal_pesanan DESC
');
$orders = $stmt->fetchAll();

ob_start();
?>
<section class="row g-5 animate-fade-in-up">
    <div class="col-12">
        <article class="section-panel h-100">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom border-soft pb-4 mb-4 gap-3">
                <div>
                    <h3 class="font-display text-white m-0" style="font-size: 24px;">Semua Pesanan Restoran</h3>
                    <p class="text-secondary small mb-0 mt-1">Pantau dan kelola seluruh transaksi serta status pesanan aktif.</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No. Order</th>
                            <th>Pelanggan</th>
                            <th>Meja</th>
                            <th>Waktu Order</th>
                            <th>Total Harga</th>
                            <th>Metode</th>
                            <th>Status Bayar</th>
                            <th>Status Pesanan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <?php
                        $statusClass = 'badge ';
                        $status = $order['status_pesanan'];
                        if ($status === 'Menunggu Pembayaran') {
                            $statusClass .= 'bg-danger text-white';
                        } elseif ($status === 'Diproses' || $status === 'Sedang Disiapkan') {
                            $statusClass .= 'bg-warning text-dark';
                        } elseif ($status === 'Siap Saji') {
                            $statusClass .= 'bg-info text-dark';
                        } elseif ($status === 'Selesai') {
                            $statusClass .= 'bg-success text-white';
                        } else {
                            $statusClass .= 'bg-secondary text-white';
                        }

                        // Payment status class
                        $payStatusClass = 'badge ';
                        $payStatus = $order['status_pembayaran'] ?? 'Menunggu';
                        if ($payStatus === 'Lunas') {
                            $payStatusClass .= 'bg-success text-white';
                        } else {
                            $payStatusClass .= 'bg-danger text-white';
                        }
                        ?>
                        <tr>
                            <td class="fw-medium text-gold">#LP-<?= $order['id_pesanan']; ?></td>
                            <td class="text-white"><?= htmlspecialchars((string)($order['username'] ?? 'Guest'), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-secondary">Meja <?= htmlspecialchars((string)$order['no_meja'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-secondary" style="font-size: 12px;"><?= date('d M Y, H:i', strtotime($order['tanggal_pesanan'])); ?></td>
                            <td class="text-white fw-medium"><?= rupiah((float)$order['total_harga']); ?></td>
                            <td class="text-secondary" style="font-size: 12px;"><?= htmlspecialchars((string)($order['metode_pembayaran'] ?? 'QRIS')); ?></td>
                            <td><span class="<?= $payStatusClass; ?>"><?= $payStatus === 'Lunas' ? 'Lunas' : 'Belum Bayar'; ?></span></td>
                            <td><span class="<?= $statusClass; ?>"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></span></td>
                            <td class="text-end">
                                <?php if ($payStatus !== 'Lunas' && ($order['metode_pembayaran'] ?? 'QRIS') === 'Tunai'): ?>
                                    <a href="<?= base_url('actions/pesanan/confirm_cash.php?id_pesanan=' . $order['id_pesanan']); ?>" class="btn btn-sm btn-warning rounded-0 fw-semibold" style="font-size: 10px; padding: 4px 8px;">Konfirmasi Cash</a>
                                <?php elseif ($payStatus !== 'Lunas' && ($order['metode_pembayaran'] ?? 'QRIS') === 'QRIS'): ?>
                                    <a href="<?= base_url('actions/pesanan/simulate_pay.php?id_pesanan=' . $order['id_pesanan']); ?>" class="btn btn-sm btn-outline-success rounded-0 fw-semibold" style="font-size: 10px; padding: 4px 8px; border-color: #28a745 !important; color: #28a745 !important;">Simulasi QRIS</a>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">Belum ada pesanan terdaftar.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>
    </div>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'brand' => 'Lumière',
    'badge' => 'Administration',
    'title' => 'Manajemen Pesanan',
    'description' => 'Kelola dan pantau seluruh alur pesanan dari satu dasbor terpusat.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
