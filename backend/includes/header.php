<?php

declare(strict_types=1);

$app = require base_path('backend/config/app.php');
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
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars(($assetBase ?? '../../assets') . '/css/app.css', ENT_QUOTES, 'UTF-8'); ?>?v=<?= time(); ?>">
</head>
<body>
