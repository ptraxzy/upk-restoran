<?php

declare(strict_types=1);

namespace Backend\PaymentGateway;

class QrisCepat
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $config = require dirname(__DIR__) . '/config/payment.php';
        $this->apiKey = $config['api_key'];
        // Pastikan baseUrl selalu berakhiran '/'
        $this->baseUrl = rtrim($config['base_url'], '/') . '/';
    }

    /**
     * Membuat request deposit (Pay-in)
     * URL: {base_url}deposit/{amount}/{apikey}
     *
     * @param int|float $amount
     * @return array|null Response dari API atau null jika gagal
     */
    public function deposit($amount): ?array
    {
        // Pastikan amount adalah integer tanpa desimal/koma
        $intAmount = (int) round((float)$amount);
        $endpoint = sprintf('deposit/%d/%s', $intAmount, $this->apiKey);
        return $this->request($endpoint);
    }

    /**
     * Membuat request withdraw (Pay-out)
     * URL: {base_url}withdraw/{amount}/{bank}/{rekening}/{apikey}
     *
     * @param int|float $amount
     * @param string $bank Kode Bank
     * @param string $rekening Nomor Rekening
     * @return array|null Response dari API atau null jika gagal
     */
    public function withdraw($amount, string $bank, string $rekening): ?array
    {
        $intAmount = (int) round((float)$amount);
        $endpoint = sprintf('withdraw/%d/%s/%s/%s', $intAmount, $bank, $rekening, $this->apiKey);
        return $this->request($endpoint);
    }

    /**
     * Mengecek status transaksi
     * URL: {base_url}trx/{trx_id}
     *
     * @param string $trxId ID Transaksi
     * @return array|null Response dari API atau null jika gagal
     */
    public function checkStatus(string $trxId): ?array
    {
        $endpoint = sprintf('trx/%s', $trxId);
        return $this->request($endpoint);
    }

    /**
     * Melakukan HTTP request ke API
     *
     * @param string $endpoint
     * @return array|null
     */
    private function request(string $endpoint): ?array
    {
        if (empty($this->apiKey)) {
            error_log('QrisCepat API Error: API Key is not set in configuration.');
            return ['status' => 'error', 'message' => 'Konfigurasi API Key belum diatur.'];
        }

        $url = $this->baseUrl . ltrim($endpoint, '/');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'UPK-Restoran-Client/1.0');
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log('QrisCepat API Error: ' . $error);
            return null;
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('QrisCepat API Error: Invalid JSON response - ' . $response);
            return null;
        }

        return $decoded;
    }
}
