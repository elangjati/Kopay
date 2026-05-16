<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $mode  = $request->get('mode', 'monthly'); // 'monthly' | 'daily'
        $year  = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);
        $date  = $request->get('date', now()->toDateString()); // YYYY-MM-DD

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        // Available years for filter
        $years = Order::selectRaw('YEAR(created_at) as year')
            ->groupByRaw('YEAR(created_at)')
            ->orderByDesc('year')
            ->pluck('year');

        if ($years->isEmpty()) {
            $years = collect([now()->year]);
        }

        // Base query builder helper
        $baseQuery = function () use ($mode, $year, $month, $date) {
            $q = Order::where('status', 'completed');
            if ($mode === 'daily') {
                $q->whereDate('created_at', $date);
            } else {
                $q->whereYear('created_at', $year)->whereMonth('created_at', $month);
            }
            return $q;
        };

        // Summary
        $summary = $baseQuery()
            ->selectRaw('COUNT(*) as total_orders, SUM(total_price) as total_revenue')
            ->first();

        // Breakdown per metode bayar
        $revenueByMethod = $baseQuery()
            ->selectRaw('payment_method, COUNT(*) as total_orders, SUM(total_price) as total_revenue')
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        $tunaiRevenue = $revenueByMethod->get('tunai')?->total_revenue ?? 0;
        $qrisRevenue  = $revenueByMethod->get('qris')?->total_revenue ?? 0;
        $tunaiOrders  = $revenueByMethod->get('tunai')?->total_orders ?? 0;
        $qrisOrders   = $revenueByMethod->get('qris')?->total_orders ?? 0;

        // Top selling items
        $topItems = OrderItem::whereHas('order', function ($q) use ($mode, $year, $month, $date) {
                $q->where('status', 'completed');
                if ($mode === 'daily') {
                    $q->whereDate('created_at', $date);
                } else {
                    $q->whereYear('created_at', $year)->whereMonth('created_at', $month);
                }
            })
            ->select('menu_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(price * quantity) as total_revenue'))
            ->with('menu:id,name')
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Monthly breakdown (hanya untuk mode bulanan)
        $monthly = null;
        if ($mode === 'monthly') {
            $monthly = Order::whereYear('created_at', $year)
                ->where('status', 'completed')
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as total_orders, SUM(total_price) as total_revenue')
                ->groupByRaw('MONTH(created_at)')
                ->orderByRaw('MONTH(created_at)')
                ->get()
                ->keyBy('month');
        }

        // Detail pesanan (paginated)
        $completedOrders = $baseQuery()
            ->with('items.menu')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Label periode untuk heading
        $periodLabel = $mode === 'daily'
            ? Carbon::parse($date)->translatedFormat('l, d F Y')
            : ($months[$month] . ' ' . $year);

        return view('admin.reports.index', compact(
            'summary', 'topItems', 'monthly', 'years', 'months',
            'year', 'month', 'date', 'mode', 'completedOrders',
            'tunaiRevenue', 'qrisRevenue', 'tunaiOrders', 'qrisOrders',
            'periodLabel'
        ));
    }
}
