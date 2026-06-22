<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Services\GoogleSheetsService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function today()
    {
        $orders = Order::with('items.menu')
            ->whereDate('created_at', today())
            ->latest()
            ->get();
        return view('admin.orders.today', compact('orders'));
    }

    public function edit(Order $order)
    {
        $order->load('items.menu');
        $menus           = Menu::where('is_available', true)->orderBy('category')->orderBy('name')->get();
        $menusByCategory = $menus->groupBy('category');
        return view('admin.orders.edit', compact('order', 'menus', 'menusByCategory'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'notes'         => 'nullable|string|max:255',
            'items'         => 'required|array|min:1',
            'items.*.menu_id'  => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $total    = 0;
        $newItems = [];

        foreach ($request->items as $item) {
            $menu = Menu::find($item['menu_id']);
            if (!$menu) continue; // skip menu yang sudah dihapus
            $total    += $menu->price * $item['quantity'];
            $newItems[] = [
                'menu_id'  => $menu->id,
                'quantity' => $item['quantity'],
                'price'    => $menu->price,
            ];
        }

        if (empty($newItems)) {
            return back()->withErrors(['items' => 'Semua menu yang dipilih sudah tidak tersedia.']);
        }

        // Gunakan transaction agar data tidak rusak jika gagal di tengah jalan
        \DB::transaction(function () use ($order, $request, $total, $newItems) {
            $order->update([
                'customer_name' => $request->customer_name,
                'notes'         => $request->notes,
                'total_price'   => $total,
            ]);

            $order->items()->delete();
            foreach ($newItems as $item) {
                $order->items()->create($item);
            }
        });

        return redirect()->route('admin.kasir.create')->with('success', "Pesanan #{$order->id} berhasil diperbarui.");
    }

    public function receipt(Order $order)
    {
        $order->load('items.menu');
        return view('admin.orders.receipt', compact('order'));
    }

    public function complete(Request $request, Order $order)
    {
        if ($order->status === 'completed') {
            return redirect()->route('admin.orders.receipt', $order->id);
        }

        $data = ['status' => 'completed'];

        // Update metode bayar jika dikirim dari form konfirmasi
        if ($request->filled('payment_method')) {
            // Validasi nilai agar tidak crash ENUM di database
            if (!in_array($request->payment_method, ['tunai', 'qris'])) {
                return back()->withErrors(['payment_method' => 'Metode pembayaran tidak valid.']);
            }
            $data['payment_method'] = $request->payment_method;
        }

        $order->update($data);

        try {
            $sheets = new GoogleSheetsService();
            $sheets->appendOrder($order->load('items.menu'));
        } catch (\Exception $e) {
            \Log::error('Google Sheets error: ' . $e->getMessage());
        }

        return redirect()->route('admin.orders.receipt', $order->id);
    }

    public function history(Request $request)
    {
        $tab    = $request->get('tab', 'pending'); // default tab pending
        $search = $request->get('search', '');

        $pendingOrders = Order::with('items.menu')
            ->where('status', 'pending')
            ->when($search, fn($q) => $q->where('customer_name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(20, ['*'], 'pending_page')
            ->withQueryString();

        $completedOrders = Order::with('items.menu')
            ->where('status', 'completed')
            ->when($search, fn($q) => $q->where('customer_name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(20, ['*'], 'completed_page')
            ->withQueryString();

        $totalPending   = Order::where('status', 'pending')->count();
        $totalCompleted = Order::where('status', 'completed')->count();

        return view('admin.orders.history', compact(
            'pendingOrders', 'completedOrders',
            'totalPending', 'totalCompleted',
            'tab', 'search'
        ));
    }

    public function changePayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_method' => 'required|in:tunai,qris',
        ]);

        $order->update(['payment_method' => $request->payment_method]);

        return back()->with('success', "Metode pembayaran pesanan #{$order->id} diubah ke " . strtoupper($request->payment_method) . '.');
    }

    public function destroy(Order $order)
    {
        // Hanya pesanan completed yang boleh dihapus
        if ($order->status !== 'completed') {
            return back()->withErrors(['error' => 'Hanya pesanan yang sudah selesai yang bisa dihapus.']);
        }

        // Log untuk audit trail
        $orderData = [
            'id'           => $order->id,
            'customer_name'=> $order->customer_name,
            'total_price'  => $order->total_price,
            'status'       => $order->status,
            'payment_method' => $order->payment_method,
            'items'        => $order->items->map(fn($i) => [
                'menu_id'  => $i->menu_id,
                'quantity' => $i->quantity,
                'price'    => $i->price,
            ])->toArray(),
            'deleted_by'   => auth()->user()->id,
            'deleted_at'   => now(),
        ];
        \Log::info('Order deleted by owner', $orderData);

        // Soft delete
        $order->delete();
        
        return back()->with('success', "Pesanan #{$order->id} berhasil dihapus. Owner dapat memulihkan di menu trash.");
    }

    public function restore(Order $order)
    {
        // Restore order yang sudah dihapus
        if (!$order->trashed()) {
            return back()->withErrors(['error' => 'Pesanan ini belum dihapus.']);
        }

        $order->restore();

        \Log::info('Order restored by owner', [
            'id' => $order->id,
            'restored_by' => auth()->user()->id,
            'restored_at' => now(),
        ]);

        return back()->with('success', "Pesanan #{$order->id} berhasil dipulihkan.");
    }

    public function trash(Request $request)
    {
        // Dashboard trash untuk owner melihat deleted orders
        $search = $request->get('search', '');

        $trashedOrders = Order::onlyTrashed()
            ->with('items.menu')
            ->when($search, fn($q) => $q->where('customer_name', 'like', "%{$search}%"))
            ->latest('deleted_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.trash', compact('trashedOrders', 'search'));
    }
}
