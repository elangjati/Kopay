<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            @page {
                size: 58mm auto;
                margin: 0;
            }
            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 58mm !important;
            }
            .no-print { display: none !important; }
            .receipt-wrapper {
                box-shadow: none !important;
                border: none !important;
                width: 58mm !important;
                min-width: unset !important;
                max-width: 58mm !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-start justify-center py-8">

<div class="w-full max-w-sm px-4 no-print mb-4">
    <a href="{{ route('admin.kasir.create') }}"
       class="flex items-center justify-center gap-2 w-full bg-primary-800 hover:bg-primary-900 text-white font-semibold py-2.5 rounded-xl transition text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Pesanan Baru
    </a>
    <button onclick="printViaRawBT()"
            class="flex items-center justify-center gap-2 w-full mt-2 bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 rounded-xl transition text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Print via RawBT
    </button>
    <button onclick="window.print()"
            class="flex items-center justify-center gap-2 w-full mt-2 border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2.5 rounded-xl transition text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Cetak Struk (Browser)
    </button>
</div>

<div class="receipt-wrapper bg-white w-[300px] shadow-lg overflow-hidden">
    <div class="p-5 font-mono text-xs">

        {{-- Header --}}
        <div class="text-center mb-4 pb-4 border-b-2 border-dashed border-gray-300">
            <img src="/images/logo.png" alt="Kopay" style="width:48px;height:48px;object-fit:cover;border-radius:8px;margin:0 auto 6px;">
            <h1 class="text-base font-bold tracking-wider text-gray-900">KOPAY</h1>
            <p class="text-[10px] text-gray-500 mt-0.5">Terima kasih atas kunjungan Anda</p>
            <span class="inline-block bg-green-100 text-green-800 text-[10px] font-semibold px-2 py-0.5 rounded-full mt-2">PEMBAYARAN BERHASIL</span>
        </div>

        {{-- Order info --}}
        <div class="space-y-1.5 mb-4 pb-3 border-b border-dashed border-gray-300">
            <div class="flex justify-between">
                <span class="text-gray-500">No. Struk</span>
                <span class="font-semibold">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Tanggal</span>
                <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Pelanggan</span>
                <span>{{ $order->customer_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Pembayaran</span>
                <span class="uppercase">{{ $order->payment_method === 'qris' ? 'QRIS' : 'Tunai' }}</span>
            </div>
        </div>

        {{-- Items --}}
        <div class="space-y-2 mb-4 pb-3 border-b border-dashed border-gray-300">
            @foreach($order->items as $item)
            <div>
                <div class="font-semibold">{{ $item->menu->name ?? 'Menu dihapus' }}</div>
                <div class="flex justify-between text-gray-500 pl-2">
                    <span>{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                    <span>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Total --}}
        <div class="flex justify-between font-bold text-sm mb-3">
            <span>TOTAL</span>
            <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
        </div>

        @if($order->notes)
        <div class="mb-3 pb-3 border-b border-dashed border-gray-300">
            <span class="text-gray-500">Catatan: {{ $order->notes }}</span>
        </div>
        @endif

        {{-- Footer --}}
        <div class="text-center text-[10px] text-gray-500 pt-2">
            <p>Pesanan telah dibayar</p>
            <p class="mt-1">— Sampai jumpa lagi! —</p>
        </div>
    </div>
</div>

<script>
    function printViaRawBT() {
        // Generate teks struk
        const lines = [];
        const sep  = '--------------------------------';
        const sep2 = '================================';

        lines.push('\x1B\x61\x01'); // center align
        lines.push('KOPAY\n');
        lines.push('Terima kasih atas kunjungan Anda\n');
        lines.push(sep2 + '\n');

        lines.push('\x1B\x61\x00'); // left align
        lines.push('No. Struk : #{{ str_pad($order->id, 4, "0", STR_PAD_LEFT) }}\n');
        lines.push('Tanggal   : {{ $order->created_at->format("d/m/Y H:i") }}\n');
        lines.push('Pelanggan : {{ $order->customer_name }}\n');
        lines.push('Pembayaran: {{ strtoupper($order->payment_method ?? "TUNAI") }}\n');
        lines.push(sep + '\n');

        @foreach($order->items as $item)
        lines.push('{{ addslashes($item->menu->name ?? "Menu dihapus") }}\n');
        lines.push('  {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ",", ".") }}    Rp {{ number_format($item->price * $item->quantity, 0, ",", ".") }}\n');
        @endforeach

        lines.push(sep + '\n');
        lines.push('\x1B\x61\x02'); // right align
        lines.push('TOTAL: Rp {{ number_format($order->total_price, 0, ",", ".") }}\n');

        @if($order->notes)
        lines.push('\x1B\x61\x00'); // left align
        lines.push(sep + '\n');
        lines.push('Catatan: {{ addslashes($order->notes) }}\n');
        @endif

        lines.push('\x1B\x61\x01'); // center align
        lines.push(sep2 + '\n');
        lines.push('Sampai jumpa lagi!\n');
        lines.push('\n\n\n'); // feed paper

        const text = lines.join('');
        const encoded = btoa(unescape(encodeURIComponent(text)));
        window.location.href = 'rawbt:base64,' + encoded;
    }

    @if(!request()->has('noauto'))
    window.addEventListener('load', function() {
        setTimeout(() => window.print(), 500);
    });
    @endif
</script>
</body>
</html>
