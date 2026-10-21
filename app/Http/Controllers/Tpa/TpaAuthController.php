<?php

namespace App\Http\Controllers\Tpa;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TpaAuthController extends Controller
{
    /**
     * Tampilkan halaman login khusus TPA.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isCalonSiswa()) {
                return redirect()->route('tpa.ruang-tunggu');
            }
        }

        return view('tpa.auth.login');
    }

    /**
     * Autentikasi calon siswa ke portal TPA.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'NISN atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput = trim($request->input('login'));
        $password = $request->input('password');

        $user = User::where(function ($query) use ($loginInput) {
            $query->where('email', $loginInput)
                  ->orWhere('username', $loginInput);
        })->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => 'Kredensial NISN/Email dan Password tidak cocok.',
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'login' => 'Akun pendaftaran Anda sedang dinonaktifkan. Hubungi panitia SPMB.',
            ]);
        }

        if (! $user->isCalonSiswa()) {
            throw ValidationException::withMessages([
                'login' => 'Portal ini khusus diperuntukkan bagi Calon Siswa SPMB.',
            ]);
        }

        // Pastikan relasi calonSiswa ada
        if (! $user->calonSiswa) {
            throw ValidationException::withMessages([
                'login' => 'Data biodata calon siswa belum terdaftar secara lengkap.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route('tpa.ruang-tunggu');
    }

    /**
     * Logout dari sesi portal TPA.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('tpa.login')->with('success', 'Anda telah berhasil keluar dari sesi ujian.');
    }
}
