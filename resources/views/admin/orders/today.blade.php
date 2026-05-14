@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Riwayat Pesanan Hari Ini</h2>
        <p class="text-sm text-gray-500 mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>
    <a href="{{ route('admin.kasir.create') }}"
       class="bg-primary-800 hover:bg-primary-900 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
        + Pesanan Baru
    </a>
</div>

@if($orders->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p class="text-gray-400 text-sm">Belum ada pesanan hari ini.</p>
    </div>
@else

{{-- Summary strip --}}
@php
    $completed = $orders->where('status', 'completed');
    $totalRevenue = $completed->sum('total_price');
@endphp
<div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Pesanan</p>
        <p class="text-2xl font-bold text-gray-900">{{ $orders->count() }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Selesai</p>
        <p class="text-2xl font-bold text-green-600">{{ $completed->count() }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4 col-span-2 sm:col-span-1">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Pendapatan</p>
        <p class="text-2xl font-bold text-primary-700">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
    </div>
</div>

{{-- Desktop table --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden hidden md:block">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">#</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Pelanggan</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Item</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Bayar</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Waktu</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($orders as $order)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-3.5 text-gray-400 text-xs">{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                <td class="px-5 py-3.5 font-medium text-gray-900">{{ $order->customer_name }}</td>
                <td class="px-5 py-3.5 text-gray-500 text-xs max-w-xs">
                    {{ $order->items->map(fn($i) => ($i->menu->name ?? 'Menu dihapus') . ' x' . $i->quantity)->join(', ') }}
                </td>
                <td class="px-5 py-3.5 font-semibold text-primary-700">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                <td class="px-5 py-3.5">
                    @php
                        $statusMap = [
                            'pending'   => ['label' => 'Pending',    'class' => 'bg-yellow-100 text-yellow-700'],
                            'completed' => ['label' => 'Selesai',    'class' => 'bg-green-100 text-green-700'],
                            'cancelled' => ['label' => 'Dibatalkan', 'class' => 'bg-red-100 text-red-700'],
                        ];
                        $s = $statusMap[$order->status] ?? ['label' => $order->status, 'class' => 'bg-gray-100 text-gray-600'];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $s['class'] }}">
                        {{ $s['label'] }}
                    </span>
                </td>
                <td class="px-5 py-3.5 text-gray-500 text-xs capitalize">
                    {{ $order->payment_method ?? '—' }}
                </td>
                <td class="px-5 py-3.5 text-gray-400 text-xs">{{ $order->created_at->format('H:i') }}</td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-2">
                        @if($order->status === 'completed')
                            <a href="{{ route('admin.orders.receipt', $order) }}"
                               class="text-xs font-medium text-primary-700 hover:text-primary-900 transition">
                                Cetak Struk
                            </a>
                        @elseif($order->status === 'pending')
                            <a href="{{ route('admin.orders.edit', $order) }}"
                               class="text-xs font-medium text-gray-600 hover:text-gray-900 transition">
                                Edit
                            </a>
                            <form action="{{ route('admin.orders.complete', $order) }}" method="POST"
                                  class="flex items-center gap-1">
                                @csrf
                                <select name="payment_method"
                                        class="text-xs border border-gray-300 rounded-lg px-2 py-1 focus:outline-none bg-white">
                                    <option value="tunai" {{ $order->payment_method === 'tunai' ? 'selected' : '' }}>Tunai</option>
                                    <option value="qris" {{ $order->payment_method === 'qris' ? 'selected' : '' }}>QRIS</option>
                                </select>
                                <button type="submit"
                                        onclick="return confirm('Konfirmasi pesanan #{{ $order->id }} sudah dibayar?')"
                                        class="text-xs font-semibold text-white px-2.5 py-1 rounded-lg"
                                        style="background:#1a3a1a">
                                    Konfirmasi
                                </button>
                            </form>
                            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST"
                                  onsubmit="return confirm('Batalkan pesanan #{{ $order->id }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700 transition">
                                    Batal
                                </button>
                            </form>
                        @else
                            <span class="text-xs text-gray-300">—</span>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Mobile card list --}}
<div class="md:hidden space-y-3">
    @foreach($orders as $order)
    @php
        $statusMap = [
            'pending'   => ['label' => 'Pending',    'class' => 'bg-yellow-100 text-yellow-700'],
            'completed' => ['label' => 'Selesai',    'class' => 'bg-green-100 text-green-700'],
            'cancelled' => ['label' => 'Dibatalkan', 'class' => 'bg-red-100 text-red-700'],
        ];
        $s = $statusMap[$order->status] ?? ['label' => $order->status, 'class' => 'bg-gray-100 text-gray-600'];
    @endphp
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-start justify-between mb-2">
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="text-xs text-gray-400">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <span class="font-semibold text-gray-900 text-sm">{{ $order->customer_name }}</span>
                </div>
                <p class="text-xs text-gray-500">
                    {{ $order->items->map(fn($i) => ($i->menu->name ?? 'Menu dihapus') . ' x' . $i->quantity)->join(', ') }}
                </p>
            </div>
            <span class="font-bold text-primary-700 text-sm ml-3 shrink-0">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
        </div>
        <div class="flex items-center justify-between mt-3">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $s['class'] }}">
                    {{ $s['label'] }}
                </span>
                @if($order->payment_method)
                    <span class="text-xs text-gray-400 capitalize">{{ $order->payment_method }}</span>
                @endif
                <span class="text-xs text-gray-400">{{ $order->created_at->format('H:i') }}</span>
            </div>
            @if($order->status === 'completed')
                <a href="{{ route('admin.orders.receipt', $order) }}"
                   class="text-xs font-medium text-primary-700 hover:text-primary-900 transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 17H17.01M17 3H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2z"/>
                    </svg>
                    Cetak Struk
                </a>
            @elseif($order->status === 'pending')
                <div class="flex flex-col gap-2 items-end">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.orders.edit', $order) }}"
                           class="text-xs font-medium text-gray-600 hover:text-gray-900 transition">Edit</a>
                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST"
                              onsubmit="return confirm('Batalkan pesanan #{{ $order->id }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700 transition">
                                Batal
                            </button>
                        </form>
                    </div>
                    <form action="{{ route('admin.orders.complete', $order) }}" method="POST"
                          class="flex items-center gap-1">
                        @csrf
                        <select name="payment_method"
                                class="text-xs border border-gray-300 rounded-lg px-2 py-1 focus:outline-none bg-white">
                            <option value="tunai" {{ $order->payment_method === 'tunai' ? 'selected' : '' }}>Tunai</option>
                            <option value="qris" {{ $order->payment_method === 'qris' ? 'selected' : '' }}>QRIS</option>
                        </select>
                        <button type="submit"
                                onclick="return confirm('Konfirmasi pesanan #{{ $order->id }} sudah dibayar?')"
                                class="text-xs font-semibold text-white px-2.5 py-1 rounded-lg"
                                style="background:#1a3a1a">
                            Konfirmasi
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
    @endforeach
</div>

@endif
@endsection
