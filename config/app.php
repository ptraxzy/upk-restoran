<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

return [
    'name' => env('APP_NAME', 'UPK Restoran'),
    'env' => env('APP_ENV', 'local'),
    'url' => env('APP_URL', 'http://localhost/upk-restoran/frontend'),
];
