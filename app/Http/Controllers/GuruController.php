<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use App\Models\Kelas;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruController extends Controller
{
    /**
     * Tampilkan dashboard guru
     */
    public function dashboard()
    {
        $soalCount = Soal::where('user_id', Auth::id())->count();
        $kelas = Kelas::all();
        $soalByKelas = Soal::where('user_id', Auth::id())
                           ->get()
                           ->groupBy('kelas_id');
        
        return view('guru.dashboard', compact('soalCount', 'kelas', 'soalByKelas'));
    }

    /**
     * Tampilkan form buat soal
     */
    public function createSoal()
    {
        $kelas = Kelas::all();
        $kategori = Kategori::all();
        
        return view('guru.soal.create', compact('kelas', 'kategori'));
    }

    /**
     * Simpan soal baru
     */
    public function storeSoal(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'kategori_id' => 'required|exists:kategori,id',
            'pertanyaan' => 'required|string|min:10',
            'pilihan_a' => 'required|string',
            'pilihan_b' => 'required|string',
            'pilihan_c' => 'required|string',
            'pilihan_d' => 'required|string',
            'jawaban_benar' => 'required|in:a,b,c,d',
        ]);

        Soal::create([
            'user_id' => Auth::id(),
            'kelas_id' => $validated['kelas_id'],
            'kategori_id' => $validated['kategori_id'],
            'pertanyaan' => $validated['pertanyaan'],
            'pilihan_a' => $validated['pilihan_a'],
            'pilihan_b' => $validated['pilihan_b'],
            'pilihan_c' => $validated['pilihan_c'],
            'pilihan_d' => $validated['pilihan_d'],
            'jawaban_benar' => $validated['jawaban_benar'],
        ]);

        return redirect('/guru/soal')->with('success', 'Soal berhasil ditambahkan');
    }

    /**
     * Tampilkan daftar soal guru
     */
    public function listSoal()
    {
        $soal = Soal::where('user_id', Auth::id())
                    ->with(['kelas', 'kategori'])
                    ->paginate(10);
        
        return view('guru.soal.list', compact('soal'));
    }

    /**
     * Edit soal
     */
    public function editSoal(Soal $soal)
    {
        // Cek apakah soal milik guru login
        if ($soal->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses ke soal ini');
        }

        $kelas = Kelas::all();
        $kategori = Kategori::all();
        
        return view('guru.soal.edit', compact('soal', 'kelas', 'kategori'));
    }

    /**
     * Update soal
     */
    public function updateSoal(Request $request, Soal $soal)
    {
        // Cek apakah soal milik guru login
        if ($soal->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses ke soal ini');
        }

        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'kategori_id' => 'required|exists:kategori,id',
            'pertanyaan' => 'required|string|min:10',
            'pilihan_a' => 'required|string',
            'pilihan_b' => 'required|string',
            'pilihan_c' => 'required|string',
            'pilihan_d' => 'required|string',
            'jawaban_benar' => 'required|in:a,b,c,d',
        ]);

        $soal->update($validated);
        return redirect('/guru/soal')->with('success', 'Soal berhasil diperbarui');
    }

    /**
     * Hapus soal
     */
    public function deleteSoal(Soal $soal)
    {
        // Cek apakah soal milik guru login
        if ($soal->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses ke soal ini');
        }

        $soal->delete();
        return back()->with('success', 'Soal berhasil dihapus');
    }

    /**
     * Tampilkan halaman scan/upload soal
     */
    public function showScanUpload()
    {
        $kelas = Kelas::all();
        $kategori = Kategori::all();
        
        return view('guru.soal.scan-upload', compact('kelas', 'kategori'));
    }

    /**
     * Proses gambar soal menggunakan OCR
     */
    public function processScanImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // max 5MB
            'kelas_id' => 'required|exists:kelas,id',
            'kategori_id' => 'required|exists:kategori,id',
        ]);

        // Simpan gambar temporary
        $image = $request->file('image');
        $imagePath = $image->store('temp-ocr', 'public');
        $imageUrl = asset('storage/' . $imagePath);

        // Return data untuk view review
        return view('guru.soal.scan-review', [
            'imageUrl' => $imageUrl,
            'imagePath' => $imagePath,
            'kelas_id' => $request->kelas_id,
            'kategori_id' => $request->kategori_id,
            'kelas' => Kelas::all(),
            'kategori' => Kategori::all(),
        ]);
    }

    /**
     * Tampilkan halaman review OCR
     */
    public function showScanReview()
    {
        $kelas = Kelas::all();
        $kategori = Kategori::all();
        
        return view('guru.soal.scan-review', compact('kelas', 'kategori'));
    }

    /**
     * Simpan hasil scan ke database
     */
    public function saveScanResult(Request $request)
    {
        $validated = $request->validate([
            'questions' => 'required|array',
            'questions.*.pertanyaan' => 'required|string|min:10',
            'questions.*.pilihan_a' => 'required|string',
            'questions.*.pilihan_b' => 'required|string',
            'questions.*.pilihan_c' => 'required|string',
            'questions.*.pilihan_d' => 'required|string',
            'questions.*.jawaban_benar' => 'required|in:a,b,c,d',
            'kelas_id' => 'required|exists:kelas,id',
            'kategori_id' => 'required|exists:kategori,id',
        ]);

        $saved_count = 0;

        // Simpan setiap soal
        foreach ($validated['questions'] as $question) {
            Soal::create([
                'user_id' => Auth::id(),
                'kelas_id' => $validated['kelas_id'],
                'kategori_id' => $validated['kategori_id'],
                'pertanyaan' => $question['pertanyaan'],
                'pilihan_a' => $question['pilihan_a'],
                'pilihan_b' => $question['pilihan_b'],
                'pilihan_c' => $question['pilihan_c'],
                'pilihan_d' => $question['pilihan_d'],
                'jawaban_benar' => $question['jawaban_benar'],
            ]);
            $saved_count++;
        }

        // Hapus gambar temporary
        if ($request->has('imagePath')) {
            \Storage::disk('public')->delete($request->imagePath);
        }

        return redirect('/guru/soal')->with('success', "$saved_count soal berhasil ditambahkan dari scan!");
    }
}
