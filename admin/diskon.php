<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Voucher Diskon';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<section class="row row-cols-1 row-cols-lg-2 g-4">
    <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <div class="d-flex flex-column gap-2 flex-md-row align-items-md-end justify-content-md-between">
            <div>
                <h3 class="h3 mb-1 text-warning">Daftar Voucher Diskon</h3>
                <p class="text-muted small mb-4">Kelola kupon, promo potongan harga, dan kampanye loyalitas.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-warning rounded-0 text-uppercase fw-medium px-4 py-2" href="<?= htmlspecialchars(base_url('admin/diskon_tambah.php'), ENT_QUOTES, 'UTF-8'); ?>">Tambah Voucher</a>
            </div>
        </div>

        <div class="list-stack mt-4">
            <div class="stack-item">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <p class="fw-medium text-light text-uppercase small mb-1">PROMO-WAGYU</p>
                        <p class="mt-2 small text-secondary">Potongan 15% untuk semua menu Wagyu A5.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge badge bg-warning text-dark">Aktif</span>
                        <a href="<?= htmlspecialchars(base_url('admin/diskon_edit.php'), ENT_QUOTES, 'UTF-8'); ?>" class="small text-muted hover:text-stone-300 text-uppercase">Edit</a>
                    </div>
                </div>
            </div>
            <div class="list-item">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <p class="fw-medium text-light text-uppercase small mb-1">NEW-MEMBER</p>
                        <p class="mt-2 small text-secondary">Potongan Rp 50.000 untuk transaksi pertama member baru.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge badge bg-secondary text-light">Kedaluwarsa</span>
                        <a href="<?= htmlspecialchars(base_url('admin/diskon_edit.php'), ENT_QUOTES, 'UTF-8'); ?>" class="small text-muted hover:text-stone-300 text-uppercase">Edit</a>
                    </div>
                </div>
            </div>
            <div class="list-item">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div>
                        <p class="fw-medium text-light text-uppercase small mb-1">DINNER-FOR-TWO</p>
                        <p class="mt-2 small text-secondary">Potongan Rp 100.000 minimum transaksi Rp 1.500.000 malam ini.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge badge bg-warning text-dark">Aktif</span>
                        <a href="<?= htmlspecialchars(base_url('admin/diskon_edit.php'), ENT_QUOTES, 'UTF-8'); ?>" class="small text-muted hover:text-stone-300 text-uppercase">Edit</a>
                    </div>
                </div>
            </div>
        </div>
    </article>

    <aside class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
        <h3 class="h3 mb-1 text-warning">Status Kampanye</h3>
        <div class="row row-cols-1 row-cols-md-3 g-3 mb-4 mt-4 !grid-cols-2">
            <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
                <p class="text-muted small text-uppercase mb-2">Aktif</p>
                <p class="h2 text-warning mb-0 !text-3xl text-amber-500">2</p>
            </article>
            <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100">
                <p class="text-muted small text-uppercase mb-2">Kedaluwarsa</p>
                <p class="h2 text-warning mb-0 !text-3xl">1</p>
            </article>
            <article class="card bg-dark text-white border-secondary p-3 rounded-0 h-100 col-span-2">
                <p class="text-muted small text-uppercase mb-2">Penggunaan Bulan Ini</p>
                <p class="h2 text-warning mb-0 !text-3xl">145<span class="small font-normal text-muted ml-2">transaksi</span></p>
            </article>
        </div>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administration',
    'title' => 'Manajemen Diskon',
    'description' => 'Tingkatkan retensi dan sales dengan manajemen diskon yang terkontrol.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
