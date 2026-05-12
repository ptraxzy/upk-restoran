<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (isset($_GET['index']) && isset($_SESSION['cart'][$_GET['index']])) {
    unset($_SESSION['cart'][$_GET['index']]);
    $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index
    set_flash('success', 'Item dihapus dari keranjang.');
}

redirect(base_url('pelanggan/keranjang.php'));
