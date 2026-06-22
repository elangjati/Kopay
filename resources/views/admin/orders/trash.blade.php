@extends('layouts.admin')

@section('title', 'Trash Pesanan - Kopay')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">🗑️ Trash Pesanan</h2>
        <p class="text-sm text-gray-500 mt-0.5">Pesanan yang telah dihapus — Owner hanya</p>
    </div>
    <a href="{{ route('admin.orders.history') }}"
       class="border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2.5 rounded-lg transition">
        ← Kembali
    </a>
</div>

{{-- Search --}}
<form method="GET" action="{{ route('admin.orders.trash') }}" class="mb-5 flex gap-2">
    <input type="text" name="search" value="{{ $search }}"
           placeholder="Cari nama pelanggan..."
           class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
    <button type="submit"
            class="bg-primary-800 hover:bg-primary-900 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        Cari
    </button>
    @if($search)
        <a href="{{ route('admin.orders.trash') }}"
           class="border border-gray-300 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-50 transition">
            Reset
        </a>
    @endif
</form>

@if($trashedOrders->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-gray-400 text-sm">Tidak ada pesanan di trash{{ $search ? " untuk \"$search\"" : '' }}.</p>
    </div>
@else

{{-- Desktop table --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden hidden md:block">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Pelanggan</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Item</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Total</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Metode</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Dihapus</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($trashedOrders as $order)
            <tr class="hover:bg-gray-50 transition opacity-75">
                <td class="px-5 py-3.5 text-gray-400 text-xs">{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                <td class="px-5 py-3.5 font-medium text-gray-900 line-through">{{ $order->customer_name }}</td>
                <td class="px-5 py-3.5 text-gray-500 text-xs max-w-xs">
                    {{ $order->items->map(fn($i) => ($i->menu->name ?? 'Menu dihapus') . ' x' . $i->quantity)->join(', ') }}
                </td>
                <td class="px-5 py-3.5 font-semibold text-primary-700">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                <td class="px-5 py-3.5 text-gray-500 text-xs capitalize">{{ $order->payment_method ?? '—' }}</td>
                <td class="px-5 py-3.5 text-gray-400 text-xs">
                    {{ $order->deleted_at->format('d/m/Y H:i') }}
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-2">
                        <form action="{{ route('admin.orders.restore', $order) }}" method="POST"
                              onsubmit="return confirm('Pulihkan pesanan #{{ $order->id }}?')">
                            @csrf
                            <button type="submit" class="text-xs font-medium text-green-600 hover:text-green-700 transition">
                                Pulihkan
                            </button>
                        </form>
                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST"
                              onsubmit="return confirm('Hapus pesanan #{{ $order->id }} SELAMANYA? Tidak bisa dikembalikan.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700 transition">
                                Hapus Permanen
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Mobile card list --}}
<div class="md:hidden space-y-3">
    @foreach($trashedOrders as $order)
    <div class="bg-white rounded-xl border border-gray-200 p-4 opacity-75">
        <div class="flex items-start justify-between mb-2">
            <div>
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="text-xs text-gray-400">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <span class="font-semibold text-gray-900 text-sm line-through">{{ $order->customer_name }}</span>
                </div>
                <p class="text-xs text-gray-500">
                    {{ $order->items->map(fn($i) => ($i->menu->name ?? 'Menu dihapus') . ' x' . $i->quantity)->join(', ') }}
                </p>
                <p class="text-xs text-gray-400 mt-0.5">Dihapus: {{ $order->deleted_at->format('d/m/Y H:i') }}</p>
            </div>
            <span class="font-bold text-primary-700 text-sm ml-3 shrink-0">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
        </div>
        <div class="flex items-center gap-2 mt-3">
            <form action="{{ route('admin.orders.restore', $order) }}" method="POST"
                  onsubmit="return confirm('Pulihkan pesanan #{{ $order->id }}?')"
                  class="flex-1">
                @csrf
                <button type="submit" class="w-full text-xs font-medium text-white bg-green-600 hover:bg-green-700 px-3 py-2 rounded-lg transition">
                    Pulihkan
                </button>
            </form>
            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST"
                  onsubmit="return confirm('Hapus SELAMANYA?')"
                  class="flex-1">
                @csrf @method('DELETE')
                <button type="submit" class="w-full text-xs font-medium text-white bg-red-600 hover:bg-red-700 px-3 py-2 rounded-lg transition">
                    Hapus Permanen
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-4">{{ $trashedOrders->links() }}</div>

@endif

@endsection
