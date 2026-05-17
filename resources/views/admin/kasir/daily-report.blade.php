@extends('layouts.admin')

@section('title', 'Laporan Harian - Kopay')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Laporan Harian</h2>
        <p class="text-sm text-gray-500 mt-0.5">Ringkasan pesanan selesai per hari</p>
    </div>
</div>

{{-- Filter tanggal --}}
<form method="GET" action="{{ route('admin.kasir.daily-report') }}"
      class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm flex flex-wrap gap-3 items-end">
    <div>
        <label class="block text-xs font-medium text-gray-700 mb-1.5">Tanggal</label>
        <input type="date" name="date" value="{{ $date }}"
               class="border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
    </div>
    <button type="submit"
            class="bg-primary-800 hover:bg-primary-900 text-white text-sm font-medium px-5 py-2.5 rounded-xl transition shadow-sm">
        Tampilkan
    </button>
</form>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Pesanan Selesai</p>
        <p class="text-3xl font-bold text-gray-900">{{ $totalOrders }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Total Pendapatan</p>
        <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Tunai</p>
        <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($tunaiRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $tunaiOrders }} pesanan</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">QRIS</p>
        <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($qrisRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $qrisOrders }} pesanan</p>
    </div>
</div>

{{-- Tabel pesanan --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-700">
            Detail Pesanan — {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
        </h3>
    </div>

    {{-- Desktop --}}
    <div class="hidden md:block">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Pelanggan</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Item</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Total</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Bayar</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50/80 transition">
                    <td class="px-5 py-3.5 text-gray-400 text-xs font-mono">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-5 py-3.5 font-medium text-gray-900">{{ $order->customer_name }}</td>
                    <td class="px-5 py-3.5 text-gray-500 text-xs max-w-[200px] truncate">
                        {{ $order->items->map(fn($i) => ($i->menu->name ?? 'Menu dihapus') . ' x' . $i->quantity)->join(', ') }}
                    </td>
                    <td class="px-5 py-3.5 font-semibold text-primary-700">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td class="px-5 py-3.5 text-xs capitalize text-gray-500">{{ $order->payment_method ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-gray-400 text-xs">{{ $order->updated_at->format('H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-12 text-gray-400">
                        <p class="text-sm">Belum ada pesanan selesai pada tanggal ini</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile --}}
    <div class="md:hidden divide-y divide-gray-100">
        @forelse($orders as $order)
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
                <p class="text-xs text-gray-400">{{ $order->updated_at->format('H:i') }}</p>
                <span class="text-xs capitalize text-gray-500">{{ $order->payment_method ?? '—' }}</span>
            </div>
        </div>
        @empty
        <div class="text-center text-gray-400 py-12 text-sm">Belum ada pesanan selesai pada tanggal ini.</div>
        @endforelse
    </div>
</div>
@endsection
