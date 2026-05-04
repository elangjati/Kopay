@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm space-y-1">
        @foreach($errors->all() as $error)<p class="flex items-center gap-2"><svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>{{ $error }}</p>@endforeach
    </div>
@endif

{{-- Image Upload --}}
<div>
    <label class="block text-xs font-medium text-gray-700 mb-2">Foto Menu</label>
    <div class="flex items-start gap-4">
        <div class="w-24 h-24 rounded-xl border-2 border-dashed border-gray-300 overflow-hidden flex items-center justify-center bg-gray-50 transition hover:border-primary-400"
             :class="imagePreview ? 'border-solid border-primary-400' : ''">
            <template x-if="imagePreview">
                <img :src="imagePreview" class="w-full h-full object-cover">
            </template>
            <template x-if="!imagePreview">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </template>
        </div>
        <div class="flex-1">
            <label class="cursor-pointer inline-flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Upload Foto
                <input type="file" name="image" accept="image/*" class="hidden" @change="handleImageUpload">
            </label>
            <p class="text-xs text-gray-400 mt-1.5">JPG, PNG atau WebP. Maks 2MB</p>
        </div>
    </div>
</div>

<div>
    <label class="block text-xs font-medium text-gray-700 mb-1.5">Nama Menu <span class="text-red-500">*</span></label>
    <input type="text" name="name" value="{{ old('name', $menu->name ?? '') }}" required
           class="w-full border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
           placeholder="Contoh: Cappuccino">
</div>

<div>
    <label class="block text-xs font-medium text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
    <select name="category" required
            class="w-full border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent bg-white transition">
        @foreach(['kopi' => 'Kopi', 'non-kopi' => 'Non-Kopi', 'makanan' => 'Makanan', 'minuman' => 'Minuman', 'lainnya' => 'Lainnya'] as $value => $label)
            <option value="{{ $value }}" {{ old('category', $menu->category ?? '') === $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
</div>

<div>
    <label class="block text-xs font-medium text-gray-700 mb-1.5">Harga <span class="text-red-500">*</span></label>
    <div class="relative">
        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-gray-500">Rp</span>
        <input type="number" name="price" value="{{ old('price', $menu->price ?? '') }}" required min="0"
               class="w-full border border-gray-300 rounded-xl pl-9 pr-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
               placeholder="25000">
    </div>
</div>

<div>
    <label class="block text-xs font-medium text-gray-700 mb-1.5">Deskripsi</label>
    <textarea name="description" rows="2"
              class="w-full border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition resize-none"
              placeholder="Deskripsi singkat menu (opsional)">{{ old('description', $menu->description ?? '') }}</textarea>
</div>

<div class="flex items-center justify-between bg-gray-50 rounded-xl p-4">
    <div>
        <p class="text-sm font-medium text-gray-700">Tersedia untuk dipesan</p>
        <p class="text-xs text-gray-400 mt-0.5">Menu akan muncul di daftar pesanan</p>
    </div>
    <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" name="is_available" value="1" class="sr-only peer"
               {{ old('is_available', $menu->is_available ?? true) ? 'checked' : '' }}>
        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
    </label>
</div>
