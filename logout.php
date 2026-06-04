<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$wasStaffOrAdmin = in_array($_SESSION['user_role'] ?? $_SESSION['level'] ?? '', ['admin', 'kasir'], true);

session_unset();
session_destroy();

if ($wasStaffOrAdmin) {
    session_start();
    $_SESSION['bypass_meja'] = true;
    redirect(base_url('login.php'));
} else {
    redirect(base_url('login.php'));
}

