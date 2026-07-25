@extends('layouts.app')

@section('title', 'Guru Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Foto Guru -->
    <div class="flex justify-center mb-8">
        @if (auth()->user()->foto)
            <img src="{{ asset('storage/' . auth()->user()->foto) }}" alt="{{ auth()->user()->name }}" class="h-40 w-40 rounded-full object-cover shadow-lg border-4 border-blue-500">
        @else
            <div class="h-40 w-40 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white text-5xl font-bold shadow-lg border-4 border-blue-500">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
        @endif
    </div>

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-4xl font-bold text-gray-800">Dashboard Guru</h1>
            <p class="text-gray-600 mt-2">Selamat datang, <span class="font-semibold">{{ auth()->user()->name }}</span></p>
        </div>
        <a href="{{ route('guru.soal.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition text-center">
            + Tambah Soal
        </a>
    </div>

    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-blue-500 text-white rounded-lg p-8 shadow-lg text-center">
            <h3 class="text-lg font-semibold opacity-90">Total Soal</h3>
            <p class="text-5xl font-bold mt-4">{{ $soalCount }}</p>
        </div>
        <div class="bg-green-500 text-white rounded-lg p-8 shadow-lg text-center">
            <h3 class="text-lg font-semibold opacity-90">Status</h3>
            <p class="text-2xl font-bold mt-4">✓ Terverifikasi</p>
        </div>
    </div>

    <!-- Tombol Quick Action -->
    <div class="flex flex-col gap-4 sm:flex-row">
        <a href="{{ route('guru.soal.list') }}" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg text-center transition">
            Lihat Semua Soal
        </a>
    </div>
</div>
@endsection

