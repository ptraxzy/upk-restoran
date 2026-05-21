<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('kasir');

$title = 'Status Pembayaran';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';
$pdo = db();

// Fetch all payments with order and user info
$stmt = $pdo->query("
    SELECT py.id_pembayaran, py.total_bayar, py.metode, py.status, py.trx_id,
           p.id_pesanan, p.no_meja,
           u.username
    FROM pembayaran py
    JOIN pesanan p ON py.id_pesanan = p.id_pesanan
    LEFT JOIN user u ON p.id_user = u.id_user
    ORDER BY py.tanggal_pembayaran DESC
    LIMIT 20
");
$payments = $stmt->fetchAll();

$countMenunggu = count(array_filter($payments, fn($p) => $p['status'] === 'Menunggu'));
$countLunas = count(array_filter($payments, fn($p) => $p['status'] === 'Lunas'));

ob_start();
?>
<section class="row g-5">
    <div class="col-lg-8">
        <article class="section-panel h-100">
            <div class="panel-header d-flex flex-column gap-3 flex-md-row justify-content-md-between align-items-md-end">
                <div>
                    <h3 class="panel-title">Status Transaksi</h3>
                    <p class="panel-desc">Pantau status pembayaran untuk setiap pesanan yang sedang berjalan.</p>
                </div>
                <div class="d-flex flex-wrap gap-3">
                    <a class="btn btn-outline-warning" href="<?= htmlspecialchars(base_url('kasir/pembayaran.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Metode</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $pay): ?>
                        <tr>
                            <td class="text-white fw-medium">#LP-<?= $pay['id_pesanan']; ?></td>
                            <td><?= htmlspecialchars($pay['username'] ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-gold"><?= rupiah((float)$pay['total_bayar']); ?></td>
                            <td><?= htmlspecialchars($pay['metode'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php
                                $badgeClass = match($pay['status']) {
                                    'Menunggu' => 'bg-warning text-dark',
                                    'Lunas' => 'bg-success',
                                    'Gagal' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
                                ?>
                                <span class="badge <?= $badgeClass; ?>"><?= htmlspecialchars($pay['status'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($payments)): ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi pembayaran.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </article>
    </div>

    <aside class="col-lg-4">
        <article class="section-panel h-100">
            <h3 class="panel-title mb-4">Ringkasan</h3>
            <div class="row row-cols-2 g-4">
                <div class="col">
                    <article class="card p-4 h-100">
                        <p class="text-secondary small mb-2">Menunggu Bayar</p>
                        <p class="h2 text-gold font-display mb-0"><?= $countMenunggu; ?></p>
                    </article>
                </div>
                <div class="col">
                    <article class="card p-4 h-100">
                        <p class="text-secondary small mb-2">Sudah Lunas</p>
                        <p class="h2 text-gold font-display mb-0"><?= $countLunas; ?></p>
                    </article>
                </div>
            </div>
        </article>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Service Floor',
    'title' => 'Status Pembayaran',
    'description' => 'Pantau status pembayaran secara real-time untuk setiap transaksi.',
    'nav_sections' => staff_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
