@extends('layouts.admin')

@section('title', 'Kelola Menu - Kopay')

@section('content')
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Kelola Menu</h2>
        <p class="text-sm text-gray-500 mt-0.5">Tambah, edit, atau hapus item menu</p>
    </div>
    <a href="{{ route('admin.menus.create') }}"
       class="bg-primary-800 hover:bg-primary-900 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm hover:shadow">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Menu
    </a>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Menu</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $menus->count() }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Tersedia</p>
        <p class="text-2xl font-bold text-green-600 mt-1">{{ $menus->where('is_available', true)->count() }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Habis</p>
        <p class="text-2xl font-bold text-red-500 mt-1">{{ $menus->where('is_available', false)->count() }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Kategori</p>
        <p class="text-2xl font-bold text-primary-700 mt-1">{{ $menus->pluck('category')->unique()->count() }}</p>
    </div>
</div>

{{-- Search & Filter --}}
<div x-data="{ search: '' }" class="mb-5">
    <div class="relative">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" x-model="search" placeholder="Cari menu berdasarkan nama atau kategori..."
               class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white shadow-sm">
    </div>
</div>

{{-- Desktop: table --}}
<div x-data="{ search: '' }" class="hidden md:block bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Menu</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Kategori</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Harga</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($menus as $menu)
            <tr class="hover:bg-gray-50/80 transition"
                :class="search && !('{{ strtolower($menu->name . ' ' . $menu->category) }}'.includes(search.toLowerCase())) ? 'hidden' : ''">
                <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                        @if($menu->image)
                        <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-10 h-10 rounded-lg object-cover bg-gray-100">
                        @else
                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        @endif
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-900 truncate">{{ $menu->name }}</p>
                            @if($menu->description)
                                <p class="text-gray-400 text-xs mt-0.5 truncate max-w-[200px]">{{ Str::limit($menu->description, 40) }}</p>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-5 py-4">
                    <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-lg font-medium capitalize">{{ $menu->category }}</span>
                </td>
                <td class="px-5 py-4 font-semibold text-primary-700">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                <td class="px-5 py-4">
                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold border inline-flex items-center gap-1
                        {{ $menu->is_available ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-100 text-gray-500 border-gray-200' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $menu->is_available ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                        {{ $menu->is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                    </span>
                </td>
                <td class="px-5 py-4">
                    <div class="flex gap-2 justify-end">
                        <a href="{{ route('admin.menus.edit', $menu) }}"
                           class="text-xs border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-3 py-1.5 rounded-lg transition inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                        <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST"
                              onsubmit="return confirm('Hapus menu ini?')">
                            @csrf @method('DELETE')
                            <button class="text-xs border border-red-200 hover:bg-red-50 text-red-600 font-medium px-3 py-1.5 rounded-lg transition inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-16">
                    <div class="text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm font-medium text-gray-500">Belum ada menu</p>
                        <p class="text-xs mt-1">Klik "Tambah Menu" untuk memulai</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Mobile: card list --}}
<div x-data="{ search: '' }" class="md:hidden space-y-3">
    <div class="relative mb-3">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text" x-model="search" placeholder="Cari menu..."
               class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white shadow-sm">
    </div>
    @forelse($menus as $menu)
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm"
         :class="search && !('{{ strtolower($menu->name . ' ' . $menu->category) }}'.includes(search.toLowerCase())) ? 'hidden' : ''">
        <div class="flex items-start gap-3 mb-3">
            @if($menu->image)
            <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-14 h-14 rounded-xl object-cover bg-gray-100 flex-shrink-0">
            @endif
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-900">{{ $menu->name }}</p>
                @if($menu->description)
                    <p class="text-gray-400 text-xs mt-0.5 line-clamp-2">{{ $menu->description }}</p>
                @endif
            </div>
        </div>
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg font-medium capitalize">{{ $menu->category }}</span>
                <span class="text-xs px-2 py-1 rounded-full font-semibold border inline-flex items-center gap-1
                    {{ $menu->is_available ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-100 text-gray-500 border-gray-200' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $menu->is_available ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                    {{ $menu->is_available ? 'Tersedia' : 'Habis' }}
                </span>
            </div>
            <span class="font-bold text-primary-700 text-sm">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
        </div>
        <div class="flex gap-2 pt-3 border-t border-gray-100">
            <a href="{{ route('admin.menus.edit', $menu) }}"
               class="flex-1 text-center text-xs border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-3 py-2 rounded-lg transition inline-flex items-center justify-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST"
                  onsubmit="return confirm('Hapus menu ini?')" class="flex-1">
                @csrf @method('DELETE')
                <button class="w-full text-xs border border-red-200 hover:bg-red-50 text-red-600 font-medium px-3 py-2 rounded-lg transition inline-flex items-center justify-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p class="text-sm font-medium text-gray-500">Belum ada menu</p>
        <p class="text-xs text-gray-400 mt-1">Klik "Tambah Menu" untuk memulai</p>
    </div>
    @endforelse
</div>
@endsection
