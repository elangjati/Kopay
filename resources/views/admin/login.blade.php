<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Kopay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                primary: { 50:'#fdf8f3', 100:'#f5e6d3', 500:'#8B4513', 600:'#7a3c10', 700:'#6b340e', 800:'#1a3a1a', 900:'#122812' }
            } } }
        }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center" style="background: linear-gradient(135deg, #122812 0%, #1a3a1a 50%, #2d1a0a 100%);">
    <div class="w-full max-w-sm px-4">

        <div class="text-center mb-8">
            <img src="/images/logo.png" alt="Kopay" class="w-20 h-20 rounded-2xl object-cover mx-auto mb-4 shadow-lg">
            <h1 class="text-3xl font-bold text-white tracking-tight">Kopay</h1>
            <p class="text-amber-300 text-sm mt-1">Dashboard Admin</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-5 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-600 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <input type="password" name="password" required
                           class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-600 focus:border-transparent transition">
                </div>
                <button type="submit"
                        class="w-full font-semibold py-2.5 rounded-lg transition text-sm text-white"
                        style="background: #1a3a1a;">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</body>
</html>
