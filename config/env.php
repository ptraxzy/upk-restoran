<?php

declare(strict_types=1);

function env(string $key, ?string $default = null): ?string
{
    static $loaded = false;
    static $values = [];

    $runtimeValue = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    if ($runtimeValue !== false && $runtimeValue !== null && $runtimeValue !== '') {
        return (string) $runtimeValue;
    }

    if (!$loaded) {
        $envPath = dirname(__DIR__) . '/.env';

        if (is_file($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                $trimmed = trim($line);

                if ($trimmed === '' || str_starts_with($trimmed, '#') || !str_contains($trimmed, '=')) {
                    continue;
                }

                [$name, $value] = explode('=', $trimmed, 2);
                $values[trim($name)] = trim($value);
            }
        }

        $loaded = true;
    }

    return $values[$key] ?? $default;
}
