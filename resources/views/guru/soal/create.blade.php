@extends('layouts.app')

@section('title', 'Buat Soal')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Buat Soal Baru</h1>
        <a href="{{ route('guru.soal.scan.upload') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition text-sm">
            📸 Scan Soal Otomatis
        </a>
    </div>

    <form action="{{ route('guru.soal.store') }}" method="POST" class="bg-white rounded-lg shadow-lg p-8 space-y-6">
        @csrf

        <!-- Kelas -->
        <div>
            <label for="kelas_id" class="block text-gray-700 font-semibold mb-2">Pilih Kelas</label>
            <select id="kelas_id" name="kelas_id" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Kelas --</option>
                @foreach ($kelas as $k)
                    <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
            @error('kelas_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Kategori -->
        <div>
            <label for="kategori_id" class="block text-gray-700 font-semibold mb-2">Tingkat Kesulitan</label>
            <select id="kategori_id" name="kategori_id" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Tingkat --</option>
                @foreach ($kategori as $k)
                    <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>
                        {{ ucfirst($k->nama_kategori) }}
                    </option>
                @endforeach
            </select>
            @error('kategori_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Pertanyaan -->
        <div>
            <label for="pertanyaan" class="block text-gray-700 font-semibold mb-2">Pertanyaan</label>
            <textarea id="pertanyaan" name="pertanyaan" required rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Tulis pertanyaan soal...">{{ old('pertanyaan') }}</textarea>
            @error('pertanyaan')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Pilihan A -->
        <div>
            <label for="pilihan_a" class="block text-gray-700 font-semibold mb-2">Pilihan A</label>
            <input type="text" id="pilihan_a" name="pilihan_a" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Jawaban pilihan A" value="{{ old('pilihan_a') }}">
            @error('pilihan_a')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Pilihan B -->
        <div>
            <label for="pilihan_b" class="block text-gray-700 font-semibold mb-2">Pilihan B</label>
            <input type="text" id="pilihan_b" name="pilihan_b" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Jawaban pilihan B" value="{{ old('pilihan_b') }}">
            @error('pilihan_b')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Pilihan C -->
        <div>
            <label for="pilihan_c" class="block text-gray-700 font-semibold mb-2">Pilihan C</label>
            <input type="text" id="pilihan_c" name="pilihan_c" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Jawaban pilihan C" value="{{ old('pilihan_c') }}">
            @error('pilihan_c')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Pilihan D -->
        <div>
            <label for="pilihan_d" class="block text-gray-700 font-semibold mb-2">Pilihan D</label>
            <input type="text" id="pilihan_d" name="pilihan_d" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Jawaban pilihan D" value="{{ old('pilihan_d') }}">
            @error('pilihan_d')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Jawaban Benar -->
        <div>
            <label for="jawaban_benar" class="block text-gray-700 font-semibold mb-2">Jawaban Benar</label>
            <select id="jawaban_benar" name="jawaban_benar" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Jawaban Benar --</option>
                <option value="a" {{ old('jawaban_benar') == 'a' ? 'selected' : '' }}>A</option>
                <option value="b" {{ old('jawaban_benar') == 'b' ? 'selected' : '' }}>B</option>
                <option value="c" {{ old('jawaban_benar') == 'c' ? 'selected' : '' }}>C</option>
                <option value="d" {{ old('jawaban_benar') == 'd' ? 'selected' : '' }}>D</option>
            </select>
            @error('jawaban_benar')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex gap-4">
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                Simpan Soal
            </button>
            <a href="{{ route('guru.soal.list') }}" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg text-center transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
