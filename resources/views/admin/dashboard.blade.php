@extends('layouts.admin')

@section('title', 'Dashboard - Kopay')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-900">Dashboard</h2>
    <p class="text-sm text-gray-500 mt-0.5">Ringkasan aktivitas hari ini</p>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow transition">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $todayOrders }}</p>
        <p class="text-xs text-gray-500 mt-1">Pesanan Hari Ini</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow transition">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-green-600">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-500 mt-1">Pendapatan Hari Ini</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow transition">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-yellow-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-yellow-600">{{ $pendingOrders }}</p>
        <p class="text-xs text-gray-500 mt-1">Menunggu Bayar</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow transition">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-blue-600">{{ $totalMenus }}</p>
        <p class="text-xs text-gray-500 mt-1">Menu Aktif</p>
    </div>
</div>

{{-- Quick Actions --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
    <a href="{{ route('admin.kasir.create') }}"
       class="bg-primary-800 hover:bg-primary-900 text-white rounded-xl p-4 transition shadow-sm hover:shadow flex items-center gap-3">
        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-sm">Pesanan Baru</p>
            <p class="text-xs text-primary-200">Buat pesanan kasir</p>
        </div>
    </a>
    <a href="{{ route('admin.menus.index') }}"
       class="bg-white hover:bg-gray-50 border border-gray-200 rounded-xl p-4 transition shadow-sm hover:shadow flex items-center gap-3">
        <div class="w-10 h-10 bg-primary-50 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-sm text-gray-800">Kelola Menu</p>
            <p class="text-xs text-gray-500">Tambah atau edit menu</p>
        </div>
    </a>
    @if(auth()->user()?->role === 'owner')
    <a href="{{ route('admin.reports.index') }}"
       class="bg-white hover:bg-gray-50 border border-gray-200 rounded-xl p-4 transition shadow-sm hover:shadow flex items-center gap-3">
        <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-sm text-gray-800">Laporan</p>
            <p class="text-xs text-gray-500">Rekap penjualan</p>
        </div>
    </a>
    @endif
</div>

{{-- Recent Orders --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-700">Pesanan Terbaru</h3>
        <span class="text-xs text-gray-400">{{ $orders->count() }} pesanan</span>
    </div>

    {{-- Desktop --}}
    <div class="hidden md:block">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Pelanggan</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Item</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Total</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Bayar</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50/80 transition">
                    <td class="px-5 py-3 font-mono text-xs text-gray-400">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $order->customer_name }}</td>
                    <td class="px-5 py-3 text-gray-500 text-xs max-w-[180px] truncate">
                        {{ $order->items->map(fn($i) => ($i->menu->name ?? '?') . ' x' . $i->quantity)->join(', ') }}
                    </td>
                    <td class="px-5 py-3 font-semibold text-primary-700">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-xs capitalize">{{ $order->payment_method ?? '-' }}</td>
                    <td class="px-5 py-3">
                        @if($order->status === 'completed')
                            <span class="text-xs px-2 py-1 rounded-full font-semibold bg-green-50 text-green-700 border border-green-200">Selesai</span>
                        @elseif($order->status === 'pending')
                            <span class="text-xs px-2 py-1 rounded-full font-semibold bg-yellow-50 text-yellow-700 border border-yellow-200">Pending</span>
                        @else
                            <span class="text-xs px-2 py-1 rounded-full font-semibold bg-gray-100 text-gray-600">{{ $order->status }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-400 text-xs">{{ $order->created_at->diffForHumans() }}</td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex gap-1.5 justify-end">
                            @if($order->status === 'completed')
                            <a href="{{ route('admin.orders.receipt', $order) }}" class="text-xs border border-gray-300 hover:bg-gray-50 text-gray-600 px-2.5 py-1.5 rounded-lg transition">Struk</a>
                            @elseif($order->status === 'pending' && $order->payment_method === 'qris')
                            <a href="{{ route('admin.kasir.qris', $order) }}" class="text-xs border border-primary-300 hover:bg-primary-50 text-primary-700 px-2.5 py-1.5 rounded-lg transition">Bayar</a>
                            @endif
                            <a href="{{ route('admin.orders.edit', $order) }}" class="text-xs border border-gray-300 hover:bg-gray-50 text-gray-600 px-2.5 py-1.5 rounded-lg transition">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-12">
                        <div class="text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-sm font-medium text-gray-500">Belum ada pesanan</p>
                            <p class="text-xs mt-1"><a href="{{ route('admin.kasir.create') }}" class="text-primary-600 hover:underline">Buat pesanan pertama</a></p>
                        </div>
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
            <div class="flex items-center gap-2 mb-2">
                @if($order->status === 'completed')
                    <span class="text-xs px-2 py-0.5 rounded-full font-semibold bg-green-50 text-green-700 border border-green-200">Selesai</span>
                @elseif($order->status === 'pending')
                    <span class="text-xs px-2 py-0.5 rounded-full font-semibold bg-yellow-50 text-yellow-700 border border-yellow-200">Pending</span>
                @endif
                <span class="text-xs text-gray-400">{{ $order->created_at->diffForHumans() }}</span>
            </div>
            <div class="flex gap-2 pt-2 border-t border-gray-100">
                @if($order->status === 'completed')
                <a href="{{ route('admin.orders.receipt', $order) }}" class="flex-1 text-center text-xs border border-gray-300 hover:bg-gray-50 text-gray-600 py-1.5 rounded-lg transition">Struk</a>
                @elseif($order->status === 'pending' && $order->payment_method === 'qris')
                <a href="{{ route('admin.kasir.qris', $order) }}" class="flex-1 text-center text-xs border border-primary-300 hover:bg-primary-50 text-primary-700 py-1.5 rounded-lg transition">Bayar</a>
                @endif
                <a href="{{ route('admin.orders.edit', $order) }}" class="flex-1 text-center text-xs border border-gray-300 hover:bg-gray-50 text-gray-600 py-1.5 rounded-lg transition">Edit</a>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-gray-400">
            <p class="text-sm">Belum ada pesanan</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
