<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

function require_role(string $role): void
{
    $currentRole = $_SESSION['user_role'] ?? $_SESSION['level'] ?? null;

    if ($currentRole !== $role) {
        set_flash('error', 'Silakan login terlebih dahulu.');
        redirect(base_url('login.php'));
    }
}
