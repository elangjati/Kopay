<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kopay — Kafe & Minuman Soreang</title>
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
<body class="bg-gray-50 min-h-screen">

    {{-- Navbar --}}
    <nav style="background:#1a3a1a;" class="text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-5 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="/images/logo.png" alt="Kopay" class="w-9 h-9 rounded-lg object-cover">
                <div>
                    <h1 class="font-bold text-base leading-none">Kopay</h1>
                    <p class="text-amber-300 text-xs mt-0.5">Kafe & Minuman</p>
                </div>
            </div>
            {{-- Link ke menu section --}}
            <a href="#menu" class="text-sm text-primary-200 hover:text-white transition">
                Lihat Menu
            </a>
        </div>
    </nav>

    {{-- Hero --}}
    <section style="background:#1a3a1a;" class="text-white py-16 px-5">
        <div class="max-w-4xl mx-auto text-center">
            <div class="inline-block bg-amber-400 text-amber-900 text-xs font-bold px-3 py-1 rounded-full mb-5 uppercase tracking-widest">
                Soreang, Bandung
            </div>
            <h2 class="text-3xl md:text-4xl font-bold mb-3 leading-tight">
                Selamat Datang di <span class="text-amber-300">Kopay</span>
            </h2>
            <p class="text-primary-200 max-w-lg mx-auto text-sm leading-relaxed">
                Nikmati minuman segar dan makanan ringan pilihan di suasana yang nyaman.
            </p>
            <a href="#menu"
               class="inline-block mt-6 bg-amber-400 hover:bg-amber-300 text-amber-900 font-semibold text-sm px-6 py-2.5 rounded-full transition">
                Lihat Menu Kami
            </a>
        </div>
    </section>

    {{-- Info singkat --}}
    <section class="bg-white border-b border-gray-100 py-5 px-5">
        <div class="max-w-4xl mx-auto flex flex-wrap justify-center gap-6 text-sm text-gray-600">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Jl. Raya Gading Tutuka 1, Soreang</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <a href="mailto:kopayid7@gmail.com" class="hover:text-primary-700 transition">kopayid7@gmail.com</a>
            </div>
        </div>
    </section>

    {{-- Menu Section --}}
    <section id="menu" class="py-12 px-5">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-1">Menu Kami</h3>
                <p class="text-gray-500 text-sm">Pesan langsung ke kasir kami</p>
            </div>

            @if($menus->isEmpty())
                <div class="text-center py-16 text-gray-400 bg-white rounded-2xl border border-gray-200">
                    <p class="text-sm">Menu belum tersedia. Silakan tanya langsung ke kasir.</p>
                </div>
            @else
                @foreach($menus as $category => $items)
                <div class="mb-8">
                    {{-- Category header --}}
                    <div class="flex items-center gap-3 mb-4">
                        <h4 class="text-xs font-bold text-primary-700 uppercase tracking-widest">{{ $category }}</h4>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>

                    {{-- Menu grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($items as $menu)
                        <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-sm transition">
                            <p class="font-semibold text-gray-900 text-sm leading-tight mb-1">{{ $menu->name }}</p>
                            @if($menu->description)
                                <p class="text-gray-400 text-xs mb-2 leading-relaxed">{{ Str::limit($menu->description, 50) }}</p>
                            @endif
                            <p class="font-bold text-primary-700 text-sm">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            @endif

            <div class="text-center mt-8 p-5 bg-primary-50 rounded-2xl border border-primary-100">
                <p class="text-sm text-primary-800 font-medium">Untuk memesan, silakan hubungi kasir kami</p>
                <p class="text-xs text-primary-600 mt-1">Pembayaran: Tunai & QRIS</p>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer style="background:#1a3a1a;" class="text-primary-200 text-center py-5 text-xs">
        <p>&copy; {{ date('Y') }} Kopay — Jalan Raya Gading Tutuka 1, Soreang, Bandung</p>
        <p class="mt-1">
            <a href="{{ route('admin.login') }}" class="text-primary-400 hover:text-primary-200 transition">Staff Login</a>
        </p>
    </footer>

</body>
</html>
