<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CalonSiswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    /**
     * Masuk sebagai Calon Siswa (Mode Impersonasi).
     */
    public function start(CalonSiswa $calonSiswa): RedirectResponse
    {
        $admin = auth()->user();

        if (!$admin || !$admin->isAdmin()) {
            abort(403, 'Hanya Administrator yang memiliki wewenang untuk masuk sebagai calon siswa.');
        }

        $studentUser = $calonSiswa->user;

        if (!$studentUser) {
            return back()->with('error', 'Calon siswa ini belum memiliki akun user yang terhubung.');
        }

        $adminId = $admin->id;
        $calonSiswaId = $calonSiswa->id;

        // Login sebagai calon siswa
        Auth::login($studentUser);

        // Set session setelah login untuk memastikan session tetap bertahan
        session()->put('impersonate_admin_id', $adminId);
        session()->put('impersonated_calon_siswa_id', $calonSiswaId);

        return redirect()->route('calon-siswa.dashboard')->with(
            'success',
            "Anda sedang masuk sebagai {$calonSiswa->nama_lengkap} (Mode Impersonasi). Semua aksi akan berlaku pada akun siswa ini."
        );
    }

    /**
     * Keluar dari Mode Impersonasi dan kembali ke akun Administrator.
     */
    public function leave(Request $request): RedirectResponse
    {
        $adminId = session('impersonate_admin_id');
        $calonSiswaId = session('impersonated_calon_siswa_id');

        if (!$adminId) {
            return redirect()->route('dashboard');
        }

        $admin = User::find($adminId);

        if (!$admin || !$admin->isAdmin()) {
            session()->forget(['impersonate_admin_id', 'impersonated_calon_siswa_id']);
            return redirect()->route('login')->with('error', 'Sesi admin tidak valid. Silakan login kembali.');
        }

        // Kembalikan login ke admin
        Auth::login($admin);

        // Hapus session impersonasi
        session()->forget(['impersonate_admin_id', 'impersonated_calon_siswa_id']);

        if ($calonSiswaId) {
            return redirect()->route('admin.calon-siswa.show', $calonSiswaId)
                ->with('success', 'Sesi impersonasi telah selesai. Anda kembali ke akun Administrator.');
        }

        return redirect()->route('admin.calon-siswa.index')
            ->with('success', 'Sesi impersonasi telah selesai. Anda kembali ke akun Administrator.');
    }
}
