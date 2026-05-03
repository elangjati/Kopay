@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Kelola Menu</h2>
        <p class="text-sm text-gray-500 mt-0.5">Tambah, edit, atau hapus item menu</p>
    </div>
    <a href="{{ route('admin.menus.create') }}"
       class="bg-primary-800 hover:bg-primary-900 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        <span class="hidden sm:inline">Tambah Menu</span>
        <span class="sm:hidden">Tambah</span>
    </a>
</div>

{{-- Desktop: tabel --}}
<div class="hidden md:block bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Menu</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Kategori</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Harga</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($menus as $menu)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-4">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $menu->name }}</p>
                        @if($menu->description)
                            <p class="text-gray-400 text-xs mt-0.5">{{ Str::limit($menu->description, 40) }}</p>
                        @endif
                    </div>
                </td>
                <td class="px-5 py-4">
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded font-medium capitalize">{{ $menu->category }}</span>
                </td>
                <td class="px-5 py-4 font-semibold text-primary-700">Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                <td class="px-5 py-4">
                    <span class="text-xs px-2 py-1 rounded-full font-semibold border
                        {{ $menu->is_available ? 'bg-primary-50 text-primary-700 border-primary-200' : 'bg-gray-100 text-gray-500 border-gray-200' }}">
                        {{ $menu->is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                    </span>
                </td>
                <td class="px-5 py-4">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.menus.edit', $menu) }}"
                           class="text-xs border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-3 py-1.5 rounded-lg transition">Edit</a>
                        <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST"
                              onsubmit="return confirm('Hapus menu ini?')">
                            @csrf @method('DELETE')
                            <button class="text-xs border border-red-200 hover:bg-red-50 text-red-600 font-medium px-3 py-1.5 rounded-lg transition">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-gray-400 py-12 text-sm">Belum ada menu.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Mobile: card list --}}
<div class="md:hidden space-y-3">
    @forelse($menus as $menu)
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center gap-3 mb-3">
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-900 truncate">{{ $menu->name }}</p>
                @if($menu->description)
                    <p class="text-gray-400 text-xs mt-0.5 truncate">{{ $menu->description }}</p>
                @endif
            </div>
        </div>
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded font-medium capitalize">{{ $menu->category }}</span>
                <span class="text-xs px-2 py-1 rounded-full font-semibold border
                    {{ $menu->is_available ? 'bg-primary-50 text-primary-700 border-primary-200' : 'bg-gray-100 text-gray-500 border-gray-200' }}">
                    {{ $menu->is_available ? 'Tersedia' : 'Habis' }}
                </span>
            </div>
            <span class="font-bold text-primary-700 text-sm">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
        </div>
        <div class="flex gap-2 pt-2 border-t border-gray-100">
            <a href="{{ route('admin.menus.edit', $menu) }}"
               class="flex-1 text-center text-xs border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium px-3 py-2 rounded-lg transition">Edit</a>
            <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST"
                  onsubmit="return confirm('Hapus menu ini?')" class="flex-1">
                @csrf @method('DELETE')
                <button class="w-full text-xs border border-red-200 hover:bg-red-50 text-red-600 font-medium px-3 py-2 rounded-lg transition">Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div class="text-center text-gray-400 py-12 bg-white rounded-xl border border-gray-200">
        <p class="text-sm">Belum ada menu.</p>
    </div>
    @endforelse
</div>
@endsection
