<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/database.php';
require_role('pelanggan');

$title = 'Edit Profil';
$assetBase = '../../assets';
require __DIR__ . '/../includes/header.php';

$userId = $_SESSION['id_user'] ?? 0;
$pdo = db();

// Fetch current user info
$stmt = $pdo->prepare('SELECT * FROM user WHERE id_user = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_user = trim($_POST['nama_user'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '') {
        set_flash('error', 'Username dan Email tidak boleh kosong.');
    } else {
        // Check for unique username
        $exists = false;
        $stmtCheck = $pdo->prepare('SELECT 1 FROM user WHERE username = ? AND id_user != ?');
        $stmtCheck->execute([$username, $userId]);
        $exists = (bool) $stmtCheck->fetch();

        if ($exists) {
            set_flash('error', 'Username telah digunakan oleh akun lain.');
        } else {
            // Update
            if ($password !== '') {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmtUpdate = $pdo->prepare('UPDATE user SET nama_user = ?, username = ?, email = ?, password = ? WHERE id_user = ?');
                $stmtUpdate->execute([$nama_user, $username, $email, $hashed, $userId]);
            } else {
                $stmtUpdate = $pdo->prepare('UPDATE user SET nama_user = ?, username = ?, email = ? WHERE id_user = ?');
                $stmtUpdate->execute([$nama_user, $username, $email, $userId]);
            }
            $_SESSION['user_name'] = $username;
            set_flash('success', 'Profil Anda berhasil diperbarui.');
            redirect(base_url('pelanggan/profil.php'));
        }
    }
}

ob_start();
?>
<section class="row g-5">
    <div class="col-lg-8 animate-fade-in-up">
        <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
            <div class="border-bottom border-soft pb-4 mb-4">
                <h3 class="h3 mb-1 text-warning font-display" style="font-size: 24px;">Perbarui Data Diri</h3>
                <p class="text-secondary small mb-0">Ubah detail personal, alamat email, dan kredensial keamanan akun Anda.</p>
            </div>

            <form class="d-flex flex-column gap-4" action="" method="post">
                <div class="row row-cols-1 row-cols-md-2 g-3">
                    <div>
                        <label class="form-label small text-secondary mb-2">Nama Lengkap</label>
                        <input class="form-control bg-black text-white border-secondary rounded-0 py-2" style="font-size: 13px;" type="text" name="nama_user" value="<?= htmlspecialchars((string)($user['nama_user'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div>
                        <label class="form-label small text-secondary mb-2">Username</label>
                        <input class="form-control bg-black text-white border-secondary rounded-0 py-2" style="font-size: 13px;" type="text" name="username" value="<?= htmlspecialchars((string)($user['username'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                </div>

                <div class="row row-cols-1 row-cols-md-2 g-3">
                    <div>
                        <label class="form-label small text-secondary mb-2">Alamat Email</label>
                        <input class="form-control bg-black text-white border-secondary rounded-0 py-2" style="font-size: 13px;" type="email" name="email" value="<?= htmlspecialchars((string)($user['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div>
                        <label class="form-label small text-secondary mb-2">Password Baru (Opsional)</label>
                        <input class="form-control bg-black text-white border-secondary rounded-0 py-2" style="font-size: 13px;" type="password" name="password" placeholder="Kosongkan jika tidak diubah">
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-2">
                    <button class="btn btn-warning rounded-0 fw-semibold px-4 py-2" style="font-size: 11px;" type="submit">Simpan Perubahan</button>
                    <a class="btn btn-outline-warning rounded-0 fw-semibold px-4 py-2 text-white" style="font-size: 11px;" href="<?= htmlspecialchars(base_url('pelanggan/profil.php'), ENT_QUOTES, 'UTF-8'); ?>">Kembali</a>
                </div>
            </form>
        </article>
    </div>

    <aside class="col-lg-4 animate-fade-in-up" style="animation-delay: 0.2s;">
        <article class="card bg-dark text-white border-secondary p-4 mb-4 rounded-0">
            <h3 class="h3 mb-4 text-warning font-display" style="font-size: 24px;">Informasi Keamanan</h3>
            <p class="text-secondary small" style="line-height: 1.6;">Demi alasan perlindungan privasi data, harap gunakan kata sandi yang kuat dan unik (mengandung kombinasi huruf, angka, dan karakter spesial) ketika memperbarui kredensial Anda.</p>
        </article>
    </aside>
</section>
<?php
$content = ob_get_clean();
render_public_shell([
    'brand' => "L'Art Culinaire",
    'eyebrow' => 'Edit Profile',
    'title' => 'Pengaturan Akun',
    'description' => 'Sesuaikan profil personal dan pertahankan kredensial keamanan akun kuliner Anda.',
], $content);
require __DIR__ . '/../includes/footer.php';
