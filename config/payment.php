<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

return [
    'api_key' => env('PAYMENT_API_KEY', ''),
    'base_url' => env('PAYMENT_BASE_URL', 'https://qriscepat.com/api/'),
];
