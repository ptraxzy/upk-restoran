<?php

declare(strict_types=1);

require_once __DIR__ . '/../../../backend/includes/bootstrap.php';

$title = 'Register Member';
$assetBase = '../../assets';
require base_path('backend/includes/header.php');
?>
<main class="auth-shell">
    <div class="auth-grid">
        <section class="auth-visual auth-visual--member">
            <div class="absolute inset-0 flex items-end p-10">
                <div class="max-w-md">
                    <p class="eyebrow">Member Registration</p>
                    <h1 class="mt-4 font-display text-5xl text-stone-50">Mulai perjalanan kuliner yang lebih personal.</h1>
                    <p class="mt-5 text-sm leading-7 text-stone-200">Daftarkan akun pelanggan untuk membuka akses member dan menyimpan pengalaman pemesananmu.</p>
                </div>
            </div>
        </section>

        <section class="auth-form-wrap">
            <p class="eyebrow">Join as Member</p>
            <h2 class="mt-4 font-display text-4xl text-stone-50">Register Member</h2>
            <p class="mt-4 max-w-md text-sm leading-7 text-stone-300">Buat akun baru dengan alur sederhana. Setelah berhasil, kamu bisa langsung login sebagai member.</p>

            <?php if ($message = get_flash('error')): ?>
                <div class="notice notice-error mt-8"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form action="../../../backend/actions/auth/register.php" method="post" class="mt-8 grid gap-5">
                <div>
                    <label class="field-label" for="register-username">Username</label>
                    <input class="field-input" id="register-username" type="text" name="username" placeholder="Buat username member" required>
                </div>
                <div>
                    <label class="field-label" for="register-password">Password</label>
                    <input class="field-input" id="register-password" type="password" name="password" placeholder="Buat password" required>
                </div>
                <button class="cta-primary mt-2 w-full" type="submit">Register Member</button>
            </form>

            <div class="mt-8 flex flex-wrap gap-4 text-sm text-stone-300">
                <a href="<?= htmlspecialchars(frontend_url('login.php'), ENT_QUOTES, 'UTF-8'); ?>">Sudah punya akun? Login di sini</a>
                <span class="text-stone-600">/</span>
                <a href="<?= htmlspecialchars(frontend_url('index.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali ke halaman utama</a>
            </div>
        </section>
    </div>
</main>
<?php require base_path('backend/includes/footer.php'); ?>
