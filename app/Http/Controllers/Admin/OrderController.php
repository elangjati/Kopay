<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Services\GoogleSheetsService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
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
            $menu      = Menu::findOrFail($item['menu_id']);
            $total    += $menu->price * $item['quantity'];
            $newItems[] = [
                'menu_id'  => $menu->id,
                'quantity' => $item['quantity'],
                'price'    => $menu->price,
            ];
        }

        $order->update([
            'customer_name' => $request->customer_name,
            'notes'         => $request->notes,
            'total_price'   => $total,
        ]);

        $order->items()->delete();
        foreach ($newItems as $item) {
            $order->items()->create($item);
        }

        return redirect()->route('admin.kasir.create')->with('success', "Pesanan #{$order->id} berhasil diperbarui.");
    }

    public function receipt(Order $order)
    {
        $order->load('items.menu');
        return view('admin.orders.receipt', compact('order'));
    }

    public function complete(Order $order)
    {
        if ($order->status === 'completed') {
            return redirect()->route('admin.orders.receipt', $order->id);
        }

        $order->update(['status' => 'completed']);

        try {
            $sheets = new GoogleSheetsService();
            $sheets->appendOrder($order->load('items.menu'));
        } catch (\Exception $e) {
            \Log::error('Google Sheets error: ' . $e->getMessage());
        }

        return redirect()->route('admin.orders.receipt', $order->id);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.kasir.create')->with('success', 'Pesanan dihapus.');
    }
}
