<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Kopay')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- Toast Container --}}
    <div id="toast-container" class="toast-container"></div>

    <nav class="bg-primary-800 text-white shadow-lg">
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
                    <a href="{{ route('admin.dashboard') }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium transition flex items-center gap-1.5
                              {{ request()->routeIs('admin.dashboard') ? 'bg-primary-700 text-white shadow-sm' : 'text-primary-200 hover:bg-primary-700/50 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.kasir.create') }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium transition flex items-center gap-1.5
                              {{ request()->routeIs('admin.kasir*') || request()->routeIs('admin.orders*') ? 'bg-primary-700 text-white shadow-sm' : 'text-primary-200 hover:bg-primary-700/50 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Pesanan
                    </a>
                    <a href="{{ route('admin.menus.index') }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium transition flex items-center gap-1.5
                              {{ request()->routeIs('admin.menus*') ? 'bg-primary-700 text-white shadow-sm' : 'text-primary-200 hover:bg-primary-700/50 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Menu
                    </a>
                    @if(auth()->user()?->role === 'owner')
                    <a href="{{ route('admin.reports.index') }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium transition flex items-center gap-1.5
                              {{ request()->routeIs('admin.reports*') ? 'bg-primary-700 text-white shadow-sm' : 'text-primary-200 hover:bg-primary-700/50 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Laporan
                    </a>
                    @endif
                </div>

                <div class="hidden md:flex items-center">
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button class="text-sm text-primary-200 hover:text-white transition flex items-center gap-1.5 px-3 py-2 rounded-lg hover:bg-primary-700/50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>

                {{-- Mobile hamburger --}}
                <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg text-primary-200 hover:text-white hover:bg-primary-700/50 transition">
                    <svg x-show="!mobileMenu" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileMenu" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Mobile menu --}}
            <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 x-cloak class="md:hidden mt-3 pb-2 border-t border-primary-700 pt-3 space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                   class="block px-3 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2
                          {{ request()->routeIs('admin.dashboard') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700/50 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.kasir.create') }}"
                   class="block px-3 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2
                          {{ request()->routeIs('admin.kasir*') || request()->routeIs('admin.orders*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700/50 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Pesanan
                </a>
                <a href="{{ route('admin.menus.index') }}"
                   class="block px-3 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2
                          {{ request()->routeIs('admin.menus*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700/50 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Menu
                </a>
                @if(auth()->user()?->role === 'owner')
                <a href="{{ route('admin.reports.index') }}"
                   class="block px-3 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2
                          {{ request()->routeIs('admin.reports*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700/50 hover:text-white' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Laporan
                </a>
                @endif
                <form action="{{ route('admin.logout') }}" method="POST" class="pt-1">
                    @csrf
                    <button class="w-full text-left px-3 py-2.5 rounded-lg text-sm text-primary-200 hover:text-white hover:bg-primary-700/50 transition flex items-center gap-2">
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
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl mb-5 flex items-center gap-2.5 text-sm shadow-sm">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="ml-auto text-green-600 hover:text-green-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 x-transition class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-5 flex items-center gap-2.5 text-sm shadow-sm">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('error') }}</span>
                <button @click="show = false" class="ml-auto text-red-600 hover:text-red-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif
        @yield('content')
    </main>

    <script>
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
                <div class="flex items-center gap-2.5">
                    ${type === 'success' ? '<svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>' : ''}
                    ${type === 'error' ? '<svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>' : ''}
                    <span class="text-sm font-medium">${message}</span>
                </div>
            `;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.3s ease-in forwards';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }
    </script>
    @stack('scripts')
</body>
</html>
