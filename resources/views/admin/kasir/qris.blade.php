@extends('layouts.admin')

@section('title', 'Pembayaran QRIS - Kopay')

@section('content')
<div class="max-w-lg mx-auto" x-data="qrisPage()">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.kasir.create') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1.5 transition bg-white px-3 py-2 rounded-lg border border-gray-200 hover:border-gray-300 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
        <h2 class="text-xl font-bold text-gray-900">Pembayaran QRIS</h2>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="bg-primary-800 px-6 py-5 text-center">
            <p class="text-primary-200 text-xs uppercase tracking-widest mb-1">Pesanan #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</p>
            <h3 class="text-white font-bold text-lg">{{ $order->customer_name }}</h3>
        </div>

        <div class="p-6">

            {{-- Item summary --}}
            <div class="bg-gray-50 rounded-xl p-4 mb-5">
                <ul class="space-y-1.5 mb-3">
                    @foreach($order->items as $item)
                    <li class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ $item->menu->name ?? 'Menu dihapus' }}
                            <span class="text-gray-400">x{{ $item->quantity }}</span>
                        </span>
                        <span class="font-medium text-gray-800">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                    </li>
                    @endforeach
                </ul>
                <div class="border-t border-gray-200 pt-2.5 flex justify-between font-bold text-sm">
                    <span class="text-gray-700">Total</span>
                    <span class="text-primary-700 text-base">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- QR Code --}}
            <div class="text-center mb-5">
                @if($order->qris_url)
                    <div class="inline-block p-3 bg-white border-2 border-gray-200 rounded-2xl shadow-sm">
                        <img src="{{ $order->qris_url }}" alt="QRIS" class="w-56 h-56 object-contain">
                    </div>
                    <p class="text-xs text-gray-400 mt-3">Scan QR di atas menggunakan aplikasi e-wallet / m-banking</p>
                @else
                    <div class="inline-flex flex-col items-center justify-center w-56 h-56 bg-gray-100 rounded-2xl border-2 border-dashed border-gray-300">
                        <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        <p class="text-xs text-gray-400 text-center px-4">QR tidak tersedia<br>(Midtrans belum dikonfigurasi)</p>
                    </div>
                @endif
            </div>

            {{-- Status indicator --}}
            <div class="flex items-center justify-center gap-2 mb-5" x-show="!paid" x-cloak>
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-yellow-500"></span>
                </span>
                <span class="text-sm text-gray-500">Menunggu pembayaran...</span>
            </div>

            <div x-show="paid" x-cloak class="flex items-center justify-center gap-2 mb-5 text-green-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-sm font-semibold">Pembayaran diterima!</span>
            </div>

            {{-- Actions --}}
            <div class="grid grid-cols-2 gap-3">
                <form action="{{ route('admin.orders.complete', $order) }}" method="POST">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Konfirmasi pesanan #{{ $order->id }} sudah dibayar?')"
                            class="w-full bg-primary-800 hover:bg-primary-900 text-white text-sm font-semibold py-2.5 rounded-xl transition shadow-sm">
                        Konfirmasi Bayar
                    </button>
                </form>

                <a href="{{ route('admin.kasir.create') }}"
                   class="w-full border border-gray-300 hover:bg-gray-50 text-gray-600 text-sm font-medium py-2.5 rounded-xl transition text-center">
                    Pesanan Baru
                </a>
            </div>

            @if($order->qris_url)
            <p class="text-center text-xs text-gray-400 mt-4">
                Halaman ini otomatis mengecek status pembayaran setiap 5 detik
            </p>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function qrisPage() {
    return {
        paid: false,
        init() {
            @if($order->qris_url && $order->status !== 'completed')
            this.poll();
            @endif
        },
        poll() {
            const interval = setInterval(async () => {
                try {
                    const res  = await fetch('{{ route('admin.kasir.checkPayment', $order) }}');
                    const data = await res.json();
                    if (data.paid) {
                        this.paid = true;
                        clearInterval(interval);
                        setTimeout(() => {
                            window.location.href = '{{ route('admin.orders.receipt', $order) }}';
                        }, 1500);
                    }
                } catch (e) { /* silent */ }
            }, 5000);
        }
    }
}
</script>
@endpush
@endsection
