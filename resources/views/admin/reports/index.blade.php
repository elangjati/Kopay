@extends('layouts.admin')

@section('title', 'Rekap Penjualan - Kopay')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Rekap Penjualan</h2>
        <p class="text-sm text-gray-500 mt-0.5">Laporan pesanan yang sudah selesai</p>
    </div>
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('admin.reports.index') }}"
      class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm"
      x-data="{ mode: '{{ $mode }}' }">

    {{-- Mode toggle --}}
    <div class="flex gap-2 mb-4">
        <label class="cursor-pointer">
            <input type="radio" name="mode" value="monthly" class="sr-only peer" x-model="mode"
                   {{ $mode === 'monthly' ? 'checked' : '' }}>
            <div class="px-4 py-2 rounded-lg text-sm font-medium border-2 transition
                        peer-checked:border-primary-700 peer-checked:bg-primary-50 peer-checked:text-primary-800
                        border-gray-200 text-gray-500 hover:border-gray-300">
                Per Bulan
            </div>
        </label>
        <label class="cursor-pointer">
            <input type="radio" name="mode" value="daily" class="sr-only peer" x-model="mode"
                   {{ $mode === 'daily' ? 'checked' : '' }}>
            <div class="px-4 py-2 rounded-lg text-sm font-medium border-2 transition
                        peer-checked:border-primary-700 peer-checked:bg-primary-50 peer-checked:text-primary-800
                        border-gray-200 text-gray-500 hover:border-gray-300">
                Per Hari
            </div>
        </label>
    </div>

    <div class="flex flex-wrap gap-3 items-end">
        {{-- Filter bulanan --}}
        <template x-if="mode === 'monthly'">
            <div class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Tahun</label>
                    <select name="year"
                            class="border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Bulan</label>
                    <select name="month"
                            class="border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
                        @foreach($months as $num => $name)
                            <option value="{{ $num }}" {{ $num == $month ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </template>

        {{-- Filter harian --}}
        <template x-if="mode === 'daily'">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Tanggal</label>
                <input type="date" name="date" value="{{ $date }}"
                       class="border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
            </div>
        </template>

        <button type="submit"
                class="bg-primary-800 hover:bg-primary-900 text-white text-sm font-medium px-5 py-2.5 rounded-xl transition shadow-sm">
            Tampilkan
        </button>
    </div>
</form>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pesanan Selesai</p>
            <div class="w-8 h-8 bg-primary-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900">{{ $summary->total_orders ?? 0 }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $periodLabel }}</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Pendapatan</p>
            <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-green-600">Rp {{ number_format($summary->total_revenue ?? 0, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $periodLabel }}</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tunai</p>
            <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($tunaiRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $tunaiOrders }} pesanan</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">QRIS</p>
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($qrisRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $qrisOrders }} pesanan</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Rata-rata / Pesanan</p>
            <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">
            Rp {{ ($summary->total_orders ?? 0) > 0 ? number_format($summary->total_revenue / $summary->total_orders, 0, ',', '.') : 0 }}
        </p>
        <p class="text-xs text-gray-400 mt-1">{{ $periodLabel }}</p>
    </div>
</div>

<div class="grid grid-cols-1 {{ $mode === 'monthly' ? 'lg:grid-cols-2' : '' }} gap-5 mb-6">

    {{-- Top Items --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
            Menu Terlaris — {{ $periodLabel }}
        </h3>
        @forelse($topItems as $i => $item)
        <div class="flex items-center justify-between py-3 {{ $i < $topItems->count() - 1 ? 'border-b border-gray-100' : '' }}">
            <div class="flex items-center gap-3">
                <span class="w-7 h-7 rounded-full text-xs font-bold flex items-center justify-center
                    {{ $i === 0 ? 'bg-primary-800 text-white' : ($i === 1 ? 'bg-gray-200 text-gray-700' : ($i === 2 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600')) }}">
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
        <div class="text-center py-10">
            <p class="text-sm text-gray-400">Belum ada data penjualan</p>
        </div>
        @endforelse
    </div>

    {{-- Grafik bulanan — hanya tampil di mode monthly --}}
    @if($mode === 'monthly' && $monthly)
    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Rekap Per Bulan — {{ $year }}</h3>
        <div class="space-y-2.5">
            @foreach($months as $num => $name)
            @php $data = $monthly->get($num); @endphp
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500 w-16 shrink-0 font-medium">{{ substr($name, 0, 3) }}</span>
                <div class="flex-1 bg-gray-100 rounded-full h-5 overflow-hidden">
                    @php
                        $maxRevenue = $monthly->max('total_revenue') ?: 1;
                        $width = $data ? round(($data->total_revenue / $maxRevenue) * 100) : 0;
                    @endphp
                    <div class="h-5 rounded-full transition-all duration-500 {{ $num == $month ? 'bg-primary-600' : 'bg-primary-300' }}"
                         style="width: {{ max($width, $data ? 8 : 0) }}%"></div>
                </div>
                <span class="text-xs text-gray-600 w-24 text-right shrink-0 font-medium">
                    {{ $data ? 'Rp ' . number_format($data->total_revenue, 0, ',', '.') : '—' }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Orders Table --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-700">Detail Pesanan Selesai — {{ $periodLabel }}</h3>
    </div>

    {{-- Desktop --}}
    <div class="hidden md:block">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">#</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Pelanggan</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Item</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Bayar</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($completedOrders as $order)
                <tr class="hover:bg-gray-50/80 transition">
                    <td class="px-5 py-3.5 text-gray-400 text-xs font-mono">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-5 py-3.5 font-medium text-gray-900">{{ $order->customer_name }}</td>
                    <td class="px-5 py-3.5 text-gray-500 text-xs max-w-[200px] truncate">
                        {{ $order->items->map(fn($i) => ($i->menu->name ?? 'Menu dihapus') . ' x' . $i->quantity)->join(', ') }}
                    </td>
                    <td class="px-5 py-3.5 font-semibold text-primary-700">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td class="px-5 py-3.5 text-xs capitalize text-gray-500">{{ $order->payment_method ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-gray-400 text-xs">{{ $order->created_at->format('d M, H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-12 text-gray-400">
                        <p class="text-sm">Belum ada pesanan selesai periode ini</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile --}}
    <div class="md:hidden divide-y divide-gray-100">
        @forelse($completedOrders as $order)
        <div class="p-4">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400 font-mono">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <span class="font-semibold text-gray-900 text-sm">{{ $order->customer_name }}</span>
                </div>
                <span class="font-bold text-primary-700 text-sm">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
            <p class="text-xs text-gray-500 mb-1.5 truncate">
                {{ $order->items->map(fn($i) => ($i->menu->name ?? 'Menu dihapus') . ' x' . $i->quantity)->join(', ') }}
            </p>
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-400">{{ $order->created_at->format('d M Y, H:i') }}</p>
                <span class="text-xs capitalize text-gray-500">{{ $order->payment_method ?? '—' }}</span>
            </div>
        </div>
        @empty
        <div class="text-center text-gray-400 py-12 text-sm">Belum ada pesanan selesai periode ini.</div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($completedOrders->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $completedOrders->links() }}
    </div>
    @endif
</div>
@endsection
