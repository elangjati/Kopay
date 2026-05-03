<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kopay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50:  '#fdf8f3',
                            100: '#f5e6d3',
                            200: '#e8c9a0',
                            300: '#d4a06a',
                            400: '#c07840',
                            500: '#8B4513',
                            600: '#7a3c10',
                            700: '#6b340e',
                            800: '#1a3a1a',
                            900: '#122812',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen">

    <nav class="text-white shadow-lg" style="background: #1a3a1a;" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                {{-- Logo --}}
                <div class="flex items-center gap-2">
                    <img src="/images/logo.png" alt="Kopay" class="w-9 h-9 rounded object-cover">
                    <span class="font-bold text-base tracking-tight">Kopay</span>
                    <span class="text-amber-300 text-xs">Admin</span>
                </div>

                {{-- Desktop nav --}}
                <div class="hidden md:flex items-center gap-1">
                    <a href="{{ route('admin.kasir.create') }}"
                       class="px-3 py-2 rounded text-sm font-medium transition
                              {{ request()->routeIs('admin.kasir*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-800 hover:text-white' }}">
                        + Pesanan Baru
                    </a>
                    <a href="{{ route('admin.orders.today') }}"
                       class="px-3 py-2 rounded text-sm font-medium transition
                              {{ request()->routeIs('admin.orders.today') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-800 hover:text-white' }}">
                        Riwayat Hari Ini
                    </a>
                    <a href="{{ route('admin.menus.index') }}"
                       class="px-3 py-2 rounded text-sm font-medium transition
                              {{ request()->routeIs('admin.menus*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-800 hover:text-white' }}">
                        Menu
                    </a>
                    <a href="{{ route('admin.reports.index') }}"
                       class="px-3 py-2 rounded text-sm font-medium transition
                              {{ request()->routeIs('admin.reports*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-800 hover:text-white' }}">
                        Laporan
                    </a>
                </div>

                <div class="hidden md:flex items-center">
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button class="text-sm text-primary-200 hover:text-white transition flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>

                {{-- Mobile hamburger --}}
                <button @click="open = !open" class="md:hidden p-2 rounded text-primary-200 hover:text-white transition">
                    <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Mobile menu --}}
            <div x-show="open" x-transition class="md:hidden mt-3 pb-2 border-t border-primary-700 pt-3 space-y-1">
                <a href="{{ route('admin.kasir.create') }}"
                   class="block px-3 py-2.5 rounded text-sm font-medium transition
                          {{ request()->routeIs('admin.kasir*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-800 hover:text-white' }}">
                    + Pesanan Baru
                </a>
                <a href="{{ route('admin.orders.today') }}"
                   class="block px-3 py-2.5 rounded text-sm font-medium transition
                          {{ request()->routeIs('admin.orders.today') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-800 hover:text-white' }}">
                    Riwayat Hari Ini
                </a>
                <a href="{{ route('admin.menus.index') }}"
                   class="block px-3 py-2.5 rounded text-sm font-medium transition
                          {{ request()->routeIs('admin.menus*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-800 hover:text-white' }}">
                    Menu
                </a>
                <a href="{{ route('admin.reports.index') }}"
                   class="block px-3 py-2.5 rounded text-sm font-medium transition
                          {{ request()->routeIs('admin.reports*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-800 hover:text-white' }}">
                    Laporan
                </a>
                <form action="{{ route('admin.logout') }}" method="POST" class="pt-1">
                    @csrf
                    <button class="w-full text-left px-3 py-2.5 rounded text-sm text-primary-200 hover:text-white hover:bg-primary-800 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-6">
        @if(session('success'))
            <div class="bg-primary-50 border border-primary-200 text-primary-800 px-4 py-3 rounded-lg mb-5 flex items-center gap-2 text-sm">
                <svg class="w-5 h-5 text-primary-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @yield('content')
    </main>

    <script src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js"></script>
</body>
</html>
