@extends('layouts.admin')

@section('content')
<div class="max-w-xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.menus.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
        <span class="text-gray-300">/</span>
        <h2 class="text-xl font-bold text-gray-900">Edit Menu</h2>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form action="{{ route('admin.menus.update', $menu) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            @include('admin.menus._form')
            <button type="submit"
                    class="w-full bg-primary-800 hover:bg-primary-900 text-white font-semibold py-2.5 rounded-lg transition text-sm">
                Update Menu
            </button>
        </form>
    </div>
</div>
@endsection
