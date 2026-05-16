@if ($paginator->hasPages())
<nav class="flex items-center justify-between">
    <p class="text-xs text-gray-500">
        Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} pesanan
    </p>
    <div class="flex items-center gap-1">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="px-2.5 py-1.5 text-xs text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">‹</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="px-2.5 py-1.5 text-xs text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">‹</a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2.5 py-1.5 text-xs text-gray-400">…</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-2.5 py-1.5 text-xs font-semibold text-white rounded-lg"
                              style="background:#1a3a1a">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                           class="px-2.5 py-1.5 text-xs text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="px-2.5 py-1.5 text-xs text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">›</a>
        @else
            <span class="px-2.5 py-1.5 text-xs text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">›</span>
        @endif

    </div>
</nav>
@endif
