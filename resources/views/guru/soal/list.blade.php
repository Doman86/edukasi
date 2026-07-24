@extends('layouts.app')

@section('title', 'Daftar Soal')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-800">Daftar Soal Saya</h1>
        <div class="flex gap-3">
            <a href="{{ route('guru.soal.scan.upload') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition flex items-center gap-2">
                📸 Scan Soal
            </a>
            <a href="{{ route('guru.soal.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                + Tambah Soal
            </a>
        </div>
    </div>

    @if ($soal->count() > 0)
        <div class="grid grid-cols-1 gap-6">
            @foreach ($soal as $s)
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $s->pertanyaan }}</h3>
                            <div class="flex gap-4 mb-4">
                                <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                                    {{ $s->kelas->nama_kelas }}
                                </span>
                                <span class="inline-block bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">
                                    {{ ucfirst($s->kategori->nama_kategori) }}
                                </span>
                            </div>
                            
                            <!-- Pilihan Jawaban -->
                            <div class="space-y-2 mb-4">
                                <div class="flex items-start">
                                    <span class="font-semibold w-8">A.</span>
                                    <span class="flex-1 {{ $s->jawaban_benar === 'a' ? 'text-green-600 font-bold' : '' }}">
                                        {{ $s->pilihan_a }}
                                        @if ($s->jawaban_benar === 'a')
                                            <span class="text-green-600">✓</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-start">
                                    <span class="font-semibold w-8">B.</span>
                                    <span class="flex-1 {{ $s->jawaban_benar === 'b' ? 'text-green-600 font-bold' : '' }}">
                                        {{ $s->pilihan_b }}
                                        @if ($s->jawaban_benar === 'b')
                                            <span class="text-green-600">✓</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-start">
                                    <span class="font-semibold w-8">C.</span>
                                    <span class="flex-1 {{ $s->jawaban_benar === 'c' ? 'text-green-600 font-bold' : '' }}">
                                        {{ $s->pilihan_c }}
                                        @if ($s->jawaban_benar === 'c')
                                            <span class="text-green-600">✓</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="flex items-start">
                                    <span class="font-semibold w-8">D.</span>
                                    <span class="flex-1 {{ $s->jawaban_benar === 'd' ? 'text-green-600 font-bold' : '' }}">
                                        {{ $s->pilihan_d }}
                                        @if ($s->jawaban_benar === 'd')
                                            <span class="text-green-600">✓</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <p class="text-gray-500 text-sm">Dibuat: {{ $s->created_at->format('d M Y H:i') }}</p>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2 ml-4">
                            <a href="{{ route('guru.soal.edit', $s) }}" 
                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded transition text-sm">
                                Edit
                            </a>
                            <form action="{{ route('guru.soal.delete', $s) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded transition text-sm"
                                    onclick="return confirm('Yakin ingin menghapus soal ini?')">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $soal->links('pagination::tailwind') }}
        </div>
    @else
        <div class="bg-blue-50 border-l-4 border-blue-500 p-8 rounded-lg text-center">
            <p class="text-blue-700 text-lg">Anda belum membuat soal apapun.</p>
            <a href="{{ route('guru.soal.create') }}" class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                Buat Soal Sekarang
            </a>
        </div>
    @endif
</div>
@endsection
