@extends('layouts.admin')

@section('title', 'Laporan Harian - Kopay')

@push('styles')
<style>
    @media print {
        @page { size: A4; margin: 15mm; }
        body { background: white !important; }
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        .shadow-sm { box-shadow: none !important; }
        .rounded-xl { border-radius: 0 !important; }
    }
    .print-only { display: none; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6 no-print">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Laporan Harian</h2>
        <p class="text-sm text-gray-500 mt-0.5">Ringkasan pesanan selesai per hari</p>
    </div>
    @if($totalOrders > 0)
    <div class="flex gap-2 no-print">
        <button onclick="window.print()"
                class="flex items-center gap-2 bg-primary-800 hover:bg-primary-900 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak A4
        </button>
        <button onclick="printLaporanRawBT()"
                class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Thermal
        </button>
    </div>
    @endif
</div>

{{-- Print header (hanya muncul saat print) --}}
<div style="display:none" class="print-only mb-6 text-center border-b-2 border-gray-800 pb-4">
    <h1 class="text-2xl font-bold">KOPAY</h1>
    <h2 class="text-lg font-semibold mt-1">Laporan Harian</h2>
    <p class="text-sm mt-1">{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</p>
</div>

{{-- Filter tanggal --}}
<form method="GET" action="{{ route('admin.kasir.daily-report') }}"
      class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm flex flex-wrap gap-3 items-end no-print">
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

{{-- Rekap Produk Terjual --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm mb-6">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-700">Rekap Produk Terjual</h3>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Produk</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Qty Terjual</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($productSummary as $product)
            <tr class="hover:bg-gray-50/80">
                <td class="px-5 py-3 font-medium text-gray-900">{{ $product['name'] }}</td>
                <td class="px-5 py-3 text-center text-gray-700 font-semibold">{{ $product['qty'] }}</td>
                <td class="px-5 py-3 text-right font-semibold text-primary-700">Rp {{ number_format($product['revenue'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center py-8 text-gray-400 text-sm">Belum ada produk terjual</td>
            </tr>
            @endforelse
        </tbody>
        @if($productSummary->count() > 0)
        <tfoot class="bg-gray-50 border-t-2 border-gray-200">
            <tr>
                <td class="px-5 py-3 font-bold text-gray-800">Total</td>
                <td class="px-5 py-3 text-center font-bold text-gray-800">{{ $productSummary->sum('qty') }}</td>
                <td class="px-5 py-3 text-right font-bold text-green-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

{{-- Tabel detail pesanan --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-700">
            Detail Pesanan — {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
        </h3>
    </div>
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

{{-- Print footer --}}
<div class="print-only mt-6 pt-4 border-t border-gray-300 text-center text-xs text-gray-500">
    <p>Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
</div>

@push('scripts')
<script>
function printLaporanRawBT() {
    const lines = [];
    const sep  = '--------------------------------';
    const sep2 = '================================';

    // Header
    lines.push('\x1B\x61\x01'); // center
    lines.push('\x1B\x21\x30'); // double size
    lines.push('KOPAY\n');
    lines.push('\x1B\x21\x00'); // normal
    lines.push('LAPORAN HARIAN\n');
    lines.push('{{ \Carbon\Carbon::parse($date)->translatedFormat("d F Y") }}\n');
    lines.push(sep2 + '\n');

    // Summary
    lines.push('\x1B\x61\x00'); // left
    lines.push('Pesanan Selesai : {{ $totalOrders }}\n');
    lines.push('Total Pendapatan: Rp {{ number_format($totalRevenue, 0, ",", ".") }}\n');
    lines.push('Tunai           : Rp {{ number_format($tunaiRevenue, 0, ",", ".") }} ({{ $tunaiOrders }} pesanan)\n');
    lines.push('QRIS            : Rp {{ number_format($qrisRevenue, 0, ",", ".") }} ({{ $qrisOrders }} pesanan)\n');
    lines.push(sep + '\n');

    // Rekap produk
    lines.push('\x1B\x61\x01'); // center
    lines.push('REKAP PRODUK TERJUAL\n');
    lines.push('\x1B\x61\x00'); // left
    lines.push(sep + '\n');

    @foreach($productSummary as $product)
    lines.push('{{ addslashes($product["name"]) }}\n');
    lines.push('  {{ $product["qty"] }} porsi  Rp {{ number_format($product["revenue"], 0, ",", ".") }}\n');
    @endforeach

    lines.push(sep + '\n');
    lines.push('Total: {{ $productSummary->sum("qty") }} porsi\n');
    lines.push('       Rp {{ number_format($totalRevenue, 0, ",", ".") }}\n');
    lines.push(sep2 + '\n');

    // Footer
    lines.push('\x1B\x61\x01'); // center
    lines.push('Dicetak: {{ now()->format("d/m/Y H:i") }}\n');
    lines.push('\n\n\n');

    const text = lines.join('');
    const encoded = btoa(unescape(encodeURIComponent(text)));
    window.location.href = 'rawbt:base64,' + encoded;
}
</script>
@endpush

@endsection
