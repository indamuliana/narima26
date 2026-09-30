<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $loginInput = trim($request->input('login'));
        $password = $request->input('password');

        // Search user by email or username (which holds NISN for students)
        $user = User::where('email', $loginInput)
            ->orWhere('username', $loginInput)
            ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'login' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi panitia SPMB SMK Wikrama 1 Garut.',
            ]);
        }

        // If previously logged in as another user, invalidate previous session first
        if (Auth::check()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        // Audit Trail login event
        if (function_exists('activity')) {
            activity('auth')
                ->causedBy($user)
                ->withProperties([
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'role' => $user->role,
                ])
                ->log("Pengguna {$user->name} ({$user->role_label}) berhasil login.");
        }

        return redirect()->intended(route($user->getDashboardRoute()))
            ->with('success', "Selamat datang kembali, {$user->name}!");
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Audit Trail logout event
        if ($user && function_exists('activity')) {
            activity('auth')
                ->causedBy($user)
                ->withProperties([
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ])
                ->log("Pengguna {$user->name} logout.");
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $redirectUrl = $request->input('redirect', $request->query('redirect', '/'));

        return redirect($redirectUrl)->with('success', 'Anda telah berhasil keluar.');
    }
}
