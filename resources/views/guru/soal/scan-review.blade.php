@extends('layouts.app')

@section('title', 'Review & Edit Soal Scan')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold mb-2 text-gray-800">📋 Review Soal Hasil Scan</h1>
    <p class="text-gray-600 mb-8">Periksa dan edit hasil OCR sebelum menyimpan ke database</p>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Image Preview (Left) -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-4 sticky top-4">
                <h3 class="font-semibold text-gray-800 mb-4">Gambar Asli</h3>
                <img src="{{ $imageUrl }}" alt="Scanned" class="w-full border rounded-lg shadow">
                <p class="text-xs text-gray-500 mt-4">Scroll ke bawah untuk melihat hasil parsing</p>
            </div>
        </div>

        <!-- Form & Processing -->
        <div class="lg:col-span-2">
    <div id="loadingSpinner" class="bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="inline-block">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                </div>
                <p class="text-gray-700 mt-4 font-semibold">Memproses gambar dengan OCR...</p>
                <p class="text-gray-600 text-sm mt-2">⏳ Ini mungkin memakan waktu 10-30 detik tergantung ukuran gambar</p>
                <div id="progressBar" class="mt-4 w-full bg-gray-200 rounded-full h-2">
                    <div id="progressFill" class="bg-blue-600 h-2 rounded-full" style="width: 0%"></div>
                </div>
                <p id="progressText" class="text-gray-600 text-xs mt-2">0%</p>
            </div>

            <form id="scanForm" action="{{ route('guru.soal.scan.save') }}" method="POST" class="hidden space-y-6">
                @csrf

                <!-- Hidden Fields -->
                <input type="hidden" name="imagePath" value="{{ $imagePath }}">
                <input type="hidden" name="kelas_id" value="{{ $kelas_id }}">
                <input type="hidden" name="kategori_id" value="{{ $kategori_id }}">

                <!-- Questions Container -->
                <div id="questionsContainer"></div>

                <!-- Action Buttons -->
                <div id="actionButtons" class="hidden bg-white rounded-lg shadow-lg p-6 space-y-4">
                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition">
                            💾 Simpan Semua Soal
                        </button>
                        <a href="{{ route('guru.soal.scan.upload') }}" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white font-bold py-3 px-4 rounded-lg text-center transition">
                            Scan Ulang
                        </a>
                    </div>
                </div>
            </form>

            <!-- Error Message -->
            <div id="errorMessage" class="hidden bg-red-100 border border-red-400 rounded-lg p-6">
                <div id="errorText" class="text-red-800 mb-4"></div>
                <div class="flex gap-4">
                    <a href="{{ route('guru.soal.scan.upload') }}" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg text-center transition">
                        🔄 Scan Ulang
                    </a>
                    <a href="{{ route('guru.soal.create') }}" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg text-center transition">
                        ✏️ Input Manual
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tesseract.js Script dengan Worker untuk performa lebih baik -->
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@4/dist/tesseract.min.js"></script>

