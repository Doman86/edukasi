<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tampilkan form login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($credentials)) {
            // Cek apakah user sudah verified
            $user = Auth::user();
            
            if ($user->status !== 'verified') {
                Auth::logout();
                return redirect('/login')->with('error', 'Akun Anda belum diverifikasi oleh admin. Silakan tunggu.');
            }

            $request->session()->regenerate();
            return redirect()->intended($this->redirectPath());
        }

        throw ValidationException::withMessages([
            'email' => 'Email atau password salah.',
        ]);
    }

    /**
     * Tampilkan form register
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Proses register
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle foto upload
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $fotoPath = $foto->store('foto_guru', 'public');
        }

        // Semua registrasi otomatis menjadi guru dengan status pending
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'guru',
            'status' => 'pending',
            'foto' => $fotoPath,
        ]);

        return redirect('/')->with('success', 'Registrasi berhasil! Silakan tunggu Admin memverifikasi akun Anda. Cek email Anda untuk informasi lebih lanjut.');
    }

    /**
     * Redirect path setelah login
     */
    protected function redirectPath()
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            return '/admin/dashboard';
        }
        
        return '/guru/dashboard';
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Logout berhasil');
    }
}
