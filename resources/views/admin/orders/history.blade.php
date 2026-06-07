@extends('layouts.admin')

@section('title', 'Riwayat Pesanan - Kopay')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Riwayat Pesanan</h2>
        <p class="text-sm text-gray-500 mt-0.5">Semua pesanan — pending & selesai</p>
    </div>
    <a href="{{ route('admin.kasir.create') }}"
       class="bg-primary-800 hover:bg-primary-900 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
        + Pesanan Baru
    </a>
</div>

{{-- Search --}}
<form method="GET" action="{{ route('admin.orders.history') }}" class="mb-5 flex gap-2">
    <input type="hidden" name="tab" value="{{ $tab }}">
    <input type="text" name="search" value="{{ $search }}"
           placeholder="Cari nama pelanggan..."
           class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
    <button type="submit"
            class="bg-primary-800 hover:bg-primary-900 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        Cari
    </button>
    @if($search)
        <a href="{{ route('admin.orders.history', ['tab' => $tab]) }}"
           class="border border-gray-300 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-50 transition">
            Reset
        </a>
    @endif
</form>

{{-- Tabs --}}
<div class="flex gap-1 mb-5 border-b border-gray-200">
    <a href="{{ route('admin.orders.history', ['tab' => 'pending', 'search' => $search]) }}"
       class="px-4 py-2.5 text-sm font-medium border-b-2 transition -mb-px
              {{ $tab === 'pending' ? 'border-primary-800 text-primary-800' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
        Pending
        <span class="ml-1.5 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold
                     {{ $tab === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-500' }}">
            {{ $totalPending }}
        </span>
    </a>
    <a href="{{ route('admin.orders.history', ['tab' => 'completed', 'search' => $search]) }}"
       class="px-4 py-2.5 text-sm font-medium border-b-2 transition -mb-px
              {{ $tab === 'completed' ? 'border-primary-800 text-primary-800' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
        Selesai
        <span class="ml-1.5 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold
                     {{ $tab === 'completed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
            {{ $totalCompleted }}
        </span>
    </a>
</div>

{{-- TAB PENDING --}}
@if($tab === 'pending')
    @if($pendingOrders->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <p class="text-gray-400 text-sm">Tidak ada pesanan pending{{ $search ? " untuk \"$search\"" : '' }}.</p>
        </div>
    @else
        {{-- Desktop --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hidden md:block">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Pelanggan</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Item</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Total</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($pendingOrders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3.5 text-gray-400 text-xs">{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-5 py-3.5 font-medium text-gray-900">{{ $order->customer_name }}</td>
                        <td class="px-5 py-3.5 text-gray-500 text-xs max-w-xs">
                            {{ $order->items->map(fn($i) => ($i->menu->name ?? 'Menu dihapus') . ' x' . $i->quantity)->join(', ') }}
                        </td>
                        <td class="px-5 py-3.5 font-semibold text-primary-700">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td class="px-5 py-3.5 text-gray-400 text-xs">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                            @if(!$order->created_at->isToday())
                                <span class="ml-1 text-orange-500 font-medium">lama</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.orders.edit', $order) }}"
                                   class="text-xs font-medium text-gray-600 hover:text-gray-900 transition">Edit</a>
                                <form action="{{ route('admin.orders.complete', $order) }}" method="POST"
                                      class="flex items-center gap-1">
                                    @csrf
                                    <select name="payment_method"
                                            class="text-xs border border-gray-300 rounded-lg px-2 py-1 bg-white focus:outline-none">
                                        <option value="tunai">Tunai</option>
                                        <option value="qris">QRIS</option>
                                    </select>
                                    <button type="submit"
                                            onclick="return confirm('Konfirmasi pesanan #{{ $order->id }} sudah dibayar?')"
                                            class="text-xs font-semibold text-white px-2.5 py-1 rounded-lg"
                                            style="background:#1a3a1a">
                                        Selesai
                                    </button>
                                </form>
                                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST"
                                      onsubmit="return confirm('Batalkan pesanan #{{ $order->id }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700 transition">
                                        Batal
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="md:hidden space-y-3">
            @foreach($pendingOrders as $order)
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-xs text-gray-400">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                            <span class="font-semibold text-gray-900 text-sm">{{ $order->customer_name }}</span>
                            @if(!$order->created_at->isToday())
                                <span class="text-xs text-orange-500 font-medium">lama</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500">
                            {{ $order->items->map(fn($i) => ($i->menu->name ?? 'Menu dihapus') . ' x' . $i->quantity)->join(', ') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <span class="font-bold text-primary-700 text-sm ml-3 shrink-0">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex flex-col gap-2 mt-3">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.orders.edit', $order) }}"
                           class="text-xs font-medium text-gray-600 border border-gray-300 px-3 py-1.5 rounded-lg hover:bg-gray-50 transition">
                            Edit
                        </a>
                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST"
                              onsubmit="return confirm('Batalkan pesanan #{{ $order->id }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-red-500 border border-red-200 px-3 py-1.5 rounded-lg hover:bg-red-50 transition">
                                Batal
                            </button>
                        </form>
                    </div>
                    <form action="{{ route('admin.orders.complete', $order) }}" method="POST"
                          class="flex items-center gap-1">
                        @csrf
                        <select name="payment_method"
                                class="flex-1 text-xs border border-gray-300 rounded-lg px-2 py-1.5 bg-white focus:outline-none">
                            <option value="tunai">Tunai</option>
                            <option value="qris">QRIS</option>
                        </select>
                        <button type="submit"
                                onclick="return confirm('Konfirmasi pesanan #{{ $order->id }} sudah dibayar?')"
                                class="text-xs font-semibold text-white px-3 py-1.5 rounded-lg"
                                style="background:#1a3a1a">
                            Selesai
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $pendingOrders->links() }}</div>
    @endif

{{-- TAB COMPLETED --}}
@else
    @if($completedOrders->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <p class="text-gray-400 text-sm">Tidak ada pesanan selesai{{ $search ? " untuk \"$search\"" : '' }}.</p>
        </div>
    @else
        {{-- Desktop --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hidden md:block">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Pelanggan</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Item</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Total</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Metode</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($completedOrders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3.5 text-gray-400 text-xs">{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-5 py-3.5 font-medium text-gray-900">{{ $order->customer_name }}</td>
                        <td class="px-5 py-3.5 text-gray-500 text-xs max-w-xs">
                            {{ $order->items->map(fn($i) => ($i->menu->name ?? 'Menu dihapus') . ' x' . $i->quantity)->join(', ') }}
                        </td>
                        <td class="px-5 py-3.5 font-semibold text-primary-700">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td class="px-5 py-3.5 text-gray-500 text-xs capitalize">{{ $order->payment_method ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-gray-400 text-xs">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.orders.receipt', $order) }}"
                                   class="text-xs font-medium text-primary-700 hover:text-primary-900 transition">
                                    Cetak Struk
                                </a>
                                <form action="{{ route('admin.orders.change-payment', $order) }}" method="POST"
                                      class="flex items-center gap-1"
                                      onsubmit="return confirm('Ubah metode pembayaran pesanan #{{ $order->id }}?')">
                                    @csrf
                                    <select name="payment_method"
                                            class="text-xs border border-gray-300 rounded-lg px-2 py-1 bg-white focus:outline-none">
                                        <option value="tunai" {{ $order->payment_method === 'tunai' ? 'selected' : '' }}>Tunai</option>
                                        <option value="qris" {{ $order->payment_method === 'qris' ? 'selected' : '' }}>QRIS</option>
                                    </select>
                                    <button type="submit"
                                            class="text-xs font-semibold text-white px-2.5 py-1 rounded-lg bg-orange-500 hover:bg-orange-600">
                                        Ganti
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="md:hidden space-y-3">
            @foreach($completedOrders as $order)
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
                        <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->format('d/m/Y H:i') }} · {{ $order->payment_method ?? '—' }}</p>
                    </div>
                    <span class="font-bold text-primary-700 text-sm ml-3 shrink-0">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center gap-2 mt-3">
                    <a href="{{ route('admin.orders.receipt', $order) }}"
                       class="text-xs font-medium text-primary-700 border border-primary-200 px-3 py-1.5 rounded-lg hover:bg-primary-50 transition">
                        Cetak Struk
                    </a>
                    <form action="{{ route('admin.orders.change-payment', $order) }}" method="POST"
                          class="flex items-center gap-1 flex-1"
                          onsubmit="return confirm('Ubah metode pembayaran?')">
                        @csrf
                        <select name="payment_method"
                                class="flex-1 text-xs border border-gray-300 rounded-lg px-2 py-1.5 bg-white focus:outline-none">
                            <option value="tunai" {{ $order->payment_method === 'tunai' ? 'selected' : '' }}>Tunai</option>
                            <option value="qris" {{ $order->payment_method === 'qris' ? 'selected' : '' }}>QRIS</option>
                        </select>
                        <button type="submit" class="text-xs font-semibold text-white px-3 py-1.5 rounded-lg bg-orange-500 hover:bg-orange-600">
                            Ganti
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $completedOrders->links() }}</div>
    @endif
@endif

@endsection