<script>
    const imageUrl = '{{ $imageUrl }}';
    const loadingSpinner = document.getElementById('loadingSpinner');
    const questionsContainer = document.getElementById('questionsContainer');
    const scanForm = document.getElementById('scanForm');
    const actionButtons = document.getElementById('actionButtons');
    const errorMessage = document.getElementById('errorMessage');

    // Start OCR processing dengan optimization
    async function processImage() {
        try {
            // Resize dan compress image untuk OCR lebih cepat
            const image = await loadImage(imageUrl);
            const resizedImage = resizeImage(image, 1200);

            // Perform OCR dengan language support yang lebih baik
            const result = await Tesseract.recognize(
                resizedImage,
                'ind+eng', // Indonesian + English
                {
                    logger: m => {
                        const progress = Math.round(m.progress * 100);
                        console.log('OCR Progress:', progress + '%');
                        document.getElementById('progressFill').style.width = progress + '%';
                        document.getElementById('progressText').textContent = progress + '%';
                    }
                }
            );

            const extractedText = result.data.text;
            console.log('Extracted Text:', extractedText);
            console.log('Confidence:', result.data.confidence);

            // Parse dengan multiple strategies
            let questions = parseQuestionsV2(extractedText);
            
            // Jika parsing gagal, coba strategy alternatif
            if (questions.length === 0) {
                console.log('Trying alternative parsing...');
                questions = parseQuestionsV3(extractedText);
            }

            console.log('Parsed Questions:', questions);

            if (questions.length === 0) {
                const debugText = extractedText.substring(0, 1000).replace(/</g, '&lt;').replace(/>/g, '&gt;');
                showError(`Tidak ada soal yang terdeteksi. Coba:<br>
• Scan ulang dengan foto yang lebih terang dan jelas<br>
• Pastikan format: nomor, pertanyaan, A) B) C) D)<br>
• Gunakan input manual jika format tidak standar<br><br>
<details style="margin-top: 10px; text-align: left;">
<summary style="cursor: pointer; color: #1e40af;">📄 Lihat Teks yang Diekstrak (Debug)</summary>
<pre style="background: #f3f4f6; padding: 10px; border-radius: 5px; max-height: 300px; overflow-y: auto; font-size: 12px; margin-top: 10px;">${debugText}</pre>
</details>`);
                return;
            }

            // Display the parsed questions
            displayQuestions(questions);
            loadingSpinner.classList.add('hidden');
            scanForm.classList.remove('hidden');
            actionButtons.classList.remove('hidden');

        } catch (error) {
            console.error('OCR Error:', error);
            showError('Gagal memproses gambar: ' + error.message + '<br><br>Silakan coba lagi dengan gambar yang lebih jelas.');
        }
    }

    // Load image sebagai canvas
    function loadImage(src) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = () => resolve(img);
            img.onerror = reject;
            img.src = src;
        });
    }

    // Resize image untuk performa OCR lebih baik
    function resizeImage(img, maxWidth) {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        
        const scale = maxWidth / img.width;
        canvas.width = maxWidth;
        canvas.height = img.height * scale;
        
        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        
        // Return JPEG dengan quality tinggi untuk OCR accuracy
        return canvas.toDataURL('image/jpeg', 0.90);
    }

    // IMPROVED Parsing V2 - Lebih robust dan flexible
    function parseQuestionsV2(text) {
        const questions = [];
        
        // Clean text - normalize whitespace dan remove OCR artifacts
        let cleanText = text
            .replace(/l\s+1/gi, '1') // Fix OCR error: 'l 1' -> '1'
            .replace(/O\s+0/gi, '0') // Fix OCR error: 'O 0' -> '0'
            .replace(/[\u2013\u2014\u2015\–\—]/g, '-') // Normalize dashes
            .replace(/[\u201C\u201D]/g, '"') // Normalize quotes
            .trim();

        const lines = cleanText.split('\n')
            .map(line => line.replace(/^\s+|\s+$/g, '')) // Trim each line
            .filter(line => line.length > 0); // Remove empty lines

        let i = 0;
        while (i < lines.length) {
            const line = lines[i];

            // Deteksi pertanyaan (bisa dengan nomor atau langsung)
            if (isLikelyQuestion(line, i, lines)) {
                let questionText = line.replace(/^\d+[\.\)\-\:\s]+/, '').trim(); // Remove number prefix
                
                if (!questionText) {
                    i++;
                    continue;
                }

                // Kumpulkan pilihan A, B, C, D
                const choices = {};
                let j = i + 1;
                let foundChoices = 0;

                while (j < lines.length && foundChoices < 4) {
                    const choiceLine = lines[j];

                    if (isChoice(choiceLine)) {
                        const [letter, text] = extractChoice(choiceLine);
                        if (text && text.length > 0) {
                            choices[letter.toLowerCase()] = text;
                            foundChoices++;
                            j++;
                        } else {
                            j++;
                        }
                    } else if (foundChoices > 0 && foundChoices < 4) {
                        // Continuation of previous choice (multi-line)
                        if (choices[Object.keys(choices)[Object.keys(choices).length - 1]] && !isLikelyQuestion(choiceLine, j, lines)) {
                            const lastLetter = Object.keys(choices)[Object.keys(choices).length - 1];
                            choices[lastLetter] += ' ' + choiceLine;
                            j++;
                        } else {
                            break;
                        }
                    } else if (isLikelyQuestion(choiceLine, j, lines)) {
                        break; // Next question found
                    } else {
                        j++;
                    }
                }

                // Validasi: harus punya 4 pilihan yang valid
                if (foundChoices === 4 && 
                    choices['a'] && choices['a'].length > 0 &&
                    choices['b'] && choices['b'].length > 0 &&
                    choices['c'] && choices['c'].length > 0 &&
                    choices['d'] && choices['d'].length > 0) {
                    
                    questions.push({
                        pertanyaan: questionText.substring(0, 200), // Limit question length
                        pilihan_a: choices['a'].substring(0, 150),
                        pilihan_b: choices['b'].substring(0, 150),
                        pilihan_c: choices['c'].substring(0, 150),
                        pilihan_d: choices['d'].substring(0, 150),
                        jawaban_benar: 'a' // Default, user akan set manually
                    });

                    i = j; // Continue from where we left choices
                } else {
                    i++;
                }
            } else {
                i++;
            }
        }

        return questions;
    }

    // ALTERNATIVE Parsing V3 - Gunakan regex grouping
    function parseQuestionsV3(text) {
        const questions = [];
        
        // Pattern untuk match: nomor/bullet, pertanyaan, dan 4 pilihan
        const questionPattern = /(?:^|\n)([\d*\-•]+[\.\)\:]?|^|\n)\s*([^a-d\n]*[^a-d\n\.\)])\n([a-d]\)\s*[^\n]+\n){3}[a-d]\)\s*[^\n]+/gmi;
        const choicePattern = /([a-d])\)\s*([^\n]+)/gi;

        // Split by question number or bullet
        const parts = text.split(/\n(?=\d+[\.\)\:]?\s|^[a-d]\))/);

        for (let part of parts) {
            const lines = part.split('\n').map(l => l.trim()).filter(l => l);
            
            if (lines.length < 5) continue; // Minimal: question + 4 choices

            // Cari pertanyaan (baris pertama yang bukan choice)
            let questionText = '';
            let choicesStart = 0;

            for (let j = 0; j < lines.length; j++) {
                if (!isChoice(lines[j])) {
                    questionText = lines[j].replace(/^\d+[\.\)\-\:]?\s*/, '');
                    choicesStart = j + 1;
                } else {
                    break;
                }
            }

            if (!questionText) continue;

            // Kumpulkan pilihan dari sisa lines
            const choices = {};
            for (let j = choicesStart; j < lines.length; j++) {
                if (isChoice(lines[j])) {
                    const [letter, text] = extractChoice(lines[j]);
                    choices[letter.toLowerCase()] = text;
                }
            }

            if (Object.keys(choices).length === 4) {
                questions.push({
                    pertanyaan: questionText,
                    pilihan_a: choices['a'] || '',
                    pilihan_b: choices['b'] || '',
                    pilihan_c: choices['c'] || '',
                    pilihan_d: choices['d'] || '',
                    jawaban_benar: 'a' // Default
                });
            }
        }

        return questions;
    }

    // Helper: Cek apakah line adalah pilihan (A, B, C, atau D)
    function isChoice(line) {
        // Pattern: A) text atau A. text atau A: text atau A] text
        if (/^[a-d][\):\.\]\s]/i.test(line)) return true;
        // Pattern: A text (dengan spasi)
        if (/^[a-d]\s+[A-Za-z0-9]/i.test(line)) return true;
        return false;
    }

    // Helper: Extract pilihan dari line
    function extractChoice(line) {
        // Try patterns: A) text, A. text, A: text, A] text, A text
        const patterns = [
            /^([a-d])\)\s*(.+)/i,
            /^([a-d]]\s*(.+)/i,
            /^([a-d]:\s*(.+)/i,
            /^([a-d]\.\s*(.+)/i,
            /^([a-d]\s+(.+)/i
        ];

        for (let pattern of patterns) {
            const match = line.match(pattern);
            if (match && match[2]) {
                return [match[1], match[2].trim()];
            }
        }
        
        return ['a', line];
    }

    // Helper: Cek apakah line kemungkinan adalah pertanyaan
    function isLikelyQuestion(line, index, lines) {
        // Punya nomor di depan: 1. atau 1) atau 1-
        if (/^\d+[\.\)\-\:\s]/.test(line)) return true;
        
        // Panjang lumayan dan tidak ada choice marker
        if (line.length > 15 && !isChoice(line)) {
            // Check if next line is a choice
            if (index + 1 < lines.length && isChoice(lines[index + 1])) {
                return true;
            }
            // Or if it ends with question mark
            if (line.endsWith('?')) return true;
        }

        return false;
    }

    // Helper: Deteksi jawaban benar (jika ditandai dengan asterisk atau bold)
    function detectAnswer(questionText, choices, lines) {
        // Default ke A jika tidak ketemu
        return 'a';
    }

    // Display parsed questions in editable form
    function displayQuestions(questions) {
        questionsContainer.innerHTML = '';
        
        questions.forEach((q, index) => {
            const questionDiv = document.createElement('div');
            questionDiv.className = 'bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500';
            
            questionDiv.innerHTML = `
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Pertanyaan #${index + 1}</label>
                    <textarea name="questions[${index}][pertanyaan]" required rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Edit pertanyaan di sini...">${escapeHtml(q.pertanyaan)}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Pilihan A</label>
                        <input type="text" name="questions[${index}][pilihan_a]" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="${escapeHtml(q.pilihan_a)}" placeholder="Pilihan A">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Pilihan B</label>
                        <input type="text" name="questions[${index}][pilihan_b]" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="${escapeHtml(q.pilihan_b)}" placeholder="Pilihan B">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Pilihan C</label>
                        <input type="text" name="questions[${index}][pilihan_c]" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="${escapeHtml(q.pilihan_c)}" placeholder="Pilihan C">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Pilihan D</label>
                        <input type="text" name="questions[${index}][pilihan_d]" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="${escapeHtml(q.pilihan_d)}" placeholder="Pilihan D">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Jawaban Benar</label>
                    <select name="questions[${index}][jawaban_benar]" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="a" ${q.jawaban_benar === 'a' ? 'selected' : ''}>A</option>
                        <option value="b" ${q.jawaban_benar === 'b' ? 'selected' : ''}>B</option>
                        <option value="c" ${q.jawaban_benar === 'c' ? 'selected' : ''}>C</option>
                        <option value="d" ${q.jawaban_benar === 'd' ? 'selected' : ''}>D</option>
                    </select>
                </div>

                <div class="mt-4 p-3 bg-blue-50 rounded border border-blue-200 text-sm text-gray-600">
                    <strong>💡 Tips:</strong> Periksa kembali teks yang diekstrak. OCR mungkin salah membaca beberapa karakter.
                </div>
            `;
            
            questionsContainer.appendChild(questionDiv);
        });
    }

    // Show error message
    function showError(message) {
        loadingSpinner.classList.add('hidden');
        const errorText = document.getElementById('errorText');
        errorText.innerHTML = `<strong>❌ Error:</strong><br>${message}`;
        errorMessage.classList.remove('hidden');
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Start processing when page loads
    window.addEventListener('load', processImage);
</script>
@endsection
