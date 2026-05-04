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
    <nav style="background:#1a3a1a;" class="text-white shadow-lg">
        <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="/images/logo.png" alt="Kopay" class="w-10 h-10 rounded-lg object-cover">
                <div>
                    <h1 class="font-bold text-lg tracking-tight leading-none">Kopay</h1>
                    <p class="text-amber-300 text-xs">Kafe & Minuman</p>
                </div>
            </div>
            <a href="{{ route('admin.login') }}"
               class="text-sm text-primary-200 hover:text-white transition border border-primary-600 hover:border-white px-4 py-2 rounded-lg">
                Login Staff
            </a>
        </div>
    </nav>

    {{-- Hero --}}
    <section style="background:#1a3a1a;" class="text-white py-20 px-6">
        <div class="max-w-5xl mx-auto text-center">
            <div class="inline-block bg-amber-400 text-amber-900 text-xs font-bold px-3 py-1 rounded-full mb-6 uppercase tracking-widest">
                Soreang, Bandung
            </div>
            <h2 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">
                Selamat Datang di <span class="text-amber-300">Kopay</span>
            </h2>
            <p class="text-primary-200 text-lg max-w-xl mx-auto mb-8">
                Nikmati minuman segar dan makanan ringan pilihan di suasana yang nyaman.
                Tersedia berbagai pilihan kopi, non-kopi, dan camilan lezat.
            </p>
            <div class="flex flex-wrap justify-center gap-3">
                <span class="bg-primary-700 text-primary-100 text-sm px-4 py-2 rounded-full">☕ Kopi</span>
                <span class="bg-primary-700 text-primary-100 text-sm px-4 py-2 rounded-full">🧋 Non-Kopi</span>
                <span class="bg-primary-700 text-primary-100 text-sm px-4 py-2 rounded-full">🥐 Makanan Ringan</span>
            </div>
        </div>
    </section>

    {{-- Menu Highlight --}}
    <section class="py-16 px-6">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-10">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Menu Kami</h3>
                <p class="text-gray-500 text-sm">Pilihan minuman dan makanan ringan terbaik</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl border border-gray-200 p-6 text-center shadow-sm hover:shadow-md transition">
                    <div class="text-5xl mb-4">☕</div>
                    <h4 class="font-bold text-gray-900 mb-2">Kopi</h4>
                    <p class="text-gray-500 text-sm">Espresso, Americano, Cappuccino, Latte, dan berbagai pilihan kopi premium</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-200 p-6 text-center shadow-sm hover:shadow-md transition">
                    <div class="text-5xl mb-4">🧋</div>
                    <h4 class="font-bold text-gray-900 mb-2">Non-Kopi</h4>
                    <p class="text-gray-500 text-sm">Matcha latte, coklat panas, dan minuman segar lainnya untuk semua selera</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-200 p-6 text-center shadow-sm hover:shadow-md transition">
                    <div class="text-5xl mb-4">🥐</div>
                    <h4 class="font-bold text-gray-900 mb-2">Makanan Ringan</h4>
                    <p class="text-gray-500 text-sm">Croissant, roti bakar, dan berbagai camilan untuk menemani minumanmu</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Pembayaran --}}
    <section class="py-12 px-6 bg-white border-t border-gray-100">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Metode Pembayaran</h3>
                <p class="text-gray-500 text-sm">Kami menerima pembayaran tunai dan digital</p>
            </div>
            <div class="flex flex-wrap justify-center gap-6">
                <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-xl px-6 py-4">
                    <span class="text-2xl">💵</span>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">Tunai</p>
                        <p class="text-gray-400 text-xs">Cash payment</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-xl px-6 py-4">
                    <span class="text-2xl">📱</span>
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">QRIS</p>
                        <p class="text-gray-400 text-xs">Powered by Midtrans</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Info & Kontak --}}
    <section class="py-16 px-6 bg-gray-50">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-10">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Informasi</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-2xl mx-auto">
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center text-xl flex-shrink-0">📍</div>
                        <div>
                            <p class="font-semibold text-gray-800 mb-1">Lokasi</p>
                            <p class="text-gray-500 text-sm leading-relaxed">Jalan Raya Gading Tutuka 1<br>Soreang, Bandung</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center text-xl flex-shrink-0">✉️</div>
                        <div>
                            <p class="font-semibold text-gray-800 mb-1">Email</p>
                            <a href="mailto:kopayid7@gmail.com"
                               class="text-primary-700 text-sm hover:underline">kopayid7@gmail.com</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer style="background:#1a3a1a;" class="text-primary-200 text-center py-6 text-sm">
        <p>&copy; {{ date('Y') }} Kopay. Jalan Raya Gading Tutuka 1, Soreang, Bandung.</p>
    </footer>

</body>
</html>
