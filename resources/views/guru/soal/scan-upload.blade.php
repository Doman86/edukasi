@extends('layouts.app')

@section('title', 'Scan Soal Otomatis')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-2 text-gray-800">📸 Scan Soal Otomatis</h1>
    <p class="text-gray-600 mb-8">Upload foto soal untuk ekstrak otomatis menggunakan OCR (Optical Character Recognition)</p>

    <form action="{{ route('guru.soal.scan.process') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow-lg p-8 space-y-6">
        @csrf

        <!-- Image Upload -->
        <div class="border-2 border-dashed border-blue-400 rounded-lg p-8 text-center bg-blue-50 cursor-pointer hover:bg-blue-100 transition"
            id="uploadArea">
            <input type="file" id="imageInput" name="image" accept="image/*" class="hidden" required>
            
            <svg class="mx-auto h-12 w-12 text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            
            <p class="text-gray-700 font-semibold mb-2">Klik atau drag gambar soal ke sini</p>
            <p class="text-gray-600 text-sm">PNG, JPG, atau GIF (max 5MB)</p>
        </div>

        <!-- Image Preview -->
        <div id="previewContainer" class="hidden">
            <label class="block text-gray-700 font-semibold mb-2">Preview Gambar:</label>
            <img id="previewImage" src="" alt="Preview" class="max-w-full h-auto border rounded-lg shadow">
            <button type="button" id="removeImage" class="mt-4 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                Ganti Gambar
            </button>
        </div>

        <!-- Kelas -->
        <div>
            <label for="kelas_id" class="block text-gray-700 font-semibold mb-2">Pilih Kelas</label>
            <select id="kelas_id" name="kelas_id" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Kelas --</option>
                @foreach ($kelas as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
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
                    <option value="{{ $k->id }}">{{ ucfirst($k->nama_kategori) }}</option>
                @endforeach
            </select>
            @error('kategori_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Processing Information -->
        <div class="bg-blue-100 border border-blue-400 rounded-lg p-4 space-y-3">
            <p class="text-blue-800 text-sm font-semibold">💡 Tips untuk Hasil Terbaik:</p>
            <ul class="text-blue-800 text-sm ml-4 space-y-2 list-disc">
                <li>Foto soal jelas, terang, dan tidak blur</li>
                <li>Teks mudah dibaca (tidak miring atau terpotong)</li>
                <li>Minimal satu soal per foto</li>
                <li>Format: <strong>1. Pertanyaan?</strong></li>
                <li style="margin-top: 10px">Diikuti pilihan dengan format:</li>
                <li style="margin-left: 20px"><code style="background: white; padding: 2px 6px; border-radius: 3px;">A) Jawaban A</code> atau <code style="background: white; padding: 2px 6px; border-radius: 3px;">A. Jawaban A</code></li>
                <li style="margin-left: 20px"><code style="background: white; padding: 2px 6px; border-radius: 3px;">B) Jawaban B</code></li>
                <li style="margin-left: 20px"><code style="background: white; padding: 2px 6px; border-radius: 3px;">C) Jawaban C</code></li>
                <li style="margin-left: 20px"><code style="background: white; padding: 2px 6px; border-radius: 3px;">D) Jawaban D</code></li>
            </ul>
            <p class="text-blue-800 text-xs mt-4 italic">⏱️ Proses OCR membutuhkan 10-30 detik. Mohon tunggu sampai selesai.</p>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4">
            <button type="submit" id="submitBtn" disabled
                class="flex-1 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-bold py-2 px-4 rounded-lg transition">
                Proses & Scan
            </button>
            <a href="{{ route('guru.soal.list') }}" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg text-center transition">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
    const uploadArea = document.getElementById('uploadArea');
    const imageInput = document.getElementById('imageInput');
    const previewContainer = document.getElementById('previewContainer');
    const previewImage = document.getElementById('previewImage');
    const removeImageBtn = document.getElementById('removeImage');
    const submitBtn = document.getElementById('submitBtn');

    // Handle drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('bg-blue-200', 'border-blue-600');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('bg-blue-200', 'border-blue-600');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('bg-blue-200', 'border-blue-600');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            imageInput.files = files;
            handleImageSelect();
        }
    });

    // Handle click upload
    uploadArea.addEventListener('click', () => {
        imageInput.click();
    });

    imageInput.addEventListener('change', handleImageSelect);

    function handleImageSelect() {
        const file = imageInput.files[0];
        if (file) {
            // Compress image sebelum display
            compressImage(file, (compressedBlob) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    uploadArea.classList.add('hidden');
                    submitBtn.disabled = false;
                };
                reader.readAsDataURL(compressedBlob);

                // Replace file input dengan compressed blob
                const dataTransfer = new DataTransfer();
                const compressedFile = new File([compressedBlob], file.name, { type: 'image/jpeg' });
                dataTransfer.items.add(compressedFile);
                imageInput.files = dataTransfer.files;
            });
        }
    }

    function compressImage(file, callback) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (e) => {
            const img = new Image();
            img.src = e.target.result;
            img.onload = () => {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;

                // Scale down if too large
                const maxDim = 1500;
                if (width > height && width > maxDim) {
                    height = Math.round(height * maxDim / width);
                    width = maxDim;
                } else if (height > maxDim) {
                    width = Math.round(width * maxDim / height);
                    height = maxDim;
                }

                canvas.width = width;
                canvas.height = height;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                // Compress dengan quality lebih tinggi untuk OCR
                canvas.toBlob(callback, 'image/jpeg', 0.85);
            };
        };
    }

    removeImageBtn.addEventListener('click', () => {
        imageInput.value = '';
        previewContainer.classList.add('hidden');
        uploadArea.classList.remove('hidden');
        submitBtn.disabled = true;
    });
</script>
@endsection
