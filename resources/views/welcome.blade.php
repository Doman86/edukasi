<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Edukasi - Beranda</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/sd.jpeg') }}" alt="Logo" class="h-10 w-10 rounded-full">
                <h1 class="text-2xl font-bold">Web Edukasi</h1>
            </div>
            <div class="flex items-center gap-4">
                @auth
                    <span>{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</span>
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="hover:bg-blue-700 px-3 py-2 rounded">Dashboard</a>
                    @else
                        <a href="{{ route('guru.dashboard') }}" class="hover:bg-blue-700 px-3 py-2 rounded">Dashboard</a>
                    @endif
                    <form action="{{ route('logout', [], false) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:bg-blue-700 px-3 py-2 rounded">Login</a>
                    <a href="{{ route('register') }}" class="bg-yellow-400 text-blue-900 px-4 py-2 rounded hover:bg-yellow-500">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="min-h-screen flex items-center justify-center bg-gradient-to-r from-blue-600 to-blue-800">
        <div class="text-center text-white max-w-4xl px-4">
            <div class="mb-8">
                <img src="{{ asset('images/sd.jpeg') }}" alt="Logo SD Negeri 2 Petungsewu" class="h-32 w-32 mx-auto mb-4 rounded-lg shadow-lg">
            </div>
            <h1 class="text-6xl font-bold mb-4">Web Edukasi</h1>
            <p class="text-2xl mb-8 opacity-90">SEKOLAH DASAR NEGERI 2 PETUNGSEWU WAGIR</p>
            
            @auth
                <div class="space-y-4">
                    @if (auth()->user()->role === 'admin')
                        <p class="text-lg mb-4">Selamat datang, Admin!</p>
                        <a href="{{ route('admin.dashboard') }}" class="inline-block bg-white text-blue-600 font-bold px-8 py-3 rounded-lg hover:bg-gray-100 transition">
                            Masuk ke Dashboard Admin
                        </a>
                    @else
                        @if (auth()->user()->status === 'verified')
                            <p class="text-lg mb-4">Selamat datang, {{ auth()->user()->name }}!</p>
                            <div class="space-x-4">
                                <a href="{{ route('guru.dashboard') }}" class="inline-block bg-white text-blue-600 font-bold px-8 py-3 rounded-lg hover:bg-gray-100 transition">
                                    Dashboard
                                </a>
                                <a href="{{ route('guru.soal.list') }}" class="inline-block bg-yellow-400 text-blue-900 font-bold px-8 py-3 rounded-lg hover:bg-yellow-500 transition">
                                    Soal Saya
                                </a>
                            </div>
                        @else
                            <div class="bg-yellow-100 text-yellow-900 px-6 py-4 rounded-lg mb-6">
                                <p class="font-semibold">⏳ Akun Anda menunggu verifikasi dari admin</p>
                                <p class="text-sm mt-2">Silakan tunggu admin untuk memverifikasi akun Anda sebelum dapat membuat soal.</p>
                            </div>
                        @endif
                    @endif
                </div>
            @else
                <div class="space-x-4">
                    <a href="{{ route('login') }}" class="inline-block bg-white text-blue-600 font-bold px-8 py-3 rounded-lg hover:bg-gray-100 transition">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="inline-block bg-yellow-400 text-blue-900 font-bold px-8 py-3 rounded-lg hover:bg-yellow-500 transition">
                        Daftar
                    </a>
                </div>
            @endauth

            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white bg-opacity-20 backdrop-blur-lg rounded-lg p-6">
                    <h3 class="text-2xl font-bold mb-2">📚 Belajar</h3>
                    <p>Akses berbagai soal pembelajaran dari guru</p>
                </div>
                <div class="bg-white bg-opacity-20 backdrop-blur-lg rounded-lg p-6">
                    <h3 class="text-2xl font-bold mb-2">✍️ Buat Soal</h3>
                    <p>Guru dapat membuat soal dengan berbagai tingkat kesulitan</p>
                </div>
                <div class="bg-white bg-opacity-20 backdrop-blur-lg rounded-lg p-6">
                    <h3 class="text-2xl font-bold mb-2">✅ Verifikasi</h3>
                    <p>Admin memverifikasi setiap guru untuk keamanan</p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
