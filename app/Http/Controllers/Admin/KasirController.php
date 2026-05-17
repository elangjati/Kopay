<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Services\GoogleSheetsService;
use Illuminate\Http\Request;

class KasirController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.menu')
            ->latest()
            ->limit(50)
            ->get();

        $todayOrders   = Order::whereDate('created_at', today())->count();
        $todayRevenue  = Order::whereDate('created_at', today())->where('status', 'completed')->sum('total_price');
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalMenus    = Menu::where('is_available', true)->count();

        return view('admin.dashboard', compact('orders', 'todayOrders', 'todayRevenue', 'pendingOrders', 'totalMenus'));
    }

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
            'payment_method' => 'nullable|in:tunai,qris',
            'pay_now'        => 'nullable|boolean',
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

        $payNow = $request->boolean('pay_now', true);

        $order = Order::create([
            'customer_name'  => $request->customer_name,
            'notes'          => $request->notes,
            'total_price'    => $total,
            'status'         => $payNow ? 'completed' : 'pending',
            'payment_method' => $payNow ? $request->payment_method : null,
        ]);

        foreach ($orderItems as $item) {
            $order->items()->create($item);
        }

        if ($payNow) {
            $this->syncSheets($order);
            return redirect()->route('admin.orders.receipt', $order->id);
        }

        // Bayar nanti — ke riwayat hari ini
        return redirect()->route('admin.orders.today')
            ->with('success', "Pesanan #{$order->id} dibuat. Menunggu pembayaran.");
    }

    protected function syncSheets(Order $order): void
    {
        try {
            $sheets = new GoogleSheetsService();
            $sheets->appendOrder($order->load('items.menu'));
        } catch (\Exception $e) {
            \Log::error('Google Sheets error: ' . $e->getMessage());
        }
    }

    public function dailyReport(Request $request)
    {
        // Validasi format tanggal, fallback ke hari ini kalau invalid
        $date = $request->get('date', today()->toDateString());
        try {
            $date = \Carbon\Carbon::parse($date)->toDateString();
        } catch (\Exception $e) {
            $date = today()->toDateString();
        }

        $orders = Order::with('items.menu')
            ->whereDate('created_at', $date)
            ->where('status', 'completed')
            ->latest()
            ->get();

        $totalOrders  = $orders->count();
        $totalRevenue = $orders->sum('total_price');
        $tunaiRevenue = $orders->where('payment_method', 'tunai')->sum('total_price');
        $qrisRevenue  = $orders->where('payment_method', 'qris')->sum('total_price');
        $tunaiOrders  = $orders->where('payment_method', 'tunai')->count();
        $qrisOrders   = $orders->where('payment_method', 'qris')->count();

        return view('admin.kasir.daily-report', compact(
            'orders', 'date', 'totalOrders', 'totalRevenue',
            'tunaiRevenue', 'qrisRevenue', 'tunaiOrders', 'qrisOrders'
        ));
    }
}
