<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Web Edukasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    @if ($errors->any())
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg">
            <h3 class="font-bold">Error!</h3>
            <ul class="text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg">
            {{ session('error') }}
        </div>
    @endif

    @auth
        <!-- Navbar -->
        <nav class="bg-blue-600 text-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col gap-4 md:flex-row md:justify-between md:items-center">
                <div class="flex items-center gap-3">
                    @if (auth()->user()->role === 'guru' && auth()->user()->foto)
                        <img src="{{ asset('storage/' . auth()->user()->foto) }}" alt="Photo" class="h-10 w-10 rounded-full object-cover">
                    @else
                        <img src="{{ asset('images/sd.jpeg') }}" alt="Logo" class="h-10 w-10 rounded-full">
                    @endif
                    @if (auth()->user()->role === 'guru')
                        <h1 class="text-2xl font-bold">{{ auth()->user()->name }}</h1>
                    @else
                        <h1 class="text-2xl font-bold">Web Edukasi</h1>
                    @endif
                </div>
                <div class="flex flex-wrap items-center gap-2 justify-end text-sm md:text-base">
                    <span class="truncate max-w-full md:max-w-xs">{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</span>
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="hover:bg-blue-700 px-3 py-2 rounded">Dashboard</a>
                    @else
                        <a href="{{ route('guru.dashboard') }}" class="hover:bg-blue-700 px-3 py-2 rounded">Dashboard</a>
                        <a href="{{ route('guru.soal.list') }}" class="hover:bg-blue-700 px-3 py-2 rounded">Soal</a>
                    @endif
                    <form action="{{ route('logout', [], false) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-3 py-2 rounded">Logout</button>
                    </form>
                </div>
            </div>
        </nav>
    @endauth

    <main class="max-w-7xl mx-auto px-4 py-8">
        @yield('content')
    </main>

    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('[class*="fixed top-4"]');
            alerts.forEach(alert => {
                alert.style.display = 'none';
            });
        }, 5000);
    </script>
</body>
</html>
