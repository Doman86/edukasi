@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-8">
    <h1 class="text-4xl font-bold text-gray-800">Admin Dashboard</h1>

    <!-- Guru Pending Verification -->
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg">
        <h2 class="text-2xl font-bold text-yellow-800 mb-6">
            Guru Menunggu Verifikasi ({{ count($guruPending) }})
        </h2>

        @if (count($guruPending) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-yellow-200">
                        <tr>
                            <th class="px-4 py-2">Foto</th>
                            <th class="px-4 py-2">Nama</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Terdaftar</th>
                            <th class="px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($guruPending as $guru)
                            <tr class="border-b hover:bg-yellow-100">
                                <td class="px-4 py-2">
                                    @if ($guru->foto)
                                        <img src="{{ asset('storage/' . $guru->foto) }}" alt="{{ $guru->name }}" class="h-12 w-12 rounded-full object-cover">
                                    @else
                                        <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-semibold">
                                            {{ substr($guru->name, 0, 1) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-2 font-semibold">{{ $guru->name }}</td>
                                <td class="px-4 py-2">{{ $guru->email }}</td>
                                <td class="px-4 py-2">{{ $guru->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-2 space-x-2">
                                    <form action="{{ route('admin.verify', $guru) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm transition">
                                            Verifikasi
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.reject', $guru) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition"
                                            onclick="return confirm('Yakin ingin menolak guru ini?')">
                                            Tolak
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-yellow-700">Semua guru sudah diverifikasi ✓</p>
        @endif
    </div>

    <!-- Guru Verified -->
    <div class="bg-green-50 border-l-4 border-green-400 p-6 rounded-lg">
        <h2 class="text-2xl font-bold text-green-800 mb-6">
            Guru Terverifikasi ({{ count($guruVerified) }})
        </h2>

        @if (count($guruVerified) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-green-200">
                        <tr>
                            <th class="px-4 py-2">Foto</th>
                            <th class="px-4 py-2">Nama</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Diverifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($guruVerified as $guru)
                            <tr class="border-b hover:bg-green-100">
                                <td class="px-4 py-2">
                                    @if ($guru->foto)
                                        <img src="{{ asset('storage/' . $guru->foto) }}" alt="{{ $guru->name }}" class="h-12 w-12 rounded-full object-cover">
                                    @else
                                        <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-semibold">
                                            {{ substr($guru->name, 0, 1) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-2 font-semibold">{{ $guru->name }}</td>
                                <td class="px-4 py-2">{{ $guru->email }}</td>
                                <td class="px-4 py-2">{{ $guru->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-green-700">Belum ada guru yang terverifikasi</p>
        @endif
    </div>
</div>
@endsection
