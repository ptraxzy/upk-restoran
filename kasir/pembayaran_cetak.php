<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('kasir');

$title = 'Cetak Struk';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

require_once __DIR__ . '/../includes/database.php';
$pdo = db();

$id_pesanan = (int)($_GET['id'] ?? 0);

$pesanan = null;
$details = [];
$pembayaran = null;

if ($id_pesanan > 0) {
    // Fetch order
    $stmt = $pdo->prepare("SELECT p.*, u.username FROM pesanan p LEFT JOIN pelanggan u ON p.id_pelanggan = u.id_pelanggan WHERE p.id_pesanan = ?");
    $stmt->execute([$id_pesanan]);
    $pesanan = $stmt->fetch();

    if ($pesanan) {
        // Fetch details
        $stmtD = $pdo->prepare("SELECT dp.*, m.nama_menu FROM detail_pesanan dp JOIN menu m ON dp.id_menu = m.id_menu WHERE dp.id_pesanan = ?");
        $stmtD->execute([$id_pesanan]);
        $details = $stmtD->fetchAll();

        $stmtP = $pdo->prepare("
            SELECT py.*, COALESCE(u.nama_karyawan, u.username, kp.nama_karyawan, kp.username) AS nama_kasir 
            FROM pembayaran py 
            LEFT JOIN karyawan u ON py.id_karyawan = u.id_karyawan 
            LEFT JOIN pesanan p ON py.id_pesanan = p.id_pesanan
            LEFT JOIN karyawan kp ON p.id_karyawan = kp.id_karyawan
            WHERE py.id_pesanan = ? 
            ORDER BY py.id_pembayaran DESC LIMIT 1
        ");
        $stmtP->execute([$id_pesanan]);
        $pembayaran = $stmtP->fetch();
    }
}

// Calculate totals
$subtotal = 0;
foreach ($details as $d) {
    $subtotal += (float)$d['harga_satuan'] * (int)$d['jumlah'];
}
$tax = $subtotal * 0.11;
$total = $subtotal + $tax;

ob_start();
?>
<section class="row g-5">
    <div class="col-lg-8">
        <article class="section-panel h-100 d-flex flex-column align-items-center">
            <div class="w-100 d-flex justify-content-between align-items-center mb-5 pb-3 border-bottom border-soft">
                <h3 class="panel-title m-0">Pratinjau Struk</h3>
                <a class="btn btn-outline-warning" href="<?= htmlspecialchars(base_url('kasir/pembayaran.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
            </div>

            <?php if (!$pesanan): ?>
                <div class="py-5 text-center w-100">
                    <p class="text-muted mb-4">Pilih pesanan untuk mencetak struk.</p>
                    <a class="btn btn-warning" href="<?= htmlspecialchars(base_url('kasir/pembayaran.php'), ENT_QUOTES, 'UTF-8'); ?>">Ke Halaman Pembayaran</a>
                </div>
            <?php else: ?>
                <div class="receipt-container border border-secondary p-5" style="width: 100%; max-width: 400px; background-color: #161616;">
                    <div class="text-center mb-4">
                        <h2 class="font-display text-gold mb-1" style="font-size: 28px; letter-spacing: 0.06em;">Lumière</h2>
                        <p class="text-secondary small m-0" style="line-height: 1.4;">Jl. Sudirman No. 45, Jakarta Selatan<br>Telp: (021) 555-0123</p>
                    </div>

                    <div class="d-flex flex-column gap-2 mb-4 text-secondary" style="font-size: 11px;">
                        <div class="d-flex justify-content-between"><span>TANGGAL:</span><span class="text-white"><?= date('d M Y, H:i', strtotime($pesanan['tanggal_pesanan'])); ?></span></div>
                        <div class="d-flex justify-content-between"><span>KASIR:</span><span class="text-white"><?= htmlspecialchars($pembayaran['nama_kasir'] ?? $_SESSION['user_name'] ?? 'Kasir', ENT_QUOTES, 'UTF-8'); ?></span></div>
                        <div class="d-flex justify-content-between"><span>NO. MEJA:</span><span class="text-white"><?= htmlspecialchars($pesanan['no_meja'], ENT_QUOTES, 'UTF-8'); ?></span></div>
                        <div class="d-flex justify-content-between"><span>NO. PESANAN:</span><span class="text-white">#LP-<?= $pesanan['id_pesanan']; ?></span></div>
                        <?php if ($pembayaran && $pembayaran['trx_id']): ?>
                        <div class="d-flex justify-content-between"><span>TRX ID:</span><span class="text-white"><?= htmlspecialchars($pembayaran['trx_id'], ENT_QUOTES, 'UTF-8'); ?></span></div>
                        <?php endif; ?>
                    </div>

                    <div class="border-top border-bottom border-soft py-3 mb-4 d-flex flex-column gap-3">
                        <?php foreach ($details as $d): ?>
                        <div class="d-flex justify-content-between text-white small">
                            <span><?= htmlspecialchars($d['nama_menu'], ENT_QUOTES, 'UTF-8'); ?><br><span class="text-secondary" style="font-size: 12px;"><?= $d['jumlah']; ?>x @ <?= number_format((float)$d['harga_satuan'], 0, ',', '.'); ?></span></span>
                            <span>Rp <?= number_format((float)$d['harga_satuan'] * (int)$d['jumlah'], 0, ',', '.'); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-flex flex-column gap-2 mb-4 text-secondary" style="font-size: 11px;">
                        <div class="d-flex justify-content-between"><span>Subtotal</span><span class="text-white">Rp <?= number_format($subtotal, 0, ',', '.'); ?></span></div>
                        <div class="d-flex justify-content-between"><span>Pajak (11%)</span><span class="text-white">Rp <?= number_format($tax, 0, ',', '.'); ?></span></div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-soft mb-4">
                        <span class="text-gold fw-medium">TOTAL</span>
                        <span class="font-display text-white" style="font-size: 24px;">Rp <?= number_format($total, 0, ',', '.'); ?></span>
                    </div>

                    <div class="d-flex flex-column gap-1 text-secondary" style="font-size: 11px;">
                        <div class="d-flex justify-content-between"><span>Metode</span><span class="text-white"><?= htmlspecialchars($pembayaran['metode'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span></div>
                        <div class="d-flex justify-content-between"><span>Status</span><span class="text-white"><?= htmlspecialchars($pembayaran['status'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></span></div>
                    </div>

                    <div class="text-center mt-5">
                        <p class="text-secondary" style="font-size: 12px;">Terima kasih atas kunjungan Anda.</p>
                        <div class="mt-3 d-flex justify-content-center">
                            <div class="d-flex align-items-end" style="height: 30px; gap: 2px;">
                                <div class="bg-secondary" style="width: 2px; height: 100%;"></div>
                                <div class="bg-secondary" style="width: 4px; height: 100%;"></div>
                                <div class="bg-secondary" style="width: 1px; height: 100%;"></div>
                                <div class="bg-secondary" style="width: 3px; height: 100%;"></div>
                                <div class="bg-secondary" style="width: 2px; height: 100%;"></div>
                                <div class="bg-secondary" style="width: 1px; height: 100%;"></div>
                                <div class="bg-secondary" style="width: 5px; height: 100%;"></div>
                                <div class="bg-secondary" style="width: 2px; height: 100%;"></div>
                                <div class="bg-secondary" style="width: 1px; height: 100%;"></div>
                                <div class="bg-secondary" style="width: 3px; height: 100%;"></div>
                                <div class="bg-secondary" style="width: 2px; height: 100%;"></div>
                                <div class="bg-secondary" style="width: 4px; height: 100%;"></div>
                                <div class="bg-secondary" style="width: 2px; height: 100%;"></div>
                                <div class="bg-secondary" style="width: 1px; height: 100%;"></div>
                                <div class="bg-secondary" style="width: 3px; height: 100%;"></div>
                                <div class="bg-secondary" style="width: 2px; height: 100%;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($pembayaran && $pembayaran['status'] === 'Lunas'): ?>
                    <div class="mt-5 w-100 d-flex justify-content-center">
                        <button class="btn btn-warning" onclick="window.print()" style="min-width: 200px;">CETAK STRUK</button>
                    </div>
                <?php else: ?>
                    <div class="w-100 d-flex justify-content-center mt-4">
                        <!-- Cashier Payment Action Form -->
                        <div class="card bg-dark border-secondary p-4 rounded-0 w-100" style="max-width: 400px; background-color: #111111;">
                            <h4 class="text-warning font-display mb-3" style="font-size: 16px; letter-spacing: 0.05em;">PROSES PEMBAYARAN KASIR</h4>
                            <form method="POST" action="<?= htmlspecialchars(base_url('actions/pembayaran/store.php'), ENT_QUOTES, 'UTF-8'); ?>" class="m-0">
                                <input type="hidden" name="id_pesanan" value="<?= $id_pesanan ?>">
                                <input type="hidden" name="total_bayar" value="<?= $total ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label text-secondary small" style="font-size: 11px;">METODE PEMBAYARAN</label>
                                    <select name="metode" class="form-select bg-black text-white border-secondary rounded-0" style="font-size: 13px;">
                                        <option value="Tunai" <?= (isset($pembayaran['metode']) && $pembayaran['metode'] === 'Tunai') ? 'selected' : '' ?>>Tunai (Cash)</option>
                                        <option value="QRIS" <?= (isset($pembayaran['metode']) && $pembayaran['metode'] === 'QRIS') ? 'selected' : '' ?>>QRIS / E-Wallet</option>
                                        <option value="Kartu Kredit" <?= (isset($pembayaran['metode']) && $pembayaran['metode'] === 'Kartu Kredit') ? 'selected' : '' ?>>Kartu Kredit / Debit</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-secondary small" style="font-size: 11px;">JUMLAH UANG DITERIMA</label>
                                    <input type="number" id="input_bayar" name="jumlah_diterima" class="form-control bg-black text-white border-secondary rounded-0" style="font-size: 13px;" placeholder="Masukkan nominal..." required min="<?= $total ?>">
                                </div>

                                <div class="mb-3 d-flex justify-content-between text-secondary small" style="font-size: 12px;">
                                    <span>Total Tagihan:</span>
                                    <span class="text-white fw-bold">Rp <?= number_format($total, 0, ',', '.'); ?></span>
                                </div>

                                <div class="mb-4 d-flex justify-content-between text-secondary small" style="font-size: 12px;">
                                    <span>Kembalian:</span>
                                    <span id="text_kembalian" class="text-warning fw-bold">Rp 0</span>
                                </div>

                                <button type="submit" class="btn btn-warning w-100 rounded-0 fw-medium py-2" style="font-size: 11px; letter-spacing: 0.06em;">KONFIRMASI BAYAR</button>
                            </form>
                        </div>
                    </div>
                    
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const inputBayar = document.getElementById("input_bayar");
                            const textKembalian = document.getElementById("text_kembalian");
                            const total = <?= (float)$total ?>;
                            
                            if (inputBayar && textKembalian) {
                                inputBayar.addEventListener("input", function() {
                                    const diterima = parseFloat(inputBayar.value) || 0;
                                    const kembalian = diterima - total;
                                    if (kembalian >= 0) {
                                        textKembalian.textContent = "Rp " + new Intl.NumberFormat("id-ID").format(kembalian);
                                        textKembalian.className = "text-warning fw-bold";
                                    } else {
                                        textKembalian.textContent = "Rp 0";
                                        textKembalian.className = "text-danger fw-bold";
                                    }
                                });
                            }
                        });
                    </script>
                <?php endif; ?>
            <?php endif; ?>
        </article>
    </div>

    <aside class="col-lg-4">
        <article class="section-panel h-100">
            <h3 class="panel-title mb-4">Pesanan Lain</h3>
            <div class="compact-list">
                <?php
                $stmtOther = $pdo->query("
                    SELECT p.id_pesanan, p.no_meja, p.total_harga, py.metode
                    FROM pesanan p
                    LEFT JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
                    ORDER BY p.tanggal_pesanan DESC
                    LIMIT 5
                ");
                $otherOrders = $stmtOther->fetchAll();
                foreach ($otherOrders as $o):
                ?>
                <div class="compact-list-item">
                    <div>
                        <p class="fw-medium text-white mb-1">#LP-<?= $o['id_pesanan']; ?> • Meja <?= htmlspecialchars($o['no_meja'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="small text-secondary mb-0"><?= rupiah((float)$o['total_harga']); ?> • <?= htmlspecialchars($o['metode'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <a class="text-gold small text-decoration-none border-bottom border-gold pb-1" href="<?= htmlspecialchars(base_url('kasir/pembayaran_cetak.php?id=' . $o['id_pesanan']), ENT_QUOTES, 'UTF-8'); ?>">Lihat</a>
                </div>
                <?php endforeach; ?>
                <?php if (empty($otherOrders)): ?>
                    <p class="text-muted small text-center py-4">Belum ada pesanan.</p>
                <?php endif; ?>
            </div>
        </article>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'brand' => 'Lumière',
    'badge' => 'Service Floor',
    'title' => 'Cetak Struk',
    'description' => 'Cetak struk pembayaran untuk pesanan yang sudah lunas.',
    'nav_sections' => staff_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
