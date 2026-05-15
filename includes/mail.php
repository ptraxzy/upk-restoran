<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/env.php';

/**
 * Mengirim email menggunakan Resend API
 * Sangat reliabel dan menggunakan domain ultramaxo.tech.
 */
function send_mail(string $to, string $subject, string $message): bool
{
    $apiKey = env('RESEND_API_KEY', '');
    $from = env('MAIL_FROM', 'no-reply@ultramaxo.tech');
    $name = env('MAIL_NAME', 'NOCTRA');

    if (empty($apiKey)) {
        error_log("Mail Error: RESEND_API_KEY tidak ditemukan di .env");
        return false;
    }

    $payload = [
        'from' => "$name <$from>",
        'to' => [$to],
        'subject' => $subject,
        'html' => $message,
    ];

    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Sesuai environment development Anda

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        return true;
    }

    error_log("Resend API Error (HTTP $httpCode): " . $response . " | CURL Error: " . $error);
    return false;
}
