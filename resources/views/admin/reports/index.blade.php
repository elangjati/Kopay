@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Rekap Penjualan</h2>
        <p class="text-sm text-gray-500 mt-0.5">Laporan pesanan yang sudah selesai</p>
    </div>
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('admin.reports.index') }}"
      class="bg-white rounded-xl border border-gray-200 p-4 mb-6 flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-medium text-gray-700 mb-1.5">Tahun</label>
        <select name="year"
                class="border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-transparent bg-white">
            @foreach($years as $y)
                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-700 mb-1.5">Bulan</label>
        <select name="month"
                class="border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-transparent bg-white">
            @foreach($months as $num => $name)
                <option value="{{ $num }}" {{ $num == $month ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit"
            class="bg-primary-800 hover:bg-primary-900 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
        Tampilkan
    </button>
</form>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Total Pesanan Selesai</p>
        <p class="text-3xl font-bold text-gray-900">{{ $summary->total_orders ?? 0 }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $months[$month] }} {{ $year }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Total Pendapatan</p>
        <p class="text-3xl font-bold text-primary-700">Rp {{ number_format($summary->total_revenue ?? 0, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $months[$month] }} {{ $year }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Rata-rata per Pesanan</p>
        <p class="text-3xl font-bold text-gray-900">
            Rp {{ ($summary->total_orders ?? 0) > 0 ? number_format($summary->total_revenue / $summary->total_orders, 0, ',', '.') : 0 }}
        </p>
        <p class="text-xs text-gray-400 mt-1">{{ $months[$month] }} {{ $year }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">

    {{-- Top Items --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Menu Terlaris — {{ $months[$month] }} {{ $year }}</h3>
        @forelse($topItems as $i => $item)
        <div class="flex items-center justify-between py-2.5 {{ $i < $topItems->count() - 1 ? 'border-b border-gray-100' : '' }}">
            <div class="flex items-center gap-3">
                <span class="w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center
                    {{ $i === 0 ? 'bg-primary-800 text-white' : 'bg-gray-100 text-gray-600' }}">
                    {{ $i + 1 }}
                </span>
                <span class="text-sm text-gray-700">{{ $item->menu->name ?? 'Menu dihapus' }}</span>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-gray-900">{{ $item->total_qty }} porsi</p>
                <p class="text-xs text-gray-400">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</p>
            </div>
        </div>
        @empty
            <p class="text-gray-400 text-sm text-center py-8">Belum ada data penjualan.</p>
        @endforelse
    </div>

    {{-- Monthly Breakdown --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Rekap Per Bulan — {{ $year }}</h3>
        <div class="space-y-2.5">
            @foreach($months as $num => $name)
            @php $data = $monthly->get($num); @endphp
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500 w-20 shrink-0">{{ $name }}</span>
                <div class="flex-1 bg-gray-100 rounded-full h-4 overflow-hidden">
                    @php
                        $maxRevenue = $monthly->max('total_revenue') ?: 1;
                        $width = $data ? round(($data->total_revenue / $maxRevenue) * 100) : 0;
                    @endphp
                    <div class="h-4 rounded-full transition-all {{ $num == $month ? 'bg-primary-700' : 'bg-primary-300' }}"
                         style="width: {{ $width }}%"></div>
                </div>
                <span class="text-xs text-gray-600 w-28 text-right shrink-0">
                    {{ $data ? 'Rp ' . number_format($data->total_revenue, 0, ',', '.') : '—' }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Orders Table --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-700">Detail Pesanan Selesai — {{ $months[$month] }} {{ $year }}</h3>
    </div>
    {{-- Desktop: tabel --}}
    <div class="hidden md:block">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">#</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Pelanggan</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Item</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($completedOrders as $order)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3.5 text-gray-400 text-xs">{{ $order->id }}</td>
                    <td class="px-5 py-3.5 font-medium text-gray-900">{{ $order->customer_name }}</td>
                    <td class="px-5 py-3.5 text-gray-500 text-xs">
                        {{ $order->items->map(fn($i) => ($i->menu->name ?? '?') . ' x' . $i->quantity)->join(', ') }}
                    </td>
                    <td class="px-5 py-3.5 font-semibold text-primary-700">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td class="px-5 py-3.5 text-gray-400 text-xs">{{ $order->created_at->format('d M, H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-gray-400 py-10 text-sm">Belum ada pesanan selesai bulan ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile: card list --}}
    <div class="md:hidden divide-y divide-gray-100">
        @forelse($completedOrders as $order)
        <div class="p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400">#{{ $order->id }}</span>
                    <span class="font-semibold text-gray-900 text-sm">{{ $order->customer_name }}</span>
                </div>
                <span class="font-bold text-primary-700 text-sm">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
            <p class="text-xs text-gray-500 mb-1.5">
                {{ $order->items->map(fn($i) => ($i->menu->name ?? '?') . ' x' . $i->quantity)->join(', ') }}
            </p>
            <p class="text-xs text-gray-400">{{ $order->created_at->format('d M Y, H:i') }}</p>
        </div>
        @empty
        <div class="text-center text-gray-400 py-10 text-sm">Belum ada pesanan selesai bulan ini.</div>
        @endforelse
    </div>
</div>
@endsection
