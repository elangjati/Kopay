<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\GoogleSheetsService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function midtrans(Request $request)
    {
        $payload     = $request->all();
        $orderId     = $payload['order_id'] ?? '';
        $statusCode  = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $serverKey   = config('services.midtrans.server_key');

        // Validasi signature Midtrans
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if ($signatureKey !== ($payload['signature_key'] ?? '')) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus       = $payload['fraud_status'] ?? '';

        // Cari order berdasarkan midtrans_order_id
        $order = Order::where('midtrans_order_id', $orderId)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if (in_array($transactionStatus, ['settlement', 'capture']) && $fraudStatus !== 'deny') {
            if ($order->status !== 'completed') {
                $order->update(['status' => 'completed']);
                try {
                    $sheets = new GoogleSheetsService();
                    $sheets->appendOrder($order->load('items.menu'));
                } catch (\Exception $e) {
                    \Log::error('Sheets error on webhook: ' . $e->getMessage());
                }
            }
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            if ($order->status === 'pending') {
                $order->update(['status' => 'cancelled']);
            }
        }

        return response()->json(['message' => 'OK']);
    }
}
