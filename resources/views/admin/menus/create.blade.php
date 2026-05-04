@extends('layouts.admin')

@section('title', 'Tambah Menu - Kopay')

@section('content')
<div class="max-w-xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.menus.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1.5 transition bg-white px-3 py-2 rounded-lg border border-gray-200 hover:border-gray-300 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
        <h2 class="text-xl font-bold text-gray-900">Tambah Menu</h2>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4" x-data="menuForm()">
            @csrf
            @include('admin.menus._form')
            <button type="submit"
                    class="w-full bg-primary-800 hover:bg-primary-900 text-white font-semibold py-2.5 rounded-xl transition text-sm flex items-center justify-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Menu
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function menuForm() {
    return {
        imagePreview: null,
        handleImageUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => { this.imagePreview = e.target.result; };
                reader.readAsDataURL(file);
            }
        }
    }
}
</script>
@endpush
