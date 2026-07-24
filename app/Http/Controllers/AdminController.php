<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Tampilkan dashboard admin dengan daftar guru pending
     */
    public function dashboard()
    {
        $guruPending = User::where('role', 'guru')
                           ->where('status', 'pending')
                           ->get();
        
        $guruVerified = User::where('role', 'guru')
                            ->where('status', 'verified')
                            ->get();
        
        return view('admin.dashboard', compact('guruPending', 'guruVerified'));
    }

    /**
     * Verifikasi guru
     */
    public function verifyGuru(User $user)
    {
        if ($user->role !== 'guru' || $user->status !== 'pending') {
            return back()->with('error', 'Data tidak valid');
        }

        $user->update(['status' => 'verified']);
        return back()->with('success', 'Guru ' . $user->name . ' berhasil diverifikasi');
    }

    /**
     * Tolak guru
     */
    public function rejectGuru(User $user)
    {
        if ($user->role !== 'guru') {
            return back()->with('error', 'Data tidak valid');
        }

        $user->update(['status' => 'rejected']);
        return back()->with('success', 'Guru ' . $user->name . ' ditolak');
    }
}
