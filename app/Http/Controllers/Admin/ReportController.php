<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        // Summary for selected month
        $summary = Order::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('status', 'completed')
            ->selectRaw('COUNT(*) as total_orders, SUM(total_price) as total_revenue')
            ->first();

        // Top selling items this month
        $topItems = OrderItem::whereHas('order', function ($q) use ($year, $month) {
                $q->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month)
                  ->where('status', 'completed');
            })
            ->select('menu_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(price * quantity) as total_revenue'))
            ->with('menu:id,name')
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Monthly breakdown for the selected year (all months)
        $monthly = Order::whereYear('created_at', $year)
            ->where('status', 'completed')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total_orders, SUM(total_price) as total_revenue')
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get()
            ->keyBy('month');

        // Available years for filter
        $years = Order::selectRaw('YEAR(created_at) as year')
            ->groupByRaw('YEAR(created_at)')
            ->orderByDesc('year')
            ->pluck('year');

        if ($years->isEmpty()) {
            $years = collect([now()->year]);
        }

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return view('admin.reports.index', compact(
            'summary', 'topItems', 'monthly', 'years', 'months', 'year', 'month'
        ));
    }
}
