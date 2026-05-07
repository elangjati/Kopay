<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu — Kopay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
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

    {{-- Header --}}
    <header style="background:#1a3a1a;" class="text-white py-4 px-6 shadow">
        <div class="max-w-2xl mx-auto flex items-center gap-3">
            <img src="/images/logo.png" alt="Kopay" class="w-9 h-9 rounded-lg object-cover">
            <div>
                <h1 class="font-bold text-base leading-none">Kopay</h1>
                <p class="text-amber-300 text-xs mt-0.5">Daftar Menu</p>
            </div>
        </div>
    </header>

    {{-- Menu Board --}}
    <main class="max-w-2xl mx-auto px-4 py-6">
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-200">
            <img src="/images/menu-board.jpg"
                 alt="Menu Kopay"
                 class="w-full h-auto"
                 onerror="this.style.display='none'; document.getElementById('no-image').style.display='flex'">

            {{-- Fallback kalau gambar belum diupload --}}
            <div id="no-image" class="hidden flex-col items-center justify-center py-20 text-gray-400">
                <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm font-medium">Menu board belum tersedia</p>
                <p class="text-xs mt-1">Silakan tanya langsung ke kasir</p>
            </div>
        </div>

        <p class="text-center text-xs text-gray-400 mt-4">
            Untuk memesan, silakan hubungi kasir kami
        </p>
    </main>

    {{-- Footer --}}
    <footer style="background:#1a3a1a;" class="text-center text-primary-200 text-xs py-4 mt-8">
        &copy; {{ date('Y') }} Kopay — Jalan Raya Gading Tutuka 1, Soreang
    </footer>

</body>
</html>
