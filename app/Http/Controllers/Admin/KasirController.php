<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Services\GoogleSheetsService;
use App\Services\MidtransService;
use Illuminate\Http\Request;

class KasirController extends Controller
{
    public function create()
    {
        $menus           = Menu::where('is_available', true)->orderBy('category')->orderBy('name')->get();
        $menusByCategory = $menus->groupBy('category');
        return view('admin.orders.edit', compact('menus', 'menusByCategory'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:100',
            'notes'          => 'nullable|string|max:255',
            'payment_method' => 'required|in:tunai,qris',
            'items'          => 'required|array|min:1',
            'items.*.menu_id'  => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $total      = 0;
        $orderItems = [];

        foreach ($request->items as $item) {
            $menu     = Menu::where('id', $item['menu_id'])->where('is_available', true)->firstOrFail();
            $subtotal = $menu->price * $item['quantity'];
            $total   += $subtotal;
            $orderItems[] = ['menu_id' => $menu->id, 'quantity' => $item['quantity'], 'price' => $menu->price];
        }

        $order = Order::create([
            'customer_name'  => $request->customer_name,
            'notes'          => $request->notes,
            'total_price'    => $total,
            'status'         => 'pending',
            'payment_method' => $request->payment_method,
        ]);

        foreach ($orderItems as $item) {
            $order->items()->create($item);
        }

        // QRIS — generate QR via Midtrans, lanjut ke halaman scan
        if ($request->payment_method === 'qris') {
            try {
                $midtrans = new MidtransService();
                $qrisUrl  = $midtrans->createQris($order);
                $order->update(['qris_url' => $qrisUrl]);
            } catch (\Exception $e) {
                \Log::error('Midtrans error: ' . $e->getMessage());
            }

            return redirect()->route('admin.kasir.qris', $order->id);
        }

        // Tunai — langsung complete & redirect ke struk
        $order->update(['status' => 'completed']);
        $this->syncSheets($order);

        return redirect()->route('admin.orders.receipt', $order->id);
    }

    public function qris(Order $order)
    {
        $order->load('items.menu');
        return view('admin.kasir.qris', compact('order'));
    }

    /**
     * Dipanggil via AJAX polling dari halaman QRIS,
     * atau via tombol "Konfirmasi Bayar" manual.
     */
    public function checkPayment(Order $order)
    {
        try {
            $midtrans = new MidtransService();
            $paid     = $midtrans->checkStatus($order);

            if ($paid) {
                $order->update(['status' => 'completed']);
                $this->syncSheets($order);
                return response()->json(['paid' => true]);
            }
        } catch (\Exception $e) {
            \Log::error('Midtrans check error: ' . $e->getMessage());
        }

        return response()->json(['paid' => false]);
    }

    /**
     * Push ke Google Sheets — silent fail agar tidak block flow kasir.
     */
    protected function syncSheets(Order $order): void
    {
        try {
            $sheets = new GoogleSheetsService();
            $sheets->appendOrder($order->load('items.menu'));
        } catch (\Exception $e) {
            \Log::error('Google Sheets error: ' . $e->getMessage());
        }
    }
}
