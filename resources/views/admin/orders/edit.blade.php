@extends('layouts.admin')

@section('title', 'Pesanan Baru - Kopay')

@section('content')
@php
    $isCreate = !isset($order);
    $formAction = $isCreate
        ? route('admin.kasir.store')
        : route('admin.orders.update', $order);
    $initialItems = $isCreate
        ? '[]'
        : json_encode($order->items->map(fn($i) => [
            'menu_id'  => $i->menu_id,
            'name'     => $i->menu->name ?? 'Menu dihapus',
            'price'    => (float) $i->price,
            'quantity' => $i->quantity,
          ]));
    $menusJson = json_encode($menus->map(fn($m) => [
        'id'       => $m->id,
        'name'     => $m->name,
        'price'    => (float) $m->price,
        'category' => $m->category,
    ]));
@endphp

<div class="max-w-4xl" x-data="orderForm({{ $initialItems }}, {{ $menusJson }})">

    <div class="flex items-center gap-3 mb-5">
        <a href="{{ route('admin.kasir.create') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1.5 transition bg-white px-3 py-2 rounded-lg border border-gray-200 hover:border-gray-300 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
        <h2 class="text-xl font-bold text-gray-900">
            @if($isCreate) Pesanan Baru
            @else Edit Pesanan <span class="text-primary-700">#{{ $order->id }}</span>
            @endif
        </h2>
    </div>

    {{-- Mobile tab switcher --}}
    <div class="flex lg:hidden bg-white border border-gray-200 rounded-xl p-1 mb-4 gap-1 shadow-sm">
        <button type="button" @click="tab = 'menu'"
                class="flex-1 py-2.5 rounded-lg text-sm font-semibold transition"
                :class="tab === 'menu' ? 'bg-primary-800 text-white' : 'text-gray-600'">
            Pilih Menu
        </button>
        <button type="button" @click="tab = 'order'"
                class="flex-1 py-2.5 rounded-lg text-sm font-semibold transition relative"
                :class="tab === 'order' ? 'bg-primary-800 text-white' : 'text-gray-600'">
            Pesanan
            <span x-show="cart.length > 0" x-cloak
                  class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[10px] w-5 h-5 rounded-full flex items-center justify-center font-bold shadow"
                  x-text="cart.length"></span>
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

        {{-- Kiri: Pilih Menu --}}
        <div class="lg:col-span-3 space-y-4"
             x-show="isDesktop || tab === 'menu'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-x-[-10px]"
             x-transition:enter-end="opacity-100 translate-x-0">

            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="search" placeholder="Cari menu..."
                       class="w-full border border-gray-200 rounded-xl pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white shadow-sm">
            </div>

            @foreach($menusByCategory as $category => $items)
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h3 class="text-xs font-bold text-primary-700 uppercase tracking-widest">{{ $category }}</h3>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($items as $menu)
                    <button type="button"
                            @click="addItem({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }})"
                            x-show="search === '' || '{{ strtolower($menu->name) }}'.includes(search.toLowerCase())"
                            class="text-left bg-white border border-gray-200 hover:border-primary-400 hover:shadow active:bg-primary-50 rounded-xl p-3 transition">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $menu->name }}</p>
                                @if($menu->description)
                                    <p class="text-xs text-gray-400 mt-0.5 truncate">{{ Str::limit($menu->description, 30) }}</p>
                                @endif
                            </div>
                            <span class="text-xs font-bold text-primary-700 whitespace-nowrap">
                                Rp {{ number_format($menu->price, 0, ',', '.') }}
                            </span>
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>
            @endforeach

            @if($menusByCategory->isEmpty())
                <div class="text-center py-16 text-gray-400 bg-white rounded-xl border border-gray-200">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-sm font-medium text-gray-500">Belum ada menu tersedia</p>
                    <p class="text-xs mt-1"><a href="{{ route('admin.menus.create') }}" class="text-primary-600 hover:underline">Tambah menu</a> terlebih dahulu</p>
                </div>
            @endif
        </div>

        {{-- Kanan: Ringkasan & Form --}}
        <div class="lg:col-span-2"
             x-show="isDesktop || tab === 'order'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-x-[10px]"
             x-transition:enter-end="opacity-100 translate-x-0">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 lg:sticky lg:top-6">
                <h3 class="text-sm font-bold text-gray-800 mb-4 pb-3 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Ringkasan Pesanan
                </h3>

                <div x-show="cart.length === 0" x-cloak class="text-center py-8 text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-xs">Pilih menu untuk memulai</p>
                </div>

                <div x-show="cart.length > 0" x-cloak>
                    <ul class="space-y-1.5 mb-3 max-h-56 overflow-y-auto pr-1">
                        <template x-for="(item, index) in cart" :key="item.id">
                            <li class="flex items-center gap-2 py-2 border-b border-gray-100 last:border-0">
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-gray-700 truncate" x-text="item.name"></p>
                                    <p class="text-[10px] text-gray-400" x-text="'Rp ' + fmt(item.price) + ' each'"></p>
                                </div>
                                <div class="flex items-center gap-0.5">
                                    <button type="button" @click="decrease(index)"
                                            class="w-6 h-6 bg-gray-100 hover:bg-gray-200 rounded text-gray-600 text-sm font-bold transition">−</button>
                                    <span x-text="item.qty" class="w-6 text-center text-xs font-semibold text-gray-800"></span>
                                    <button type="button" @click="item.qty++"
                                            class="w-6 h-6 bg-gray-100 hover:bg-gray-200 rounded text-gray-600 text-sm font-bold transition">+</button>
                                </div>
                                <span class="text-primary-700 font-semibold text-xs w-16 text-right"
                                      x-text="'Rp ' + fmt(item.price * item.qty)"></span>
                                <button type="button" @click="cart.splice(index, 1)"
                                        class="text-gray-300 hover:text-red-400 transition ml-0.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </li>
                        </template>
                    </ul>

                    <div class="bg-primary-50 rounded-xl px-4 py-3 mb-4 flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Total</span>
                        <span class="text-lg font-bold text-primary-700" x-text="'Rp ' + fmt(total())"></span>
                    </div>
                </div>

                <form id="order-form" action="{{ $formAction }}" method="POST" @submit.prevent="submitForm">
                    @csrf
                    @if(!$isCreate) @method('PUT') @endif

                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2.5 rounded-xl mb-3 text-xs">
                            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                        </div>
                    @endif

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Nama Pelanggan <span class="text-red-500">*</span></label>
                            <input type="text" name="customer_name" required
                                   value="{{ old('customer_name', $order->customer_name ?? '') }}"
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                                   placeholder="Nama pelanggan">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Catatan</label>
                            <input type="text" name="notes"
                                   value="{{ old('notes', $order->notes ?? '') }}"
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                                   placeholder="Less sugar, no ice... (opsional)">
                        </div>

                        @if($isCreate)
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Metode Pembayaran <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="payment_method" value="tunai"
                                           class="peer sr-only" {{ old('payment_method', 'tunai') === 'tunai' ? 'checked' : '' }}>
                                    <div class="border-2 border-gray-200 peer-checked:border-primary-600 peer-checked:bg-primary-50 rounded-xl p-3 text-center transition hover:border-gray-300">
                                        <svg class="w-5 h-5 mx-auto text-green-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <p class="text-xs font-semibold text-gray-700">Tunai</p>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="payment_method" value="qris"
                                           class="peer sr-only" {{ old('payment_method') === 'qris' ? 'checked' : '' }}>
                                    <div class="border-2 border-gray-200 peer-checked:border-primary-600 peer-checked:bg-primary-50 rounded-xl p-3 text-center transition hover:border-gray-300">
                                        <svg class="w-5 h-5 mx-auto text-blue-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                        </svg>
                                        <p class="text-xs font-semibold text-gray-700">QRIS</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                        @else
                        @if($order->payment_method)
                        <div class="bg-gray-50 rounded-xl px-4 py-3 flex items-center justify-between text-xs">
                            <span class="text-gray-500 font-medium">Metode Bayar</span>
                            <span class="font-semibold text-gray-700">
                                {{ $order->payment_method === 'qris' ? 'QRIS' : 'Tunai' }}
                            </span>
                        </div>
                        @endif
                        @endif

                        <div id="order-items-input"></div>

                        <button type="submit"
                                :disabled="cart.length === 0 || submitting"
                                @click="submitting = true"
                                class="w-full text-white font-semibold py-3 rounded-xl transition text-sm flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed shadow-sm hover:shadow disabled:shadow-none"
                                style="background:#1a3a1a">
                            <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            <span x-text="submitting ? 'Memproses...' : '{{ $isCreate ? 'Buat Pesanan' : 'Simpan Perubahan' }}'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function orderForm(initialItems, menus) {
    return {
        menus,
        search: '',
        tab: 'menu',
        isDesktop: window.innerWidth >= 1024,
        submitting: false,
        init() {
            const update = () => { this.isDesktop = window.innerWidth >= 1024; };
            window.addEventListener('resize', update);
        },
        cart: initialItems.map(i => ({
            id:    Number(i.menu_id),
            name:  i.name,
            price: i.price,
            qty:   i.quantity,
        })),
        addItem(id, name, price) {
            const existing = this.cart.find(i => i.id === id);
            if (existing) { existing.qty++; }
            else { this.cart.push({ id, name, price, qty: 1 }); }
        },
        decrease(index) {
            if (this.cart[index].qty > 1) { this.cart[index].qty--; }
            else { this.cart.splice(index, 1); }
        },
        total() { return this.cart.reduce((sum, i) => sum + i.price * i.qty, 0); },
        fmt(val) { return val.toLocaleString('id-ID'); },
        submitForm() {
            if (this.cart.length === 0) return;
            const container = document.getElementById('order-items-input');
            container.innerHTML = '';
            this.cart.forEach((item, index) => {
                container.innerHTML += `<input type="hidden" name="items[${index}][menu_id]" value="${item.id}">`;
                container.innerHTML += `<input type="hidden" name="items[${index}][quantity]" value="${item.qty}">`;
            });
            document.getElementById('order-form').submit();
        }
    }
}
</script>
@endpush
@endsection
