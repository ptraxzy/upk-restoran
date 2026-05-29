<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$title = 'Pengaturan Sistem';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

// Ambil semua data pengaturan dari database
$settings = get_all_settings();

ob_start();
?>
<style>
/* Custom Figma style overrides for Settings Page */
.settings-title-area {
    margin-bottom: 30px;
}
.settings-sub-title {
    font-family: var(--font-sans);
    font-size: 12px;
    letter-spacing: 0.04em;
    color: rgba(255, 255, 255, 0.38);
    text-transform: uppercase;
    margin-top: 4px;
}
.btn-save-settings {
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
    cursor: pointer;
}
.btn-save-settings:hover {
    background-color: #ffffff !important;
    color: #000000 !important;
    transform: translateY(-1px);
}
.settings-card {
    background: rgba(255, 255, 255, 0.01);
    border: 1px solid rgba(255, 255, 255, 0.04);
    border-radius: 8px;
    padding: 24px;
    height: 100%;
}
.settings-card-title {
    font-family: var(--font-sans);
    font-size: 13px;
    font-weight: 600;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 24px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    padding-bottom: 14px;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}
.brand-showcase-box {
    background: rgba(197, 160, 89, 0.01);
    border: 1px solid rgba(197, 160, 89, 0.06);
    border-radius: 6px;
    padding: 24px 20px;
    text-align: center;
    margin-top: 24px;
}
.brand-showcase-est {
    font-family: var(--font-display);
    font-size: 11px;
    color: var(--gold);
    letter-spacing: 0.2em;
    text-transform: uppercase;
    font-weight: 700;
}
.brand-showcase-title {
    font-family: var(--font-display);
    font-size: 14px;
    color: #ffffff;
    letter-spacing: 0.08em;
    margin-top: 6px;
    font-weight: 600;
}
</style>

<div class="container-fluid p-0">
    <form method="POST" action="<?= htmlspecialchars(base_url('actions/pengaturan/update.php'), ENT_QUOTES, 'UTF-8'); ?>" class="m-0">
        <!-- Header Title and Save Button -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start settings-title-area gap-3">
            <div>
                <h2 class="font-display text-white mb-0" style="font-size: 28px; letter-spacing: 0.02em;">Pengaturan Sistem</h2>
                <div class="settings-sub-title">Konfigurasi identitas restoran dan preferensi layanan global.</div>
            </div>
            <div>
                <button type="submit" class="btn-save-settings">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                    SIMPAN PENGATURAN
                </button>
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

        <!-- Two Column Form Grid -->
        <div class="row g-4">
            <!-- Left Column: Profil Restoran -->
            <div class="col-lg-8">
                <div class="settings-card">
                    <div class="settings-card-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        Profil Restoran
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.04em;">NAMA RESTORAN</label>
                            <input class="form-control bg-black text-white border-secondary rounded-0" name="nama_restoran" type="text" value="<?= htmlspecialchars($settings['nama_restoran'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required style="font-size: 13px; padding: 10px; border-color: rgba(255, 255, 255, 0.08) !important;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.04em;">NOMOR TELEPON</label>
                            <input class="form-control bg-black text-white border-secondary rounded-0" name="nomor_telepon" type="text" value="<?= htmlspecialchars($settings['nomor_telepon'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required style="font-size: 13px; padding: 10px; border-color: rgba(255, 255, 255, 0.08) !important;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-secondary fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.04em;">ALAMAT LENGKAP</label>
                        <textarea class="form-control bg-black text-white border-secondary rounded-0" name="alamat_lengkap" rows="3" required style="font-size: 13px; padding: 10px; border-color: rgba(255, 255, 255, 0.08) !important;"><?= htmlspecialchars($settings['alamat_lengkap'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.04em;">JAM OPERASIONAL</label>
                            <div class="position-relative">
                                <input class="form-control bg-black text-white border-secondary rounded-0 pe-5" name="jam_operasional" type="text" value="<?= htmlspecialchars($settings['jam_operasional'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required style="font-size: 13px; padding: 10px; border-color: rgba(255, 255, 255, 0.08) !important;">
                                <span class="position-absolute top-50 end-0 translate-middle-y pe-3 text-secondary">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-secondary fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.04em;">MODE LAYANAN UTAMA</label>
                            <select class="form-select bg-black text-white border-secondary rounded-0" name="mode_layanan" required style="font-size: 13px; padding: 10px; border-color: rgba(255, 255, 255, 0.08) !important; cursor: pointer;">
                                <option value="Dine In & Delivery" <?= ($settings['mode_layanan'] ?? '') === 'Dine In & Delivery' ? 'selected' : '' ?>>Dine In & Delivery</option>
                                <option value="Dine In Only" <?= ($settings['mode_layanan'] ?? '') === 'Dine In Only' ? 'selected' : '' ?>>Dine In Only</option>
                                <option value="Delivery Only" <?= ($settings['mode_layanan'] ?? '') === 'Delivery Only' ? 'selected' : '' ?>>Delivery Only</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Filosofi Brand -->
            <div class="col-lg-4">
                <div class="settings-card d-flex flex-column justify-content-between">
                    <div>
                        <div class="settings-card-title">
`                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><polygon points="12 2 22 8.5 22 15.5 12 22 2 15.5 2 8.5 12 2"></polygon><polyline points="2 8.5 12 15 22 8.5"></polyline><line x1="12" y1="22" x2="12" y2="15"></line></svg>
`                            Filosofi Brand
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-secondary fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.04em;">ARAH VISUAL</label>
                            <textarea class="form-control bg-black text-white border-secondary rounded-0" name="arah_visual" rows="3" required style="font-size: 13px; padding: 10px; border-color: rgba(255, 255, 255, 0.08) !important;"><?= htmlspecialchars($settings['arah_visual'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-secondary fw-semibold mb-1" style="font-size: 11px; letter-spacing: 0.04em;">NADA LAYANAN</label>
                            <textarea class="form-control bg-black text-white border-secondary rounded-0" name="nada_layanan" rows="3" required style="font-size: 13px; padding: 10px; border-color: rgba(255, 255, 255, 0.08) !important;"><?= htmlspecialchars($settings['nada_layanan'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                    </div>

                    <div class="brand-showcase-box">
                        <div class="brand-showcase-est">EST. 2024</div>
                        <div class="brand-showcase-title">Lumière Global Standard</div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?php
$content = ob_get_clean();
render_internal_shell([
    'badge' => 'Administration',
    'title' => 'Pengaturan Sistem',
    'description' => 'Konfigurasi identitas restoran dan preferensi layanan global.',
    'nav_sections' => admin_nav_sections(),
], $content);
require __DIR__ . '/../includes/footer.php';
