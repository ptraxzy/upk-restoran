<?php

declare(strict_types=1);

$app = require __DIR__ . '/../config/app.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? $app['name'], ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="description" content="Sistem restoran UPK dengan area admin, karyawan, dan member.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= htmlspecialchars(base_url('assets/css/style.css'), ENT_QUOTES, 'UTF-8'); ?>?v=<?= time(); ?>">
</head>
<body>
