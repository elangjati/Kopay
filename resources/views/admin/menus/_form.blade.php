@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
        @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
    </div>
@endif

<div>
    <label class="block text-xs font-medium text-gray-700 mb-1.5">Nama Menu *</label>
    <input type="text" name="name" value="{{ old('name', $menu->name ?? '') }}" required
           class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-transparent transition"
           placeholder="Contoh: Cappuccino">
</div>

<div>
    <label class="block text-xs font-medium text-gray-700 mb-1.5">Kategori *</label>
    <select name="category" required
            class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-transparent bg-white transition">
        @foreach(['kopi', 'non-kopi', 'makanan', 'minuman', 'lainnya'] as $cat)
            <option value="{{ $cat }}" {{ old('category', $menu->category ?? '') === $cat ? 'selected' : '' }}>
                {{ ucfirst($cat) }}
            </option>
        @endforeach
    </select>
</div>

<div>
    <label class="block text-xs font-medium text-gray-700 mb-1.5">Harga *</label>
    <div class="relative">
        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-gray-500">Rp</span>
        <input type="number" name="price" value="{{ old('price', $menu->price ?? '') }}" required min="0"
               class="w-full border border-gray-300 rounded-lg pl-9 pr-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-transparent transition"
               placeholder="25000">
    </div>
</div>

<div>
    <label class="block text-xs font-medium text-gray-700 mb-1.5">Deskripsi</label>
    <textarea name="description" rows="2"
              class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-600 focus:border-transparent transition resize-none"
              placeholder="Deskripsi singkat menu (opsional)">{{ old('description', $menu->description ?? '') }}</textarea>
</div>

<div class="flex items-center gap-3">
    <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" name="is_available" value="1" class="sr-only peer"
               {{ old('is_available', $menu->is_available ?? true) ? 'checked' : '' }}>
        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary-700"></div>
    </label>
    <span class="text-sm text-gray-700">Tersedia untuk dipesan</span>
</div>
