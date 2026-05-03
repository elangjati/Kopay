@extends('layouts.admin')

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

<style>
    [x-cloak] { display: none !important; }
</style>

<div class="max-w-3xl" x-data="orderForm({{ $initialItems }}, {{ $menusJson }})" x-cloak>

    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('admin.dashboard') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
        <span class="text-gray-300">/</span>
        <h2 class="text-xl font-bold text-gray-900">
            @if($isCreate) Pesanan Baru
            @else Edit Pesanan <span class="text-primary-700">#{{ $order->id }}</span>
            @endif
        </h2>
    </div>

    {{-- Mobile tab switcher --}}
    <div class="flex lg:hidden bg-white border border-gray-200 rounded-xl p-1 mb-4 gap-1">
        <button type="button" @click="tab = 'menu'"
                class="flex-1 py-2.5 rounded-lg text-sm font-semibold transition"
                :style="tab === 'menu' ? 'background:#1a3a1a;color:white' : 'color:#4b5563'">
            Pilih Menu
        </button>
        <button type="button" @click="tab = 'order'"
                class="flex-1 py-2.5 rounded-lg text-sm font-semibold transition relative"
                :style="tab === 'order' ? 'background:#1a3a1a;color:white' : 'color:#4b5563'">
            Pesanan
            <span x-show="cart.length > 0"
                  class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold"
                  x-text="cart.length"></span>
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

        {{-- Kiri: Pilih Menu --}}
        <div class="lg:col-span-3 space-y-4"
             x-show="isDesktop || tab === 'menu'"
             x-transition>

            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="search" placeholder="Cari menu..."
                       class="w-full border border-gray-300 rounded-lg pl-9 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-transparent bg-white">
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
                            class="text-left bg-white border border-gray-200 hover:border-primary-400 active:bg-primary-50 rounded-xl p-3 transition">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ $menu->name }}</p>
                                @if($menu->description)
                                    <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $menu->description }}</p>
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
                <div class="text-center py-12 text-gray-400 bg-white rounded-xl border border-gray-200">
                    <p class="text-3xl mb-2"></p>
                    <p class="text-sm">Belum ada menu tersedia.</p>
                </div>
            @endif
        </div>

        {{-- Kanan: Ringkasan & Form --}}
        <div class="lg:col-span-2"
             x-show="isDesktop || tab === 'order'"
             x-transition>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 lg:sticky lg:top-6">
                <h3 class="text-sm font-bold text-gray-800 mb-4 pb-3 border-b border-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Ringkasan Pesanan
                </h3>

                <div x-show="cart.length === 0" class="text-center py-6 text-gray-400">
                    <p class="text-2xl mb-1">🛒</p>
                    <p class="text-xs">Pilih menu di tab sebelumnya</p>
                </div>

                <div x-show="cart.length > 0">
                    <ul class="space-y-1.5 mb-3 max-h-48 overflow-y-auto pr-1">
                        <template x-for="(item, index) in cart" :key="item.id">
                            <li class="flex items-center gap-2 py-2 border-b border-gray-100 last:border-0">
                                <span class="text-gray-700 text-xs flex-1 leading-tight" x-text="item.name"></span>
                                <div class="flex items-center gap-1">
                                    <button type="button" @click="decrease(index)"
                                            class="w-7 h-7 bg-gray-100 hover:bg-gray-200 rounded text-gray-600 text-sm font-bold transition">−</button>
                                    <span x-text="item.qty" class="w-6 text-center text-sm font-semibold text-gray-800"></span>
                                    <button type="button" @click="item.qty++"
                                            class="w-7 h-7 bg-gray-100 hover:bg-gray-200 rounded text-gray-600 text-sm font-bold transition">+</button>
                                </div>
                                <span class="text-primary-700 font-semibold text-xs w-16 text-right"
                                      x-text="'Rp ' + fmt(item.price * item.qty)"></span>
                                <button type="button" @click="cart.splice(index, 1)"
                                        class="text-gray-300 hover:text-red-400 transition ml-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </li>
                        </template>
                    </ul>

                    <div class="bg-primary-50 rounded-lg px-3 py-2.5 mb-4 flex justify-between font-bold text-sm">
                        <span class="text-gray-700">Total</span>
                        <span class="text-primary-700" x-text="'Rp ' + fmt(total())"></span>
                    </div>
                </div>

                <form id="order-form" action="{{ $formAction }}" method="POST" @submit.prevent="submitForm">
                    @csrf
                    @if(!$isCreate) @method('PUT') @endif

                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg mb-3 text-xs">
                            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                        </div>
                    @endif

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Nama Pelanggan *</label>
                            <input type="text" name="customer_name" required
                                   value="{{ old('customer_name', $order->customer_name ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-transparent transition"
                                   placeholder="Nama pelanggan">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Catatan</label>
                            <input type="text" name="notes"
                                   value="{{ old('notes', $order->notes ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-transparent transition"
                                   placeholder="Less sugar, no ice... (opsional)">
                        </div>

                        @if($isCreate)
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1.5">Metode Pembayaran *</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="payment_method" value="tunai"
                                           class="peer sr-only" {{ old('payment_method', 'tunai') === 'tunai' ? 'checked' : '' }}>
                                    <div class="border-2 border-gray-200 peer-checked:border-primary-600 peer-checked:bg-primary-50 rounded-lg p-3 text-center transition">
                                        <span class="text-lg"></span>
                                        <p class="text-xs font-semibold text-gray-700 mt-1">Tunai</p>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="payment_method" value="qris"
                                           class="peer sr-only" {{ old('payment_method') === 'qris' ? 'checked' : '' }}>
                                    <div class="border-2 border-gray-200 peer-checked:border-primary-600 peer-checked:bg-primary-50 rounded-lg p-3 text-center transition">
                                        <span class="text-lg"></span>
                                        <p class="text-xs font-semibold text-gray-700 mt-1">QRIS</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                        @else
                        @if($order->payment_method)
                        <div class="bg-gray-50 rounded-lg px-3 py-2.5 flex items-center justify-between text-xs">
                            <span class="text-gray-500 font-medium">Metode Bayar</span>
                            <span class="font-semibold text-gray-700">
                                {{ $order->payment_method === 'qris' ? '📱 QRIS' : '💵 Tunai' }}
                            </span>
                        </div>
                        @endif
                        @endif

                        <div id="order-items-input"></div>

                        <button type="submit"
                                :disabled="cart.length === 0"
                                class="w-full text-white font-semibold py-3 rounded-lg transition text-sm flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed"
                                style="background:#1a3a1a">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $isCreate ? 'Buat Pesanan' : 'Simpan Perubahan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Fallback sebelum Alpine load --}}
<noscript>
    <style>.max-w-3xl { display: block !important; }</style>
</noscript>

<script>
function orderForm(initialItems, menus) {
    return {
        menus,
        search: '',
        tab: 'menu',
        isDesktop: window.innerWidth >= 1024,
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
@endsection
