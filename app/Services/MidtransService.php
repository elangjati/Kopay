<?php

namespace App\Services;

use App\Models\Order;

class MidtransService
{
    protected string $serverKey;
    protected string $baseUrl;
    protected bool   $isProduction;

    public function __construct()
    {
        $this->serverKey    = config('services.midtrans.server_key', '');
        $this->isProduction = config('services.midtrans.is_production', false);
        $this->baseUrl      = $this->isProduction
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }

    /**
     * Buat transaksi QRIS via Midtrans
     * Return URL QR code image
     */
    public function createQris(Order $order): string
    {
        if (empty($this->serverKey)) {
            // Mode demo — return placeholder QR
            return 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=KOPAY-DEMO-' . $order->id;
        }

        $orderId = 'KOPAY-' . $order->id . '-' . time();

        $payload = [
            'payment_type'  => 'qris',
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $order->total_price,
            ],
            'qris' => ['acquirer' => 'gopay'],
        ];

        $response = $this->request('POST', '/charge', $payload);

        \Log::info('Midtrans charge response', $response);

        if (isset($response['error_messages']) || !in_array($response['status_code'] ?? '', ['200', '201', 200, 201])) {
            throw new \Exception('Midtrans error: ' . json_encode($response));
        }

        $order->update(['midtrans_order_id' => $orderId]);

        // Gunakan qr_string untuk generate QR via Google Charts API (tidak butuh auth)
        $qrString = $response['qr_string'] ?? '';
        if ($qrString) {
            return 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrString);
        }

        return $response['actions'][0]['url'] ?? '';
    }

    /**
     * Cek status pembayaran
     */
    public function checkStatus(Order $order): bool
    {
        if (empty($this->serverKey) || empty($order->midtrans_order_id)) {
            return false;
        }

        $response = $this->request('GET', '/' . $order->midtrans_order_id . '/status');

        \Log::info('Midtrans check status', [
            'order_id'           => $order->midtrans_order_id,
            'transaction_status' => $response['transaction_status'] ?? 'N/A',
            'status_code'        => $response['status_code'] ?? 'N/A',
        ]);

        return in_array($response['transaction_status'] ?? '', ['settlement', 'capture']);
    }

    protected function request(string $method, string $endpoint, array $body = []): array
    {
        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($this->serverKey . ':'),
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true) ?? [];
    }
}
